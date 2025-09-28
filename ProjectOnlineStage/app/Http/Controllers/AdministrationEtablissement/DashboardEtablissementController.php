<?php
// app/Http/Controllers/AdministrationEtablissement/DashboardEtablissementController.php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardEtablissementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur est un directeur d'établissement
        if ($user->role !== 'directeur_etablissement') {
            return redirect()->route('welcome')
                ->with('error', 'Accès refusé. Vous n\'êtes pas directeur d\'établissement.');
        }
        
        // Vérifier si le directeur a un établissement associé
        $etablissement = $user->etablissement;
        if (!$etablissement) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun établissement associé à votre compte. Veuillez contacter l\'administrateur.');
        }
        
        return view('administrationetablissement.dashboard', compact('user', 'etablissement'));
    }
}