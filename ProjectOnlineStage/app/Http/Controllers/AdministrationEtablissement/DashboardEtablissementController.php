<?php
namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DataImport;

class DashboardEtablissementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement') {
            return redirect()->route('welcome')
                ->with('error', 'Accès refusé. Vous n\'êtes pas directeur d\'établissement.');
        }
        
        $etablissement = $user->etablissement;
        if (!$etablissement) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun établissement associé à votre compte. Veuillez contacter l\'administrateur.');
        }

        $filters = [
            'formateur' => $request->input('formateur'),
            'module' => $request->input('module'),
            'groupe' => $request->input('groupe'),
            'secteur' => $request->input('secteur'),
            'filiere' => $request->input('filiere'),
            'niveau' => $request->input('niveau'),
        ];

        $stats = $this->getStats($etablissement);
        $chartData = $this->getChartData($etablissement, $filters);
        $heuresData = $this->getHeuresData($etablissement, $filters);
        $tableauDetaille = $this->getTableauDetaille($etablissement, $filters);
        $filterOptions = $this->getFilterOptions($etablissement);
        $nonAssigned = $this->getNonAssignedEntities($etablissement, $filters);
        
        return view('administrationetablissement.dashboard', compact(
            'user', 
            'etablissement', 
            'stats', 
            'chartData', 
            'heuresData',
            'tableauDetaille',
            'filterOptions',
            'filters',
            'nonAssigned'
        ));
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

            $import = new DataImport();
            Excel::import($import, $request->file('excel_file'));

            $imported = $import->getImported();
            $skipped = $import->getSkipped();
            $errors = $import->getErrors();

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
            Log::error('Erreur lors de l\'importation Excel: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }

    private function getStats($etablissement)
    {
        try {
            $formations = $etablissement->formations()->count();
            $groupes = $etablissement->groupes()->count();
            $apprenants = $etablissement->groupes()->sum('effectif_groupe');
            
            $formateurs = \App\Models\Formateur::whereIn('mle', function($query) use ($etablissement) {
                $query->select('mle_presentiel')
                      ->from('avancements')
                      ->whereIn('groupe', function($subQuery) use ($etablissement) {
                          $subQuery->select('groupe')
                                  ->from('groupes')
                                  ->whereIn('id_formation', function($formQuery) use ($etablissement) {
                                      $formQuery->select('id')
                                              ->from('formations')
                                              ->where('code_efp', $etablissement->code_efp);
                                  });
                      })
                      ->whereNotNull('mle_presentiel');
            })->orWhereIn('mle', function($query) use ($etablissement) {
                $query->select('mle_syn')
                      ->from('avancements')
                      ->whereIn('groupe', function($subQuery) use ($etablissement) {
                          $subQuery->select('groupe')
                                  ->from('groupes')
                                  ->whereIn('id_formation', function($formQuery) use ($etablissement) {
                                      $formQuery->select('id')
                                              ->from('formations')
                                              ->where('code_efp', $etablissement->code_efp);
                                  });
                      })
                      ->whereNotNull('mle_syn');
            })->count();

            return [
                'formations' => $formations,
                'apprenants' => $apprenants,
                'groupes' => $groupes,
                'formateurs' => $formateurs
            ];
            
        } catch (\Exception $e) {
            Log::error('Erreur lors du calcul des statistiques: ' . $e->getMessage());
            return [
                'formations' => 0,
                'apprenants' => 0,
                'groupes' => 0,
                'formateurs' => 0
            ];
        }
    }

    private function getChartData($etablissement, $filters)
    {
        try {
            $query = DB::table('avancements')
                ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
                ->join('formations', 'groupes.id_formation', '=', 'formations.id')
                ->join('modules', 'avancements.code_module', '=', 'modules.code_module')
                ->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere')
                ->where('formations.code_efp', $etablissement->code_efp)
                ->whereNull('avancements.deleted_at');

            // Application des filtres
            if (!empty($filters['formateur'])) {
                $query->where(function($q) use ($filters) {
                    $q->where('avancements.mle_presentiel', $filters['formateur'])
                      ->orWhere('avancements.mle_syn', $filters['formateur']);
                });
            }
            if (!empty($filters['module'])) {
                $query->where('avancements.code_module', $filters['module']);
            }
            if (!empty($filters['groupe'])) {
                $query->where('avancements.groupe', $filters['groupe']);
            }
            if (!empty($filters['secteur'])) {
                $query->where('filieres.nom_secteur', $filters['secteur']);
            }
            if (!empty($filters['filiere'])) {
                $query->where('formations.code_filiere', $filters['filiere']);
            }
            if (!empty($filters['niveau'])) {
                $query->where('formations.niveau', $filters['niveau']);
            }

            // Taux global
            $tauxGlobal = $query->selectRaw('
                AVG(CASE 
                    WHEN avancements.mh_totale_drif > 0 
                    THEN (avancements.mh_realisee_globale / avancements.mh_totale_drif * 100) 
                    ELSE 0 
                END) as taux_moyen
            ')->first();

            // Top modules
            $topModulesQuery = DB::table('avancements')
                ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
                ->join('formations', 'groupes.id_formation', '=', 'formations.id')
                ->join('modules', 'avancements.code_module', '=', 'modules.code_module')
                ->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere')
                ->where('formations.code_efp', $etablissement->code_efp)
                ->whereNull('avancements.deleted_at');

            // Application des mêmes filtres
            if (!empty($filters['formateur'])) {
                $topModulesQuery->where(function($q) use ($filters) {
                    $q->where('avancements.mle_presentiel', $filters['formateur'])
                      ->orWhere('avancements.mle_syn', $filters['formateur']);
                });
            }
            if (!empty($filters['groupe'])) {
                $topModulesQuery->where('avancements.groupe', $filters['groupe']);
            }
            if (!empty($filters['secteur'])) {
                $topModulesQuery->where('filieres.nom_secteur', $filters['secteur']);
            }
            if (!empty($filters['filiere'])) {
                $topModulesQuery->where('formations.code_filiere', $filters['filiere']);
            }
            if (!empty($filters['niveau'])) {
                $topModulesQuery->where('formations.niveau', $filters['niveau']);
            }

            $topModules = $topModulesQuery
                ->select('modules.nom_module', 'modules.code_module')
                ->selectRaw('AVG(CASE 
                    WHEN avancements.mh_totale_drif > 0 
                    THEN (avancements.mh_realisee_globale / avancements.mh_totale_drif * 100) 
                    ELSE 0 
                END) as taux_moyen')
                ->groupBy('modules.nom_module', 'modules.code_module')
                ->having('taux_moyen', '>', 0)
                ->orderByDesc('taux_moyen')
                ->limit(10)
                ->get();

            return [
                'taux_global' => round($tauxGlobal->taux_moyen ?? 0, 2),
                'top_modules' => $topModules,
            ];

        } catch (\Exception $e) {
            Log::error('Erreur getChartData: ' . $e->getMessage());
            return [
                'taux_global' => 0,
                'top_modules' => collect([]),
            ];
        }
    }

    private function getHeuresData($etablissement, $filters)
    {
        try {
            $query = DB::table('avancements')
                ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
                ->join('formations', 'groupes.id_formation', '=', 'formations.id')
                ->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere')
                ->where('formations.code_efp', $etablissement->code_efp)
                ->whereNull('avancements.deleted_at');

            // Application des filtres
            if (!empty($filters['formateur'])) {
                $query->where(function($q) use ($filters) {
                    $q->where('avancements.mle_presentiel', $filters['formateur'])
                      ->orWhere('avancements.mle_syn', $filters['formateur']);
                });
            }
            if (!empty($filters['module'])) {
                $query->where('avancements.code_module', $filters['module']);
            }
            if (!empty($filters['groupe'])) {
                $query->where('avancements.groupe', $filters['groupe']);
            }
            if (!empty($filters['secteur'])) {
                $query->where('filieres.nom_secteur', $filters['secteur']);
            }
            if (!empty($filters['filiere'])) {
                $query->where('formations.code_filiere', $filters['filiere']);
            }
            if (!empty($filters['niveau'])) {
                $query->where('formations.niveau', $filters['niveau']);
            }

            $heures = $query->selectRaw('
                COALESCE(SUM(mh_totale_drif), 0) as heures_requises,
                COALESCE(SUM(mh_affectee_globale), 0) as heures_affectees,
                COALESCE(SUM(mh_realisee_globale), 0) as heures_realisees
            ')->first();

            $heuresRequises = $heures->heures_requises ?? 0;
            $heuresAffectees = $heures->heures_affectees ?? 0;
            $heuresRealisees = $heures->heures_realisees ?? 0;

            return [
                'heures_requises' => round($heuresRequises, 2),
                'heures_affectees' => round($heuresAffectees, 2),
                'heures_realisees' => round($heuresRealisees, 2),
                'difference_affectees' => round($heuresRequises - $heuresAffectees, 2),
                'difference_realisees' => round($heuresRequises - $heuresRealisees, 2),
                'taux_affectation' => $heuresRequises > 0 ? round(($heuresAffectees / $heuresRequises) * 100, 2) : 0,
                'taux_realisation' => $heuresRequises > 0 ? round(($heuresRealisees / $heuresRequises) * 100, 2) : 0,
            ];

        } catch (\Exception $e) {
            Log::error('Erreur getHeuresData: ' . $e->getMessage());
            return [
                'heures_requises' => 0,
                'heures_affectees' => 0,
                'heures_realisees' => 0,
                'difference_affectees' => 0,
                'difference_realisees' => 0,
                'taux_affectation' => 0,
                'taux_realisation' => 0,
            ];
        }
    }

    private function getTableauDetaille($etablissement, $filters)
    {
        try {
            $query = DB::table('avancements')
                ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
                ->join('formations', 'groupes.id_formation', '=', 'formations.id')
                ->join('modules', 'avancements.code_module', '=', 'modules.code_module')
                ->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere')
                ->leftJoin('formateurs as f1', 'avancements.mle_presentiel', '=', 'f1.mle')
                ->leftJoin('formateurs as f2', 'avancements.mle_syn', '=', 'f2.mle')
                ->where('formations.code_efp', $etablissement->code_efp)
                ->whereNull('avancements.deleted_at');

            // Application des filtres
            if (!empty($filters['formateur'])) {
                $query->where(function($q) use ($filters) {
                    $q->where('avancements.mle_presentiel', $filters['formateur'])
                      ->orWhere('avancements.mle_syn', $filters['formateur']);
                });
            }
            if (!empty($filters['module'])) {
                $query->where('avancements.code_module', $filters['module']);
            }
            if (!empty($filters['groupe'])) {
                $query->where('avancements.groupe', $filters['groupe']);
            }
            if (!empty($filters['secteur'])) {
                $query->where('filieres.nom_secteur', $filters['secteur']);
            }
            if (!empty($filters['filiere'])) {
                $query->where('formations.code_filiere', $filters['filiere']);
            }
            if (!empty($filters['niveau'])) {
                $query->where('formations.niveau', $filters['niveau']);
            }

            $donnees = $query->select(
                'avancements.groupe',
                'modules.nom_module',
                'modules.code_module',
                'filieres.nom_filiere',
                'filieres.nom_secteur',
                'formations.niveau',
                'f1.nom_formateur as formateur_presentiel',
                'f2.nom_formateur as formateur_synchrone',
                'avancements.mh_totale_drif',
                'avancements.mh_affectee_globale',
                'avancements.mh_realisee_globale'
            )
            ->selectRaw('CASE 
                WHEN avancements.mh_totale_drif > 0 
                THEN (avancements.mh_realisee_globale / avancements.mh_totale_drif * 100) 
                ELSE 0 
            END as taux_realisation_global')
            ->orderBy('filieres.nom_secteur')
            ->orderBy('avancements.groupe')
            ->orderBy('modules.nom_module')
            ->get();

            return $donnees;

        } catch (\Exception $e) {
            Log::error('Erreur getTableauDetaille: ' . $e->getMessage());
            return collect([]);
        }
    }

    private function getFilterOptions($etablissement)
    {
        try {
            $formateurs = DB::table('formateurs')
                ->whereIn('mle', function($query) use ($etablissement) {
                    $query->select('mle_presentiel')
                          ->from('avancements')
                          ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
                          ->join('formations', 'groupes.id_formation', '=', 'formations.id')
                          ->where('formations.code_efp', $etablissement->code_efp)
                          ->whereNotNull('mle_presentiel')
                          ->whereNull('avancements.deleted_at');
                })
                ->orWhereIn('mle', function($query) use ($etablissement) {
                    $query->select('mle_syn')
                          ->from('avancements')
                          ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
                          ->join('formations', 'groupes.id_formation', '=', 'formations.id')
                          ->where('formations.code_efp', $etablissement->code_efp)
                          ->whereNotNull('mle_syn')
                          ->whereNull('avancements.deleted_at');
                })
                ->distinct()
                ->orderBy('nom_formateur')
                ->get();

            return [
                'formateurs' => $formateurs,
            ];

        } catch (\Exception $e) {
            Log::error('Erreur getFilterOptions: ' . $e->getMessage());
            return [
                'formateurs' => collect([]),
            ];
        }
    }

    private function getNonAssignedEntities($etablissement, $filters)
    {
        try {
            $query = DB::table('avancements')
                ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
                ->join('formations', 'groupes.id_formation', '=', 'formations.id')
                ->join('modules', 'avancements.code_module', '=', 'modules.code_module')
                ->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere')
                ->where('formations.code_efp', $etablissement->code_efp)
                ->whereNull('avancements.deleted_at');

            // Application des filtres (sauf formateur car conflictuel pour non-affectés)
            if (!empty($filters['module'])) {
                $query->where('avancements.code_module', $filters['module']);
            }
            if (!empty($filters['groupe'])) {
                $query->where('avancements.groupe', $filters['groupe']);
            }
            if (!empty($filters['secteur'])) {
                $query->where('filieres.nom_secteur', $filters['secteur']);
            }
            if (!empty($filters['filiere'])) {
                $query->where('formations.code_filiere', $filters['filiere']);
            }
            if (!empty($filters['niveau'])) {
                $query->where('formations.niveau', $filters['niveau']);
            }

            $non_assigned_query = clone $query;
            $non_assigned_query->whereNull('avancements.mle_presentiel')
                               ->whereNull('avancements.mle_syn');

            $non_assigned_modules = $non_assigned_query->select('modules.code_module', 'modules.nom_module')
                ->distinct()
                ->orderBy('modules.nom_module')
                ->get();

            $non_assigned_groups = $non_assigned_query->select('avancements.groupe')
                ->distinct()
                ->orderBy('avancements.groupe')
                ->get();

            // Pour les formateurs non affectés
            $assigned_mles_query = DB::table('avancements')
                ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
                ->join('formations', 'groupes.id_formation', '=', 'formations.id')
                ->where('formations.code_efp', $etablissement->code_efp)
                ->whereNull('avancements.deleted_at');

            // Application des filtres (sauf formateur)
            if (!empty($filters['module'])) {
                $assigned_mles_query->where('avancements.code_module', $filters['module']);
            }
            if (!empty($filters['groupe'])) {
                $assigned_mles_query->where('avancements.groupe', $filters['groupe']);
            }
            if (!empty($filters['secteur'])) {
                $assigned_mles_query->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere')
                    ->where('filieres.nom_secteur', $filters['secteur']);
            }
            if (!empty($filters['filiere'])) {
                $assigned_mles_query->where('formations.code_filiere', $filters['filiere']);
            }
            if (!empty($filters['niveau'])) {
                $assigned_mles_query->where('formations.niveau', $filters['niveau']);
            }

            $assigned_mles = $assigned_mles_query->whereNotNull('avancements.mle_presentiel')
                ->pluck('avancements.mle_presentiel')
                ->merge(
                    $assigned_mles_query->whereNotNull('avancements.mle_syn')
                        ->pluck('avancements.mle_syn')
                )
                ->unique()
                ->filter();

            $non_assigned_formateurs = \App\Models\Formateur::whereNotIn('mle', $assigned_mles)
                ->orderBy('nom_formateur')
                ->get();

            return [
                'modules' => $non_assigned_modules,
                'groupes' => $non_assigned_groups,
                'formateurs' => $non_assigned_formateurs
            ];

        } catch (\Exception $e) {
            Log::error('Erreur getNonAssignedEntities: ' . $e->getMessage());
            return [
                'modules' => collect([]),
                'groupes' => collect([]),
                'formateurs' => collect([])
            ];
        }
    }

    public function getFilteredOptions(Request $request)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if (!$etablissement) {
            return response()->json(['error' => 'Établissement non trouvé'], 404);
        }

        $formateur = $request->input('formateur');
        $module = $request->input('module');
        $groupe = $request->input('groupe');
        $secteur = $request->input('secteur');
        $filiere = $request->input('filiere');

        try {
            $result = [];

            // Modules
            if ($request->has('get_modules')) {
                $query = DB::table('modules')
                    ->join('avancements', 'modules.code_module', '=', 'avancements.code_module')
                    ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
                    ->join('formations', 'groupes.id_formation', '=', 'formations.id')
                    ->where('formations.code_efp', $etablissement->code_efp)
                    ->whereNull('avancements.deleted_at');

                if ($formateur) {
                    $query->where(function($q) use ($formateur) {
                        $q->where('avancements.mle_presentiel', $formateur)
                          ->orWhere('avancements.mle_syn', $formateur);
                    });
                }
                if ($groupe) {
                    $query->where('avancements.groupe', $groupe);
                }
                if ($secteur) {
                    $query->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere')
                          ->where('filieres.nom_secteur', $secteur);
                }
                if ($filiere) {
                    $query->where('formations.code_filiere', $filiere);
                }

                $result['modules'] = $query
                    ->select('modules.code_module', 'modules.nom_module')
                    ->distinct()
                    ->orderBy('modules.nom_module')
                    ->get();
            }

            // Groupes
            if ($request->has('get_groupes')) {
                $query = DB::table('groupes')
                    ->join('formations', 'groupes.id_formation', '=', 'formations.id')
                    ->where('formations.code_efp', $etablissement->code_efp);

                if ($formateur || $module) {
                    $query->join('avancements', 'groupes.groupe', '=', 'avancements.groupe')
                          ->whereNull('avancements.deleted_at');

                    if ($formateur) {
                        $query->where(function($q) use ($formateur) {
                            $q->where('avancements.mle_presentiel', $formateur)
                              ->orWhere('avancements.mle_syn', $formateur);
                        });
                    }
                    if ($module) {
                        $query->where('avancements.code_module', $module);
                    }
                }

                if ($secteur || $filiere) {
                    if (!$query->getQuery()->joins || !collect($query->getQuery()->joins)->contains(fn($j) => strpos($j->table, 'filieres') !== false)) {
                        $query->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere');
                    }
                    if ($secteur) {
                        $query->where('filieres.nom_secteur', $secteur);
                    }
                }
                if ($filiere) {
                    $query->where('formations.code_filiere', $filiere);
                }

                $result['groupes'] = $query
                    ->select('groupes.groupe')
                    ->distinct()
                    ->orderBy('groupes.groupe')
                    ->get();
            }

            // Secteurs
            if ($request->has('get_secteurs')) {
                $query = DB::table('secteurs')
                    ->join('filieres', 'secteurs.nom_secteur', '=', 'filieres.nom_secteur')
                    ->join('formations', 'filieres.code_filiere', '=', 'formations.code_filiere')
                    ->where('formations.code_efp', $etablissement->code_efp);

                if ($formateur || $module || $groupe) {
                    $query->join('groupes', 'formations.id', '=', 'groupes.id_formation')
                          ->join('avancements', 'groupes.groupe', '=', 'avancements.groupe')
                          ->whereNull('avancements.deleted_at');

                    if ($formateur) {
                        $query->where(function($q) use ($formateur) {
                            $q->where('avancements.mle_presentiel', $formateur)
                              ->orWhere('avancements.mle_syn', $formateur);
                        });
                    }
                    if ($module) {
                        $query->where('avancements.code_module', $module);
                    }
                    if ($groupe) {
                        $query->where('avancements.groupe', $groupe);
                    }
                }

                $result['secteurs'] = $query
                    ->select('secteurs.nom_secteur')
                    ->distinct()
                    ->orderBy('secteurs.nom_secteur')
                    ->get();
            }

            // Filières
            if ($request->has('get_filieres')) {
                $query = DB::table('filieres')
                    ->join('formations', 'filieres.code_filiere', '=', 'formations.code_filiere')
                    ->where('formations.code_efp', $etablissement->code_efp);

                if ($secteur) {
                    $query->where('filieres.nom_secteur', $secteur);
                }

                if ($formateur || $module || $groupe) {
                    $query->join('groupes', 'formations.id', '=', 'groupes.id_formation')
                          ->join('avancements', 'groupes.groupe', '=', 'avancements.groupe')
                          ->whereNull('avancements.deleted_at');

                    if ($formateur) {
                        $query->where(function($q) use ($formateur) {
                            $q->where('avancements.mle_presentiel', $formateur)
                              ->orWhere('avancements.mle_syn', $formateur);
                        });
                    }
                    if ($module) {
                        $query->where('avancements.code_module', $module);
                    }
                    if ($groupe) {
                        $query->where('avancements.groupe', $groupe);
                    }
                }

                $result['filieres'] = $query
                    ->select('filieres.code_filiere', 'filieres.nom_filiere')
                    ->distinct()
                    ->orderBy('filieres.nom_filiere')
                    ->get();
            }

            // Niveaux
            if ($request->has('get_niveaux')) {
                $query = DB::table('niveaux')
                    ->join('formations', 'niveaux.niveau', '=', 'formations.niveau')
                    ->where('formations.code_efp', $etablissement->code_efp);

                if ($secteur || $filiere) {
                    $query->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere');
                    if ($secteur) {
                        $query->where('filieres.nom_secteur', $secteur);
                    }
                }
                if ($filiere) {
                    $query->where('formations.code_filiere', $filiere);
                }

                if ($formateur || $module || $groupe) {
                    $query->join('groupes', 'formations.id', '=', 'groupes.id_formation')
                          ->join('avancements', 'groupes.groupe', '=', 'avancements.groupe')
                          ->whereNull('avancements.deleted_at');

                    if ($formateur) {
                        $query->where(function($q) use ($formateur) {
                            $q->where('avancements.mle_presentiel', $formateur)
                              ->orWhere('avancements.mle_syn', $formateur);
                        });
                    }
                    if ($module) {
                        $query->where('avancements.code_module', $module);
                    }
                    if ($groupe) {
                        $query->where('avancements.groupe', $groupe);
                    }
                }

                $result['niveaux'] = $query
                    ->select('niveaux.niveau')
                    ->distinct()
                    ->orderBy('niveaux.niveau')
                    ->get();
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Erreur getFilteredOptions: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}