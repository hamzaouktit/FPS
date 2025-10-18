<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
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
        if (!$user) {
            return back()->with('error', 'Cet email n\'existe pas.');
        }

        $code = rand(100000, 999999);
        session(['reset_code' => $code, 'reset_email' => $user->email]);

        Mail::to($user->email)->send(new MyEmail($code));

        return redirect()->route('forgot.password.code.form')->with('success', 'Code envoyé à votre email.');
    }

    public function showVerifyCodeForm()
    {
        return view('auth.verifycode');
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required']);

        if ($request->code != session('reset_code')) {
            return back()->with('error', 'Code incorrect.');
        }

        // ✅ Si le code est bon → rediriger vers la page de réinitialisation
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
