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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class DashboardEtablissementController extends Controller
{
    
public function index(Request $request)
{
    $user = Auth::user();
    $etablissement = $user->etablissement;

    if (!$etablissement) {
        return redirect()->route('welcome')->with('error', 'Aucun établissement associé à cet utilisateur.');
    }
    
    $filters = [
        'groupe' => $request->input('groupe'),
        'module' => $request->input('module'),
        'module_text' => $request->input('module_text'),
        'formateur' => $request->input('formateur'),
        'formateur_text' => $request->input('formateur_text'),
        'niveau' => $request->input('niveau'),
        'filiere' => $request->input('filiere'),
        'annee' => $request->input('annee'),
        'type_formation' => $request->input('type_formation'),
        'mode_formation' => $request->input('mode_formation'),
        'formateur_detail' => $request->input('formateur_detail'),
        'type_formateur' => $request->input('type_formateur'),
        'groupe_detail' => $request->input('groupe_detail'),
        'module_detail' => $request->input('module_detail'),
        'taux_min' => $request->input('taux_min'),
        'taux_max' => $request->input('taux_max'),
    ];

    $groupesQuery = Groupe::where('code_efp', $etablissement->code_efp);
    
    if ($filters['groupe']) {
        $groupesQuery->where('id', $filters['groupe']);
    }
    if ($filters['filiere']) {
        $groupesQuery->where('filiere_id', $filters['filiere']);
    }
    if ($filters['annee']) {
        $groupesQuery->where('annee_formation', $filters['annee']);
    }

    if (!empty($filters['type_formation'])) {
        $groupesQuery->whereHas('formation', function($q) use ($filters) {
            $q->where('type', $filters['type_formation']);
        });
    }
    
    $groupes = $groupesQuery->get();
    $groupeIds = $groupes->pluck('id');

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

    if ($filters['module']) {
        $affectationsQuery->where('module_id', $filters['module']);
    }
    
    // Recherche textuelle sur le module (nom ou code)
    if (!empty($filters['module_text'])) {
        $affectationsQuery->whereHas('module', function($query) use ($filters) {
            $query->where('nom_module', 'like', '%' . $filters['module_text'] . '%')
                  ->orWhere('code_module', 'like', '%' . $filters['module_text'] . '%');
        });
    }
    
    if ($filters['formateur']) {
        $affectationsQuery->where(function($query) use ($filters) {
            $query->where('mle_affecte_presentiel', $filters['formateur'])
                  ->orWhere('mle_affecte_syn', $filters['formateur']);
        });
    }

    // Filtre par mode de formation
    if (!empty($filters['mode_formation'])) {
        if ($filters['mode_formation'] == 'residentiel') {
            $affectationsQuery->where('mh_affectee_presentiel', '>', 0);
        } elseif ($filters['mode_formation'] == 'alterne') {
            $affectationsQuery->where('mh_affectee_sync', '>', 0);
        }
    }

    // Recherche textuelle sur le formateur (nom ou MLE) dans les affectations
    if (!empty($filters['formateur_text'])) {
        $affectationsQuery->where(function($query) use ($filters) {
            $query->whereHas('formateurPresentiel', function($q2) use ($filters) {
                $q2->where('nom_complet', 'like', '%' . $filters['formateur_text'] . '%')
                   ->orWhere('mle', 'like', '%' . $filters['formateur_text'] . '%');
            })->orWhereHas('formateurSyn', function($q2) use ($filters) {
                $q2->where('nom_complet', 'like', '%' . $filters['formateur_text'] . '%')
                   ->orWhere('mle', 'like', '%' . $filters['formateur_text'] . '%');
            });
        });
    }

    if ($filters['niveau']) {
        $affectationsQuery->whereHas('groupe.formation', function($query) use ($filters) {
            $query->where('niveau_id', $filters['niveau']);
        });
    }

    $affectations = $affectationsQuery->get();

    $statistics = $this->calculateStatistics($affectations, $etablissement, $filters);
    $heuresAnalysis = $this->calculateHeuresAnalysis($affectations);
    $heuresData = $this->calculateHeuresData($affectations);
    $tauxChartData = $this->getTauxChartData($affectations);
    $detailedData = $this->getDetailedGroupeModuleData($affectations);
    
    $perPage = 15;
    $currentPage = LengthAwarePaginator::resolveCurrentPage();
    $currentItems = $detailedData->slice(($currentPage - 1) * $perPage, $perPage)->all();
    $paginatedDetailedData = new LengthAwarePaginator($currentItems, $detailedData->count(), $perPage);
    $paginatedDetailedData->setPath($request->url());
    $paginatedDetailedData->appends($request->query());
    
    $topModules = $this->getTopModules($affectations);
    $chartData = $this->getChartData($affectations);
    $formateurStats = $this->getFormateurStats($affectations);
    $filterOptions = $this->getFilterOptions($etablissement);

    $affectationsNonAffectes = $affectations->filter(function ($affectation) {
        $aucunFormateur = is_null($affectation->mle_affecte_presentiel) && is_null($affectation->mle_affecte_syn);
        $heuresNonAffectees = ($affectation->mh_affectee_globale == 0 || is_null($affectation->mh_affectee_globale)) 
                              && $affectation->mh_totale_drif > 0;
        return $aucunFormateur || $heuresNonAffectees;
    });

    $affectationsNonAffectes = $affectationsNonAffectes->map(function ($affectation) {
        $raisons = [];
        if (is_null($affectation->mle_affecte_presentiel) && is_null($affectation->mle_affecte_syn)) {
            $raisons[] = 'Aucun formateur affecté';
        }
        if (($affectation->mh_affectee_globale == 0 || is_null($affectation->mh_affectee_globale)) && $affectation->mh_totale_drif > 0) {
            $raisons[] = 'Masse horaire non affectée';
        }
        $affectation->raison = implode(', ', $raisons) ?: 'Autres raisons';
        return $affectation;
    });

    $nonAffectesParModule = $affectationsNonAffectes->groupBy('module_id')->map(function ($group) {
        $firstAffectation = $group->first();
        return [
            'code_module' => $firstAffectation->module ? $firstAffectation->module->code_module : 'N/A',
            'nom_module' => $firstAffectation->module ? $firstAffectation->module->nom_module : 'N/A',
            'groupes' => $group->pluck('groupe.code_groupe')->unique()->implode(', '),
            'masse_horaire' => $group->sum('mh_totale_drif'),
            'formateur' => 'Non affecté',
            'raisons' => $group->pluck('raison')->unique()->implode(', '),
        ];
    })->values();

    $totalNonAffectesModule = $affectationsNonAffectes->sum('mh_totale_drif');

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
            'raisons' => $group->pluck('raison')->unique()->implode(', '),
        ];
    })->filter(function($item) {
        return $item['code_filiere'] !== null;
    })->values();

    $totalNonAffectesFiliere = $affectationsNonAffectes->sum('mh_totale_drif');

    // ✅ SECTION CORRIGÉE : Construction des données formateurs
    $formateursData = collect();
    
    // Récupérer tous les MLEs uniques des affectations
    $formateursIds = $affectations->map(function($aff) {
        return [
            $aff->mle_affecte_presentiel,
            $aff->mle_affecte_syn
        ];
    })->flatten()->filter()->unique();

    foreach ($formateursIds as $mle) {
        $formateur = Formateur::where('mle', $mle)
            ->whereHas('etablissements', function($query) use ($etablissement) {
                $query->where('etablissements.code_efp', $etablissement->code_efp);
            })
            ->first();
        
        if (!$formateur) continue;
        
        // ✅ CORRECTION MAJEURE : Séparer présentiel et synchrone pour éviter le double comptage
        // Affectations où ce formateur est en PRÉSENTIEL
        $affPresentiel = $affectations->filter(function ($aff) use ($mle) {
            return $aff->mle_affecte_presentiel == $mle;
        });
        
        // Affectations où ce formateur est en SYNCHRONE
        $affSync = $affectations->filter(function ($aff) use ($mle) {
            return $aff->mle_affecte_syn == $mle;
        });
        
        // ✅ CORRECTION CRITIQUE : Calculer les heures DEMANDÉES en incluant TOUTES les heures DRIF
        // même si le formateur n'a pas encore tout affecté
        // On prend mhp_totale_drif et mhsyn_totale_drif (heures DRIF complètes)
        $heuresDemandeesPresentiel = $affPresentiel->sum('mhp_totale_drif');
        $heuresDemandeesSync = $affSync->sum('mhsyn_totale_drif');
        $heuresDemandees = $heuresDemandeesPresentiel + $heuresDemandeesSync;
        
        // Calculer les heures AFFECTÉES selon le rôle
        $heuresAffecteesPresentiel = $affPresentiel->sum('mh_affectee_presentiel');
        $heuresAffecteesSync = $affSync->sum('mh_affectee_sync');
        $heuresAffectees = $heuresAffecteesPresentiel + $heuresAffecteesSync;
        
        // Ne garder que les formateurs avec des heures
        if ($heuresDemandees > 0 || $heuresAffectees > 0) {
            $formateursData->push([
                'mle' => $mle,
                'nom_formateur' => $formateur->nom_complet,
                'type' => $formateur->type ?? 'permanent',
                'masse_horaire_reglementaire' => $formateur->masse_horaire ?? 910,
                'heures_demandees' => $heuresDemandees,
                'heures_affectees' => $heuresAffectees,
                'heures_manquantes' => max(0, $heuresDemandees - $heuresAffectees),
                'taux_affectation' => $heuresDemandees > 0 
                    ? ($heuresAffectees / $heuresDemandees) * 100 
                    : 0,
            ]);
        }
    }

    // Trier par heures demandées décroissant
    $formateursData = $formateursData->sortByDesc('heures_demandees')->values();

    // Calculer les totaux
    $totalFormateurs = [
        'masse_horaire_reglementaire' => $formateursData->sum('masse_horaire_reglementaire'),
        'heures_demandees' => $formateursData->sum('heures_demandees'),
        'heures_affectees' => $formateursData->sum('heures_affectees'),
        'heures_manquantes' => $formateursData->sum('heures_manquantes'),
    ];

    $formateursSansAffectation = $etablissement->formateurs()
        ->whereDoesntHave('affectationsPresentiel')
        ->whereDoesntHave('affectationsSyn')
        ->get();

    $groupesSansAffectation = Groupe::where('code_efp', $etablissement->code_efp)
        ->whereDoesntHave('affectations')
        ->get();

    $modulesSansAffectation = Module::where('code_efp', $etablissement->code_efp)
        ->whereDoesntHave('affectations')
        ->get();

    $formateursDetailsAvecGroupes = $this->getFormateursDetailsAvecGroupes($affectations, $filters);

    return view('administrationetablissement.dashboard', compact(
        'etablissement',
        'statistics',
        'heuresAnalysis',
        'heuresData',
        'tauxChartData',
        'detailedData',
        'paginatedDetailedData',
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
        'totalFormateurs',
        'groupesSansAffectation',
        'modulesSansAffectation',
        'formateursSansAffectation',
        'formateursDetailsAvecGroupes'
    ));
}
      
// ============================================================================
 // ÉTAPE 2 : AJOUTER CES TROIS NOUVELLES MÉTHODES PRIVÉES À LA FIN DU CONTRÔLEUR
// ============================================================================

            /**
             * Récupère les détails des formateurs avec leurs groupes et modules
             * avec application des filtres
             */
            private function getFormateursDetailsAvecGroupes($affectations, $filters = [])
            {
                $details = collect();
                
                // Parcourir toutes les affectations
                foreach ($affectations as $affectation) {
                    // Traiter le formateur présentiel
                    if ($affectation->mle_affecte_presentiel) {
                        $formateur = $affectation->formateurPresentiel;
                        if ($formateur) {
                            $detail = $this->buildFormateurDetail(
                                $formateur,
                                $affectation,
                                'Présentiel',
                                $affectation->mh_affectee_presentiel,
                                $affectation->avancement ? $affectation->avancement->mh_realisee_presentiel : 0
                            );
                            
                            if ($this->matchesDetailFilters($detail, $filters)) {
                                $details->push($detail);
                            }
                        }
                    }
                    
                    // Traiter le formateur synchrone
                    if ($affectation->mle_affecte_syn) {
                        $formateur = $affectation->formateurSyn;
                        if ($formateur) {
                            $detail = $this->buildFormateurDetail(
                                $formateur,
                                $affectation,
                                'Synchrone',
                                $affectation->mh_affectee_sync,
                                $affectation->avancement ? $affectation->avancement->mh_realisee_sync : 0
                            );
                            
                            if ($this->matchesDetailFilters($detail, $filters)) {
                                $details->push($detail);
                            }
                        }
                    }
                }
                
                return $details->sortBy([
                    ['nom_formateur', 'asc'],
                    ['groupe', 'asc'],
                    ['nom_module', 'asc']
                ])->values();
            }

            /**
             * Construit les détails d'un formateur pour un groupe/module donné
             */
            private function buildFormateurDetail($formateur, $affectation, $mode, $heuresAffectees, $heuresRealisees)
            {
                $groupe = $affectation->groupe;
                $module = $affectation->module;
                
                $tauxRealisation = $heuresAffectees > 0 
                    ? ($heuresRealisees / $heuresAffectees) * 100 
                    : 0;
                
                return [
                    'mle' => $formateur->mle,
                    'nom_formateur' => $formateur->nom_complet,
                    'type' => $formateur->type ?? 'permanent',
                    'groupe' => $groupe ? $groupe->code_groupe : 'N/A',
                    'groupe_id' => $groupe ? $groupe->id : null,
                    'code_module' => $module ? $module->code_module : 'N/A',
                    'nom_module' => $module ? $module->nom_module : 'N/A',
                    'module_id' => $module ? $module->id : null,
                    'heures_affectees' => $heuresAffectees,
                    'heures_realisees' => $heuresRealisees,
                    'taux_realisation' => round($tauxRealisation, 2),
                    'mode' => $mode,
                ];
            }

        /**
         * Vérifie si un détail correspond aux filtres appliqués
         */
        private function matchesDetailFilters($detail, $filters)
        {
            // Filtre formateur
            if (!empty($filters['formateur_detail']) && $detail['mle'] != $filters['formateur_detail']) {
                return false;
            }
            
            // Filtre type formateur
            if (!empty($filters['type_formateur']) && $detail['type'] != $filters['type_formateur']) {
                return false;
            }
            
            // Filtre groupe
            if (!empty($filters['groupe_detail']) && $detail['groupe'] != $filters['groupe_detail']) {
                return false;
            }
            
            // Filtre module
            if (!empty($filters['module_detail']) && $detail['code_module'] != $filters['module_detail']) {
                return false;
            }
            
            // Filtre taux minimum
            if (!empty($filters['taux_min']) && $detail['taux_realisation'] < $filters['taux_min']) {
                return false;
            }
            
            // Filtre taux maximum
            if (!empty($filters['taux_max']) && $detail['taux_realisation'] > $filters['taux_max']) {
                return false;
            }
            
            return true;
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
    
    $formationsIds = $groupes->pluck('formation_id')->unique();
    $totalFormations = $formationsIds->count();
    
    $modulesIds = $affectations->pluck('module_id')->unique();
    $totalModules = $modulesIds->count();
    
    $formateursIds = $affectations->map(function($affectation) {
        return [$affectation->mle_affecte_presentiel, $affectation->mle_affecte_syn];
    })->flatten()->filter()->unique();
    $totalFormateurs = $formateursIds->count();

    // ✅ CORRECTION : Calculer les heures réglementaires correctement
    $heuresReglementaires = $etablissement->formateurs()
        ->whereIn('mle', $formateursIds)
        ->sum('masse_horaire') ?: 0;
    
    $filieresIds = $groupes->pluck('filiere_id')->unique();
    $totalFilieres = $filieresIds->count();
    
    $secteursIds = Filiere::whereIn('id', $filieresIds)->pluck('secteur_id')->unique();
    $totalSecteurs = $secteursIds->count();
    
    // ✅ CORRECTION MAJEURE : Calculer les heures demandées SANS DOUBLE COMPTAGE
    // Somme de mhp_totale_drif + mhsyn_totale_drif (pas mh_totale_drif qui contient la somme déjà)
    $heuresRequisesPresentiel = $affectations->sum('mhp_totale_drif') ?: 0;
    $heuresRequisesSynchrone = $affectations->sum('mhsyn_totale_drif') ?: 0;
    $heuresRequisesTotal = $heuresRequisesPresentiel + $heuresRequisesSynchrone;
    
    // ✅ CORRECTION : Calculer les heures affectées SANS DOUBLE COMPTAGE
    // Somme de mh_affectee_presentiel + mh_affectee_sync (pas mh_affectee_globale)
    $heuresAffecteesPresentiel = $affectations->sum('mh_affectee_presentiel') ?: 0;
    $heuresAffecteesSync = $affectations->sum('mh_affectee_sync') ?: 0;
    $heuresAffecteesTotal = $heuresAffecteesPresentiel + $heuresAffecteesSync;
    
    $heuresRealiseesTotal = $affectations->sum(function($affectation) {
        return $affectation->avancement ? $affectation->avancement->mh_realisee_globale : 0;
    });
    
    $tauxRealisationGlobal = $heuresRequisesTotal > 0 ? ($heuresRealiseesTotal / $heuresRequisesTotal) * 100 : 0;
    $tauxAffectation = $heuresRequisesTotal > 0 ? ($heuresAffecteesTotal / $heuresRequisesTotal) * 100 : 0;
    
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
        'heures_reglementaires' => round($heuresReglementaires, 2),
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
    // ✅ CORRECTION : Calculer sans double comptage
    $heuresRequisesPresentiel = $affectations->sum('mhp_totale_drif') ?: 0;
    $heuresRequisesSynchrone = $affectations->sum('mhsyn_totale_drif') ?: 0;
    $heuresRequises = $heuresRequisesPresentiel + $heuresRequisesSynchrone;
    
    // ✅ CORRECTION : Somme de presentiel + sync, pas globale
    $heuresAffecteesPresentiel = $affectations->sum('mh_affectee_presentiel') ?: 0;
    $heuresAffecteesSync = $affectations->sum('mh_affectee_sync') ?: 0;
    $heuresAffectees = $heuresAffecteesPresentiel + $heuresAffecteesSync;
    
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
    // ✅ CORRECTION : Calculer sans double comptage
    $heuresRequisesPresentiel = $affectations->sum('mhp_totale_drif') ?: 0;
    $heuresRequisesSynchrone = $affectations->sum('mhsyn_totale_drif') ?: 0;
    $heuresRequises = $heuresRequisesPresentiel + $heuresRequisesSynchrone;
    
    // ✅ CORRECTION : Somme de presentiel + sync
    $heuresAffecteesPresentiel = $affectations->sum('mh_affectee_presentiel') ?: 0;
    $heuresAffecteesSync = $affectations->sum('mh_affectee_sync') ?: 0;
    $heuresAffectees = $heuresAffecteesPresentiel + $heuresAffecteesSync;
    
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

        $repartitionMode = [
            'Présentiel' => round($affectations->sum(function($affectation) {
                return $affectation->avancement ? $affectation->avancement->mh_realisee_presentiel : 0;
            }), 2),
            'Synchrone' => round($affectations->sum(function($affectation) {
                return $affectation->avancement ? $affectation->avancement->mh_realisee_sync : 0;
            }), 2),
        ];

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

    // ✅ CORRECTION MAJEURE : Utiliser la relation Many-to-Many pour récupérer les formateurs
    private function getFilterOptions($etablissement)
    {
        // Récupérer tous les groupes de l'établissement
        $groupes = Groupe::where('code_efp', $etablissement->code_efp)->get();
        $groupeIds = $groupes->pluck('id');
        
        // Récupérer les modules liés aux affectations de ces groupes
        $modules = Module::whereIn('id', 
            Affectation::whereIn('groupe_id', $groupeIds)->pluck('module_id')
        )->get();
        
        // ✅ CORRECTION : Utiliser la relation Many-to-Many pour récupérer les formateurs
        // Récupérer TOUS les formateurs rattachés à cet établissement
        $formateurs = $etablissement->formateurs()->get();

        // Récupérer les niveaux via les formations des groupes
        $formationsIds = $groupes->pluck('formation_id')->unique();
        $formations = Formation::whereIn('id', $formationsIds)->get();
        $niveaux = Niveau::whereIn('id', $formations->pluck('niveau_id'))->get();
        
        // Récupérer les types de formation (ex: initiale/continue, etc.)
        $typesFormation = $formations->pluck('type')->filter()->unique()->values();
        
        // Récupérer les filières
        $filieresIds = $groupes->pluck('filiere_id')->unique();
        $filieres = Filiere::whereIn('id', $filieresIds)->get();
        
        // Récupérer les années disponibles
        $annees = $groupes->pluck('annee_formation')->unique()->sort()->values();

        return [
            'groupes' => $groupes,
            'modules' => $modules,
            'formateurs' => $formateurs, // ✅ Maintenant récupéré via la relation Many-to-Many
            'niveaux' => $niveaux,
            'types_formation' => $typesFormation,
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

    public function importExcel(Request $request, \App\Services\AdministrationEtablissementImportService $importService)
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

            // Utiliser le service pour stocker et lancer le Job
            $importService->import($request->file('excel_file'), $etablissement);

            return redirect()->route('administration.etablissement.dashboard')
                ->with('success', 'Le fichier a été validé et l\'import a démarré en arrière-plan.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'initialisation de l\'importation Excel: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }
}