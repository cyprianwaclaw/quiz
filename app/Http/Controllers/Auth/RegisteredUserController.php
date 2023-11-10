<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request): \Illuminate\View\View
    {

        if ($request->invitation) {
            $invite = Invite::where('token', $request->invitation)->first();
            if($invite) {
                return view('auth.register', ['invitation' => $invite->token]);
//                    dd($invite->user->id);  // user id zgadza sie
//                    dd('jakis jest');
            }else{
                // TODO: zrobić widok
                dd('nie znaleziono tokena');
            }
        }else{
            if (User::count() < env('MAX_REGISTER_USERS')) {
                return view('auth.register');
            }else {
                // TODO: zrobić widok
                dd('Możesz się zarejestrować tylko przez link zapraszający');
            }
        }
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $invited_by = null;
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if ($request->invitation) {
            $invite = Invite::where('token', $request->invitation)->first();
            if($invite) {
                $invited_by = $invite->user->id;
//                dd($invite->user->id);  // user id zgadza sie
//                dd('jakis jest');
            }else{
                dd('nie znaleziono tokena');
            }
        }elseif(User::count() >= env('MAX_REGISTER_USERS')){
            dd('Możesz się zarejestrować tylko przez link zapraszający');
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'invited_by' => $invited_by,
            'points' => 0,
        ]);
        $invite = $user->invite()->create([
            'token' => Str::random(20),
        ]);

        event(new Registered($user));
        if(User::count() <= env('MAX_REGISTER_USERS')){
            $plan = app('rinvex.subscriptions.plan')->find(1);
            $user->assignRole('premium');
            $user->newPlanSubscription('permanent', $plan);
        }
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
