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
            'code' => [
                'required',
                'string',
                'max:50',
                // Le code doit être unique pour cet établissement uniquement
                'unique:secteurs,code,NULL,id,code_efp,' . $etablissement->code_efp
            ],
            'nom' => 'required|string|max:255',
        ], [
            'code.required' => 'Le code du secteur est obligatoire',
            'code.unique' => 'Ce code de secteur existe déjà dans votre établissement',
            'nom.required' => 'Le nom du secteur est obligatoire',
        ]);

        Secteur::create([
            'code' => strtoupper($request->code),
            'nom' => $request->nom,
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
            'filieres.niveau',
            'filieres.groupes' => function($query) use ($etablissement) {
                $query->where('code_efp', $etablissement->code_efp);
            },
            'filieres.groupes.formation'
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
                return $filiere->groupes->sum('effectif');
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
            'code' => [
                'required',
                'string',
                'max:50',
                // Le code doit être unique pour cet établissement uniquement
                'unique:secteurs,code,' . $id . ',id,code_efp,' . $etablissement->code_efp
            ],
            'nom' => 'required|string|max:255',
        ], [
            'code.required' => 'Le code du secteur est obligatoire',
            'code.unique' => 'Ce code de secteur existe déjà dans votre établissement',
            'nom.required' => 'Le nom du secteur est obligatoire',
        ]);

        $secteur->update([
            'code' => strtoupper($request->code),
            'nom' => $request->nom,
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