<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Affectation;
use App\Models\Etablissement;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Formateur;
use App\Models\Avancement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffectationController extends Controller
{
/**
 * Afficher la liste des affectations de l'établissement avec filtrage
 */
public function index(Request $request)
{
    // Récupérer l'établissement de l'utilisateur connecté
    $etablissement = Auth::user()->etablissement;
    
    if (!$etablissement) {
        return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
    }

    // Query de base
    $query = Affectation::where('code_efp', $etablissement->code_efp)
                ->with(['groupe.filiere.secteur', 'module', 'formateurPresentiel', 'formateurSyn', 'avancement']);

    // Filtrage par secteur
    if ($request->filled('secteur_id')) {
        $query->whereHas('groupe.filiere.secteur', function($q) use ($request) {
            $q->where('id', $request->secteur_id);
        });
    }

    // Filtrage par filière
    if ($request->filled('filiere_id')) {
        $query->whereHas('groupe', function($q) use ($request) {
            $q->where('filiere_id', $request->filiere_id);
        });
    }

    // Filtrage par groupe
    if ($request->filled('groupe_id')) {
        $query->where('groupe_id', $request->groupe_id);
    }

    // Filtrage par module
    if ($request->filled('module_id')) {
        $query->where('module_id', $request->module_id);
    }

    // Filtrage par formateur présentiel
    if ($request->filled('formateur_presentiel')) {
        $query->where('mle_affecte_presentiel', $request->formateur_presentiel);
    }

    // Filtrage par formateur synchrone
    if ($request->filled('formateur_syn')) {
        $query->where('mle_affecte_syn', $request->formateur_syn);
    }

    // Récupérer les résultats
    $affectations = $query->orderBy('created_at', 'desc')->paginate(15);

    // Données pour les filtres
    $secteurs = \App\Models\Secteur::where('code_efp', $etablissement->code_efp)->get();
    $filieres = \App\Models\Filiere::where('code_efp', $etablissement->code_efp)->get();
    $groupes = \App\Models\Groupe::where('code_efp', $etablissement->code_efp)->get();
    $modules = \App\Models\Module::where('code_efp', $etablissement->code_efp)->get();
    $formateurs = \App\Models\Formateur::where('code_efp', $etablissement->code_efp)->get();

    return view('administrationetablissement.affectations.index', compact(
        'affectations', 
        'etablissement',
        'secteurs',
        'filieres',
        'groupes',
        'modules',
        'formateurs'
    ));
}
    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
        }

        // Récupérer les données nécessaires
        $groupes = Groupe::where('code_efp', $etablissement->code_efp)
                        ->with(['filiere', 'formation'])
                        ->get();
        
        $modules = Module::where('code_efp', $etablissement->code_efp)->get();
        
        $formateurs = Formateur::where('code_efp', $etablissement->code_efp)->get();

        return view('administrationetablissement.affectations.create', compact(
            'etablissement', 
            'groupes', 
            'modules', 
            'formateurs'
        ));
    }

    /**
     * Enregistrer une nouvelle affectation
     */
    public function store(Request $request)
    {
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
        }

        $request->validate([
            'groupe_id' => 'required|exists:groupes,id',
            'module_id' => 'required|exists:modules,id',
            'mle_affecte_presentiel' => 'nullable|exists:formateurs,mle',
            'mle_affecte_syn' => 'nullable|exists:formateurs,mle',
            // Masses horaires Semestre 1
            'mhp_s1_drif' => 'nullable|numeric|min:0',
            'mhsyn_s1_drif' => 'nullable|numeric|min:0',
            'mhasyn_s1_drif' => 'nullable|numeric|min:0',
            'mh_totale_s1_drif' => 'nullable|numeric|min:0',
            // Masses horaires Semestre 2
            'mhp_s2_drif' => 'nullable|numeric|min:0',
            'mhsyn_s2_drif' => 'nullable|numeric|min:0',
            'mhasyn_s2_drif' => 'nullable|numeric|min:0',
            'mh_totale_s2_drif' => 'nullable|numeric|min:0',
            // Masses horaires Affectées
            'mh_affectee_presentiel' => 'nullable|numeric|min:0',
            'mh_affectee_sync' => 'nullable|numeric|min:0',
            'mh_affectee_globale' => 'nullable|numeric|min:0',
        ]);

        try {
            // Vérifier si l'affectation existe déjà
            $existingAffectation = Affectation::where('groupe_id', $request->groupe_id)
                ->where('module_id', $request->module_id)
                ->first();

            if ($existingAffectation) {
                return redirect()->back()
                    ->with('error', 'Cette affectation existe déjà pour ce groupe et ce module.')
                    ->withInput();
            }

            // Récupérer les noms des formateurs
            $formateurPresentiel = null;
            $formateurSyn = null;

            if ($request->mle_affecte_presentiel) {
                $formateurPresentiel = Formateur::where('mle', $request->mle_affecte_presentiel)->first();
            }

            if ($request->mle_affecte_syn) {
                $formateurSyn = Formateur::where('mle', $request->mle_affecte_syn)->first();
            }

            // Calculer les totaux automatiquement si non fournis
            $mh_totale_s1_drif = $request->mh_totale_s1_drif ?? 
                (($request->mhp_s1_drif ?? 0) + ($request->mhsyn_s1_drif ?? 0) + ($request->mhasyn_s1_drif ?? 0));
            
            $mh_totale_s2_drif = $request->mh_totale_s2_drif ?? 
                (($request->mhp_s2_drif ?? 0) + ($request->mhsyn_s2_drif ?? 0) + ($request->mhasyn_s2_drif ?? 0));

            $mhp_totale_drif = ($request->mhp_s1_drif ?? 0) + ($request->mhp_s2_drif ?? 0);
            $mhsyn_totale_drif = ($request->mhsyn_s1_drif ?? 0) + ($request->mhsyn_s2_drif ?? 0);
            $mhasyn_totale_drif = ($request->mhasyn_s1_drif ?? 0) + ($request->mhasyn_s2_drif ?? 0);
            $mh_totale_drif = $mh_totale_s1_drif + $mh_totale_s2_drif;

            $mh_affectee_globale = $request->mh_affectee_globale ?? 
                (($request->mh_affectee_presentiel ?? 0) + ($request->mh_affectee_sync ?? 0));

            $affectation = Affectation::create([
                'groupe_id' => $request->groupe_id,
                'module_id' => $request->module_id,
                'code_efp' => $etablissement->code_efp,
                // Formateurs
                'mle_affecte_presentiel' => $request->mle_affecte_presentiel,
                'formateur_affecte_presentiel' => $formateurPresentiel ? $formateurPresentiel->nom_complet : null,
                'mle_affecte_syn' => $request->mle_affecte_syn,
                'formateur_affecte_syn' => $formateurSyn ? $formateurSyn->nom_complet : null,
                // Semestre 1
                'mhp_s1_drif' => $request->mhp_s1_drif ?? 0,
                'mhsyn_s1_drif' => $request->mhsyn_s1_drif ?? 0,
                'mhasyn_s1_drif' => $request->mhasyn_s1_drif ?? 0,
                'mh_totale_s1_drif' => $mh_totale_s1_drif,
                // Semestre 2
                'mhp_s2_drif' => $request->mhp_s2_drif ?? 0,
                'mhsyn_s2_drif' => $request->mhsyn_s2_drif ?? 0,
                'mhasyn_s2_drif' => $request->mhasyn_s2_drif ?? 0,
                'mh_totale_s2_drif' => $mh_totale_s2_drif,
                // Totaux
                'mhp_totale_drif' => $mhp_totale_drif,
                'mhsyn_totale_drif' => $mhsyn_totale_drif,
                'mhasyn_totale_drif' => $mhasyn_totale_drif,
                'mh_totale_drif' => $mh_totale_drif,
                // Masses horaires affectées
                'mh_affectee_presentiel' => $request->mh_affectee_presentiel ?? 0,
                'mh_affectee_sync' => $request->mh_affectee_sync ?? 0,
                'mh_affectee_globale' => $mh_affectee_globale,
            ]);

            return redirect()->route('administration.etablissement.affectations.index')
                           ->with('success', 'Affectation créée avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la création de l\'affectation: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Afficher les détails d'une affectation
     */
    public function show($id)
    {
        $etablissement = Auth::user()->etablissement;
        $affectation = Affectation::where('code_efp', $etablissement->code_efp)
                       ->with([
                           'groupe.filiere.secteur', 
                           'module', 
                           'formateurPresentiel', 
                           'formateurSyn',
                           'avancement'
                       ])
                       ->findOrFail($id);

        return view('administrationetablissement.affectations.show', compact('affectation'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id)
    {
        $etablissement = Auth::user()->etablissement;
        $affectation = Affectation::where('code_efp', $etablissement->code_efp)
                       ->with(['groupe', 'module'])
                       ->findOrFail($id);

        // Récupérer les données nécessaires
        $groupes = Groupe::where('code_efp', $etablissement->code_efp)
                        ->with(['filiere', 'formation'])
                        ->get();
        
        $modules = Module::where('code_efp', $etablissement->code_efp)->get();
        
        $formateurs = Formateur::where('code_efp', $etablissement->code_efp)->get();

        return view('administrationetablissement.affectations.edit', compact(
            'affectation',
            'etablissement', 
            'groupes', 
            'modules', 
            'formateurs'
        ));
    }

    /**
     * Mettre à jour une affectation
     */
    public function update(Request $request, $id)
    {
        $etablissement = Auth::user()->etablissement;
        $affectation = Affectation::where('code_efp', $etablissement->code_efp)->findOrFail($id);

        $request->validate([
            'groupe_id' => 'required|exists:groupes,id',
            'module_id' => 'required|exists:modules,id',
            'mle_affecte_presentiel' => 'nullable|exists:formateurs,mle',
            'mle_affecte_syn' => 'nullable|exists:formateurs,mle',
            // Masses horaires Semestre 1
            'mhp_s1_drif' => 'nullable|numeric|min:0',
            'mhsyn_s1_drif' => 'nullable|numeric|min:0',
            'mhasyn_s1_drif' => 'nullable|numeric|min:0',
            'mh_totale_s1_drif' => 'nullable|numeric|min:0',
            // Masses horaires Semestre 2
            'mhp_s2_drif' => 'nullable|numeric|min:0',
            'mhsyn_s2_drif' => 'nullable|numeric|min:0',
            'mhasyn_s2_drif' => 'nullable|numeric|min:0',
            'mh_totale_s2_drif' => 'nullable|numeric|min:0',
            // Masses horaires Affectées
            'mh_affectee_presentiel' => 'nullable|numeric|min:0',
            'mh_affectee_sync' => 'nullable|numeric|min:0',
            'mh_affectee_globale' => 'nullable|numeric|min:0',
        ]);

        try {
            // Vérifier si l'affectation existe déjà (pour un autre enregistrement)
            $existingAffectation = Affectation::where('groupe_id', $request->groupe_id)
                ->where('module_id', $request->module_id)
                ->where('id', '!=', $id)
                ->first();

            if ($existingAffectation) {
                return redirect()->back()
                    ->with('error', 'Cette affectation existe déjà pour ce groupe et ce module.')
                    ->withInput();
            }

            // Récupérer les noms des formateurs
            $formateurPresentiel = null;
            $formateurSyn = null;

            if ($request->mle_affecte_presentiel) {
                $formateurPresentiel = Formateur::where('mle', $request->mle_affecte_presentiel)->first();
            }

            if ($request->mle_affecte_syn) {
                $formateurSyn = Formateur::where('mle', $request->mle_affecte_syn)->first();
            }

            // Calculer les totaux automatiquement si non fournis
            $mh_totale_s1_drif = $request->mh_totale_s1_drif ?? 
                (($request->mhp_s1_drif ?? 0) + ($request->mhsyn_s1_drif ?? 0) + ($request->mhasyn_s1_drif ?? 0));
            
            $mh_totale_s2_drif = $request->mh_totale_s2_drif ?? 
                (($request->mhp_s2_drif ?? 0) + ($request->mhsyn_s2_drif ?? 0) + ($request->mhasyn_s2_drif ?? 0));

            $mhp_totale_drif = ($request->mhp_s1_drif ?? 0) + ($request->mhp_s2_drif ?? 0);
            $mhsyn_totale_drif = ($request->mhsyn_s1_drif ?? 0) + ($request->mhsyn_s2_drif ?? 0);
            $mhasyn_totale_drif = ($request->mhasyn_s1_drif ?? 0) + ($request->mhasyn_s2_drif ?? 0);
            $mh_totale_drif = $mh_totale_s1_drif + $mh_totale_s2_drif;

            $mh_affectee_globale = $request->mh_affectee_globale ?? 
                (($request->mh_affectee_presentiel ?? 0) + ($request->mh_affectee_sync ?? 0));

            $affectation->update([
                'groupe_id' => $request->groupe_id,
                'module_id' => $request->module_id,
                // Formateurs
                'mle_affecte_presentiel' => $request->mle_affecte_presentiel,
                'formateur_affecte_presentiel' => $formateurPresentiel ? $formateurPresentiel->nom_complet : null,
                'mle_affecte_syn' => $request->mle_affecte_syn,
                'formateur_affecte_syn' => $formateurSyn ? $formateurSyn->nom_complet : null,
                // Semestre 1
                'mhp_s1_drif' => $request->mhp_s1_drif ?? 0,
                'mhsyn_s1_drif' => $request->mhsyn_s1_drif ?? 0,
                'mhasyn_s1_drif' => $request->mhasyn_s1_drif ?? 0,
                'mh_totale_s1_drif' => $mh_totale_s1_drif,
                // Semestre 2
                'mhp_s2_drif' => $request->mhp_s2_drif ?? 0,
                'mhsyn_s2_drif' => $request->mhsyn_s2_drif ?? 0,
                'mhasyn_s2_drif' => $request->mhasyn_s2_drif ?? 0,
                'mh_totale_s2_drif' => $mh_totale_s2_drif,
                // Totaux
                'mhp_totale_drif' => $mhp_totale_drif,
                'mhsyn_totale_drif' => $mhsyn_totale_drif,
                'mhasyn_totale_drif' => $mhasyn_totale_drif,
                'mh_totale_drif' => $mh_totale_drif,
                // Masses horaires affectées
                'mh_affectee_presentiel' => $request->mh_affectee_presentiel ?? 0,
                'mh_affectee_sync' => $request->mh_affectee_sync ?? 0,
                'mh_affectee_globale' => $mh_affectee_globale,
            ]);

            return redirect()->route('administration.etablissement.affectations.index')
                           ->with('success', 'Affectation modifiée avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la modification de l\'affectation: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Supprimer une affectation
     */
    public function destroy($id)
    {
        $etablissement = Auth::user()->etablissement;
        $affectation = Affectation::where('code_efp', $etablissement->code_efp)->findOrFail($id);

        try {
            // Vérifier s'il y a des avancements liés à cette affectation
            if ($affectation->avancement) {
                return redirect()->back()
                               ->with('error', 'Impossible de supprimer cette affectation car elle est liée à des données d\'avancement.');
            }

            $affectation->delete();

            return redirect()->route('administration.etablissement.affectations.index')
                           ->with('success', 'Affectation supprimée avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la suppression de l\'affectation: ' . $e->getMessage());
        }
    }
}