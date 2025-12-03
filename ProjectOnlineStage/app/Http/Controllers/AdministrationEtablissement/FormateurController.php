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
use Illuminate\Support\Facades\DB;

class FormateurController extends Controller
{
    /**
     * Afficher la liste des formateurs avec statistiques
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Aucun établissement associé à votre compte.');
        }

        // Query de base avec relation Many-to-Many
        $query = Formateur::whereHas('etablissements', function($q) use ($etablissement) {
                $q->where('etablissements.code_efp', $etablissement->code_efp);
            })
            ->with(['secteurs', 'modules', 'etablissements']);

        // Filtrage par recherche
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

        // Calculer les statistiques pour chaque formateur
        foreach ($formateurs as $formateur) {
            $formateur->stats = $this->calculerStatistiquesFormateur($formateur);
        }

        // Récupérer secteurs et modules pour les filtres
        $secteurs = Secteur::where('code_efp', $etablissement->code_efp)
            ->orderBy('nom_secteur')
            ->get();
        
        $modules = Module::where('code_efp', $etablissement->code_efp)
            ->orderBy('nom_module')
            ->get();

        // Statistiques globales
        $stats = [
            'total' => Formateur::whereHas('etablissements', function($q) use ($etablissement) {
                $q->where('etablissements.code_efp', $etablissement->code_efp);
            })->count(),
            'permanents' => Formateur::whereHas('etablissements', function($q) use ($etablissement) {
                $q->where('etablissements.code_efp', $etablissement->code_efp);
            })->where('type', 'permanent')->count(),
            'vacataires' => Formateur::whereHas('etablissements', function($q) use ($etablissement) {
                $q->where('etablissements.code_efp', $etablissement->code_efp);
            })->where('type', 'vacataire')->count(),
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
            'masse_horaire' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'secteurs' => 'array',
            'modules' => 'array',
        ]);

        $formateur = Formateur::create([
            'mle' => $request->mle,
            'nom_complet' => $request->nom_complet,
            'type' => $request->type,
            'masse_horaire' => $request->masse_horaire ?? 910.00,
            'description' => $request->description ?? 'Aucun description',
        ]);

        // Attacher l'établissement
        $formateur->etablissements()->attach($etablissement->code_efp);

        // Attacher secteurs et modules
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
        $this->authorizeAccess($formateur);

        $formateur->load(['secteurs', 'modules', 'etablissements']);

        // Récupérer toutes les affectations du formateur
        $affectations = Affectation::where(function ($query) use ($formateur) {
                $query->where('mle_affecte_presentiel', $formateur->mle)
                      ->orWhere('mle_affecte_syn', $formateur->mle);
            })
            ->with(['module', 'groupe', 'groupe.filiere', 'groupe.formation'])
            ->get();

        // Calculs globaux
        $offre = $formateur->masse_horaire;
        
        $demande = $affectations->sum('mh_totale_drif');
        
        $heuresAffectees = $affectations->sum('mh_affectee_globale');
        
        $manque = max(0, $demande - $heuresAffectees);
        
        $disponibilite = $offre - $demande;
        
        $heuresRestantes = max(0, $offre - $heuresAffectees);
        
        $tauxAffectation = $demande > 0 ? round(($heuresAffectees / $demande) * 100, 2) : 0;

        // Statistiques globales
        $statsGlobales = [
            'offre' => $offre,
            'demande' => $demande,
            'heures_affectees' => $heuresAffectees,
            'manque' => $manque,
            'taux_affectation' => $tauxAffectation,
            'disponibilite' => $disponibilite,
            'heures_restantes' => $heuresRestantes,
        ];

        // Détail par module
        $heuresParModule = $this->calculerHeuresParModule($affectations, $formateur->mle);

        return view('administrationetablissement.formateurs.show', compact(
            'formateur',
            'affectations',
            'heuresParModule',
            'statsGlobales'
        ));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Formateur $formateur)
    {
        $this->authorizeAccess($formateur);

        $user = Auth::user();
        $etablissement = $user->etablissement;

        $secteurs = Secteur::where('code_efp', $etablissement->code_efp)->get();
        $modules = Module::where('code_efp', $etablissement->code_efp)->get();

        $formateur->load(['secteurs', 'modules']);

        return view('administrationetablissement.formateurs.edit', compact(
            'formateur', 
            'secteurs', 
            'modules', 
            'etablissement'
        ));
    }

    /**
     * Mettre à jour un formateur
     */
    public function update(Request $request, Formateur $formateur)
    {
        $this->authorizeAccess($formateur);

        $request->validate([
            'mle' => 'required|unique:formateurs,mle,' . $formateur->id,
            'nom_complet' => 'required|string|max:255',
            'type' => 'required|in:permanent,vacataire',
            'masse_horaire' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'secteurs' => 'array',
            'modules' => 'array',
        ]);

        $formateur->update([
            'mle' => $request->mle,
            'nom_complet' => $request->nom_complet,
            'type' => $request->type,
            'masse_horaire' => $request->masse_horaire ?? $formateur->masse_horaire,
            'description' => $request->description ?? $formateur->description,
        ]);

        // Synchroniser secteurs et modules
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
        $this->authorizeAccess($formateur);

        // Vérifier si le formateur a des affectations actives
        $hasAffectations = Affectation::where(function($q) use ($formateur) {
            $q->where('mle_affecte_presentiel', $formateur->mle)
              ->orWhere('mle_affecte_syn', $formateur->mle);
        })->exists();

        if ($hasAffectations) {
            return redirect()->route('administration.etablissement.formateurs.index')
                ->with('error', 'Impossible de supprimer ce formateur car il a des affectations actives.');
        }

        $formateur->delete();

        return redirect()->route('administration.etablissement.formateurs.index')
            ->with('success', 'Formateur supprimé avec succès.');
    }

    /**
     * Calculer les statistiques d'un formateur
     */
    private function calculerStatistiquesFormateur(Formateur $formateur)
    {
        $affectations = Affectation::where(function ($query) use ($formateur) {
                $query->where('mle_affecte_presentiel', $formateur->mle)
                      ->orWhere('mle_affecte_syn', $formateur->mle);
            })->get();

        $offre = $formateur->masse_horaire;
        $demande = $affectations->sum('mh_totale_drif');
        $heuresAffectees = $affectations->sum('mh_affectee_globale');
        $manque = max(0, $demande - $heuresAffectees);
        $disponibilite = $offre - $demande;

        return [
            'offre' => $offre,
            'demande' => $demande,
            'heures_affectees' => $heuresAffectees,
            'manque' => $manque,
            'disponibilite' => $disponibilite,
            'taux_affectation' => $demande > 0 ? round(($heuresAffectees / $demande) * 100, 2) : 0,
        ];
    }

    /**
     * Calculer les heures par module
     */
    private function calculerHeuresParModule($affectations, $mleFormateur)
    {
        $heuresParModule = [];

        foreach ($affectations as $affectation) {
            $moduleId = $affectation->module_id;
            $moduleNom = $affectation->module->nom_module;
            $moduleCode = $affectation->module->code_module;
            $groupeNom = $affectation->groupe->code_groupe;

            if (!isset($heuresParModule[$moduleId])) {
                $heuresParModule[$moduleId] = [
                    'module_code' => $moduleCode,
                    'module_nom' => $moduleNom,
                    'groupes' => [],
                    'total_demande' => 0,
                    'total_affecte' => 0,
                    'total_manquant' => 0
                ];
            }

            $demande = $affectation->mh_totale_drif;
            $affecte = $affectation->mh_affectee_globale;
            $manquant = max(0, $demande - $affecte);

            // Déterminer le type d'affectation
            if ($affectation->mle_affecte_presentiel === $mleFormateur) {
                $typeAffectation = 'Présentiel';
            } elseif ($affectation->mle_affecte_syn === $mleFormateur) {
                $typeAffectation = 'Synchrone';
            } else {
                $typeAffectation = 'Mixte';
            }

            $heuresParModule[$moduleId]['groupes'][$groupeNom] = [
                'demande' => $demande,
                'affecte' => $affecte,
                'manquant' => $manquant,
                'type_affectation' => $typeAffectation,
                'filiere' => $affectation->groupe->filiere->nom_filiere ?? 'N/A'
            ];

            $heuresParModule[$moduleId]['total_demande'] += $demande;
            $heuresParModule[$moduleId]['total_affecte'] += $affecte;
            $heuresParModule[$moduleId]['total_manquant'] += $manquant;
        }

        return $heuresParModule;
    }

    /**
     * Autoriser l'accès au formateur
     */
    private function authorizeAccess(Formateur $formateur)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if (!$formateur->etablissements->contains('code_efp', $etablissement->code_efp)) {
            abort(403, 'Accès non autorisé à ce formateur.');
        }
    }
}