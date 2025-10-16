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
            'annee' => $request->input('annee'),
        ];

        // Récupérer tous les groupes de l'établissement
        $groupesQuery = Groupe::where('code_efp', $etablissement->code_efp);
        
        // Application des filtres sur les groupes
        if ($filters['groupe']) {
            $groupesQuery->where('id', $filters['groupe']);
        }
        if ($filters['filiere']) {
            $groupesQuery->where('filiere_id', $filters['filiere']);
        }
        if ($filters['annee']) {
            $groupesQuery->where('annee_formation', $filters['annee']);
        }
        
        $groupes = $groupesQuery->get();
        $groupeIds = $groupes->pluck('id');

        // Construction de la requête des affectations avec avancements
        $affectationsQuery = Affectation::whereIn('groupe_id', $groupeIds)
            ->with([
                'groupe.filiere.secteur',
                'groupe.formation.niveau',
                'groupe.formation',
                'module',
                'formateurPresentiel',
                'formateurSyn',
                'avancement'
            ]);

        // Application des filtres sur les affectations
        if ($filters['module']) {
            $affectationsQuery->where('module_id', $filters['module']);
        }
        if ($filters['formateur']) {
            $affectationsQuery->where(function($query) use ($filters) {
                $query->where('mle_affecte_presentiel', $filters['formateur'])
                      ->orWhere('mle_affecte_syn', $filters['formateur']);
            });
        }
        if ($filters['niveau']) {
            $affectationsQuery->whereHas('groupe.formation', function($query) use ($filters) {
                $query->where('niveau_id', $filters['niveau']);
            });
        }

        $affectations = $affectationsQuery->get();

        // Statistiques principales
        $statistics = $this->calculateStatistics($affectations, $etablissement, $filters);

        // Analyse des heures par type
        $heuresAnalysis = $this->calculateHeuresAnalysis($affectations);
        
        // Données pour l'analyse des heures
        $heuresData = $this->calculateHeuresData($affectations);
        
        // Graphiques de taux
        $tauxChartData = $this->getTauxChartData($affectations);
        
        // Données détaillées par groupe et module
        $detailedData = $this->getDetailedGroupeModuleData($affectations);
        
        // Top 10 modules avec meilleurs taux
        $topModules = $this->getTopModules($affectations);
        
        // Données pour les graphiques
        $chartData = $this->getChartData($affectations);
        
        // Taux de réalisation par formateur
        $formateurStats = $this->getFormateurStats($affectations);
        
        // Options pour les filtres
        $filterOptions = $this->getFilterOptions($etablissement);

        // ✅ CORRECTION : Modules non affectés (vérification complète)
        $affectationsNonAffectes = $affectations->filter(function ($affectation) {
            // Un module est considéré non affecté si :
            // 1. Aucun formateur présentiel ET synchrone n'est affecté
            // OU
            // 2. Les heures affectées sont à 0 alors que les heures requises sont > 0
            $aucunFormateur = is_null($affectation->mle_affecte_presentiel) && is_null($affectation->mle_affecte_syn);
            $heuresNonAffectees = ($affectation->mh_affectee_globale == 0 || is_null($affectation->mh_affectee_globale)) 
                                  && $affectation->mh_totale_drif > 0;
            
            return $aucunFormateur || $heuresNonAffectees;
        });

        // Par module
        $nonAffectesParModule = $affectationsNonAffectes->groupBy('module_id')->map(function ($group) {
            $firstAffectation = $group->first();
            return [
                'code_module' => $firstAffectation->module ? $firstAffectation->module->code_module : 'N/A',
                'nom_module' => $firstAffectation->module ? $firstAffectation->module->nom_module : 'N/A',
                'groupes' => $group->pluck('groupe.code_groupe')->unique()->implode(', '),
                'masse_horaire' => $group->sum('mh_totale_drif'),
                'formateur' => 'Non affecté',
            ];
        })->values();

        $totalNonAffectesModule = $affectationsNonAffectes->sum('mh_totale_drif');

        // Par filière avec modules
        $nonAffectesParFiliere = $affectationsNonAffectes->groupBy(function ($affectation) {
            return $affectation->groupe && $affectation->groupe->filiere ? $affectation->groupe->filiere->id : null;
        })->map(function ($group) {
            $firstAffectation = $group->first();
            $filiere = ($firstAffectation->groupe && $firstAffectation->groupe->filiere) ? $firstAffectation->groupe->filiere : null;
            
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

        $totalNonAffectesFiliere = $affectationsNonAffectes->sum('mh_totale_drif');

        // Liste des formateurs avec totaux
        $formateursData = $filterOptions['formateurs']->map(function ($formateur) use ($affectations) {
            $affForForm = $affectations->filter(function ($aff) use ($formateur) {
                return $aff->mle_affecte_presentiel == $formateur->mle || $aff->mle_affecte_syn == $formateur->mle;
            });
            $heuresRequises = $affForForm->sum('mh_totale_drif');
            $heuresAffectees = $affForForm->sum('mh_affectee_globale');
            $heuresManquantes = $heuresRequises - $heuresAffectees;
            return [
                'nom_formateur' => $formateur->nom_complet,
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

    private function calculateStatistics($affectations, $etablissement, $filters = [])
    {
        // Récupérer les groupes de l'établissement
        $groupesQuery = Groupe::where('code_efp', $etablissement->code_efp);
        
        if (!empty($filters['annee'])) {
            $groupesQuery->where('annee_formation', $filters['annee']);
        }
        if (!empty($filters['filiere'])) {
            $groupesQuery->where('filiere_id', $filters['filiere']);
        }
        
        $groupes = $groupesQuery->get();
        $totalGroupes = $groupes->count();
        
        // Compter les formations uniques
        $formationsIds = $groupes->pluck('formation_id')->unique();
        $totalFormations = $formationsIds->count();
        
        // Compter les modules uniques
        $modulesIds = $affectations->pluck('module_id')->unique();
        $totalModules = $modulesIds->count();
        
        // Compter les formateurs uniques
        $formateursIds = $affectations->map(function($affectation) {
            return [$affectation->mle_affecte_presentiel, $affectation->mle_affecte_syn];
        })->flatten()->filter()->unique();
        $totalFormateurs = $formateursIds->count();
        
        // Compter les filières uniques
        $filieresIds = $groupes->pluck('filiere_id')->unique();
        $totalFilieres = $filieresIds->count();
        
        // Compter les secteurs via filières
        $secteursIds = Filiere::whereIn('id', $filieresIds)->pluck('secteur_id')->unique();
        $totalSecteurs = $secteursIds->count();
        
        // Heures (basé sur les affectations)
        $heuresRequisesTotal = $affectations->sum('mh_totale_drif') ?: 0;
        
        // ✅ CORRECTION : Calcul correct des heures affectées
        // On compte uniquement les heures où au moins un formateur est affecté
        $heuresAffecteesTotal = $affectations->filter(function($affectation) {
            return !is_null($affectation->mle_affecte_presentiel) || !is_null($affectation->mle_affecte_syn);
        })->sum('mh_affectee_globale') ?: 0;
        
        // Heures réalisées depuis les avancements
        $heuresRealiseesTotal = $affectations->sum(function($affectation) {
            return $affectation->avancement ? $affectation->avancement->mh_realisee_globale : 0;
        });
        
        $tauxRealisationGlobal = $heuresRequisesTotal > 0 ? ($heuresRealiseesTotal / $heuresRequisesTotal) * 100 : 0;
        
        // ✅ CORRECTION : Taux d'affectation basé sur les heures réellement affectées
        $tauxAffectation = $heuresRequisesTotal > 0 ? ($heuresAffecteesTotal / $heuresRequisesTotal) * 100 : 0;
        
        // Taux par mode
        $heuresRequisesPresentiel = $affectations->sum('mhp_totale_drif') ?: 0;
        $heuresRequisesSynchrone = $affectations->sum('mhsyn_totale_drif') ?: 0;
        
        $heuresRealiseesPresentiel = $affectations->sum(function($affectation) {
            return $affectation->avancement ? $affectation->avancement->mh_realisee_presentiel : 0;
        });
        
        $heuresRealiseesSynchrone = $affectations->sum(function($affectation) {
            return $affectation->avancement ? $affectation->avancement->mh_realisee_sync : 0;
        });
        
        $tauxRealisationPresentiel = $heuresRequisesPresentiel > 0 ? ($heuresRealiseesPresentiel / $heuresRequisesPresentiel) * 100 : 0;
        $tauxRealisationSynchrone = $heuresRequisesSynchrone > 0 ? ($heuresRealiseesSynchrone / $heuresRequisesSynchrone) * 100 : 0;
        
        $moyenneAbsence = $affectations->avg(function($affectation) {
            return $affectation->avancement ? $affectation->avancement->moyenne_absence : 0;
        }) ?? 0;
        
        $totalCC = $affectations->sum(function($affectation) {
            return $affectation->avancement ? $affectation->avancement->nb_cc : 0;
        });
        
        $totalEFM = $affectations->filter(function($affectation) {
            return $affectation->avancement && $affectation->avancement->validation_efm == 'oui';
        })->count();

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

    private function calculateHeuresData($affectations)
    {
        $heuresRequises = $affectations->sum('mh_totale_drif') ?: 0;
        
        // ✅ CORRECTION : Heures affectées uniquement si formateur présent
        $heuresAffectees = $affectations->filter(function($affectation) {
            return !is_null($affectation->mle_affecte_presentiel) || !is_null($affectation->mle_affecte_syn);
        })->sum('mh_affectee_globale') ?: 0;
        
        $heuresRealisees = $affectations->sum(function($affectation) {
            return $affectation->avancement ? $affectation->avancement->mh_realisee_globale : 0;
        });

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

    private function calculateHeuresAnalysis($affectations)
    {
        $heuresRealiseesPresentiel = $affectations->sum(function($affectation) {
            return $affectation->avancement ? $affectation->avancement->mh_realisee_presentiel : 0;
        });
        
        $heuresRealiseesSynchrone = $affectations->sum(function($affectation) {
            return $affectation->avancement ? $affectation->avancement->mh_realisee_sync : 0;
        });
        
        $heuresRealiseesGlobale = $affectations->sum(function($affectation) {
            return $affectation->avancement ? $affectation->avancement->mh_realisee_globale : 0;
        });

        // ✅ CORRECTION : Heures affectées avec vérification formateur
        $heuresAffecteesPresentiel = $affectations->filter(function($affectation) {
            return !is_null($affectation->mle_affecte_presentiel);
        })->sum('mh_affectee_presentiel') ?: 0;
        
        $heuresAffecteesSync = $affectations->filter(function($affectation) {
            return !is_null($affectation->mle_affecte_syn);
        })->sum('mh_affectee_sync') ?: 0;
        
        $heuresAffecteesGlobale = $affectations->filter(function($affectation) {
            return !is_null($affectation->mle_affecte_presentiel) || !is_null($affectation->mle_affecte_syn);
        })->sum('mh_affectee_globale') ?: 0;

        return [
            'presentiel' => [
                's1' => round($affectations->sum('mhp_s1_drif'), 2),
                's2' => round($affectations->sum('mhp_s2_drif'), 2),
                'total' => round($affectations->sum('mhp_totale_drif'), 2),
                'affectee' => round($heuresAffecteesPresentiel, 2),
                'realisee' => round($heuresRealiseesPresentiel, 2),
            ],
            'synchrone' => [
                's1' => round($affectations->sum('mhsyn_s1_drif'), 2),
                's2' => round($affectations->sum('mhsyn_s2_drif'), 2),
                'total' => round($affectations->sum('mhsyn_totale_drif'), 2),
                'affectee' => round($heuresAffecteesSync, 2),
                'realisee' => round($heuresRealiseesSynchrone, 2),
            ],
            'asynchrone' => [
                's1' => round($affectations->sum('mhasyn_s1_drif'), 2),
                's2' => round($affectations->sum('mhasyn_s2_drif'), 2),
                'total' => round($affectations->sum('mhasyn_totale_drif'), 2),
            ],
            'global' => [
                's1' => round($affectations->sum('mh_totale_s1_drif'), 2),
                's2' => round($affectations->sum('mh_totale_s2_drif'), 2),
                'total' => round($affectations->sum('mh_totale_drif'), 2),
                'affectee' => round($heuresAffecteesGlobale, 2),
                'realisee' => round($heuresRealiseesGlobale, 2),
            ],
        ];
    }

    private function getTauxChartData($affectations)
    {
        $heuresRequises = $affectations->sum('mh_totale_drif') ?: 0;
        
        // ✅ CORRECTION
        $heuresAffectees = $affectations->filter(function($affectation) {
            return !is_null($affectation->mle_affecte_presentiel) || !is_null($affectation->mle_affecte_syn);
        })->sum('mh_affectee_globale') ?: 0;
        
        $heuresRealisees = $affectations->sum(function($affectation) {
            return $affectation->avancement ? $affectation->avancement->mh_realisee_globale : 0;
        });
        
        $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;
        $tauxAffectation = $heuresRequises > 0 ? ($heuresAffectees / $heuresRequises) * 100 : 0;
        $moyenneAbsence = $affectations->avg(function($affectation) {
            return $affectation->avancement ? $affectation->avancement->moyenne_absence : 0;
        }) ?? 0;
        
        return [
            'taux_realisation' => round($tauxRealisation, 2),
            'taux_affectation' => round($tauxAffectation, 2),
            'moyenne_absence' => round($moyenneAbsence, 2),
        ];
    }

    private function getDetailedGroupeModuleData($affectations)
    {
        return $affectations->map(function($affectation) {
            $groupe = $affectation->groupe;
            $filiere = $groupe ? $groupe->filiere : null;
            $formation = $groupe ? $groupe->formation : null;
            $niveau = $formation ? $formation->niveau : null;
            $avancement = $affectation->avancement;
            
            return [
                'groupe' => $groupe ? $groupe->code_groupe : 'N/A',
                'groupe_info' => $groupe,
                'module' => $affectation->module ? $affectation->module->code_module : 'N/A',
                'module_nom' => $affectation->module ? $affectation->module->nom_module : 'N/A',
                'formation' => $filiere ? $filiere->nom_filiere : 'N/A',
                'niveau' => $niveau ? $niveau->nom : 'N/A',
                'annee' => $groupe ? $groupe->annee_formation : 'N/A',
                'formateur_presentiel' => $affectation->formateurPresentiel ? $affectation->formateurPresentiel->nom_complet : 'N/A',
                'formateur_synchrone' => $affectation->formateurSyn ? $affectation->formateurSyn->nom_complet : 'N/A',
                'heures_affectees' => round($affectation->mh_affectee_globale, 2),
                'heures_realisees' => $avancement ? round($avancement->mh_realisee_globale, 2) : 0,
                'taux_realisation' => $avancement ? round($avancement->taux_realisation_globale, 2) : 0,
                'taux_realisation_presentiel' => $avancement ? round($avancement->taux_realisation_presentiel, 2) : 0,
                'taux_realisation_synchrone' => $avancement ? round($avancement->taux_realisation_syn, 2) : 0,
                'moyenne_absence' => $avancement ? round($avancement->moyenne_absence, 2) : 0,
                'nb_cc' => $avancement ? $avancement->nb_cc : 0,
                'efm_valide' => $avancement && $avancement->validation_efm == 'oui' ? 'Oui' : 'Non',
                'date_maj' => $avancement && $avancement->date_maj ? $avancement->date_maj->format('d/m/Y') : 'N/A',
            ];
        })->sortByDesc('taux_realisation')->values();
    }

    private function getTopModules($affectations)
    {
        return $affectations->groupBy('module_id')
            ->map(function($moduleAffectations) {
                $module = $moduleAffectations->first()->module;
                $heuresRequises = $moduleAffectations->sum('mh_totale_drif') ?: 0;
                $heuresRealisees = $moduleAffectations->sum(function($affectation) {
                    return $affectation->avancement ? $affectation->avancement->mh_realisee_globale : 0;
                });
                $tauxMoyen = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;
                return [
                    'code_module' => $module ? $module->code_module : 'N/A',
                    'nom_module' => $module ? $module->nom_module : 'N/A',
                    'taux_moyen' => round($tauxMoyen, 2),
                    'heures_realisees' => round($heuresRealisees, 2),
                    'heures_affectees' => round($moduleAffectations->sum('mh_affectee_globale'), 2),
                    'nb_groupes' => $moduleAffectations->unique('groupe_id')->count(),
                ];
            })
            ->sortByDesc('taux_moyen')
            ->take(10)
            ->values();
    }

    private function getChartData($affectations)
    {
        // Évolution mensuelle des heures réalisées
        $evolutionMensuelle = $affectations->groupBy(function($affectation) {
            $avancement = $affectation->avancement;
            return $avancement && $avancement->date_maj ? $avancement->date_maj->format('Y-m') : 'N/A';
        })->map(function($group) {
            return [
                'heures_realisees' => round($group->sum(function($affectation) {
                    return $affectation->avancement ? $affectation->avancement->mh_realisee_globale : 0;
                }), 2),
                'heures_affectees' => round($group->sum('mh_affectee_globale'), 2),
            ];
        });

        // Répartition par mode de formation
        $repartitionMode = [
            'Présentiel' => round($affectations->sum(function($affectation) {
                return $affectation->avancement ? $affectation->avancement->mh_realisee_presentiel : 0;
            }), 2),
            'Synchrone' => round($affectations->sum(function($affectation) {
                return $affectation->avancement ? $affectation->avancement->mh_realisee_sync : 0;
            }), 2),
        ];

        // Taux de réalisation par filière
        $tauxParFiliere = $affectations->groupBy(function($affectation) {
            $groupe = $affectation->groupe;
            if ($groupe && $groupe->filiere) {
                return $groupe->filiere->nom_filiere;
            }
            return 'N/A';
        })->map(function($group) {
            $heuresRequises = $group->sum('mh_totale_drif') ?: 0;
            $heuresRealisees = $group->sum(function($affectation) {
                return $affectation->avancement ? $affectation->avancement->mh_realisee_globale : 0;
            });
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

    private function getFormateurStats($affectations)
    {
        $formateursPresentiel = $affectations->where('mle_affecte_presentiel', '!=', null)
            ->groupBy('mle_affecte_presentiel')
            ->map(function($group) {
                $formateur = $group->first()->formateurPresentiel;
                $heuresRequises = $group->sum('mhp_totale_drif') ?: 0;
                $heuresRealisees = $group->sum(function($affectation) {
                    return $affectation->avancement ? $affectation->avancement->mh_realisee_presentiel : 0;
                });
                $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;
                return [
                    'mle' => $group->first()->mle_affecte_presentiel,
                    'nom' => $formateur ? $formateur->nom_complet : 'N/A',
                    'heures_realisees' => round($heuresRealisees, 2),
                    'heures_affectees' => round($group->sum('mh_affectee_presentiel'), 2),
                    'taux_realisation' => round($tauxRealisation, 2),
                    'nb_modules' => $group->unique('module_id')->count(),
                    'nb_groupes' => $group->unique('groupe_id')->count(),
                ];
            });

        $formateursSynchrone = $affectations->where('mle_affecte_syn', '!=', null)
            ->groupBy('mle_affecte_syn')
            ->map(function($group) {
                $formateur = $group->first()->formateurSyn;
                $heuresRequises = $group->sum('mhsyn_totale_drif') ?: 0;
                $heuresRealisees = $group->sum(function($affectation) {
                    return $affectation->avancement ? $affectation->avancement->mh_realisee_sync : 0;
                });
                $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;
                return [
                    'mle' => $group->first()->mle_affecte_syn,
                    'nom' => $formateur ? $formateur->nom_complet : 'N/A',
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

    private function getFilterOptions($etablissement)
    {
        // Récupérer tous les groupes de l'établissement
        $groupes = Groupe::where('code_efp', $etablissement->code_efp)->get();
        $groupeIds = $groupes->pluck('id');
        
        // Récupérer les modules liés aux affectations de ces groupes
        $modules = Module::whereIn('id', 
            Affectation::whereIn('groupe_id', $groupeIds)->pluck('module_id')
        )->get();
        
        // Récupérer les formateurs liés aux affectations
        $affectations = Affectation::whereIn('groupe_id', $groupeIds)->get();
        $formateursIds = $affectations->map(function($affectation) {
            return [$affectation->mle_affecte_presentiel, $affectation->mle_affecte_syn];
        })->flatten()->filter()->unique();
        
        $formateurs = Formateur::whereIn('mle', $formateursIds)->get();

        // Récupérer les niveaux via les formations des groupes
        $formationsIds = $groupes->pluck('formation_id')->unique();
        $formations = Formation::whereIn('id', $formationsIds)->get();
        $niveaux = Niveau::whereIn('id', $formations->pluck('niveau_id'))->get();
        
        // Récupérer les filières
        $filieresIds = $groupes->pluck('filiere_id')->unique();
        $filieres = Filiere::whereIn('id', $filieresIds)->get();
        
        // Récupérer les années disponibles
        $annees = $groupes->pluck('annee_formation')->unique()->sort()->values();

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