<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\UserResource;
use App\Models\Invite;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\test;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use const Grpc\STATUS_PERMISSION_DENIED;
use Illuminate\Support\Str;
use App\Models\UserStats;

class AuthController extends APIController
{
    public function verifyUserEmail($id)
    {
        // $request->validate([
        //  'user_id' => 'required|exists:users,id',
        // ]);

        $user = User::find($id);

        // Przyjmij, że przyjmuje bieżącą datę i godzinę jako email_verified_atžž
        $user->email_verified_at = now();
        $user->save();

        return response(['user' => $user]);
    }

    /**
     * Registration
     * @unauthenticated
     * @group Authorization
     * @responseFile status=201 scenario="Registration success" storage/api-docs/responses/auth/register.201.json
     * @responseFile status=422 scenario="Validation error" storage/api-docs/responses/error.422.json
     * @responseFile status=403 scenario="Registration limit reached" storage/api-docs/responses/auth/register.403.json
     * @responseFile status=404 scenario="Invitation token not found" storage/api-docs/responses/auth/register.403.json
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            // Example: test5
            'name' => 'required|max:55',
            // Example: test@example.com
            'email' => 'email|required|unique:users',
            // Example: test123456
            'password' => 'required|confirmed|min:8',
            // Example: test123456
            'password_confirmation' => 'required|same:password',
            // Example: y12rOwSuEDxuI3691N1v
            'invitation' => 'exists:App\Models\Invite,token'
        ]);

        if ($request->invitation) {
            $invite = Invite::where('token', $request->invitation)->first();
            if ($invite) {
                $validatedData['invited_by'] = $invite->user->id;
                //                dd($invite->user->id);  // user id zgadza sie
                //                return response('jakis jest');
            } else {
                return response(['message' => 'Invitation token not found'], Response::HTTP_NOT_FOUND);
            }
        } elseif (User::count() >= config('auth.max_register_users')) {
            //            return response(['message'=>'Możesz się zarejestrować tylko przez link zapraszający'], Response::HTTP_FORBIDDEN);
            return response(['message' => 'Registration by invitation only'], Response::HTTP_FORBIDDEN);
        }
        //        return response(['message' => 'Registartion successfully'], Response::HTTP_CREATED);
        $validatedData['password'] = Hash::make($request->password);
        $user = User::create($validatedData);

        // Mail::to($user->email)->send(new test($user));
        $token = Str::random(20);
        Invite::create([
            'user_id' => $user->id, // Przypisz ID nowo-zarejestrowanego użytkownika
            'token' => $token,
        ]);

        // event(new Registered($user));
        $accessToken = $user->createToken('authToken')->plainTextToken;
        Auth::login($user);
        return response(['message' => 'Registartion successfully', 'user' => $user, 'access_token' => $accessToken, 'invitation_token' => $invite->token,], Response::HTTP_CREATED);
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
    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required|min:8'
        ]);
        if (!Auth::attempt($loginData)) {
            return response(['message' => 'This User does not exist, check your details'], Response::HTTP_NOT_FOUND);
        }
        /** @var $user User */
        $user = Auth::user();

        $accessToken = $user->createToken('authToken')->plainTextToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);
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

    public function getCurrentUser(Request $request)
    {
        $user = User::findOrFail(auth()->user()->id);
        $stats = $user->stats;
        $invited = User::where('invited_by', $user->id)->count();
        return response([
            'user_name' => $user->name,
            'user_surname' => $user->surname,
            'answers' => [
                'correct' => $stats->correct_answers,
                'incorrect' => $stats->incorrect_answers,
                'all' => $stats->correct_answers + $stats->incorrect_answers,
            ],
            'points' => $user->points,
            'invited_people' => $invited,
            'invitation_token' => $user->invite->token,
            'avatar' => $user->avatar_path ? $user->avatar_path : false,
            'plan' => $user->hasPremium() ? 'Premium' : 'Standard'
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
}
