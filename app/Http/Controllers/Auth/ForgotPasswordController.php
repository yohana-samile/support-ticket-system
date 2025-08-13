<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Access\Client;
use App\Models\Access\User;
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
            'email' => 'required|email',
        ]);

        $email = $request->email;

        if (User::where('email', $email)->exists()) {
            $response = Password::broker('users')->sendResetLink(['email' => $email]);
        } elseif (Client::where('email', $email)->exists()) {
            $response = Password::broker('clients')->sendResetLink(['email' => $email]);
        } else {
            return back()->withErrors(['email' => __('We could not find an account with that email address.')]);
        }

        return $response == Password::RESET_LINK_SENT
            ? back()->with('status', __('We have e-mailed your password reset link!'))
            : back()->withErrors(['email' => __('Unable to send reset link.')]);
    }

    public function reset(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $email = $validated['email'];
        $broker = User::where('email', $email)->exists() ? 'users' : 'clients';

        $response = Password::broker($broker)->reset(
            $validated,
            function ($account) use ($request, $broker) {
                $account->forceFill([
                    'password' => Hash::make($request->password),
                ])->save();

                if (!$account->is_active) {
                    throw new \Exception('Your account is blocked. Please contact the administrator.');
                }

                Auth::guard($broker === 'users' ? 'web' : 'client')->login($account);
            }
        );

        return $response == Password::PASSWORD_RESET
            ? redirect()->route('home')->with('status', __('Your password has been reset!'))
            : back()->withErrors(['email' => __('This password reset token is invalid.')]);
    }
}
