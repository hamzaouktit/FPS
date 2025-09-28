<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Les informations d\'identification ne correspondent pas.',
        ])->onlyInput('email');
    }

    /**
     * Rediriger l'utilisateur selon son rôle
     */
    protected function redirectBasedOnRole($user)
    {
        switch ($user->role) {
            case 'directeur_complexe':
                return redirect()->route('administration.complexe.dashboard')
                    ->with('success', 'Bienvenue ' . $user->nom . ' - ' . ($user->complexe->nom ?? 'Complexe'));
                
            case 'directeur_etablissement':
                return redirect()->route('administration.etablissement.dashboard')
                    ->with('success', 'Bienvenue ' . $user->nom . ' - ' . ($user->etablissement->nom_efp ?? 'Établissement'));
                
            default:
                return redirect()->route('welcome')
                    ->with('success', 'Bienvenue ' . $user->nom);
        }
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('welcome')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Vérifier l'authentification et rediriger
     */
    public function checkAuth()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        
        return redirect()->route('login');
    }
}