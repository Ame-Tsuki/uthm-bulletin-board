<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'uthm_id' => ['required', 'string', 'max:20', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'regex:/^[a-zA-Z0-9._%+-]+@(student\.)?uthm\.edu\.my$/i'],
            'phone' => ['nullable', 'string', 'regex:/^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/'],
            'role' => ['required', 'string', Rule::in(['student', 'staff', 'admin', 'club_admin'])],
            'faculty' => ['required_if:role,student', 'nullable', 'string', 'max:100'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'],
        ], [
            'email.regex' => 'Please use a valid UTHM email address',
            'phone.regex' => 'Please enter a valid Malaysian phone number',
            'faculty.required_if' => 'Faculty is required for students',
            'terms.required' => 'You must accept the terms and conditions',
        ]);

        // Create user
        $user = User::create([
            'uthm_id' => strtoupper($request->uthm_id),
            'name' => $request->name,
            'email' => strtolower($request->email),
            'phone' => $request->phone,
            'role' => $request->role,
            'faculty' => $request->role === 'student' ? $request->faculty : null,
            'is_verified' => false, // Needs admin approval
            'password' => Hash::make($request->password),
        ]);

        // Trigger registered event
        event(new Registered($user));

        // Log user in
        Auth::login($user);

        // Redirect to verification notice
        return redirect()->route('verification.notice');
    }
}