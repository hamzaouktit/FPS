<?php
// app/Http/Controllers/AdministrationComplexe/DashboardComplexeController.php

namespace App\Http\Controllers\AdministrationComplexe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardComplexeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur est un directeur de complexe
        if ($user->role !== 'directeur_complexe') {
            return redirect()->route('welcome')
                ->with('error', 'Accès refusé. Vous n\'êtes pas directeur de complexe.');
        }
        
        // Vérifier si le directeur a un complexe associé
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé à votre compte. Veuillez contacter l\'administrateur.');
        }
        
        return view('administrationcomplexe.dashboard', compact('user', 'complexe'));
    }
}