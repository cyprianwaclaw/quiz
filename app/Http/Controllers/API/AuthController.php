<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\UserResource;
use App\Models\Invite;
use Illuminate\Auth\Events\Registered;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\MyMail;
use App\Mail\VerificationMail;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use const Grpc\STATUS_PERMISSION_DENIED;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Models\UserStats;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends APIController
{

    /**
     * Registration
     * @unauthenticated
     * @group Authorization
     * @responseFile status=201 scenario="Registration success" storage/api-docs/responses/auth/register.201.json
     * @responseFile status=422 scenario="Validation error" storage/api-docs/responses/error.422.json
     * @responseFile status=403 scenario="Registration limit reached" storage/api-docs/responses/auth/register.403.json
     * @responseFile status=404 scenario="Invitation token not found" storage/api-docs/responses/auth/register.403.json
     */

    public function registerUser(registerUserRequest $request)
    {
        $verificationCode = Str::random(6);
        $user = User::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_code' => $verificationCode,
        ]);

        // Mail::to('cyprianwaclaw@gmail.com')->send(new VerificationMail($user, $request->page_name));
        // Wysyłka e-maila z kodem do usera
        Mail::to($user->email)->send(new VerificationMail($user, $request->page_name));

        return response()->json([
            'success' => true,
            'message' => 'Utworzono nowego użytkownika',
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ], 201);
    }

    /**
     * Login
     *
     * @group Authorization
     * @responseFile status=201 scenario="Registration success" storage/api-docs/responses/auth/login.200.json
     * @responseFile status=404 scenario="The given data was invalid" storage/api-docs/responses/auth/login.404.json
     * @responseFile status=422 scenario="Validation error" storage/api-docs/responses/error.422.json
     * @unauthenticated
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        $user = User::where('email', $credentials['email'])->first();
        // !Hash::check($credentials['password'], $user->password)
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'errors' => [
                    'notExist' => ['Błędne dane logowania'],
                    // 'user' => $user,
                    // 'password' => $credentials['password'],
                ]
            ], 401);
        }

        return response()->json([
            'user_image' => $user->image,
            'isVerified' => $user->email_verified ? true : false,
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ], 200);
    }

    /**
     * Return logged user object
     *
     * @group Operation about user
     *
     * @responseFile status=200 scenario="Object fetched" api-docs/responses/users/current.200.json
     * @responseFile status=401 scenario="Unauthenticated" api-docs/responses/401.json
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     *
     */

    public function getCurrentUser1(Request $request)
    {
        $user = User::findOrFail(auth()->user()->id);
        $stats = $user->stats;
        $invited = User::where('invited_by', $user->id)->count();
        return response([
            'user_name' => $user->name,
            'user_surname' => $user->surname,
            'user_email' => $user->email,
            'user_phone' => $user->phone,

            'answers' => [
                'correct' => $stats->correct_answers,
                'incorrect' => $stats->incorrect_answers,
                'all' => $stats->correct_answers + $stats->incorrect_answers,
            ],
            'points' => $user->points,
            'avatar' => $user->avatar_path ? $user->avatar_path : false,
            'plan' => $user->hasPremium() ? 'Premium' : 'Standard'
        ]);
    }

    public function getCurrentUser(Request $request)
    {
        $user = User::findOrFail(auth()->user()->id);
        $stats = $user->stats;
        $invited = User::where('invited_by', $user->id)->count();

        $isPremium = $user->hasPremium();
        $premiumEndsAt = null;

        if ($isPremium) {
            $subscription = $user->subscriptions()
                ->where('name', 'premium')
                ->whereNull('canceled_at')
                ->orderByDesc('ends_at')
                ->first();

            if ($subscription) {
                $premiumEndsAt = $subscription->ends_at;
            }
        }

        return response([
            'user_name' => $user->name,
            'user_surname' => $user->surname,
            'user_email' => $user->email,
            'user_phone' => $user->phone,

            'answers' => [
                'correct' => $stats->correct_answers,
                'incorrect' => $stats->incorrect_answers,
                'all' => $stats->correct_answers + $stats->incorrect_answers,
            ],
            'points' => $user->points,
            'avatar' => $user->avatar_path ?: false,
            'plan' => $isPremium ? 'Premium' : 'Standard',
            'premium_end' => $premiumEndsAt,
        ]);
    }

    /**
     * Return invitation token for logged user
     *
     * @group Operation about user
     *
     * @responseFile status=200 scenario="Object fetched" api-docs/responses/users/getInvitationToken.200.json
     * @responseFile status=401 scenario="Unauthenticated" api-docs/responses/401.json
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|Response
     *
     */
    public function getInvitationToken()
    {
        return response(['invitationToken' => Auth::user()->invite->token]);
    }

    public function verifyEmail(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'verification_code' => 'required',
        ], [
            'verification_code.required' => 'Kod wymagany',
        ]);

        $user = User::where('email', $request->email)
            ->where('verification_code', $request->verification_code)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Błędny kod weryfikacyjny'
            ], 400);
        }

        $user->email_verified_at = now();
        $user->email_verified = 1;
        $user->verification_code = null;
        $user->save();

        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'E-mail został zweryfikowany pomyślnie',
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ]);
    }

    public function sendNewCode(Request $request)
    {

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Użytkownik nie istnieje'
            ], 404);
        }


        $newVerificationCode = Str::random(6);
        $user->verification_code = $newVerificationCode;
        $user->save();

        // Mail::to('cyprianwaclaw@gmail.com')->send(new VerificationMail($user, $request->page_name));
        // Wysyłka e-maila z kodem do usera
        Mail::to($user->email)->send(new VerificationMail($user, $request->page_name));

        return response()->json([
            'success' => true,
            'message' => 'Nowy kod weryfikacyjny został wysłany na Twój e-mail'
        ], 200);
    }

    public function sendChangeEmailCode(Request $request)
    {
        // Walidacja czy email już istnieje w bazie
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'Ten e-mail jest już zajęty'
            ], 422);
        }

        $user = Auth::user();

        $newVerificationCode = Str::random(6);
        $user->verification_code = $newVerificationCode;
        $user->save();

        // Wysyłka e-maila z kodem do usera
        Mail::to($request->email)->send(new VerificationMail($user, $request->page_name));

        return response()->json([
            'e-mail' => $request->email,
            "code" => $newVerificationCode,
            'success' => true,
            'message' => 'Nowy kod weryfikacyjny został wysłany na Twój e-mail'
        ], 200);
    }

    public function sendResetPasswordCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Błędny adres e-mail',
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Użytkownik o podanym adresie e-mail nie istnieje',
            ], 404);
        }

        $verificationCode = Str::random(6);
        $user->verification_code = $verificationCode;
        $user->save();

        // Mail::to('cyprianwaclaw@gmail.com')->send(new VerificationMail($user, $request->page_name));
        // Wysyłka e-maila z kodem do usera
        Mail::to($user->email)->send(new VerificationMail($user, $request->page_name));

        return response()->json([
            'success' => true,
            'your-code' =>  $verificationCode,
            'message' => 'Nowy kod weryfikacyjny został wysłany na Twój e-mail'
        ], 200);
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'verification_code' => 'required|string',
            'password' => 'required|string|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{6,}$/',
            'confirm_password' => 'required|string|same:password',
        ]);

        $user = User::where('email', $request->email)
            ->where('verification_code', $request->verification_code)
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'messageError' => 'Błędny kod'
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->verification_code = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Hasło zostało zmienione pomyślnie',
            'isVerified' => $user->email_verified,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+])[A-Za-z\d!@#$%^&*()_+]{6,}$/',
            'confirm_password' => 'required|string|same:password',
        ]);

        $user = Auth::user();

        if (!$user || !Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'messageError' => 'Błędne hasło'
            ], 400);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Hasło zostało zmienione pomyślnie',
        ]);
    }

    public function changeEmail(Request $request)
    {
        $request->validate([
            'new_email' => 'required|email',
            'confirm_email' => 'required|email|same:new_email',
            'code' => 'required|string',
        ]);

        $user = Auth::user(); // Pobiera aktualnie zalogowanego użytkownika

        if (!$user || $user->verification_code !== $request->code) {
            return response()->json([
                'success' => false,
                'messageError' => 'Błędny kod weryfikacyjny'
            ], 400);
        }

        $user->email = $request->new_email;
        $user->verification_code = null; // Czyszczenie kodu po poprawnej zmianie e-maila
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Adres e-mail został zmieniony pomyślnie',
        ]);
    }
}