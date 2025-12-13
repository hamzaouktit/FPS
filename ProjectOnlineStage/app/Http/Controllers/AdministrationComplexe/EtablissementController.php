<?php

namespace App\Http\Controllers\AdministrationComplexe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
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

        // Optimisation avec eager loading complet
        $etablissements = Etablissement::where('complexe_id', $complexe->id)
            ->with([
                'user',
                'groupes' => function($query) {
                    $query->where('statut', 'Actif');
                },
                'secteurs',
                'modules',
                'formateurs',
                'affectations' => function($query) {
                    $query->with(['avancement', 'groupe.formation']);
                }
            ])
            ->paginate(10);

        // Calcul des stats pour chaque établissement
        foreach ($etablissements as $etablissement) {
            // Récupérer les affectations avec formations
            $affectations = $etablissement->affectations;
            
            // Compter les formations UNIQUES via les affectations
            $formations_count = $affectations->pluck('groupe.formation.id')
                ->unique()
                ->filter()
                ->count();
            
            // Nombre de groupes actifs
            $nb_groupes = $etablissement->groupes->count();
            
            // Nombre de stagiaires (somme des effectifs des groupes actifs)
            $nb_stagiaires = $etablissement->groupes->sum('effectif_groupe');
            
            // Nombre de secteurs
            $nb_secteurs = $etablissement->secteurs->count();
            
            // Nombre de modules
            $nb_modules = $etablissement->modules->count();
            
            // Nombre de formateurs
            $formateurs = $etablissement->formateurs;
            $nb_formateurs = $formateurs->count();
            $nb_permanents = $formateurs->where('type', 'permanent')->count();
            $nb_vacataires = $formateurs->where('type', 'vacataire')->count();
            
            // Calcul du taux de réalisation
            $mh_totale = $affectations->sum('mh_totale_drif');
            $mh_realisee = $affectations->sum(function($affectation) {
                return $affectation->avancement ? $affectation->avancement->mh_realisee_globale : 0;
            });
            
            $taux_realisation = $mh_totale > 0 
                ? ($mh_realisee / $mh_totale) * 100 
                : 0;
            
            // Stocker les stats dans l'objet établissement
            $etablissement->stats = [
                'nb_groupes' => $nb_groupes,
                'nb_stagiaires' => $nb_stagiaires,
                'nb_secteurs' => $nb_secteurs,
                'nb_modules' => $nb_modules,
                'nb_formateurs' => $nb_formateurs,
                'nb_permanents' => $nb_permanents,
                'nb_vacataires' => $nb_vacataires,
                'mh_totale' => $mh_totale,
                'mh_realisee' => $mh_realisee,
                'taux_realisation' => $taux_realisation,
            ];
            
            $etablissement->formations_count = $formations_count;
        }

        return view('administrationcomplexe.etablissements.index', compact(
            'user',
            'complexe',
            'etablissements'
        ));
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

        $etablissement = Etablissement::where('code_efp', $code_efp)
            ->where('complexe_id', $complexe->id)
            ->with(['user', 'complexe'])
            ->firstOrFail();

        // ✅ FILTRES COMPLETS (tableau principal + détails formateurs)
        $filters = [
            // Filtres du tableau principal
            'formateur' => $request->input('formateur'),
            'module' => $request->input('module'),
            'groupe' => $request->input('groupe'),
            'filiere' => $request->input('filiere'),
            'secteur' => $request->input('secteur'),
            // Filtres de la section formateurs détaillés
            'formateur_detail' => $request->input('formateur_detail'),
            'type_formateur' => $request->input('type_formateur'),
            'groupe_detail' => $request->input('groupe_detail'),
            'module_detail' => $request->input('module_detail'),
            'taux_min' => $request->input('taux_min'),
            'taux_max' => $request->input('taux_max'),
        ];

        $query = $this->buildDetailedQuery($code_efp, $filters);
        $detailedData = $query->paginate(20)->appends($request->except('page'));
        $statistics = $this->calculateStatistics($code_efp, $filters);
        $chartData = $this->getChartData($code_efp, $filters);
        $filterOptions = $this->getFilterOptions($code_efp);
        $modulesNonAffectesParFiliere = $this->getModulesNonAffectesParFiliere($code_efp);
        $modulesNonAffectesParGroupe = $this->getModulesNonAffectesParGroupe($code_efp);
        $formateursStats = $this->getFormateursStats($code_efp);
        $formateursDetailsAvecGroupes = $this->getFormateursDetailsAvecGroupes($code_efp, $filters);

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
            'formateursStats',
            'formateursDetailsAvecGroupes'
        ));
    }

    // ========== METHODES PRIVEES ==========

    /**
     * ✅ Modules non affectés par filière
     */
    private function getModulesNonAffectesParFiliere($code_efp)
    {
        $cacheKey = "modules_non_affectes_filiere_{$code_efp}";
        
        return Cache::remember($cacheKey, 300, function() use ($code_efp) {
            $filieres = Filiere::where('code_efp', $code_efp)
                ->with(['modules' => function($query) use ($code_efp) {
                    $query->where('modules.code_efp', $code_efp);
                }])
                ->get();
            
            $result = collect();
            
            foreach ($filieres as $filiere) {
                // Récupérer les IDs des groupes actifs
                $groupesIds = Groupe::where('filiere_id', $filiere->id)
                    ->where('code_efp', $code_efp)
                    ->where('statut', 'Actif')
                    ->pluck('id');
                
                if ($groupesIds->isEmpty()) {
                    foreach ($filiere->modules as $module) {
                        $result->push((object)[
                            'code_filiere' => $filiere->code_filiere,
                            'nom_filiere' => $filiere->nom_filiere,
                            'code_module' => $module->code_module,
                            'nom_module' => $module->nom_module,
                            'raison' => 'Aucun groupe actif'
                        ]);
                    }
                    continue;
                }
                
                foreach ($filiere->modules as $module) {
                    // Vérifier si au moins UNE affectation complète existe
                    $affectationComplete = Affectation::where('module_id', $module->id)
                        ->where('code_efp', $code_efp)
                        ->whereIn('groupe_id', $groupesIds)
                        ->where(function($query) {
                            $query->whereNotNull('mle_affecte_presentiel')
                                  ->orWhereNotNull('mle_affecte_syn');
                        })
                        ->exists();
                    
                    if (!$affectationComplete) {
                        $affectationsSansFormateur = Affectation::where('module_id', $module->id)
                            ->where('code_efp', $code_efp)
                            ->whereIn('groupe_id', $groupesIds)
                            ->whereNull('mle_affecte_presentiel')
                            ->whereNull('mle_affecte_syn')
                            ->exists();
                        
                        $result->push((object)[
                            'code_filiere' => $filiere->code_filiere,
                            'nom_filiere' => $filiere->nom_filiere,
                            'code_module' => $module->code_module,
                            'nom_module' => $module->nom_module,
                            'raison' => $affectationsSansFormateur ? 'Affectations sans formateur' : 'Aucune affectation'
                        ]);
                    }
                }
            }
            
            return $result->sortBy('nom_filiere')->groupBy('nom_filiere');
        });
    }

    /**
     * ✅ Modules non affectés par groupe
     */
    private function getModulesNonAffectesParGroupe($code_efp)
    {
        $cacheKey = "modules_non_affectes_groupe_{$code_efp}";
        
        return Cache::remember($cacheKey, 300, function() use ($code_efp) {
            $groupes = Groupe::where('code_efp', $code_efp)
                ->where('statut', 'Actif')
                ->with(['filiere.modules' => function($query) use ($code_efp) {
                    $query->where('modules.code_efp', $code_efp);
                }])
                ->get();
            
            $result = collect();
            
            foreach ($groupes as $groupe) {
                if (!$groupe->filiere || $groupe->filiere->modules->isEmpty()) {
                    continue;
                }
                
                foreach ($groupe->filiere->modules as $module) {
                    // Vérifier l'affectation pour CE groupe spécifique
                    $affectationComplete = Affectation::where('module_id', $module->id)
                        ->where('groupe_id', $groupe->id)
                        ->where('code_efp', $code_efp)
                        ->where(function($query) {
                            $query->whereNotNull('mle_affecte_presentiel')
                                  ->orWhereNotNull('mle_affecte_syn');
                        })
                        ->exists();
                    
                    if (!$affectationComplete) {
                        $affectationExiste = Affectation::where('module_id', $module->id)
                            ->where('groupe_id', $groupe->id)
                            ->where('code_efp', $code_efp)
                            ->exists();
                        
                        $result->push((object)[
                            'code_groupe' => $groupe->code_groupe,
                            'code_filiere' => $groupe->filiere->code_filiere,
                            'nom_filiere' => $groupe->filiere->nom_filiere,
                            'code_module' => $module->code_module,
                            'nom_module' => $module->nom_module,
                            'raison' => $affectationExiste ? 'Affectation sans formateur' : 'Aucune affectation',
                            'effectif' => $groupe->effectif_groupe
                        ]);
                    }
                }
            }
            
            return $result->sortBy('code_groupe')->groupBy('code_groupe');
        });
    }

    /**
     * ✅ Stats des formateurs
     */
    private function getFormateursStats($code_efp)
    {
        $cacheKey = "formateurs_stats_{$code_efp}";
        
        return Cache::remember($cacheKey, 300, function() use ($code_efp) {
            // ✅ Récupérer les formateurs via la relation many-to-many
            $formateurs = Formateur::whereHas('etablissements', function($query) use ($code_efp) {
                    $query->where('etablissements.code_efp', $code_efp);
                })
                ->with([
                    'affectationsPresentiel' => function($query) use ($code_efp) {
                        $query->where('code_efp', $code_efp)
                              ->select('id', 'mle_affecte_presentiel', 'mhp_totale_drif', 'mh_affectee_presentiel');
                    },
                    'affectationsSyn' => function($query) use ($code_efp) {
                        $query->where('code_efp', $code_efp)
                              ->select('id', 'mle_affecte_syn', 'mhsyn_totale_drif', 'mh_affectee_sync');
                    }
                ])
                ->get();

            $stats = [];

            foreach ($formateurs as $formateur) {
                $heuresRequisesPresentiel = $formateur->affectationsPresentiel->sum('mhp_totale_drif');
                $heuresRequisesSyn = $formateur->affectationsSyn->sum('mhsyn_totale_drif');
                $heuresRequisesTotal = $heuresRequisesPresentiel + $heuresRequisesSyn;

                $heuresAffecteesPresentiel = $formateur->affectationsPresentiel->sum('mh_affectee_presentiel');
                $heuresAffecteesSyn = $formateur->affectationsSyn->sum('mh_affectee_sync');
                $heuresAffecteesTotal = $heuresAffecteesPresentiel + $heuresAffecteesSyn;

                $heuresManquantes = max(0, $heuresRequisesTotal - $heuresAffecteesTotal);
                $tauxAffectation = $heuresRequisesTotal > 0 
                    ? ($heuresAffecteesTotal / $heuresRequisesTotal) * 100 
                    : 0;

                $stats[] = [
                    'mle' => $formateur->mle,
                    'nom_complet' => $formateur->nom_complet,
                    'type' => $formateur->type,
                    'heures_requises' => round($heuresRequisesTotal, 2),
                    'heures_affectees' => round($heuresAffecteesTotal, 2),
                    'heures_manquantes' => round($heuresManquantes, 2),
                    'taux_affectation' => round($tauxAffectation, 2),
                ];
            }

            usort($stats, function($a, $b) {
                return $b['heures_manquantes'] <=> $a['heures_manquantes'];
            });

            $totaux = [
                'heures_requises' => round(array_sum(array_column($stats, 'heures_requises')), 2),
                'heures_affectees' => round(array_sum(array_column($stats, 'heures_affectees')), 2),
                'heures_manquantes' => round(array_sum(array_column($stats, 'heures_manquantes')), 2),
                'taux_affectation' => 0,
            ];

            if ($totaux['heures_requises'] > 0) {
                $totaux['taux_affectation'] = round(($totaux['heures_affectees'] / $totaux['heures_requises']) * 100, 2);
            }

            return [
                'formateurs' => $stats,
                'totaux' => $totaux,
            ];
        });
    }

    /**
     * ✅ Stats détaillées des formateurs avec leurs groupes et modules - AVEC FILTRES
     */
    private function getFormateursDetailsAvecGroupes($code_efp, $filters = [])
    {
        // Créer une clé de cache unique basée sur les filtres
        $filterKey = md5(json_encode(array_intersect_key($filters, array_flip([
            'formateur_detail', 'type_formateur', 'groupe_detail', 'module_detail', 'taux_min', 'taux_max'
        ]))));
        $cacheKey = "formateurs_details_groupes_{$code_efp}_{$filterKey}";
        
        return Cache::remember($cacheKey, 300, function() use ($code_efp, $filters) {
            $query = Formateur::whereHas('etablissements', function($query) use ($code_efp) {
                    $query->where('etablissements.code_efp', $code_efp);
                });

            // ✅ FILTRE par formateur spécifique
            if (!empty($filters['formateur_detail'])) {
                $query->where('mle', $filters['formateur_detail']);
            }

            // ✅ FILTRE par type de formateur
            if (!empty($filters['type_formateur'])) {
                $query->where('type', $filters['type_formateur']);
            }

            $formateurs = $query->with([
                    'affectationsPresentiel' => function($query) use ($code_efp, $filters) {
                        $query->where('code_efp', $code_efp);
                        
                        // ✅ FILTRE par groupe
                        if (!empty($filters['groupe_detail'])) {
                            $query->whereHas('groupe', function($q) use ($filters) {
                                $q->where('code_groupe', $filters['groupe_detail']);
                            });
                        }
                        
                        // ✅ FILTRE par module
                        if (!empty($filters['module_detail'])) {
                            $query->whereHas('module', function($q) use ($filters) {
                                $q->where('code_module', $filters['module_detail']);
                            });
                        }
                        
                        $query->with([
                            'groupe:id,code_groupe,effectif_groupe',
                            'module:id,code_module,nom_module',
                            'avancement:id,affectation_id,mh_realisee_globale,taux_realisation_globale,mh_realisee_presentiel,taux_realisation_presentiel'
                        ]);
                    },
                    'affectationsSyn' => function($query) use ($code_efp, $filters) {
                        $query->where('code_efp', $code_efp);
                        
                        // ✅ FILTRE par groupe
                        if (!empty($filters['groupe_detail'])) {
                            $query->whereHas('groupe', function($q) use ($filters) {
                                $q->where('code_groupe', $filters['groupe_detail']);
                            });
                        }
                        
                        // ✅ FILTRE par module
                        if (!empty($filters['module_detail'])) {
                            $query->whereHas('module', function($q) use ($filters) {
                                $q->where('code_module', $filters['module_detail']);
                            });
                        }
                        
                        $query->with([
                            'groupe:id,code_groupe,effectif_groupe',
                            'module:id,code_module,nom_module',
                            'avancement:id,affectation_id,mh_realisee_globale,taux_realisation_globale,mh_realisee_sync,taux_realisation_syn'
                        ]);
                    }
                ])
                ->get();

            $formateursData = [];

            foreach ($formateurs as $formateur) {
                $affectationsDetails = [];
                $totalHeuresRealisees = 0;
                $totalHeuresRequises = 0;

                // Traiter les affectations présentiel
                foreach ($formateur->affectationsPresentiel as $affectation) {
                    if (!$affectation->groupe || !$affectation->module) continue;
                    
                    $mhRequise = $affectation->mhp_totale_drif;
                    $mhRealisee = $affectation->avancement 
                        ? $affectation->avancement->mh_realisee_presentiel 
                        : 0;
                    $tauxRealisation = $affectation->avancement 
                        ? $affectation->avancement->taux_realisation_presentiel 
                        : 0;

                    $affectationsDetails[] = [
                        'groupe' => $affectation->groupe->code_groupe,
                        'effectif' => $affectation->groupe->effectif_groupe,
                        'module_code' => $affectation->module->code_module,
                        'module_nom' => $affectation->module->nom_module,
                        'mode' => 'Présentiel',
                        'mh_requise' => $mhRequise,
                        'mh_realisee' => $mhRealisee,
                        'taux_realisation' => $tauxRealisation,
                    ];

                    $totalHeuresRealisees += $mhRealisee;
                    $totalHeuresRequises += $mhRequise;
                }

                // Traiter les affectations synchrone
                foreach ($formateur->affectationsSyn as $affectation) {
                    if (!$affectation->groupe || !$affectation->module) continue;
                    
                    $mhRequise = $affectation->mhsyn_totale_drif;
                    $mhRealisee = $affectation->avancement 
                        ? $affectation->avancement->mh_realisee_sync 
                        : 0;
                    $tauxRealisation = $affectation->avancement 
                        ? $affectation->avancement->taux_realisation_syn 
                        : 0;

                    $affectationsDetails[] = [
                        'groupe' => $affectation->groupe->code_groupe,
                        'effectif' => $affectation->groupe->effectif_groupe,
                        'module_code' => $affectation->module->code_module,
                        'module_nom' => $affectation->module->nom_module,
                        'mode' => 'Synchrone',
                        'mh_requise' => $mhRequise,
                        'mh_realisee' => $mhRealisee,
                        'taux_realisation' => $tauxRealisation,
                    ];

                    $totalHeuresRealisees += $mhRealisee;
                    $totalHeuresRequises += $mhRequise;
                }

                // Calculer le taux global du formateur
                $tauxGlobal = $totalHeuresRequises > 0 
                    ? ($totalHeuresRealisees / $totalHeuresRequises) * 100 
                    : 0;

                // ✅ FILTRE par taux de réalisation
                if (!empty($filters['taux_min']) && $tauxGlobal < $filters['taux_min']) {
                    continue;
                }
                if (!empty($filters['taux_max']) && $tauxGlobal > $filters['taux_max']) {
                    continue;
                }

                // Trier les affectations par groupe puis module
                usort($affectationsDetails, function($a, $b) {
                    $groupeCompare = strcmp($a['groupe'], $b['groupe']);
                    if ($groupeCompare !== 0) return $groupeCompare;
                    return strcmp($a['module_nom'], $b['module_nom']);
                });

                if (!empty($affectationsDetails)) {
                    $formateursData[] = [
                        'mle' => $formateur->mle,
                        'nom_complet' => $formateur->nom_complet,
                        'type' => $formateur->type,
                        'affectations' => $affectationsDetails,
                        'total_heures_requises' => round($totalHeuresRequises, 2),
                        'total_heures_realisees' => round($totalHeuresRealisees, 2),
                        'taux_global' => round($tauxGlobal, 2),
                        'nb_affectations' => count($affectationsDetails),
                    ];
                }
            }

            // Trier par taux de réalisation croissant (formateurs en retard en premier)
            usort($formateursData, function($a, $b) {
                return $a['taux_global'] <=> $b['taux_global'];
            });

            return $formateursData;
        });
    }

    /**
     * Construction de la requête détaillée avec filtres
     */
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
                'groupes.annee_formation',
                'affectations.fusion_groupe',
                'affectations.code_fusion',
                'modules.code_module',
                'modules.nom_module as module',
                'modules.regional',
                'modules.module_pie',
                'modules.efp_pie',
                'affectations.mle_affecte_presentiel as mle_presentiel',
                'affectations.mle_affecte_syn as mle_syn',
                'f_presentiel.nom_complet as formateur_presentiel',
                'f_syn.nom_complet as formateur_syn',
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
                'affectations.mh_affectee_presentiel',
                'affectations.mh_affectee_sync',
                'affectations.mh_affectee_globale',
                DB::raw('COALESCE(avancements.mh_realisee_presentiel, 0) as mh_realisee_presentiel'),
                DB::raw('COALESCE(avancements.mh_realisee_sync, 0) as mh_realisee_sync'),
                DB::raw('COALESCE(avancements.mh_realisee_globale, 0) as mh_realisee_globale'),
                DB::raw('COALESCE(avancements.taux_realisation_presentiel, 0) as taux_realisation_presentiel'),
                DB::raw('COALESCE(avancements.taux_realisation_syn, 0) as taux_realisation_syn'),
                DB::raw('COALESCE(avancements.taux_realisation_globale, 0) as taux_realisation_global'),
                DB::raw('COALESCE(avancements.moyenne_absence, 0) as moy_absence'),
                DB::raw('COALESCE(avancements.nb_cc, 0) as nb_cc'),
                DB::raw('COALESCE(avancements.seance_efm, "Non") as seance_efm'),
                DB::raw('COALESCE(avancements.validation_efm, "non") as validation_efm'),
                'avancements.classe_teams',
                'avancements.date_maj'
            );

        // Appliquer les filtres (uniquement les filtres du tableau principal)
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

    /**
     * Calcul des statistiques
     */
    private function calculateStatistics($code_efp, $filters)
    {
        $query = Affectation::query()
            ->join('groupes', 'affectations.groupe_id', '=', 'groupes.id')
            ->join('modules', 'affectations.module_id', '=', 'modules.id')
            ->join('formations', 'groupes.formation_id', '=', 'formations.id')
            ->join('filieres', 'groupes.filiere_id', '=', 'filieres.id')
            ->join('secteurs', 'filieres.secteur_id', '=', 'secteurs.id')
            ->leftJoin('avancements', 'affectations.id', '=', 'avancements.affectation_id')
            ->where('affectations.code_efp', $code_efp);

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

        $heuresRequises = $affectations->sum('mh_totale_drif') ?: 0;
        $heuresAffectees = $affectations->sum('mh_affectee_globale') ?: 0;
        $heuresRealisees = $affectations->sum(function($item) {
            return $item->mh_realisee_globale ?? 0;
        });
        $difference = $heuresRequises - $heuresRealisees;

        $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;
        $tauxAffectation = $heuresRequises > 0 ? ($heuresAffectees / $heuresRequises) * 100 : 0;

        $nbFormations = $affectations->pluck('formation_id')->unique()->filter()->count();
        $nbGroupes = $affectations->pluck('groupe_id')->unique()->filter()->count();
        $nbModules = $affectations->pluck('module_id')->unique()->filter()->count();
        
        $formateursPresentiel = $affectations->pluck('mle_affecte_presentiel')->filter()->unique();
        $formateursSyn = $affectations->pluck('mle_affecte_syn')->filter()->unique();
        $nbFormateurs = $formateursPresentiel->merge($formateursSyn)->unique()->count();
        
        $groupesUniques = $affectations->groupBy('groupe_id')->map(function($items) {
            return $items->first()->effectif_groupe ?? 0;
        });
        $nbApprenants = $groupesUniques->sum();

        return [
            'nb_formations' => $nbFormations,
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

    /**
     * Données pour les graphiques
     */
    private function getChartData($code_efp, $filters)
    {
        $query = Affectation::query()
            ->join('groupes', 'affectations.groupe_id', '=', 'groupes.id')
            ->join('filieres', 'groupes.filiere_id', '=', 'filieres.id')
            ->join('secteurs', 'filieres.secteur_id', '=', 'secteurs.id')
            ->join('modules', 'affectations.module_id', '=', 'modules.id')
            ->leftJoin('avancements', 'affectations.id', '=', 'avancements.affectation_id')
            ->where('affectations.code_efp', $code_efp);

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

    /**
     * Options de filtrage
     */
    private function getFilterOptions($code_efp)
    {
        $secteurs = Secteur::where('code_efp', $code_efp)
            ->select('id', 'nom_secteur')
            ->distinct()
            ->orderBy('nom_secteur')
            ->get();

        $filieres = Filiere::where('code_efp', $code_efp)
            ->select('id', 'code_filiere', 'nom_filiere')
            ->orderBy('nom_filiere')
            ->get();

        // ✅ Récupérer les formateurs via la table pivot
        $formateursMle = DB::table('affectations')
            ->where('code_efp', $code_efp)
            ->where(function($q) {
                $q->whereNotNull('mle_affecte_presentiel')
                  ->orWhereNotNull('mle_affecte_syn');
            })
            ->get()
            ->flatMap(function($item) {
                return array_filter([
                    $item->mle_affecte_presentiel,
                    $item->mle_affecte_syn
                ]);
            })
            ->unique()
            ->values();

        $formateurs = Formateur::whereIn('mle', $formateursMle)
            ->select('mle', 'nom_complet as nom_formateur')
            ->orderBy('nom_complet')
            ->get();

        $modules = Module::where('code_efp', $code_efp)
            ->select('id', 'code_module', 'nom_module')
            ->orderBy('nom_module')
            ->get();

        $groupes = Groupe::where('code_efp', $code_efp)
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_complexe') {
            return redirect()->route('welcome')
                ->with('error', 'Accès refusé.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé.');
        }

        $directeurs = User::where('role', 'directeur_etablissement')
            ->whereDoesntHave('etablissement')
            ->orderBy('nom')
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
                ->with('error', 'Accès refusé.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé.');
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
        
        try {
            Etablissement::create($validated);
            
            // Vider le cache
            Cache::forget("modules_non_affectes_filiere_{$validated['code_efp']}");
            Cache::forget("modules_non_affectes_groupe_{$validated['code_efp']}");
            Cache::forget("formateurs_stats_{$validated['code_efp']}");
            
            return redirect()->route('administration.complexe.etablissements.index')
                ->with('success', 'Établissement créé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de l\'établissement: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($code_efp)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_complexe') {
            return redirect()->route('welcome')
                ->with('error', 'Accès refusé.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé.');
        }

        $etablissement = Etablissement::where('code_efp', $code_efp)
            ->where('complexe_id', $complexe->id)
            ->firstOrFail();

        $directeurs = User::where('role', 'directeur_etablissement')
            ->where(function($query) use ($etablissement) {
                $query->whereDoesntHave('etablissement')
                      ->orWhere('id', $etablissement->user_id);
            })
            ->orderBy('nom')
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
                ->with('error', 'Accès refusé.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé.');
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

        try {
            $etablissement->update($validated);
            
            // Vider le cache
            Cache::forget("modules_non_affectes_filiere_{$code_efp}");
            Cache::forget("modules_non_affectes_groupe_{$code_efp}");
            Cache::forget("formateurs_stats_{$code_efp}");
            
            return redirect()->route('administration.complexe.etablissements.index')
                ->with('success', 'Établissement modifié avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erreur lors de la modification: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($code_efp)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_complexe') {
            return redirect()->route('welcome')
                ->with('error', 'Accès refusé.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé.');
        }

        $etablissement = Etablissement::where('code_efp', $code_efp)
            ->where('complexe_id', $complexe->id)
            ->firstOrFail();

        // Vérifier s'il y a des données liées
        $hasRelatedData = $etablissement->formations()->count() > 0 
                       || $etablissement->groupes()->count() > 0 
                       || $etablissement->affectations()->count() > 0;

        if ($hasRelatedData) {
            return redirect()->route('administration.complexe.etablissements.index')
                ->with('error', 'Impossible de supprimer cet établissement car il contient des données (formations, groupes ou affectations).');
        }

        try {
            $etablissement->delete();
            
            // Vider le cache
            Cache::forget("modules_non_affectes_filiere_{$code_efp}");
            Cache::forget("modules_non_affectes_groupe_{$code_efp}");
            Cache::forget("formateurs_stats_{$code_efp}");
            
            return redirect()->route('administration.complexe.etablissements.index')
                ->with('success', 'Établissement supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
}