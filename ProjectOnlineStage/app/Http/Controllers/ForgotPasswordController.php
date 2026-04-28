<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use App\Mail\MyEmail;

class ForgotPasswordController extends Controller
{
    public function showForgetPasswordForm()
    {
        return view('auth.forgetpassword');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        
        // Generic response to prevent user enumeration
        $genericMessage = 'Si votre adresse email existe dans notre base de données, vous recevrez un code de réinitialisation.';

        if ($user) {
            $code = rand(100000, 999999);
            session(['reset_code' => $code, 'reset_email' => $user->email]);

            // Utilisation de queue() au lieu de send() pour éviter les attaques temporelles (Timing Attacks)
            Mail::to($user->email)->queue(new MyEmail($code));
        }

        return redirect()->route('forgot.password.code.form')->with('success', $genericMessage);
    }

    public function showVerifyCodeForm()
    {
        return view('auth.verifycode');
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required']);

        // Identify the user by session ID to track failed attempts
        $key = 'verify_code_attempts:' . session()->getId();

        // Check if the user has already exceeded the maximum number of attempts (3)
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            // Clear the reset code so they must request a new one
            session()->forget(['reset_code', 'reset_email']);
            return redirect()->route('forgot.password.form')->with('error', "Trop de tentatives échouées. Veuillez demander un nouveau code (réessayez dans $seconds secondes).");
        }

        if ($request->code != session('reset_code')) {
            // Record a failed attempt
            RateLimiter::hit($key, 600); // 600 seconds = 10 minutes lockout
            $attemptsLeft = 3 - RateLimiter::attempts($key);
            
            if ($attemptsLeft > 0) {
                return back()->with('error', "Code incorrect. Il vous reste $attemptsLeft tentative(s).");
            } else {
                session()->forget(['reset_code', 'reset_email']);
                return redirect()->route('forgot.password.form')->with('error', "Trop de tentatives échouées. Veuillez demander un nouveau code.");
            }
        }

        // ✅ Si le code est bon → clear the rate limiter and redirect
        RateLimiter::clear($key);
        return redirect()->route('forgot.password.reset.form')->with('success', 'Code vérifié, vous pouvez maintenant changer votre mot de passe.');
    }

    public function showResetPasswordForm()
    {
        return view('auth.resetpassword');
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['password' => 'required|min:6|confirmed']);

        $user = User::where('email', session('reset_email'))->first();

        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        session()->forget(['reset_code', 'reset_email']);

        return redirect()->route('login')->with('success', 'Mot de passe changé avec succès.');
    }
}
