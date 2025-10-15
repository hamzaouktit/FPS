<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Groupe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        $groupes = Groupe::where('code_efp', $etablissement->code_efp)->paginate(10);

        return view('administrationetablissement.groupes.index', compact('groupes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        $filieres = $etablissement->filieres;
        $formations = $etablissement->formations;

        return view('administrationetablissement.groupes.create', compact('filieres', 'formations', 'etablissement'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'effectif' => 'required|integer|min:0',
            'statut' => 'required|string|in:Actif,Inactif',
            'fusion_groupe' => 'nullable|string|max:255',
            'code_fusion' => 'nullable|string|max:255',
            'annee_formation' => 'required|integer',
            'annee' => 'required|integer',
            'filiere_id' => 'required|exists:filieres,id',
            'formation_id' => 'required|exists:formations,id',
        ]);

        // Ajouter automatiquement les informations de l'établissement
        $validated['code_efp'] = $etablissement->code_efp;
        $validated['efp_code'] = $etablissement->code_efp;
        $validated['efp_nom'] = $etablissement->nom_efp;

        Groupe::create($validated);

        return redirect()->route('administration.etablissement.groupes.index')
            ->with('success', 'Groupe créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Groupe $groupe)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        // Vérifier que le groupe appartient à l'établissement
        if ($groupe->code_efp !== $etablissement->code_efp) {
            abort(403);
        }

        $groupe->load('filiere', 'formation', 'affectations.module', 'affectations.formateurPresentiel', 'affectations.formateurSynchrone', 'affectations.avancement');

        return view('administrationetablissement.groupes.show', compact('groupe'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Groupe $groupe)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        // Vérifier que le groupe appartient à l'établissement
        if ($groupe->code_efp !== $etablissement->code_efp) {
            abort(403);
        }

        $filieres = $etablissement->filieres;
        $formations = $etablissement->formations;

        return view('administrationetablissement.groupes.edit', compact('groupe', 'filieres', 'formations', 'etablissement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Groupe $groupe)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        // Vérifier que le groupe appartient à l'établissement
        if ($groupe->code_efp !== $etablissement->code_efp) {
            abort(403);
        }

        $validated = $request->validate([
            'code' => 'required|string|max:255',
            'effectif' => 'required|integer|min:0',
            'statut' => 'required|string|in:Actif,Inactif',
            'fusion_groupe' => 'nullable|string|max:255',
            'code_fusion' => 'nullable|string|max:255',
            'annee_formation' => 'required|integer',
            'annee' => 'required|integer',
            'filiere_id' => 'required|exists:filieres,id',
            'formation_id' => 'required|exists:formations,id',
        ]);

        // Mettre à jour automatiquement les informations de l'établissement
        $validated['efp_code'] = $etablissement->code_efp;
        $validated['efp_nom'] = $etablissement->nom_efp;

        $groupe->update($validated);

        return redirect()->route('administration.etablissement.groupes.index')
            ->with('success', 'Groupe mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Groupe $groupe)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        // Vérifier que le groupe appartient à l'établissement
        if ($groupe->code_efp !== $etablissement->code_efp) {
            abort(403);
        }

        $groupe->delete();

        return redirect()->route('administration.etablissement.groupes.index')
            ->with('success', 'Groupe supprimé avec succès.');
    }
}