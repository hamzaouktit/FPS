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

        // Construction de la requête de base avec les avancements de l'établissement
        $avancementsQuery = Avancement::whereHas('groupe.formation', function($query) use ($etablissement) {
            $query->where('code_efp', $etablissement->code_efp);
        });

        // Application des filtres
        if ($filters['groupe']) {
            $avancementsQuery->where('groupe', $filters['groupe']);
        }
        if ($filters['module']) {
            $avancementsQuery->where('code_module', $filters['module']);
        }
        if ($filters['formateur']) {
            $avancementsQuery->where(function($query) use ($filters) {
                $query->where('mle_presentiel', $filters['formateur'])
                      ->orWhere('mle_syn', $filters['formateur']);
            });
        }
        if ($filters['niveau']) {
            $avancementsQuery->whereHas('groupe.formation', function($query) use ($filters) {
                $query->where('niveau', $filters['niveau']);
            });
        }
        if ($filters['filiere']) {
            $avancementsQuery->whereHas('groupe.formation', function($query) use ($filters) {
                $query->where('code_filiere', $filters['filiere']);
            });
        }
        if ($filters['annee']) {
            $avancementsQuery->whereHas('groupe.formation', function($query) use ($filters) {
                $query->where('annee', $filters['annee']);
            });
        }

        $avancements = $avancementsQuery->with(['groupe.formation.filiere', 'module', 'formateurPresentiel', 'formateurSynchrone'])->get();

        // Statistiques principales - CORRIGÉ pour utiliser l'établissement connecté
        $stats = $this->calculateStats($avancements, $etablissement, $filters);
        
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
        $filterOptions = $this->getFilterOptions($etablissement);

        // Modules non affectés
        $avancementsNonAffectes = $avancements->filter(function ($avancement) {
            return is_null($avancement->mle_presentiel) && is_null($avancement->mle_syn);
        });

        // Par module
        $nonAffectesParModule = $avancementsNonAffectes->groupBy('code_module')->map(function ($group) {
            return [
                'code_module' => $group->first()->code_module,
                'nom_module' => $group->first()->module->nom_module ?? 'N/A',
                'groupes' => $group->pluck('groupe')->unique()->implode(', '),
                'masse_horaire' => $group->sum('mh_totale_drif'),
                'formateur' => 'Non affecté',
            ];
        })->values();

        $totalNonAffectesModule = $avancementsNonAffectes->sum('mh_totale_drif');

        // Par filière avec modules
        $nonAffectesParFiliere = $avancementsNonAffectes->groupBy(function ($avancement) {
            $groupeObj = $avancement->groupe()->first();
            return $groupeObj && $groupeObj->formation ? $groupeObj->formation->code_filiere : 'N/A';
        })->map(function ($group) {
            $firstAvancement = $group->first();
            $groupeObj = $firstAvancement->groupe()->first();
            $filiere = $groupeObj && $groupeObj->formation ? $groupeObj->formation->filiere : null;
            
            $modules = $group->groupBy('code_module')->map(function ($moduleGroup) {
                return $moduleGroup->first()->module->nom_module ?? 'N/A';
            })->implode(', ');
            
            return [
                'code_filiere' => $filiere ? $filiere->code_filiere : 'N/A',
                'nom_filiere' => $filiere ? $filiere->nom_filiere : 'N/A',
                'modules' => $modules,
                'masse_horaire' => $group->sum('mh_totale_drif'),
            ];
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
            'stats',
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

    private function calculateStats($avancements, $etablissement, $filters = [])
    {
        // Récupérer TOUTES les données de l'établissement (pas seulement les avancements filtrés)
        $baseQuery = Formation::where('code_efp', $etablissement->code_efp);
        
        // Appliquer les mêmes filtres que pour les avancements
        if (!empty($filters['annee'])) {
            $baseQuery->where('annee', $filters['annee']);
        }
        if (!empty($filters['filiere'])) {
            $baseQuery->where('code_filiere', $filters['filiere']);
        }
        if (!empty($filters['niveau'])) {
            $baseQuery->where('niveau', $filters['niveau']);
        }

        $formations = $baseQuery->get();
        
        // Compter les formations
        $totalFormations = $formations->count();
        
        // Compter les groupes
        $groupesQuery = Groupe::whereIn('id_formation', $formations->pluck('id'));
        if (!empty($filters['groupe'])) {
            $groupesQuery->where('groupe', $filters['groupe']);
        }
        $groupes = $groupesQuery->get();
        $totalGroupes = $groupes->count();
        
        // Compter les modules uniques de l'établissement
        $modulesQuery = Module::whereIn('code_module', 
            Avancement::whereIn('groupe', $groupes->pluck('groupe'))->pluck('code_module')
        );
        if (!empty($filters['module'])) {
            $modulesQuery->where('code_module', $filters['module']);
        }
        $modules = $modulesQuery->get();
        $totalModules = $modules->count();
        
        // Compter les formateurs uniques de l'établissement
        $formateursIds = Avancement::whereIn('groupe', $groupes->pluck('groupe'))
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
        
        // Compter les filières uniques de l'établissement
        $filieres = $formations->pluck('code_filiere')->unique();
        $totalFilieres = $filieres->count();
        
        // Compter les secteurs via filières
        $secteurs = Filiere::whereIn('code_filiere', $filieres)->pluck('nom_secteur')->unique();
        $totalSecteurs = $secteurs->count();
        
        // Taux de réalisation (basé sur les avancements filtrés)
        $tauxRealisationGlobal = $avancements->avg('taux_realisation_global') ?? 0;
        $tauxRealisationPresentiel = $avancements->avg('taux_realisation_presentiel') ?? 0;
        $tauxRealisationSynchrone = $avancements->avg('taux_realisation_syn') ?? 0;
        
        // Heures (basé sur les avancements filtrés)
        $heuresRequisesTotal = $avancements->sum('mh_totale_drif');
        $heuresAffecteesTotal = $avancements->sum('mh_affectee_globale');
        $heuresRealiseesTotal = $avancements->sum('mh_realisee_globale');
        
        $tauxAffectation = $heuresRequisesTotal > 0 
            ? ($heuresAffecteesTotal / $heuresRequisesTotal) * 100 
            : 0;
        
        $moyenneAbsence = $avancements->avg('moy_absence') ?? 0;
        $totalCC = $avancements->sum('nb_cc');
        $totalEFM = $avancements->where('validation_efm', 1)->count();

        return [
            'total_formations' => $totalFormations,
            'total_formateurs' => $totalFormateurs,
            'total_filieres' => $totalFilieres,
            'total_groupes' => $totalGroupes,
            'total_modules' => $totalModules,
            'total_secteurs' => $totalSecteurs,
            'taux_realisation_global' => round($tauxRealisationGlobal, 2),
            'taux_realisation_presentiel' => round($tauxRealisationPresentiel, 2),
            'taux_realisation_synchrone' => round($tauxRealisationSynchrone, 2),
            'heures_affectees' => round($heuresAffecteesTotal, 2),
            'heures_realisees' => round($heuresRealiseesTotal, 2),
            'taux_affectation' => round($tauxAffectation, 2),
            'moyenne_absence' => round($moyenneAbsence, 2),
            'total_cc' => $totalCC,
            'total_efm' => $totalEFM,
        ];
    }

    private function calculateHeuresData($avancements)
    {
        $heuresRequises = $avancements->sum('mh_totale_drif');
        $heuresAffectees = $avancements->sum('mh_affectee_globale');
        $heuresRealisees = $avancements->sum('mh_realisee_globale');

        $differenceAffectees = $heuresRequises - $heuresAffectees;
        $differenceRealisees = $heuresAffectees - $heuresRealisees;
        
        $tauxAffectation = $heuresRequises > 0 ? ($heuresAffectees / $heuresRequises) * 100 : 0;
        $tauxRealisation = $heuresAffectees > 0 ? ($heuresRealisees / $heuresAffectees) * 100 : 0;

        return [
            'heures_requises' => $heuresRequises,
            'heures_affectees' => $heuresAffectees,
            'heures_realisees' => $heuresRealisees,
            'difference_affectees' => $differenceAffectees,
            'difference_realisees' => $differenceRealisees,
            'taux_affectation' => round($tauxAffectation, 2),
            'taux_realisation' => round($tauxRealisation, 2),
        ];
    }

    private function calculateHeuresAnalysis($avancements)
    {
        return [
            'presentiel' => [
                's1' => $avancements->sum('mhp_s1_drif'),
                's2' => $avancements->sum('mhp_s2_drif'),
                'total' => $avancements->sum('mhp_totale_drif'),
                'affectee' => $avancements->sum('mh_affectee_presentiel'),
                'realisee' => $avancements->sum('mh_realisee_presentiel'),
            ],
            'synchrone' => [
                's1' => $avancements->sum('mhsyn_s1_drif'),
                's2' => $avancements->sum('mhsyn_s2_drif'),
                'total' => $avancements->sum('mhsyn_totale_drif'),
                'affectee' => $avancements->sum('mh_affectee_sync'),
                'realisee' => $avancements->sum('mh_realisee_sync'),
            ],
            'asynchrone' => [
                's1' => $avancements->sum('mhasyn_s1_drif'),
                's2' => $avancements->sum('mhasyn_s2_drif'),
                'total' => $avancements->sum('mhasyn_totale_drif'),
            ],
            'global' => [
                's1' => $avancements->sum('mh_totale_s1_drif'),
                's2' => $avancements->sum('mh_totale_s2_drif'),
                'total' => $avancements->sum('mh_totale_drif'),
                'affectee' => $avancements->sum('mh_affectee_globale'),
                'realisee' => $avancements->sum('mh_realisee_globale'),
            ],
        ];
    }

    private function getTauxChartData($avancements)
    {
        $heuresRequises = $avancements->sum('mh_totale_drif');
        $heuresAffectees = $avancements->sum('mh_affectee_globale');
        $heuresRealisees = $avancements->sum('mh_realisee_globale');
        
        $tauxRealisation = $heuresAffectees > 0 ? ($heuresRealisees / $heuresAffectees) * 100 : 0;
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
            $groupeObj = $avancement->groupe()->first();
            $formation = $groupeObj ? $groupeObj->formation : null;
            return [
                'groupe' => $avancement->groupe,
                'groupe_info' => $groupeObj,
                'module' => $avancement->code_module,
                'module_nom' => $avancement->module->nom_module ?? 'N/A',
                'formation' => $formation && $formation->filiere ? $formation->filiere->nom_filiere : 'N/A',
                'niveau' => $formation ? $formation->niveau : 'N/A',
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
        return $avancements->groupBy('code_module')
            ->map(function($moduleAvancements, $codeModule) {
                $module = $moduleAvancements->first()->module;
                return [
                    'code_module' => $codeModule,
                    'nom_module' => $module->nom_module ?? 'N/A',
                    'taux_moyen' => round($moduleAvancements->avg('taux_realisation_global'), 2),
                    'heures_realisees' => round($moduleAvancements->sum('mh_realisee_globale'), 2),
                    'heures_affectees' => round($moduleAvancements->sum('mh_affectee_globale'), 2),
                    'nb_groupes' => $moduleAvancements->unique('groupe')->count(),
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
            $groupeObj = $avancement->groupe()->first();
            return $groupeObj && $groupeObj->formation && $groupeObj->formation->filiere ? $groupeObj->formation->filiere->nom_filiere : 'N/A';
        })->map(function($group, $filiere) {
            $heuresAffectees = $group->sum('mh_affectee_globale');
            $heuresRealisees = $group->sum('mh_realisee_globale');
            $taux = $heuresAffectees > 0 ? ($heuresRealisees / $heuresAffectees) * 100 : 0;
            return round($taux, 2);
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
                return [
                    'mle' => $group->first()->mle_presentiel,
                    'nom' => $formateur->nom_formateur ?? 'N/A',
                    'heures_realisees' => round($group->sum('mh_realisee_presentiel'), 2),
                    'heures_affectees' => round($group->sum('mh_affectee_presentiel'), 2),
                    'taux_realisation' => round($group->avg('taux_realisation_presentiel'), 2),
                    'nb_modules' => $group->unique('code_module')->count(),
                    'nb_groupes' => $group->unique('groupe')->count(),
                ];
            });

        $formateursSynchrone = $avancements->where('mle_syn', '!=', null)
            ->groupBy('mle_syn')
            ->map(function($group) {
                $formateur = $group->first()->formateurSynchrone;
                return [
                    'mle' => $group->first()->mle_syn,
                    'nom' => $formateur->nom_formateur ?? 'N/A',
                    'heures_realisees' => round($group->sum('mh_realisee_sync'), 2),
                    'heures_affectees' => round($group->sum('mh_affectee_sync'), 2),
                    'taux_realisation' => round($group->avg('taux_realisation_syn'), 2),
                    'nb_modules' => $group->unique('code_module')->count(),
                    'nb_groupes' => $group->unique('groupe')->count(),
                ];
            });

        return [
            'presentiel' => $formateursPresentiel->sortByDesc('taux_realisation')->values(),
            'synchrone' => $formateursSynchrone->sortByDesc('taux_realisation')->values(),
        ];
    }

    private function getFilterOptions($etablissement)
    {
        $formations = Formation::where('code_efp', $etablissement->code_efp)->get();
        
        $groupes = Groupe::whereIn('id_formation', $formations->pluck('id'))->get();
        
        $modules = Module::whereIn('code_module', 
            Avancement::whereIn('groupe', $groupes->pluck('groupe'))->pluck('code_module')
        )->get();
        
        $formateurs = Formateur::whereIn('mle', 
            Avancement::whereIn('groupe', $groupes->pluck('groupe'))
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

        $niveaux = $formations->pluck('niveau')->unique();
        $filieres = $formations->pluck('code_filiere', 'filiere.nom_filiere')->unique();
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

        $filterOptions = $this->getFilterOptions($etablissement);

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
            $skipped = $import->getSkipped();
            $errors = $import->getErrors();

            DB::commit();

            $successMessages = [];
            if ($imported > 0) {
                $successMessages[] = "{$imported} enregistrement(s) importé(s) avec succès.";
            }
            if ($skipped > 0) {
                $successMessages[] = "{$skipped} ligne(s) ignorée(s) (ne concernent pas votre établissement).";
            }

            if (count($errors) > 0) {
                return redirect()->back()
                    ->with('warning', implode(' ', $successMessages))
                    ->withErrors(['import_errors' => $errors]);
            } else {
                return redirect()->route('administration.etablissement.dashboard')
                    ->with('success', implode(' ', $successMessages));
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