<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Avancement;
use App\Models\Affectation;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvancementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer l'établissement du directeur connecté
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
        }

        // Récupérer les avancements avec les relations et trier
        $avancements = Avancement::with([
                'affectation.groupe',
                'affectation.module',
                'affectation.formateurPresentiel',
                'affectation.formateurSyn'
            ])
            ->where('code_efp', $etablissement->code_efp)
            ->get()
            ->groupBy(function($avancement) {
                return $avancement->affectation->groupe->code_groupe . ' - ' . $avancement->affectation->module->nom_module;
            })
            ->sortBy(function($avancementsGroupe, $key) {
                // Extraire le code_groupe et nom_module pour le tri
                $firstAvancement = $avancementsGroupe->first();
                $groupeCode = $firstAvancement->affectation->groupe->code_groupe;
                $moduleNom = $firstAvancement->affectation->module->nom_module;
                
                // Créer une clé de tri combinée
                return $groupeCode . '|' . $moduleNom;
            }, SORT_NATURAL | SORT_FLAG_CASE);

        return view('administrationetablissement.avancements.index', compact('avancements', 'etablissement'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
        }

        // Récupérer les affectations de l'établissement qui n'ont pas encore d'avancement
        $affectations = Affectation::with(['groupe', 'module', 'formateurPresentiel', 'formateurSyn'])
            ->where('code_efp', $etablissement->code_efp)
            ->whereDoesntHave('avancement')
            ->get()
            ->sortBy(function($affectation) {
                return $affectation->groupe->code_groupe . '|' . $affectation->module->nom_module;
            });

        return view('administrationetablissement.avancements.create', compact('affectations', 'etablissement'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'affectation_id' => 'required|exists:affectations,id',
            'mh_realisee_presentiel' => 'required|numeric|min:0',
            'mh_realisee_sync' => 'required|numeric|min:0',
            'mh_realisee_globale' => 'required|numeric|min:0',
            'taux_realisation_presentiel' => 'required|numeric|min:0|max:100',
            'taux_realisation_syn' => 'required|numeric|min:0|max:100',
            'taux_realisation_globale' => 'required|numeric|min:0|max:100',
            'moyenne_absence' => 'required|numeric|min:0|max:100',
            'nb_cc' => 'required|integer|min:0',
            'seance_efm' => 'required|in:Oui,Non',
            'validation_efm' => 'required|in:oui,non',
            'classe_teams' => 'nullable|string',
            'date_maj' => 'required|date',
        ]);

        $user = Auth::user();
        $etablissement = $user->etablissement;

        // Vérifier que l'affectation appartient bien à l'établissement du directeur
        $affectation = Affectation::where('id', $request->affectation_id)
            ->where('code_efp', $etablissement->code_efp)
            ->first();

        if (!$affectation) {
            return redirect()->back()->with('error', 'Affectation non trouvée dans votre établissement.');
        }

        Avancement::create([
            'affectation_id' => $request->affectation_id,
            'mh_realisee_presentiel' => $request->mh_realisee_presentiel,
            'mh_realisee_sync' => $request->mh_realisee_sync,
            'mh_realisee_globale' => $request->mh_realisee_globale,
            'taux_realisation_presentiel' => $request->taux_realisation_presentiel,
            'taux_realisation_syn' => $request->taux_realisation_syn,
            'taux_realisation_globale' => $request->taux_realisation_globale,
            'moyenne_absence' => $request->moyenne_absence,
            'nb_cc' => $request->nb_cc,
            'seance_efm' => $request->seance_efm,
            'validation_efm' => $request->validation_efm,
            'classe_teams' => $request->classe_teams,
            'date_maj' => $request->date_maj,
            'code_efp' => $etablissement->code_efp,
        ]);

        return redirect()->route('administration.etablissement.avancements.index')
            ->with('success', 'Avancement créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Avancement $avancement)
    {
        // Vérifier que l'avancement appartient à l'établissement du directeur
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if ($avancement->code_efp !== $etablissement->code_efp) {
            abort(403, 'Accès non autorisé.');
        }

        $avancement->load([
            'affectation.groupe',
            'affectation.module',
            'affectation.formateurPresentiel',
            'affectation.formateurSyn'
        ]);

        return view('administrationetablissement.avancements.show', compact('avancement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Avancement $avancement)
    {
        // Vérifier que l'avancement appartient à l'établissement du directeur
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if ($avancement->code_efp !== $etablissement->code_efp) {
            abort(403, 'Accès non autorisé.');
        }

        $avancement->load(['affectation.groupe', 'affectation.module']);

        return view('administrationetablissement.avancements.edit', compact('avancement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Avancement $avancement)
    {
        // Vérifier que l'avancement appartient à l'établissement du directeur
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if ($avancement->code_efp !== $etablissement->code_efp) {
            abort(403, 'Accès non autorisé.');
        }

        $request->validate([
            'mh_realisee_presentiel' => 'required|numeric|min:0',
            'mh_realisee_sync' => 'required|numeric|min:0',
            'mh_realisee_globale' => 'required|numeric|min:0',
            'taux_realisation_presentiel' => 'required|numeric|min:0|max:100',
            'taux_realisation_syn' => 'required|numeric|min:0|max:100',
            'taux_realisation_globale' => 'required|numeric|min:0|max:100',
            'moyenne_absence' => 'required|numeric|min:0|max:100',
            'nb_cc' => 'required|integer|min:0',
            'seance_efm' => 'required|in:Oui,Non',
            'validation_efm' => 'required|in:oui,non',
            'classe_teams' => 'nullable|string',
            'date_maj' => 'required|date',
        ]);

        $avancement->update($request->all());

        return redirect()->route('administration.etablissement.avancements.index')
            ->with('success', 'Avancement modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Avancement $avancement)
    {
        // Vérifier que l'avancement appartient à l'établissement du directeur
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if ($avancement->code_efp !== $etablissement->code_efp) {
            abort(403, 'Accès non autorisé.');
        }

        $avancement->delete();

        return redirect()->route('administration.etablissement.avancements.index')
            ->with('success', 'Avancement supprimé avec succès.');
    }
}