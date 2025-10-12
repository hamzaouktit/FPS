<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Etablissement;
use App\Models\Formation;
use App\Models\Groupe;
use App\Models\Avancement;
use App\Models\Affectation;
use App\Models\Module;
use App\Models\Formateur;
use App\Models\Filiere;
use App\Models\Secteur;
use App\Models\Niveau;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AvancementImport;
use App\Imports\DataImport;
use Illuminate\Support\Facades\Log;

class DashboardEtablissementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if (!$etablissement) {
            return redirect()->route('welcome')->with('error', 'Aucun établissement associé à cet utilisateur.');
        }
        
        // Récupération des filtres
        $filters = [
            'groupe' => $request->input('groupe'),
            'module' => $request->input('module'),
            'formateur' => $request->input('formateur'),
            'niveau' => $request->input('niveau'),
            'filiere' => $request->input('filiere'),
            'annee' => $request->input('annee', date('Y')),
        ];

        // Construction de la requête de base avec les avancements filtrés par utilisateur
        $avancementsQuery = Avancement::forUser($user);

        // Application des filtres
        if ($filters['groupe']) {
            $avancementsQuery->where('groupe_id', $filters['groupe']);
        }
        if ($filters['module']) {
            $avancementsQuery->where('module_id', $filters['module']);
        }
        if ($filters['formateur']) {
            $avancementsQuery->where(function($query) use ($filters) {
                $query->where('mle_presentiel', $filters['formateur'])
                      ->orWhere('mle_syn', $filters['formateur']);
            });
        }
        if ($filters['niveau']) {
            $avancementsQuery->whereHas('groupe.formation', function($query) use ($filters) {
                $query->where('niveau_id', $filters['niveau']);
            });
        }
        if ($filters['filiere']) {
            $avancementsQuery->whereHas('groupe.formation', function($query) use ($filters) {
                $query->where('filiere_id', $filters['filiere']);
            });
        }
        if ($filters['annee']) {
            $avancementsQuery->whereHas('groupe.formation', function($query) use ($filters) {
                $query->where('annee', $filters['annee']);
            });
        }

        // Eager loading complet pour éviter les erreurs
        $avancements = $avancementsQuery->with([
            'groupe.formation.filiere.secteur',
            'groupe.formation.niveau',
            'groupe.etablissement',
            'module',
            'formateurPresentiel',
            'formateurSynchrone'
        ])->get();

        // Statistiques principales
        $statistics = $this->calculateStatistics($avancements, $etablissement, $filters, $user);

        // Analyse des heures par type
        $heuresAnalysis = $this->calculateHeuresAnalysis($avancements);
        
        // Données pour l'analyse des heures
        $heuresData = $this->calculateHeuresData($avancements);
        
        // Graphiques de taux
        $tauxChartData = $this->getTauxChartData($avancements);
        
        // Données détaillées par groupe et module
        $detailedData = $this->getDetailedGroupeModuleData($avancements);
        
        // Top 10 modules avec meilleurs taux
        $topModules = $this->getTopModules($avancements);
        
        // Données pour les graphiques
        $chartData = $this->getChartData($avancements);
        
        // Taux de réalisation par formateur
        $formateurStats = $this->getFormateurStats($avancements);
        
        // Options pour les filtres
        $filterOptions = $this->getFilterOptions($etablissement, $user);

        // Modules non affectés
        $avancementsNonAffectes = $avancements->filter(function ($avancement) {
            return is_null($avancement->mle_presentiel) && is_null($avancement->mle_syn);
        });

        // Par module
        $nonAffectesParModule = $avancementsNonAffectes->groupBy('module_id')->map(function ($group) {
            $firstAvancement = $group->first();
            return [
                'code_module' => $firstAvancement->module ? $firstAvancement->module->code_module : 'N/A',
                'nom_module' => $firstAvancement->module ? $firstAvancement->module->nom_module : 'N/A',
                'groupes' => $group->pluck('groupe.nom_groupe')->unique()->implode(', '),
                'masse_horaire' => $group->sum('mh_totale_drif'),
                'formateur' => 'Non affecté',
            ];
        })->values();

        $totalNonAffectesModule = $avancementsNonAffectes->sum('mh_totale_drif');

        // Par filière avec modules
        $nonAffectesParFiliere = $avancementsNonAffectes->groupBy(function ($avancement) {
            return $avancement->groupe && $avancement->groupe->formation ? $avancement->groupe->formation->filiere_id : null;
        })->map(function ($group) {
            $firstAvancement = $group->first();
            $filiere = ($firstAvancement->groupe && $firstAvancement->groupe->formation) ? $firstAvancement->groupe->formation->filiere : null;
            
            $modules = $group->groupBy('module_id')->map(function ($moduleGroup) {
                return $moduleGroup->first()->module ? $moduleGroup->first()->module->nom_module : 'N/A';
            })->implode(', ');
            
            return [
                'code_filiere' => $filiere ? $filiere->code_filiere : 'N/A',
                'nom_filiere' => $filiere ? $filiere->nom_filiere : 'N/A',
                'modules' => $modules,
                'masse_horaire' => $group->sum('mh_totale_drif'),
            ];
        })->filter(function($item) {
            return $item['code_filiere'] !== null;
        })->values();

        $totalNonAffectesFiliere = $avancementsNonAffectes->sum('mh_totale_drif');

        // Liste des formateurs avec totaux
        $formateursData = $filterOptions['formateurs']->map(function ($formateur) use ($avancements) {
            $avForForm = $avancements->filter(function ($av) use ($formateur) {
                return $av->mle_presentiel == $formateur->mle || $av->mle_syn == $formateur->mle;
            });
            $heuresRequises = $avForForm->sum('mh_totale_drif');
            $heuresAffectees = $avForForm->sum('mh_affectee_globale');
            $heuresManquantes = $heuresRequises - $heuresAffectees;
            return [
                'nom_formateur' => $formateur->nom_formateur,
                'heures_requises' => $heuresRequises,
                'heures_affectees' => $heuresAffectees,
                'heures_manquantes' => $heuresManquantes,
            ];
        })->filter(function ($data) {
            return $data['heures_requises'] > 0;
        })->values();

        // Calculer les totaux des formateurs
        $totalFormateurs = [
            'heures_requises' => $formateursData->sum('heures_requises'),
            'heures_affectees' => $formateursData->sum('heures_affectees'),
            'heures_manquantes' => $formateursData->sum('heures_manquantes'),
        ];

        return view('administrationetablissement.dashboard', compact(
            'etablissement',
            'statistics',
            'heuresAnalysis',
            'heuresData',
            'tauxChartData',
            'detailedData',
            'topModules',
            'chartData',
            'formateurStats',
            'filterOptions',
            'filters',
            'nonAffectesParModule',
            'totalNonAffectesModule',
            'nonAffectesParFiliere',
            'totalNonAffectesFiliere',
            'formateursData',
            'totalFormateurs'
        ));
    }

    private function calculateStatistics($avancements, $etablissement, $filters = [], $user)
    {
        // Récupérer TOUTES les données filtrées par utilisateur
        $baseQuery = Formation::forUser($user);
        
        // Appliquer les mêmes filtres que pour les avancements
        if (!empty($filters['annee'])) {
            $baseQuery->where('annee', $filters['annee']);
        }
        if (!empty($filters['filiere'])) {
            $baseQuery->where('filiere_id', $filters['filiere']);
        }
        if (!empty($filters['niveau'])) {
            $baseQuery->where('niveau_id', $filters['niveau']);
        }

        $formations = $baseQuery->get();
        
        // Compter les formations
        $totalFormations = $formations->count();
        
        // Compter les groupes
        $groupesQuery = Groupe::forUser($user)->whereIn('formation_id', $formations->pluck('id'));
        if (!empty($filters['groupe'])) {
            $groupesQuery->where('id', $filters['groupe']);
        }
        $groupes = $groupesQuery->get();
        $totalGroupes = $groupes->count();
        
        // Compter les modules uniques
        $modulesQuery = Module::forUser($user)->whereIn('id', 
            Avancement::forUser($user)->whereIn('groupe_id', $groupes->pluck('id'))->pluck('module_id')
        );
        if (!empty($filters['module'])) {
            $modulesQuery->where('id', $filters['module']);
        }
        $modules = $modulesQuery->get();
        $totalModules = $modules->count();
        
        // Compter les formateurs uniques
        $formateursIds = Avancement::forUser($user)->whereIn('groupe_id', $groupes->pluck('id'))
            ->where(function($query) {
                $query->whereNotNull('mle_presentiel')
                      ->orWhereNotNull('mle_syn');
            })
            ->get()
            ->flatMap(function($avancement) {
                return [$avancement->mle_presentiel, $avancement->mle_syn];
            })
            ->filter()
            ->unique();
        
        $totalFormateurs = $formateursIds->count();
        
        // Compter les filières uniques
        $filieres = $formations->pluck('filiere_id')->unique();
        $totalFilieres = Filiere::forUser($user)->whereIn('id', $filieres)->count();
        
        // Compter les secteurs via filières
        $secteurs = Filiere::forUser($user)->whereIn('id', $filieres)->pluck('secteur_id')->unique();
        $totalSecteurs = Secteur::forUser($user)->whereIn('id', $secteurs)->count();
        
        // Heures (basé sur les avancements filtrés)
        $heuresRequisesTotal = $avancements->sum('mh_totale_drif') ?: 0;
        $heuresAffecteesTotal = $avancements->sum('mh_affectee_globale') ?: 0;
        $heuresRealiseesTotal = $avancements->sum('mh_realisee_globale') ?: 0;
        
        $tauxRealisationGlobal = $heuresRequisesTotal > 0 ? ($heuresRealiseesTotal / $heuresRequisesTotal) * 100 : 0;
        $tauxAffectation = $heuresRequisesTotal > 0 ? ($heuresAffecteesTotal / $heuresRequisesTotal) * 100 : 0;
        
        // Taux par mode
        $heuresRequisesPresentiel = $avancements->sum('mhp_totale_drif') ?: 0;
        $heuresRequisesSynchrone = $avancements->sum('mhsyn_totale_drif') ?: 0;
        
        $tauxRealisationPresentiel = $heuresRequisesPresentiel > 0 ? ($avancements->sum('mh_realisee_presentiel') / $heuresRequisesPresentiel) * 100 : 0;
        $tauxRealisationSynchrone = $heuresRequisesSynchrone > 0 ? ($avancements->sum('mh_realisee_sync') / $heuresRequisesSynchrone) * 100 : 0;
        
        $moyenneAbsence = $avancements->avg('moy_absence') ?? 0;
        $totalCC = $avancements->sum('nb_cc');
        $totalEFM = $avancements->where('validation_efm', 1)->count();

        return [
            'nb_formations' => $totalFormations,
            'nb_filieres' => $totalFilieres,
            'nb_secteurs' => $totalSecteurs,
            'nb_formateurs' => $totalFormateurs,
            'nb_groupes' => $totalGroupes,
            'nb_modules' => $totalModules,
            'heures_requises' => round($heuresRequisesTotal, 2),
            'heures_affectees' => round($heuresAffecteesTotal, 2),
            'heures_realisees' => round($heuresRealiseesTotal, 2),
            'difference' => round($heuresRequisesTotal - $heuresRealiseesTotal, 2),
            'taux_realisation' => round($tauxRealisationGlobal, 2),
            'taux_affectation' => round($tauxAffectation, 2),
            'taux_realisation_presentiel' => round($tauxRealisationPresentiel, 2),
            'taux_realisation_synchrone' => round($tauxRealisationSynchrone, 2),
            'moyenne_absence' => round($moyenneAbsence, 2),
            'total_cc' => $totalCC,
            'total_efm' => $totalEFM,
        ];
    }

    private function calculateHeuresData($avancements)
    {
        $heuresRequises = $avancements->sum('mh_totale_drif') ?: 0;
        $heuresAffectees = $avancements->sum('mh_affectee_globale') ?: 0;
        $heuresRealisees = $avancements->sum('mh_realisee_globale') ?: 0;

        $difference = $heuresRequises - $heuresRealisees;
        
        $tauxAffectation = $heuresRequises > 0 ? ($heuresAffectees / $heuresRequises) * 100 : 0;
        $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;

        return [
            'heures_requises' => round($heuresRequises, 2),
            'heures_affectees' => round($heuresAffectees, 2),
            'heures_realisees' => round($heuresRealisees, 2),
            'difference' => round($difference, 2),
            'taux_affectation' => round($tauxAffectation, 2),
            'taux_realisation' => round($tauxRealisation, 2),
        ];
    }

    private function calculateHeuresAnalysis($avancements)
    {
        return [
            'presentiel' => [
                's1' => round($avancements->sum('mhp_s1_drif'), 2),
                's2' => round($avancements->sum('mhp_s2_drif'), 2),
                'total' => round($avancements->sum('mhp_totale_drif'), 2),
                'affectee' => round($avancements->sum('mh_affectee_presentiel'), 2),
                'realisee' => round($avancements->sum('mh_realisee_presentiel'), 2),
            ],
            'synchrone' => [
                's1' => round($avancements->sum('mhsyn_s1_drif'), 2),
                's2' => round($avancements->sum('mhsyn_s2_drif'), 2),
                'total' => round($avancements->sum('mhsyn_totale_drif'), 2),
                'affectee' => round($avancements->sum('mh_affectee_sync'), 2),
                'realisee' => round($avancements->sum('mh_realisee_sync'), 2),
            ],
            'asynchrone' => [
                's1' => round($avancements->sum('mhasyn_s1_drif'), 2),
                's2' => round($avancements->sum('mhasyn_s2_drif'), 2),
                'total' => round($avancements->sum('mhasyn_totale_drif'), 2),
            ],
            'global' => [
                's1' => round($avancements->sum('mh_totale_s1_drif'), 2),
                's2' => round($avancements->sum('mh_totale_s2_drif'), 2),
                'total' => round($avancements->sum('mh_totale_drif'), 2),
                'affectee' => round($avancements->sum('mh_affectee_globale'), 2),
                'realisee' => round($avancements->sum('mh_realisee_globale'), 2),
            ],
        ];
    }

    private function getTauxChartData($avancements)
    {
        $heuresRequises = $avancements->sum('mh_totale_drif') ?: 0;
        $heuresAffectees = $avancements->sum('mh_affectee_globale') ?: 0;
        $heuresRealisees = $avancements->sum('mh_realisee_globale') ?: 0;
        
        $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;
        $tauxAffectation = $heuresRequises > 0 ? ($heuresAffectees / $heuresRequises) * 100 : 0;
        $moyenneAbsence = $avancements->avg('moy_absence') ?? 0;
        
        return [
            'taux_realisation' => round($tauxRealisation, 2),
            'taux_affectation' => round($tauxAffectation, 2),
            'moyenne_absence' => round($moyenneAbsence, 2),
        ];
    }

    private function getDetailedGroupeModuleData($avancements)
    {
        return $avancements->map(function($avancement) {
            $groupe = $avancement->groupe;
            $formation = $groupe ? $groupe->formation : null;
            $filiere = $formation ? $formation->filiere : null;
            $niveau = $formation ? $formation->niveau : null;
            
            return [
                'groupe' => $groupe ? $groupe->nom_groupe : 'N/A',
                'groupe_info' => $groupe,
                'module' => $avancement->module ? $avancement->module->code_module : 'N/A',
                'module_nom' => $avancement->module ? $avancement->module->nom_module : 'N/A',
                'formation' => $filiere ? $filiere->nom_filiere : 'N/A',
                'niveau' => $niveau ? $niveau->niveau : 'N/A',
                'annee' => $formation ? $formation->annee : 'N/A',
                'formateur_presentiel' => $avancement->formateurPresentiel ? $avancement->formateurPresentiel->nom_formateur : 'N/A',
                'formateur_synchrone' => $avancement->formateurSynchrone ? $avancement->formateurSynchrone->nom_formateur : 'N/A',
                'heures_affectees' => round($avancement->mh_affectee_globale, 2),
                'heures_realisees' => round($avancement->mh_realisee_globale, 2),
                'taux_realisation' => round($avancement->taux_realisation_global, 2),
                'taux_realisation_presentiel' => round($avancement->taux_realisation_presentiel, 2),
                'taux_realisation_synchrone' => round($avancement->taux_realisation_syn, 2),
                'moyenne_absence' => round($avancement->moy_absence, 2),
                'nb_cc' => $avancement->nb_cc,
                'efm_valide' => $avancement->validation_efm ? 'Oui' : 'Non',
                'date_maj' => $avancement->date_maj ? $avancement->date_maj->format('d/m/Y') : 'N/A',
            ];
        })->sortByDesc('taux_realisation')->values();
    }

    private function getTopModules($avancements)
    {
        return $avancements->groupBy('module_id')
            ->map(function($moduleAvancements) {
                $module = $moduleAvancements->first()->module;
                $heuresRequises = $moduleAvancements->sum('mh_totale_drif') ?: 0;
                $heuresRealisees = $moduleAvancements->sum('mh_realisee_globale') ?: 0;
                $tauxMoyen = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;
                return [
                    'code_module' => $module ? $module->code_module : 'N/A',
                    'nom_module' => $module ? $module->nom_module : 'N/A',
                    'taux_moyen' => round($tauxMoyen, 2),
                    'heures_realisees' => round($heuresRealisees, 2),
                    'heures_affectees' => round($moduleAvancements->sum('mh_affectee_globale'), 2),
                    'nb_groupes' => $moduleAvancements->unique('groupe_id')->count(),
                ];
            })
            ->sortByDesc('taux_moyen')
            ->take(10)
            ->values();
    }

    private function getChartData($avancements)
    {
        // Évolution mensuelle des heures réalisées
        $evolutionMensuelle = $avancements->groupBy(function($avancement) {
            return $avancement->date_maj ? $avancement->date_maj->format('Y-m') : 'N/A';
        })->map(function($group) {
            return [
                'heures_realisees' => round($group->sum('mh_realisee_globale'), 2),
                'heures_affectees' => round($group->sum('mh_affectee_globale'), 2),
            ];
        });

        // Répartition par mode de formation
        $repartitionMode = [
            'Présentiel' => round($avancements->sum('mh_realisee_presentiel'), 2),
            'Synchrone' => round($avancements->sum('mh_realisee_sync'), 2),
        ];

        // Taux de réalisation par filière
        $tauxParFiliere = $avancements->groupBy(function($avancement) {
            $groupe = $avancement->groupe;
            if ($groupe && $groupe->formation && $groupe->formation->filiere) {
                return $groupe->formation->filiere->nom_filiere;
            }
            return 'N/A';
        })->map(function($group) {
            $heuresRequises = $group->sum('mh_totale_drif') ?: 0;
            $heuresRealisees = $group->sum('mh_realisee_globale') ?: 0;
            $taux = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;
            return round($taux, 2);
        })->filter(function($value, $key) {
            return $key !== 'N/A';
        });

        return [
            'evolution_mensuelle' => $evolutionMensuelle,
            'repartition_mode' => $repartitionMode,
            'taux_par_filiere' => $tauxParFiliere,
        ];
    }

    private function getFormateurStats($avancements)
    {
        $formateursPresentiel = $avancements->where('mle_presentiel', '!=', null)
            ->groupBy('mle_presentiel')
            ->map(function($group) {
                $formateur = $group->first()->formateurPresentiel;
                $heuresRequises = $group->sum('mhp_totale_drif') ?: 0;
                $heuresRealisees = $group->sum('mh_realisee_presentiel') ?: 0;
                $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;
                return [
                    'mle' => $group->first()->mle_presentiel,
                    'nom' => $formateur ? $formateur->nom_formateur : 'N/A',
                    'heures_realisees' => round($heuresRealisees, 2),
                    'heures_affectees' => round($group->sum('mh_affectee_presentiel'), 2),
                    'taux_realisation' => round($tauxRealisation, 2),
                    'nb_modules' => $group->unique('module_id')->count(),
                    'nb_groupes' => $group->unique('groupe_id')->count(),
                ];
            });

        $formateursSynchrone = $avancements->where('mle_syn', '!=', null)
            ->groupBy('mle_syn')
            ->map(function($group) {
                $formateur = $group->first()->formateurSynchrone;
                $heuresRequises = $group->sum('mhsyn_totale_drif') ?: 0;
                $heuresRealisees = $group->sum('mh_realisee_sync') ?: 0;
                $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;
                return [
                    'mle' => $group->first()->mle_syn,
                    'nom' => $formateur ? $formateur->nom_formateur : 'N/A',
                    'heures_realisees' => round($heuresRealisees, 2),
                    'heures_affectees' => round($group->sum('mh_affectee_sync'), 2),
                    'taux_realisation' => round($tauxRealisation, 2),
                    'nb_modules' => $group->unique('module_id')->count(),
                    'nb_groupes' => $group->unique('groupe_id')->count(),
                ];
            });

        return [
            'presentiel' => $formateursPresentiel->sortByDesc('taux_realisation')->values(),
            'synchrone' => $formateursSynchrone->sortByDesc('taux_realisation')->values(),
        ];
    }

    private function getFilterOptions($etablissement, $user)
    {
        $formations = Formation::forUser($user)->get();
        
        $groupes = Groupe::forUser($user)->whereIn('formation_id', $formations->pluck('id'))->get();
        
        $modules = Module::forUser($user)->whereIn('id', 
            Avancement::forUser($user)->whereIn('groupe_id', $groupes->pluck('id'))->pluck('module_id')
        )->get();
        
        $formateurs = Formateur::forUser($user)->whereIn('mle', 
            Avancement::forUser($user)->whereIn('groupe_id', $groupes->pluck('id'))
                ->where(function($query) {
                    $query->whereNotNull('mle_presentiel')
                          ->orWhereNotNull('mle_syn');
                })
                ->get()
                ->flatMap(function($avancement) {
                    return [$avancement->mle_presentiel, $avancement->mle_syn];
                })
                ->filter()
                ->unique()
        )->get();

        $niveaux = Niveau::forUser($user)->whereIn('id', $formations->pluck('niveau_id'))->get();
        $filieres = Filiere::forUser($user)->whereIn('id', $formations->pluck('filiere_id'))->get();
        $annees = $formations->pluck('annee')->unique()->sort()->values();

        return [
            'groupes' => $groupes,
            'modules' => $modules,
            'formateurs' => $formateurs,
            'niveaux' => $niveaux,
            'filieres' => $filieres,
            'annees' => $annees,
        ];
    }

    public function getFilteredOptions(Request $request)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        $filters = [
            'groupe' => $request->input('groupe'),
            'module' => $request->input('module'),
            'formateur' => $request->input('formateur'),
            'niveau' => $request->input('niveau'),
            'filiere' => $request->input('filiere'),
            'annee' => $request->input('annee'),
        ];

        $filterOptions = $this->getFilterOptions($etablissement, $user);

        return response()->json($filterOptions);
    }

    public function importForm()
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Aucun établissement associé à votre compte.');
        }
        
        return view('administrationetablissement.import', compact('user', 'etablissement'));
    }

    public function importExcel(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);
        
        $request->validate([
            'excel_file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:10240'
            ]
        ], [
            'excel_file.required' => 'Veuillez sélectionner un fichier Excel.',
            'excel_file.mimes' => 'Le fichier doit être de type Excel (.xlsx, .xls) ou CSV.',
            'excel_file.max' => 'Le fichier ne doit pas dépasser 10MB.'
        ]);

        try {
            $user = Auth::user();
            $etablissement = $user->etablissement;
            
            if (!$etablissement) {
                return redirect()->back()
                    ->with('error', 'Aucun établissement associé à votre compte.');
            }

            DB::beginTransaction();
            
            $import = new DataImport();
            Excel::import($import, $request->file('excel_file'));

            $imported = $import->getImported();
            $updated = $import->getUpdated();
            $skipped = $import->getSkipped();
            $errors = $import->getErrors();

            DB::commit();

            $successMessages = [];
            if ($imported > 0) {
                $successMessages[] = "{$imported} nouvel(nouveaux) enregistrement(s) créé(s).";
            }
            if ($updated > 0) {
                $successMessages[] = "{$updated} enregistrement(s) mis à jour.";
            }
            if ($skipped > 0) {
                $successMessages[] = "{$skipped} ligne(s) ignorée(s) (ne concernent pas votre établissement).";
            }

            if (count($errors) > 0) {
                return redirect()->back()
                    ->with('warning', implode(' ', $successMessages))
                    ->withErrors(['import_errors' => $errors]);
            } else {
                $message = !empty($successMessages) ? implode(' ', $successMessages) : 'Importation terminée.';
                return redirect()->route('administration.etablissement.dashboard')
                    ->with('success', $message);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'importation Excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }
}