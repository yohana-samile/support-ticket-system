<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showForgotPassForm(){
        return view('auth.forgot-password');
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $response = Password::sendResetLink($request->only('email'));
        if ($response == Password::RESET_LINK_SENT) {
            return back()->with('status', __('We have e-mailed your password reset link!'));
        }

        return back()->withErrors(['email' => __('We could not find a user with that email address.')]);
    }

    public function reset(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $response = Password::reset(
            $validated,
            function ($user) use ($request) {
                $user->forceFill([
                    'password' =>  Hash::make($request->password),
                ])->save();
                 Auth::login($user);
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            return redirect('/gbv/layouts/dashboard');
        }
        return back()->withErrors(['email' => __('This password reset token is invalid.')]);
    }
}
