<?php

namespace App\Http\Controllers\AdministrationComplexe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\{Etablissement, Formateur, Formation, Module, Groupe, Filiere, Secteur, Affectation, Avancement};

class DashboardComplexeController extends Controller
{
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

        // Récupérer les filtres
        $filters = [
            'etablissement' => $request->input('etablissement'),
            'formateur' => $request->input('formateur'),
            'module' => $request->input('module'),
            'groupe' => $request->input('groupe'),
            'filiere' => $request->input('filiere'),
            'secteur' => $request->input('secteur'),
        ];

        // Construire la requête de base
        $query = $this->buildDetailedQuery($complexe->id, $filters);
        
        // Récupérer les données avec pagination
        $detailedData = $query->paginate(20)->appends($request->except('page'));

        // Calculer les statistiques
        $statistics = $this->calculateStatistics($complexe->id, $filters);

        // Récupérer les données pour les graphiques
        $chartData = $this->getChartData($complexe->id, $filters);

        // Récupérer les options de filtrage
        $filterOptions = $this->getFilterOptions($complexe->id);

        // Récupérer les statistiques par établissement
        $etablissementsStats = $this->getEtablissementsStats($complexe->id);

        return view('administrationcomplexe.dashboard', compact(
            'user', 
            'complexe', 
            'detailedData', 
            'statistics', 
            'chartData', 
            'filterOptions',
            'filters',
            'etablissementsStats'
        ));
    }

    private function buildDetailedQuery($complexeId, $filters)
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
            ->where('etablissements.complexe_id', $complexeId)
            ->select(
                'affectations.id as affectation_id',
                'etablissements.code_efp',
                'etablissements.nom_efp as efp',
                'formations.annee',
                'formations.type',
                'formations.mode',
                'formations.creneau',
                'niveaux.nom as niveau',
                'secteurs.nom_secteur as secteur',
                'filieres.code_filiere',
                'filieres.nom_filiere as filiere',
                'groupes.code_groupe as groupe',
                'groupes.effectif_groupe',
                'groupes.statut',
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
                'f_presentiel.nom_complet as formateur_presentiel',
                'f_syn.nom_complet as formateur_syn',
                'affectations.mle_affecte_presentiel',
                'affectations.mle_affecte_syn',
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
                DB::raw('COALESCE(avancements.moyenne_absence, 0) as moyenne_absence'),
                DB::raw('COALESCE(avancements.nb_cc, 0) as nb_cc'),
                DB::raw('COALESCE(avancements.seance_efm, "Non") as seance_efm'),
                DB::raw('COALESCE(avancements.validation_efm, "non") as validation_efm'),
                'avancements.classe_teams',
                'avancements.date_maj'
            );

        // Appliquer les filtres
        if (!empty($filters['etablissement'])) {
            $query->where('etablissements.code_efp', $filters['etablissement']);
        }
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

    private function calculateStatistics($complexeId, $filters)
    {
        $query = Affectation::query()
            ->join('groupes', 'affectations.groupe_id', '=', 'groupes.id')
            ->join('modules', 'affectations.module_id', '=', 'modules.id')
            ->join('formations', 'groupes.formation_id', '=', 'formations.id')
            ->join('filieres', 'groupes.filiere_id', '=', 'filieres.id')
            ->join('secteurs', 'filieres.secteur_id', '=', 'secteurs.id')
            ->join('etablissements', 'affectations.code_efp', '=', 'etablissements.code_efp')
            ->leftJoin('avancements', 'affectations.id', '=', 'avancements.affectation_id')
            ->where('etablissements.complexe_id', $complexeId);

        // Appliquer les mêmes filtres
        if (!empty($filters['etablissement'])) {
            $query->where('etablissements.code_efp', $filters['etablissement']);
        }
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

        // Récupérer les données
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
        
        // Calculer l'effectif total correctement
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

    private function getChartData($complexeId, $filters)
    {
        $query = Affectation::query()
            ->join('groupes', 'affectations.groupe_id', '=', 'groupes.id')
            ->join('formations', 'groupes.formation_id', '=', 'formations.id')
            ->join('filieres', 'groupes.filiere_id', '=', 'filieres.id')
            ->join('secteurs', 'filieres.secteur_id', '=', 'secteurs.id')
            ->join('etablissements', 'affectations.code_efp', '=', 'etablissements.code_efp')
            ->leftJoin('avancements', 'affectations.id', '=', 'avancements.affectation_id')
            ->where('etablissements.complexe_id', $complexeId);

        // Appliquer les filtres
        if (!empty($filters['etablissement'])) {
            $query->where('etablissements.code_efp', $filters['etablissement']);
        }
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

    private function getFilterOptions($complexeId)
    {
        $etablissements = Etablissement::where('complexe_id', $complexeId)
            ->select('code_efp', 'nom_efp')
            ->orderBy('nom_efp')
            ->get();

        $secteurs = Secteur::whereHas('filieres.groupes.formation.etablissement', function($q) use ($complexeId) {
            $q->where('complexe_id', $complexeId);
        })
        ->select('id', 'nom_secteur')
        ->distinct()
        ->orderBy('nom_secteur')
        ->get();

        $filieres = Filiere::whereHas('groupes.formation.etablissement', function($q) use ($complexeId) {
            $q->where('complexe_id', $complexeId);
        })
        ->select('id', 'code_filiere', 'nom_filiere')
        ->orderBy('nom_filiere')
        ->get();

        // Récupérer les formateurs via la table affectations
        $formateursMle = Affectation::query()
            ->join('etablissements', 'affectations.code_efp', '=', 'etablissements.code_efp')
            ->where('etablissements.complexe_id', $complexeId)
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
            ->select('mle', 'nom_complet')
            ->orderBy('nom_complet')
            ->get();

        $modules = Module::whereHas('affectations', function($q) use ($complexeId) {
            $q->join('etablissements', 'affectations.code_efp', '=', 'etablissements.code_efp')
              ->where('etablissements.complexe_id', $complexeId);
        })
        ->select('id', 'code_module', 'nom_module')
        ->orderBy('nom_module')
        ->get();

        $groupes = Groupe::whereHas('formation.etablissement', function($q) use ($complexeId) {
            $q->where('complexe_id', $complexeId);
        })
        ->select('id', 'code_groupe')
        ->orderBy('code_groupe')
        ->get();

        return [
            'etablissements' => $etablissements,
            'secteurs' => $secteurs,
            'filieres' => $filieres,
            'formateurs' => $formateurs,
            'modules' => $modules,
            'groupes' => $groupes,
        ];
    }

    private function getEtablissementsStats($complexeId)
    {
        $etablissements = Etablissement::where('complexe_id', $complexeId)->get();
        
        $stats = [];
        foreach ($etablissements as $etablissement) {
            $affectations = Affectation::query()
                ->join('groupes', 'affectations.groupe_id', '=', 'groupes.id')
                ->join('formations', 'groupes.formation_id', '=', 'formations.id')
                ->leftJoin('avancements', 'affectations.id', '=', 'avancements.affectation_id')
                ->where('affectations.code_efp', $etablissement->code_efp)
                ->select(
                    'affectations.mh_totale_drif',
                    'avancements.mh_realisee_globale',
                    'groupes.id as groupe_id',
                    'groupes.effectif_groupe',
                    'formations.id as formation_id',
                    'affectations.mle_affecte_presentiel',
                    'affectations.mle_affecte_syn'
                )
                ->get();

            $heuresRequises = $affectations->sum('mh_totale_drif') ?: 0;
            $heuresRealisees = $affectations->sum(function($item) {
                return $item->mh_realisee_globale ?? 0;
            });
            $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;

            $nbFormations = $affectations->pluck('formation_id')->unique()->filter()->count();
            $nbGroupes = $affectations->pluck('groupe_id')->unique()->filter()->count();
            
            $formateursPresentiel = $affectations->pluck('mle_affecte_presentiel')->filter()->unique();
            $formateursSyn = $affectations->pluck('mle_affecte_syn')->filter()->unique();
            $nbFormateurs = $formateursPresentiel->merge($formateursSyn)->unique()->count();

            $groupesUniques = $affectations->groupBy('groupe_id')->map(function($items) {
                return $items->first()->effectif_groupe ?? 0;
            });
            $nbApprenants = $groupesUniques->sum();

            $stats[] = [
                'code_efp' => $etablissement->code_efp,
                'nom_efp' => $etablissement->nom_efp,
                'nb_formations' => $nbFormations,
                'nb_groupes' => $nbGroupes,
                'nb_formateurs' => $nbFormateurs,
                'nb_apprenants' => $nbApprenants,
                'heures_requises' => round($heuresRequises, 2),
                'heures_realisees' => round($heuresRealisees, 2),
                'taux_realisation' => round($tauxRealisation, 2),
            ];
        }

        return collect($stats);
    }
}