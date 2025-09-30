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

        // Calculs des statistiques principales
        $stats = $this->calculateStats($avancements);
        
        // Analyse des heures par type
        $heuresAnalysis = $this->calculateHeuresAnalysis($avancements);
        
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

        return view('administrationetablissement.dashboard', compact(
            'etablissement',
            'stats',
            'heuresAnalysis',
            'detailedData',
            'topModules',
            'chartData',
            'formateurStats',
            'filterOptions',
            'filters'
        ));
    }

    private function calculateStats($avancements)
    {
        $totalModules = $avancements->unique('code_module')->count();
        $totalGroupes = $avancements->unique('groupe')->count();
        
        $tauxRealisationGlobal = $avancements->avg('taux_realisation_global') ?? 0;
        $tauxRealisationPresentiel = $avancements->avg('taux_realisation_presentiel') ?? 0;
        $tauxRealisationSynchrone = $avancements->avg('taux_realisation_syn') ?? 0;
        
        $heuresAffecteesTotal = $avancements->sum('mh_affectee_globale');
        $heuresRealiseesTotal = $avancements->sum('mh_realisee_globale');
        
        $tauxAffectation = $heuresAffecteesTotal > 0 
            ? ($heuresRealiseesTotal / $heuresAffecteesTotal) * 100 
            : 0;
        
        $moyenneAbsence = $avancements->avg('moy_absence') ?? 0;
        $totalCC = $avancements->sum('nb_cc');
        $totalEFM = $avancements->where('validation_efm', 1)->count();

        return [
            'total_modules' => $totalModules,
            'total_groupes' => $totalGroupes,
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

    private function getDetailedGroupeModuleData($avancements)
    {
        return $avancements->map(function($avancement) {
            return [
                'groupe' => $avancement->groupe,
                'groupe_info' => $avancement->groupe()->first(),
                'module' => $avancement->code_module,
                'module_nom' => $avancement->module->nom_module ?? 'N/A',
                'formation' => $avancement->groupe->formation->filiere->nom_filiere ?? 'N/A',
                'niveau' => $avancement->groupe->formation->niveau ?? 'N/A',
                'formateur_presentiel' => $avancement->formateurPresentiel->nom_formateur ?? 'N/A',
                'formateur_synchrone' => $avancement->formateurSynchrone->nom_formateur ?? 'N/A',
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
            return $avancement->groupe->formation->filiere->nom_filiere ?? 'N/A';
        })->map(function($group) {
            return round($group->avg('taux_realisation_global'), 2);
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
    // Augmenter les limites de mémoire et de temps d'exécution
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

        // Utiliser une transaction pour la sécurité
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