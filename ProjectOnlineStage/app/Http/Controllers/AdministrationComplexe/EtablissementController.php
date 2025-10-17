<?php

namespace App\Http\Controllers\AdministrationComplexe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\{Etablissement, Formateur, Formation, Module, Groupe, Filiere, Secteur, Affectation, Avancement, User};

class EtablissementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_complexe') {
            return redirect()->route('welcome')
                ->with('error', 'Accès refusé. Vous n\'êtes pas directeur de complexe.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé à votre compte.');
        }

        // Récupérer les établissements avec leurs statistiques
        $etablissements = Etablissement::where('complexe_id', $complexe->id)
            ->with(['user', 'formations'])
            ->withCount('formations')
            ->paginate(10);

        // Calculer les statistiques pour chaque établissement
        foreach ($etablissements as $etablissement) {
            $stats = $this->getEtablissementStats($etablissement->code_efp);
            $etablissement->stats = $stats;
        }

        return view('administrationcomplexe.etablissements.index', compact(
            'user',
            'complexe',
            'etablissements'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_complexe') {
            return redirect()->route('welcome')
                ->with('error', 'Accès refusé. Vous n\'êtes pas directeur de complexe.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé à votre compte.');
        }

        // Récupérer les utilisateurs sans établissement et avec le rôle directeur_etablissement
        $directeurs = User::where('role', 'directeur_etablissement')
            ->whereDoesntHave('etablissement')
            ->get();

        return view('administrationcomplexe.etablissements.create', compact(
            'user',
            'complexe',
            'directeurs'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_complexe') {
            return redirect()->route('welcome')
                ->with('error', 'Accès refusé. Vous n\'êtes pas directeur de complexe.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé à votre compte.');
        }

        $validated = $request->validate([
            'code_efp' => 'required|string|max:50|unique:etablissements,code_efp',
            'nom_efp' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
        ], [
            'code_efp.required' => 'Le code EFP est obligatoire.',
            'code_efp.unique' => 'Ce code EFP existe déjà.',
            'nom_efp.required' => 'Le nom de l\'établissement est obligatoire.',
            'user_id.exists' => 'Le directeur sélectionné n\'existe pas.',
        ]);

        $validated['complexe_id'] = $complexe->id;

        Etablissement::create($validated);

        return redirect()->route('administration.complexe.etablissements.index')
            ->with('success', 'Établissement créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $code_efp)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_complexe') {
            return redirect()->route('welcome')
                ->with('error', 'Accès refusé. Vous n\'êtes pas directeur de complexe.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé à votre compte.');
        }

        // Récupérer l'établissement
        $etablissement = Etablissement::where('code_efp', $code_efp)
            ->where('complexe_id', $complexe->id)
            ->firstOrFail();

        // Récupérer les filtres
        $filters = [
            'formateur' => $request->input('formateur'),
            'module' => $request->input('module'),
            'groupe' => $request->input('groupe'),
            'filiere' => $request->input('filiere'),
            'secteur' => $request->input('secteur'),
        ];

        // Construire la requête de base
        $query = $this->buildDetailedQuery($code_efp, $filters);
        
        // Récupérer les données avec pagination
        $detailedData = $query->paginate(20)->appends($request->except('page'));

        // Calculer les statistiques
        $statistics = $this->calculateStatistics($code_efp, $filters);

        // Récupérer les données pour les graphiques
        $chartData = $this->getChartData($code_efp, $filters);

        // Récupérer les options de filtrage
        $filterOptions = $this->getFilterOptions($code_efp);

        // Nouveaux tableaux ajoutés
        $modulesNonAffectesParFiliere = $this->getModulesNonAffectesParFiliere($code_efp);
        $modulesNonAffectesParGroupe = $this->getModulesNonAffectesParGroupe($code_efp);
        $formateursStats = $this->getFormateursStats($code_efp);

        return view('administrationcomplexe.etablissements.show', compact(
            'user', 
            'complexe', 
            'etablissement',
            'detailedData', 
            'statistics', 
            'chartData', 
            'filterOptions',
            'filters',
            'modulesNonAffectesParFiliere',
            'modulesNonAffectesParGroupe',
            'formateursStats'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($code_efp)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_complexe') {
            return redirect()->route('welcome')
                ->with('error', 'Accès refusé. Vous n\'êtes pas directeur de complexe.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé à votre compte.');
        }

        $etablissement = Etablissement::where('code_efp', $code_efp)
            ->where('complexe_id', $complexe->id)
            ->firstOrFail();

        // Récupérer les directeurs disponibles (sans établissement) + le directeur actuel
        $directeurs = User::where('role', 'directeur_etablissement')
            ->where(function($query) use ($etablissement) {
                $query->whereDoesntHave('etablissement')
                      ->orWhere('id', $etablissement->user_id);
            })
            ->get();

        return view('administrationcomplexe.etablissements.edit', compact(
            'user',
            'complexe',
            'etablissement',
            'directeurs'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $code_efp)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_complexe') {
            return redirect()->route('welcome')
                ->with('error', 'Accès refusé. Vous n\'êtes pas directeur de complexe.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé à votre compte.');
        }

        $etablissement = Etablissement::where('code_efp', $code_efp)
            ->where('complexe_id', $complexe->id)
            ->firstOrFail();

        $validated = $request->validate([
            'nom_efp' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
        ], [
            'nom_efp.required' => 'Le nom de l\'établissement est obligatoire.',
            'user_id.exists' => 'Le directeur sélectionné n\'existe pas.',
        ]);

        $etablissement->update($validated);

        return redirect()->route('administration.complexe.etablissements.index')
            ->with('success', 'Établissement modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($code_efp)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_complexe') {
            return redirect()->route('welcome')
                ->with('error', 'Accès refusé. Vous n\'êtes pas directeur de complexe.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé à votre compte.');
        }

        $etablissement = Etablissement::where('code_efp', $code_efp)
            ->where('complexe_id', $complexe->id)
            ->firstOrFail();

        // Vérifier s'il y a des formations associées
        if ($etablissement->formations()->count() > 0) {
            return redirect()->route('administration.complexe.etablissements.index')
                ->with('error', 'Impossible de supprimer cet établissement car il contient des formations.');
        }

        $etablissement->delete();

        return redirect()->route('administration.complexe.etablissements.index')
            ->with('success', 'Établissement supprimé avec succès.');
    }

    // ========== METHODES PRIVEES ==========

    /**
     * Récupère les modules non affectés par filière (CORRIGÉ)
     */
    private function getModulesNonAffectesParFiliere($code_efp)
    {
        // Récupérer toutes les filières de cet établissement
        $filieres = Filiere::where('code_efp', $code_efp)->get();
        
        $result = collect();
        
        foreach ($filieres as $filiere) {
            // Récupérer tous les modules de cette filière
            $modulesFiliere = DB::table('filiere_module')
                ->join('modules', 'filiere_module.module_id', '=', 'modules.id')
                ->where('filiere_module.filiere_id', $filiere->id)
                ->select('modules.id', 'modules.code_module', 'modules.nom_module')
                ->get();
            
            // Récupérer tous les groupes de cette filière dans cet établissement
            $groupesIds = Groupe::where('filiere_id', $filiere->id)
                ->where('code_efp', $code_efp)
                ->pluck('id');
            
            // Pour chaque module, vérifier s'il est affecté à au moins un groupe de cette filière
            foreach ($modulesFiliere as $module) {
                $estAffecte = Affectation::where('module_id', $module->id)
                    ->where('code_efp', $code_efp)
                    ->whereIn('groupe_id', $groupesIds)
                    ->exists();
                
                if (!$estAffecte) {
                    $result->push((object)[
                        'code_filiere' => $filiere->code_filiere,
                        'nom_filiere' => $filiere->nom_filiere,
                        'code_module' => $module->code_module,
                        'nom_module' => $module->nom_module
                    ]);
                }
            }
        }
        
        return $result->groupBy('nom_filiere');
    }

    /**
     * Récupère les modules non affectés par groupe (CORRIGÉ)
     */
    private function getModulesNonAffectesParGroupe($code_efp)
    {
        // Récupérer tous les groupes de cet établissement
        $groupes = Groupe::where('code_efp', $code_efp)
            ->with('filiere')
            ->get();
        
        $result = collect();
        
        foreach ($groupes as $groupe) {
            // Récupérer tous les modules de la filière de ce groupe
            $modulesFiliere = DB::table('filiere_module')
                ->join('modules', 'filiere_module.module_id', '=', 'modules.id')
                ->where('filiere_module.filiere_id', $groupe->filiere_id)
                ->select('modules.id', 'modules.code_module', 'modules.nom_module')
                ->get();
            
            // Pour chaque module, vérifier s'il est affecté à ce groupe
            foreach ($modulesFiliere as $module) {
                $estAffecte = Affectation::where('module_id', $module->id)
                    ->where('groupe_id', $groupe->id)
                    ->where('code_efp', $code_efp)
                    ->exists();
                
                if (!$estAffecte) {
                    $result->push((object)[
                        'code_groupe' => $groupe->code_groupe,
                        'code_filiere' => $groupe->filiere->code_filiere,
                        'nom_filiere' => $groupe->filiere->nom_filiere,
                        'code_module' => $module->code_module,
                        'nom_module' => $module->nom_module
                    ]);
                }
            }
        }
        
        return $result->groupBy('code_groupe');
    }

    /**
     * Récupère les statistiques des formateurs (SIMPLIFIÉ)
     */
    private function getFormateursStats($code_efp)
    {
        $formateurs = Formateur::where('code_efp', $code_efp)
            ->with(['affectationsPresentiel', 'affectationsSyn'])
            ->get();

        $stats = [];

        foreach ($formateurs as $formateur) {
            // Heures requises (somme des heures DRIF des affectations)
            $heuresRequisesPresentiel = $formateur->affectationsPresentiel
                ->where('code_efp', $code_efp)
                ->sum('mhp_totale_drif');
            $heuresRequisesSyn = $formateur->affectationsSyn
                ->where('code_efp', $code_efp)
                ->sum('mhsyn_totale_drif');
            $heuresRequisesTotal = $heuresRequisesPresentiel + $heuresRequisesSyn;

            // Heures affectées
            $heuresAffecteesPresentiel = $formateur->affectationsPresentiel
                ->where('code_efp', $code_efp)
                ->sum('mh_affectee_presentiel');
            $heuresAffecteesSyn = $formateur->affectationsSyn
                ->where('code_efp', $code_efp)
                ->sum('mh_affectee_sync');
            $heuresAffecteesTotal = $heuresAffecteesPresentiel + $heuresAffecteesSyn;

            // Heures manquantes
            $heuresManquantes = max(0, $heuresRequisesTotal - $heuresAffecteesTotal);

            $stats[] = [
                'mle' => $formateur->mle,
                'nom_complet' => $formateur->nom_complet,
                'type' => $formateur->type,
                'heures_requises' => $heuresRequisesTotal,
                'heures_affectees' => $heuresAffecteesTotal,
                'heures_manquantes' => $heuresManquantes,
            ];
        }

        // Calcul des totaux
        $totaux = [
            'heures_requises' => collect($stats)->sum('heures_requises'),
            'heures_affectees' => collect($stats)->sum('heures_affectees'),
            'heures_manquantes' => collect($stats)->sum('heures_manquantes'),
        ];

        return [
            'formateurs' => $stats,
            'totaux' => $totaux,
        ];
    }

    private function getEtablissementStats($code_efp)
    {
        $affectations = Affectation::query()
            ->join('groupes', 'affectations.groupe_id', '=', 'groupes.id')
            ->join('formations', 'groupes.formation_id', '=', 'formations.id')
            ->leftJoin('avancements', 'affectations.id', '=', 'avancements.affectation_id')
            ->where('affectations.code_efp', $code_efp)
            ->select(
                'groupes.id as groupe_id',
                'groupes.effectif_groupe',
                'affectations.mh_totale_drif',
                'avancements.mh_realisee_globale'
            )
            ->get();

        $nbGroupes = $affectations->pluck('groupe_id')->unique()->count();
        $groupesUniques = $affectations->groupBy('groupe_id')->map(function($items) {
            return $items->first()->effectif_groupe ?? 0;
        });
        $nbApprenants = $groupesUniques->sum();

        $heuresRequises = $affectations->sum('mh_totale_drif') ?: 0;
        $heuresRealisees = $affectations->sum(function($item) {
            return $item->mh_realisee_globale ?? 0;
        });
        $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;

        return [
            'nb_groupes' => $nbGroupes,
            'nb_apprenants' => $nbApprenants,
            'taux_realisation' => round($tauxRealisation, 2),
        ];
    }

    private function buildDetailedQuery($code_efp, $filters)
    {
        $query = Affectation::query()
            ->join('groupes', 'affectations.groupe_id', '=', 'groupes.id')
            ->join('modules', 'affectations.module_id', '=', 'modules.id')
            ->join('formations', 'groupes.formation_id', '=', 'formations.id')
            ->join('filieres', 'groupes.filiere_id', '=', 'filieres.id')
            ->join('secteurs', 'filieres.secteur_id', '=', 'secteurs.id')
            ->join('niveaux', 'formations.niveau_id', '=', 'niveaux.id')
            ->join('etablissements', 'affectations.code_efp', '=', 'etablissements.code_efp')
            ->leftJoin('avancements', 'affectations.id', '=', 'avancements.affectation_id')
            ->leftJoin('formateurs as f_presentiel', 'affectations.mle_affecte_presentiel', '=', 'f_presentiel.mle')
            ->leftJoin('formateurs as f_syn', 'affectations.mle_affecte_syn', '=', 'f_syn.mle')
            ->where('etablissements.code_efp', $code_efp)
            ->select(
                'affectations.id as affectation_id',
                'etablissements.code_efp',
                'etablissements.nom_efp as efp',
                'formations.annee',
                'formations.type as type_formation',
                'formations.mode',
                'formations.creneau',
                'niveaux.nom as niveau',
                'secteurs.nom_secteur as secteur',
                'filieres.code_filiere',
                'filieres.nom_filiere as filiere',
                'groupes.code_groupe as groupe',
                'groupes.effectif_groupe',
                'groupes.sous_groupe',
                'groupes.statut_sous_groupe',
                'groupes.fusion_groupe',
                'groupes.code_fusion',
                'groupes.annee_formation',
                'modules.code_module',
                'modules.nom_module as module',
                'modules.regional',
                'modules.module_pie',
                'modules.efp_pie',
                'affectations.mle_affecte_presentiel as mle_presentiel',
                'affectations.mle_affecte_syn as mle_syn',
                'f_presentiel.nom_complet as formateur_presentiel',
                'f_syn.nom_complet as formateur_syn',
                // Masses horaires DRIF
                'affectations.mhp_s1_drif',
                'affectations.mhsyn_s1_drif',
                'affectations.mhasyn_s1_drif',
                'affectations.mh_totale_s1_drif',
                'affectations.mhp_s2_drif',
                'affectations.mhsyn_s2_drif',
                'affectations.mhasyn_s2_drif',
                'affectations.mh_totale_s2_drif',
                'affectations.mhp_totale_drif',
                'affectations.mhsyn_totale_drif',
                'affectations.mhasyn_totale_drif',
                'affectations.mh_totale_drif',
                // Masses horaires affectées
                'affectations.mh_affectee_presentiel',
                'affectations.mh_affectee_sync',
                'affectations.mh_affectee_globale',
                // Masses horaires réalisées (depuis avancements)
                DB::raw('COALESCE(avancements.mh_realisee_presentiel, 0) as mh_realisee_presentiel'),
                DB::raw('COALESCE(avancements.mh_realisee_sync, 0) as mh_realisee_sync'),
                DB::raw('COALESCE(avancements.mh_realisee_globale, 0) as mh_realisee_globale'),
                // Taux de réalisation
                DB::raw('COALESCE(avancements.taux_realisation_presentiel, 0) as taux_realisation_presentiel'),
                DB::raw('COALESCE(avancements.taux_realisation_syn, 0) as taux_realisation_syn'),
                DB::raw('COALESCE(avancements.taux_realisation_globale, 0) as taux_realisation_global'),
                // Autres infos avancements
                DB::raw('COALESCE(avancements.moyenne_absence, 0) as moy_absence'),
                DB::raw('COALESCE(avancements.nb_cc, 0) as nb_cc'),
                DB::raw('COALESCE(avancements.seance_efm, "Non") as seance_efm'),
                DB::raw('COALESCE(avancements.validation_efm, "non") as validation_efm'),
                'avancements.classe_teams',
                'avancements.date_maj'
            );

        // Appliquer les filtres
        if (!empty($filters['secteur'])) {
            $query->where('secteurs.nom_secteur', $filters['secteur']);
        }
        if (!empty($filters['filiere'])) {
            $query->where('filieres.code_filiere', $filters['filiere']);
        }
        if (!empty($filters['formateur'])) {
            $query->where(function($q) use ($filters) {
                $q->where('affectations.mle_affecte_presentiel', $filters['formateur'])
                  ->orWhere('affectations.mle_affecte_syn', $filters['formateur']);
            });
        }
        if (!empty($filters['module'])) {
            $query->where('modules.code_module', $filters['module']);
        }
        if (!empty($filters['groupe'])) {
            $query->where('groupes.code_groupe', $filters['groupe']);
        }

        return $query->orderBy('avancements.date_maj', 'desc')
                     ->orderBy('groupes.code_groupe', 'asc');
    }

    private function calculateStatistics($code_efp, $filters)
    {
        $query = Affectation::query()
            ->join('groupes', 'affectations.groupe_id', '=', 'groupes.id')
            ->join('modules', 'affectations.module_id', '=', 'modules.id')
            ->join('formations', 'groupes.formation_id', '=', 'formations.id')
            ->join('filieres', 'groupes.filiere_id', '=', 'filieres.id')
            ->join('secteurs', 'filieres.secteur_id', '=', 'secteurs.id')
            ->join('etablissements', 'affectations.code_efp', '=', 'etablissements.code_efp')
            ->leftJoin('avancements', 'affectations.id', '=', 'avancements.affectation_id')
            ->where('etablissements.code_efp', $code_efp);

        // Appliquer les filtres
        if (!empty($filters['secteur'])) {
            $query->where('secteurs.nom_secteur', $filters['secteur']);
        }
        if (!empty($filters['filiere'])) {
            $query->where('filieres.code_filiere', $filters['filiere']);
        }
        if (!empty($filters['formateur'])) {
            $query->where(function($q) use ($filters) {
                $q->where('affectations.mle_affecte_presentiel', $filters['formateur'])
                  ->orWhere('affectations.mle_affecte_syn', $filters['formateur']);
            });
        }
        if (!empty($filters['module'])) {
            $query->where('modules.code_module', $filters['module']);
        }
        if (!empty($filters['groupe'])) {
            $query->where('groupes.code_groupe', $filters['groupe']);
        }

        $affectations = $query->select(
            'affectations.mh_totale_drif',
            'affectations.mh_affectee_globale',
            'avancements.mh_realisee_globale',
            'groupes.id as groupe_id',
            'groupes.effectif_groupe',
            'formations.id as formation_id',
            'filieres.id as filiere_id',
            'secteurs.id as secteur_id',
            'modules.id as module_id',
            'affectations.mle_affecte_presentiel',
            'affectations.mle_affecte_syn'
        )->get();

        // Calculer les statistiques
        $heuresRequises = $affectations->sum('mh_totale_drif') ?: 0;
        $heuresAffectees = $affectations->sum('mh_affectee_globale') ?: 0;
        $heuresRealisees = $affectations->sum(function($item) {
            return $item->mh_realisee_globale ?? 0;
        });
        $difference = $heuresRequises - $heuresRealisees;

        $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;
        $tauxAffectation = $heuresRequises > 0 ? ($heuresAffectees / $heuresRequises) * 100 : 0;

        // Compter les entités uniques
        $nbFormations = $affectations->pluck('formation_id')->unique()->filter()->count();
        $nbFilieres = $affectations->pluck('filiere_id')->unique()->filter()->count();
        $nbSecteurs = $affectations->pluck('secteur_id')->unique()->filter()->count();
        $nbGroupes = $affectations->pluck('groupe_id')->unique()->filter()->count();
        $nbModules = $affectations->pluck('module_id')->unique()->filter()->count();
        
        // Formateurs uniques
        $formateursPresentiel = $affectations->pluck('mle_affecte_presentiel')->filter()->unique();
        $formateursSyn = $affectations->pluck('mle_affecte_syn')->filter()->unique();
        $nbFormateurs = $formateursPresentiel->merge($formateursSyn)->unique()->count();
        
        // Calculer l'effectif total
        $groupesUniques = $affectations->groupBy('groupe_id')->map(function($items) {
            return $items->first()->effectif_groupe ?? 0;
        });
        $nbApprenants = $groupesUniques->sum();

        return [
            'nb_formations' => $nbFormations,
            'nb_filieres' => $nbFilieres,
            'nb_secteurs' => $nbSecteurs,
            'nb_formateurs' => $nbFormateurs,
            'nb_groupes' => $nbGroupes,
            'nb_modules' => $nbModules,
            'nb_apprenants' => $nbApprenants,
            'heures_requises' => round($heuresRequises, 2),
            'heures_affectees' => round($heuresAffectees, 2),
            'heures_realisees' => round($heuresRealisees, 2),
            'difference' => round($difference, 2),
            'taux_realisation' => round($tauxRealisation, 2),
            'taux_affectation' => round($tauxAffectation, 2),
        ];
    }

    private function getChartData($code_efp, $filters)
    {
        $query = Affectation::query()
            ->join('groupes', 'affectations.groupe_id', '=', 'groupes.id')
            ->join('formations', 'groupes.formation_id', '=', 'formations.id')
            ->join('filieres', 'groupes.filiere_id', '=', 'filieres.id')
            ->join('secteurs', 'filieres.secteur_id', '=', 'secteurs.id')
            ->join('etablissements', 'affectations.code_efp', '=', 'etablissements.code_efp')
            ->leftJoin('avancements', 'affectations.id', '=', 'avancements.affectation_id')
            ->where('etablissements.code_efp', $code_efp);

        // Appliquer les filtres
        if (!empty($filters['secteur'])) {
            $query->where('secteurs.nom_secteur', $filters['secteur']);
        }
        if (!empty($filters['filiere'])) {
            $query->where('filieres.code_filiere', $filters['filiere']);
        }
        if (!empty($filters['formateur'])) {
            $query->where(function($q) use ($filters) {
                $q->where('affectations.mle_affecte_presentiel', $filters['formateur'])
                  ->orWhere('affectations.mle_affecte_syn', $filters['formateur']);
            });
        }
        if (!empty($filters['module'])) {
            $query->where('modules.code_module', $filters['module']);
        }
        if (!empty($filters['groupe'])) {
            $query->where('groupes.code_groupe', $filters['groupe']);
        }

        $affectations = $query->select(
            'affectations.mhp_s1_drif',
            'affectations.mhsyn_s1_drif',
            'affectations.mhasyn_s1_drif',
            'affectations.mhp_s2_drif',
            'affectations.mhsyn_s2_drif',
            'affectations.mhasyn_s2_drif',
            'affectations.mh_affectee_presentiel',
            'affectations.mh_affectee_sync',
            'avancements.mh_realisee_presentiel',
            'avancements.mh_realisee_sync'
        )->get();

        return [
            'heures_par_semestre' => [
                's1' => [
                    'presentiel' => round($affectations->sum('mhp_s1_drif'), 2),
                    'synchrone' => round($affectations->sum('mhsyn_s1_drif'), 2),
                    'asynchrone' => round($affectations->sum('mhasyn_s1_drif'), 2),
                ],
                's2' => [
                    'presentiel' => round($affectations->sum('mhp_s2_drif'), 2),
                    'synchrone' => round($affectations->sum('mhsyn_s2_drif'), 2),
                    'asynchrone' => round($affectations->sum('mhasyn_s2_drif'), 2),
                ],
            ],
            'heures_par_mode' => [
                'presentiel' => round($affectations->sum(function($item) {
                    return $item->mh_realisee_presentiel ?? 0;
                }), 2),
                'synchrone' => round($affectations->sum(function($item) {
                    return $item->mh_realisee_sync ?? 0;
                }), 2),
            ],
            'taux_par_mode' => [
                'presentiel' => [
                    'affectee' => round($affectations->sum('mh_affectee_presentiel'), 2),
                    'realisee' => round($affectations->sum(function($item) {
                        return $item->mh_realisee_presentiel ?? 0;
                    }), 2),
                ],
                'synchrone' => [
                    'affectee' => round($affectations->sum('mh_affectee_sync'), 2),
                    'realisee' => round($affectations->sum(function($item) {
                        return $item->mh_realisee_sync ?? 0;
                    }), 2),
                ],
            ],
        ];
    }

    private function getFilterOptions($code_efp)
    {
        $secteurs = Secteur::whereHas('filieres.groupes.formation', function($q) use ($code_efp) {
            $q->join('affectations', 'formations.id', '=', DB::raw('(SELECT formation_id FROM groupes WHERE groupes.id = affectations.groupe_id LIMIT 1)'))
              ->where('affectations.code_efp', $code_efp);
        })
        ->select('id', 'nom_secteur')
        ->distinct()
        ->orderBy('nom_secteur')
        ->get();

        $filieres = Filiere::whereHas('groupes.formation', function($q) use ($code_efp) {
            $q->join('affectations', 'groupes.id', '=', 'affectations.groupe_id')
              ->where('affectations.code_efp', $code_efp);
        })
        ->select('id', 'code_filiere', 'nom_filiere')
        ->orderBy('nom_filiere')
        ->get();

        // Récupérer les formateurs via la table affectations
        $formateursMle = Affectation::query()
            ->where('affectations.code_efp', $code_efp)
            ->where(function($q) {
                $q->whereNotNull('affectations.mle_affecte_presentiel')
                  ->orWhereNotNull('affectations.mle_affecte_syn');
            })
            ->select('affectations.mle_affecte_presentiel', 'affectations.mle_affecte_syn')
            ->distinct()
            ->get();

        // Collecter tous les MLE uniques
        $mleList = collect();
        foreach ($formateursMle as $item) {
            if (!empty($item->mle_affecte_presentiel)) {
                $mleList->push($item->mle_affecte_presentiel);
            }
            if (!empty($item->mle_affecte_syn)) {
                $mleList->push($item->mle_affecte_syn);
            }
        }
        $mleList = $mleList->unique()->values();

        // Récupérer les informations des formateurs
        $formateurs = Formateur::whereIn('mle', $mleList)
            ->select('mle', 'nom_complet as nom_formateur')
            ->orderBy('nom_complet')
            ->get();

        $modules = Module::whereHas('affectations', function($q) use ($code_efp) {
            $q->where('affectations.code_efp', $code_efp);
        })
        ->select('id', 'code_module', 'nom_module')
        ->orderBy('nom_module')
        ->get();

        $groupes = Groupe::whereHas('formation', function($q) use ($code_efp) {
            $q->join('affectations', 'groupes.id', '=', 'affectations.groupe_id')
              ->where('affectations.code_efp', $code_efp);
        })
        ->select('id', 'code_groupe as groupe')
        ->orderBy('code_groupe')
        ->get();

        return [
            'secteurs' => $secteurs,
            'filieres' => $filieres,
            'formateurs' => $formateurs,
            'modules' => $modules,
            'groupes' => $groupes,
        ];
    }
}