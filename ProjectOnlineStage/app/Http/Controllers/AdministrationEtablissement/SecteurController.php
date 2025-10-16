<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Secteur;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecteurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        // Récupérer uniquement les secteurs de l'établissement connecté
        $secteurs = Secteur::with(['filieres'])
            ->where('code_efp', $etablissement->code_efp)
            ->withCount(['filieres'])
            ->latest()
            ->paginate(10);

        return view('administrationetablissement.secteurs.index', compact('secteurs', 'etablissement'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        return view('administrationetablissement.secteurs.create', compact('etablissement'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        $request->validate([
            'nom_secteur' => 'required|string|max:255',
        ], [
            'nom_secteur.required' => 'Le nom du secteur est obligatoire',
        ]);

        Secteur::create([
            'nom_secteur' => $request->nom_secteur,
            'code_efp' => $etablissement->code_efp,
        ]);

        return redirect()->route('administration.etablissement.secteurs.index')
            ->with('success', 'Secteur créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        // Vérifier que le secteur appartient à l'établissement
        $secteur = Secteur::with([
            'filieres' => function($query) use ($etablissement) {
                $query->where('code_efp', $etablissement->code_efp);
            },
            'filieres.groupes' => function($query) use ($etablissement) {
                $query->where('code_efp', $etablissement->code_efp);
            }
        ])
        ->where('code_efp', $etablissement->code_efp)
        ->findOrFail($id);

        // Calculer les statistiques
        $stats = [
            'total_filieres' => $secteur->filieres->count(),
            'total_groupes' => $secteur->filieres->sum(function ($filiere) {
                return $filiere->groupes->count();
            }),
            'total_stagiaires' => $secteur->filieres->sum(function ($filiere) {
                return $filiere->groupes->sum('effectif_groupe');
            }),
        ];

        return view('administrationetablissement.secteurs.show', compact('secteur', 'stats', 'etablissement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        // Vérifier que le secteur appartient à l'établissement
        $secteur = Secteur::where('code_efp', $etablissement->code_efp)
            ->findOrFail($id);

        return view('administrationetablissement.secteurs.edit', compact('secteur', 'etablissement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        // Vérifier que le secteur appartient à l'établissement
        $secteur = Secteur::where('code_efp', $etablissement->code_efp)
            ->findOrFail($id);

        $request->validate([
            'nom_secteur' => 'required|string|max:255',
        ], [
            'nom_secteur.required' => 'Le nom du secteur est obligatoire',
        ]);

        $secteur->update([
            'nom_secteur' => $request->nom_secteur,
        ]);

        return redirect()->route('administration.etablissement.secteurs.index')
            ->with('success', 'Secteur modifié avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        // Vérifier que le secteur appartient à l'établissement
        $secteur = Secteur::where('code_efp', $etablissement->code_efp)
            ->findOrFail($id);

        // Vérifier s'il y a des filières associées de cet établissement
        $filieresCount = $secteur->filieres()
            ->where('code_efp', $etablissement->code_efp)
            ->count();
            
        if ($filieresCount > 0) {
            return back()->with('error', 'Impossible de supprimer ce secteur car il contient des filières');
        }

        $secteur->delete();

        return redirect()->route('administration.etablissement.secteurs.index')
            ->with('success', 'Secteur supprimé avec succès');
    }
}