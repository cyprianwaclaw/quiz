<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index',[
            'users' => User::with(['quizzes', 'invited'])->paginate(15),
        ]);
    }

    public function show(User $user)
    {
        $user->load(['quizzes', 'invited']);
        return view('users.show',[
            'user' => $user,
        ]);
    }
}
