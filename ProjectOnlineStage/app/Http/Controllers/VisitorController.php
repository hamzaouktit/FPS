<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Etablissement;
use App\Models\Secteur;
use App\Models\Filiere;

class VisitorController extends Controller
{
    public function index()
    {
        // Récupérer tous les établissements avec leur complexe
        $etablissements = Etablissement::with('complexe')->get();
        
        // Récupérer tous les secteurs avec leurs filières
        $secteurs = Secteur::with('filieres')->get();
        
        // Informations du complexe (vous pouvez les modifier selon vos besoins)
        $complexeInfo = [
            'nom' => 'Complexe de Formation Professionnelle Marrakech',
            'description' => 'Un pôle d\'excellence en formation professionnelle offrant des formations de qualité dans divers secteurs.',
            'telephone' => '+212 5XX XX XX XX',
            'email' => 'contact@complexemarrakech.ma',
            'adresse' => 'Marrakech, Maroc'
        ];
        
        return view('visitor', compact('etablissements', 'secteurs', 'complexeInfo'));
    }
}