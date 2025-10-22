<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Formateur;
use App\Models\Etablissement;
use App\Models\Secteur;
use App\Models\Module;
use App\Models\Affectation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormateurController extends Controller
{
/**
 * Afficher la liste des formateurs de l'établissement avec filtrage
 */
public function index(Request $request)
{
    // Récupérer l'établissement du directeur connecté
    $user = Auth::user();
    $etablissement = $user->etablissement;
    
    if (!$etablissement) {
        return redirect()->route('administration.etablissement.dashboard')
            ->with('error', 'Aucun établissement associé à votre compte.');
    }

    // Query de base
    $query = Formateur::where('code_efp', $etablissement->code_efp)
        ->with(['secteurs', 'modules']);

    // Filtrage par recherche (MLE ou Nom)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('mle', 'like', "%{$search}%")
              ->orWhere('nom_complet', 'like', "%{$search}%");
        });
    }

    // Filtrage par type
    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    // Filtrage par secteur
    if ($request->filled('secteur_id')) {
        $query->whereHas('secteurs', function($q) use ($request) {
            $q->where('secteurs.id', $request->secteur_id);
        });
    }

    // Filtrage par module
    if ($request->filled('module_id')) {
        $query->whereHas('modules', function($q) use ($request) {
            $q->where('modules.id', $request->module_id);
        });
    }

    // Tri
    $sortBy = $request->get('sort_by', 'nom_complet');
    $sortOrder = $request->get('sort_order', 'asc');
    $query->orderBy($sortBy, $sortOrder);

    $formateurs = $query->paginate(15)->withQueryString();

    // Récupérer les secteurs et modules pour les filtres
    $secteurs = Secteur::where('code_efp', $etablissement->code_efp)
        ->orderBy('nom_secteur')
        ->get();
    
    $modules = Module::where('code_efp', $etablissement->code_efp)
        ->orderBy('nom_module')
        ->get();

    // Statistiques
    $stats = [
        'total' => Formateur::where('code_efp', $etablissement->code_efp)->count(),
        'permanents' => Formateur::where('code_efp', $etablissement->code_efp)
            ->where('type', 'permanent')->count(),
        'vacataires' => Formateur::where('code_efp', $etablissement->code_efp)
            ->where('type', 'vacataire')->count(),
    ];

    return view('administrationetablissement.formateurs.index', compact(
        'formateurs', 
        'etablissement', 
        'secteurs', 
        'modules',
        'stats'
    ));
}

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Aucun établissement associé à votre compte.');
        }

        // Récupérer les secteurs et modules de l'établissement
        $secteurs = Secteur::where('code_efp', $etablissement->code_efp)->get();
        $modules = Module::where('code_efp', $etablissement->code_efp)->get();

        return view('administrationetablissement.formateurs.create', compact('secteurs', 'modules', 'etablissement'));
    }

    /**
     * Enregistrer un nouveau formateur
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Aucun établissement associé à votre compte.');
        }

        $request->validate([
            'mle' => 'required|unique:formateurs,mle',
            'nom_complet' => 'required|string|max:255',
            'type' => 'required|in:permanent,vacataire',
            'secteurs' => 'array',
            'modules' => 'array',
        ]);

        // Créer le formateur avec le code_efp automatique
        $formateur = Formateur::create([
            'mle' => $request->mle,
            'nom_complet' => $request->nom_complet,
            'type' => $request->type,
            'code_efp' => $etablissement->code_efp,
        ]);

        // Attacher les secteurs et modules
        if ($request->has('secteurs')) {
            $formateur->secteurs()->attach($request->secteurs);
        }

        if ($request->has('modules')) {
            $formateur->modules()->attach($request->modules);
        }

        return redirect()->route('administration.etablissement.formateurs.index')
            ->with('success', 'Formateur créé avec succès.');
    }

    /**
     * Afficher les détails d'un formateur
     */
    public function show(Formateur $formateur)
    {
        // Vérifier que le formateur appartient à l'établissement du directeur
        $this->authorizeAccess($formateur);

        // Charger les relations
        $formateur->load(['secteurs', 'modules', 'etablissement']);

        // Récupérer les affectations avec les détails des modules et groupes
        $affectations = Affectation::where(function($query) use ($formateur) {
                $query->where('mle_affecte_presentiel', $formateur->mle)
                      ->orWhere('mle_affecte_syn', $formateur->mle);
            })
            ->with(['module', 'groupe', 'groupe.filiere'])
            ->get();

        // Calculer les heures par module
        $heuresParModule = $this->calculerHeuresParModule($affectations, $formateur->mle);

        return view('administrationetablissement.formateurs.show', compact('formateur', 'affectations', 'heuresParModule'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Formateur $formateur)
    {
        // Vérifier que le formateur appartient à l'établissement du directeur
        $this->authorizeAccess($formateur);

        $user = Auth::user();
        $etablissement = $user->etablissement;

        $secteurs = Secteur::where('code_efp', $etablissement->code_efp)->get();
        $modules = Module::where('code_efp', $etablissement->code_efp)->get();

        $formateur->load(['secteurs', 'modules']);

        return view('administrationetablissement.formateurs.edit', compact('formateur', 'secteurs', 'modules', 'etablissement'));
    }

    /**
     * Mettre à jour un formateur
     */
    public function update(Request $request, Formateur $formateur)
    {
        // Vérifier que le formateur appartient à l'établissement du directeur
        $this->authorizeAccess($formateur);

        $request->validate([
            'mle' => 'required|unique:formateurs,mle,' . $formateur->id,
            'nom_complet' => 'required|string|max:255',
            'type' => 'required|in:permanent,vacataire',
            'secteurs' => 'array',
            'modules' => 'array',
        ]);

        $formateur->update([
            'mle' => $request->mle,
            'nom_complet' => $request->nom_complet,
            'type' => $request->type,
        ]);

        // Synchroniser les secteurs et modules
        $formateur->secteurs()->sync($request->secteurs ?? []);
        $formateur->modules()->sync($request->modules ?? []);

        return redirect()->route('administration.etablissement.formateurs.index')
            ->with('success', 'Formateur modifié avec succès.');
    }

    /**
     * Supprimer un formateur
     */
    public function destroy(Formateur $formateur)
    {
        // Vérifier que le formateur appartient à l'établissement du directeur
        $this->authorizeAccess($formateur);

        $formateur->delete();

        return redirect()->route('administration.etablissement.formateurs.index')
            ->with('success', 'Formateur supprimé avec succès.');
    }

    /**
     * Vérifier que le formateur appartient à l'établissement du directeur connecté
     */
    private function authorizeAccess(Formateur $formateur)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if ($formateur->code_efp !== $etablissement->code_efp) {
            abort(403, 'Accès non autorisé à ce formateur.');
        }
    }

    /**
     * Calculer les heures par module pour l'affichage détaillé
     */
    private function calculerHeuresParModule($affectations, $mleFormateur)
    {
        $heuresParModule = [];

        foreach ($affectations as $affectation) {
            $moduleId = $affectation->module_id;
            $moduleNom = $affectation->module->nom_module;
            $groupeNom = $affectation->groupe->code_groupe;

            if (!isset($heuresParModule[$moduleId])) {
                $heuresParModule[$moduleId] = [
                    'module_nom' => $moduleNom,
                    'groupes' => [],
                    'total_requis' => 0,
                    'total_affecte' => 0,
                    'total_manquant' => 0
                ];
            }

            // Déterminer les heures selon le type d'affectation
            if ($affectation->mle_affecte_presentiel === $mleFormateur) {
                $heuresRequises = $affectation->mh_totale_drif;
                $heuresAffectees = $affectation->mh_affectee_presentiel;
            } elseif ($affectation->mle_affecte_syn === $mleFormateur) {
                $heuresRequises = $affectation->mh_totale_drif;
                $heuresAffectees = $affectation->mh_affectee_sync;
            } else {
                continue;
            }

            $heuresManquantes = max(0, $heuresRequises - $heuresAffectees);

            $heuresParModule[$moduleId]['groupes'][$groupeNom] = [
                'heures_requises' => $heuresRequises,
                'heures_affectees' => $heuresAffectees,
                'heures_manquantes' => $heuresManquantes
            ];

            $heuresParModule[$moduleId]['total_requis'] += $heuresRequises;
            $heuresParModule[$moduleId]['total_affecte'] += $heuresAffectees;
            $heuresParModule[$moduleId]['total_manquant'] += $heuresManquantes;
        }

        return $heuresParModule;
    }
}