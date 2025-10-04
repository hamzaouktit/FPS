<?php

namespace App\Http\Controllers\AdministrationComplexe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\{Etablissement, Formateur, Formation, Module, Groupe, Filiere, Secteur, Avancement};

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

        return view('administrationcomplexe.dashboard', compact(
            'user', 
            'complexe', 
            'detailedData', 
            'statistics', 
            'chartData', 
            'filterOptions',
            'filters'
        ));
    }

    private function buildDetailedQuery($complexeId, $filters)
    {
        $query = Avancement::query()
            ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
            ->join('formations', 'groupes.id_formation', '=', 'formations.id')
            ->join('etablissements', 'formations.code_efp', '=', 'etablissements.code_efp')
            ->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere')
            ->join('secteurs', 'filieres.nom_secteur', '=', 'secteurs.nom_secteur')
            ->join('modules', 'avancements.code_module', '=', 'modules.code_module')
            ->leftJoin('formateurs as f_presentiel', 'avancements.mle_presentiel', '=', 'f_presentiel.mle')
            ->leftJoin('formateurs as f_syn', 'avancements.mle_syn', '=', 'f_syn.mle')
            ->where('etablissements.complexe_id', $complexeId)
            ->select(
                'avancements.*',
                'etablissements.code_efp',
                'etablissements.nom_efp as efp',
                'formations.niveau',
                'formations.annee',
                'formations.type_formation',
                'formations.creneau',
                'secteurs.nom_secteur as secteur',
                'filieres.code_filiere',
                'filieres.nom_filiere as filiere',
                'groupes.groupe',
                'groupes.effectif_groupe',
                'groupes.sous_groupe',
                'groupes.statut_sous_groupe',
                'groupes.fusion_groupe',
                'groupes.code_fusion',
                'groupes.annee_formation',
                'modules.code_module',
                'modules.nom_module as module',
                'modules.regional',
                'f_presentiel.nom_formateur as formateur_presentiel',
                'f_syn.nom_formateur as formateur_syn'
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
                $q->where('avancements.mle_presentiel', $filters['formateur'])
                  ->orWhere('avancements.mle_syn', $filters['formateur']);
            });
        }
        if (!empty($filters['module'])) {
            $query->where('avancements.code_module', $filters['module']);
        }
        if (!empty($filters['groupe'])) {
            $query->where('groupes.groupe', $filters['groupe']);
        }

        return $query->orderBy('avancements.date_maj', 'desc');
    }

    private function calculateStatistics($complexeId, $filters)
    {
        // Construire la requête de base avec les mêmes filtres
        $query = Avancement::query()
            ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
            ->join('formations', 'groupes.id_formation', '=', 'formations.id')
            ->join('etablissements', 'formations.code_efp', '=', 'etablissements.code_efp')
            ->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere')
            ->join('secteurs', 'filieres.nom_secteur', '=', 'secteurs.nom_secteur')
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
                $q->where('avancements.mle_presentiel', $filters['formateur'])
                  ->orWhere('avancements.mle_syn', $filters['formateur']);
            });
        }
        if (!empty($filters['module'])) {
            $query->where('avancements.code_module', $filters['module']);
        }
        if (!empty($filters['groupe'])) {
            $query->where('groupes.groupe', $filters['groupe']);
        }

        $avancements = $query->get();

        // Calculer les statistiques
        $heuresRequises = $avancements->sum('mh_totale_drif') ?: 0;
        $heuresAffectees = $avancements->sum('mh_affectee_globale') ?: 0;
        $heuresRealisees = $avancements->sum('mh_realisee_globale') ?: 0;
        $difference = $heuresRequises - $heuresRealisees;

        $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;
        $tauxAffectation = $heuresRequises > 0 ? ($heuresAffectees / $heuresRequises) * 100 : 0;

        // Compter les entités uniques
        $nbFormations = $avancements->pluck('id_formation')->unique()->count();
        $nbFilieres = $avancements->pluck('code_filiere')->unique()->count();
        $nbSecteurs = $avancements->pluck('secteur')->unique()->count();
        $nbGroupes = $avancements->pluck('groupe')->unique()->count();
        $nbModules = $avancements->pluck('code_module')->unique()->count();
        
        // Formateurs uniques
        $formateursPresentiel = $avancements->pluck('mle_presentiel')->filter()->unique();
        $formateursSyn = $avancements->pluck('mle_syn')->filter()->unique();
        $nbFormateurs = $formateursPresentiel->merge($formateursSyn)->unique()->count();
        
        // FIX PRINCIPAL: Calculer l'effectif total correctement
        // On récupère les groupes uniques avec leur effectif
        $groupesUniques = $avancements->groupBy('groupe')->map(function($items) {
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
        $query = Avancement::query()
            ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
            ->join('formations', 'groupes.id_formation', '=', 'formations.id')
            ->join('etablissements', 'formations.code_efp', '=', 'etablissements.code_efp')
            ->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere')
            ->join('secteurs', 'filieres.nom_secteur', '=', 'secteurs.nom_secteur')
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
                $q->where('avancements.mle_presentiel', $filters['formateur'])
                  ->orWhere('avancements.mle_syn', $filters['formateur']);
            });
        }
        if (!empty($filters['module'])) {
            $query->where('avancements.code_module', $filters['module']);
        }
        if (!empty($filters['groupe'])) {
            $query->where('groupes.groupe', $filters['groupe']);
        }

        $avancements = $query->get();

        return [
            'heures_par_semestre' => [
                's1' => [
                    'presentiel' => round($avancements->sum('mhp_s1_drif'), 2),
                    'synchrone' => round($avancements->sum('mhsyn_s1_drif'), 2),
                    'asynchrone' => round($avancements->sum('mhasyn_s1_drif'), 2),
                ],
                's2' => [
                    'presentiel' => round($avancements->sum('mhp_s2_drif'), 2),
                    'synchrone' => round($avancements->sum('mhsyn_s2_drif'), 2),
                    'asynchrone' => round($avancements->sum('mhasyn_s2_drif'), 2),
                ],
            ],
            'heures_par_mode' => [
                'presentiel' => round($avancements->sum('mh_realisee_presentiel'), 2),
                'synchrone' => round($avancements->sum('mh_realisee_sync'), 2),
            ],
            'taux_par_mode' => [
                'presentiel' => [
                    'affectee' => round($avancements->sum('mh_affectee_presentiel'), 2),
                    'realisee' => round($avancements->sum('mh_realisee_presentiel'), 2),
                ],
                'synchrone' => [
                    'affectee' => round($avancements->sum('mh_affectee_sync'), 2),
                    'realisee' => round($avancements->sum('mh_realisee_sync'), 2),
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

        $secteurs = Secteur::whereHas('filieres.formations.etablissement', function($q) use ($complexeId) {
            $q->where('complexe_id', $complexeId);
        })
        ->select('nom_secteur')
        ->distinct()
        ->orderBy('nom_secteur')
        ->get();

        $filieres = Filiere::whereHas('formations.etablissement', function($q) use ($complexeId) {
            $q->where('complexe_id', $complexeId);
        })
        ->select('code_filiere', 'nom_filiere')
        ->orderBy('nom_filiere')
        ->get();

        // Récupérer les formateurs via la table avancements
        $formateursMle = DB::table('avancements')
            ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
            ->join('formations', 'groupes.id_formation', '=', 'formations.id')
            ->join('etablissements', 'formations.code_efp', '=', 'etablissements.code_efp')
            ->where('etablissements.complexe_id', $complexeId)
            ->where(function($q) {
                $q->whereNotNull('avancements.mle_presentiel')
                  ->orWhereNotNull('avancements.mle_syn');
            })
            ->select('avancements.mle_presentiel', 'avancements.mle_syn')
            ->distinct()
            ->get();

        // Collecter tous les MLE uniques
        $mleList = collect();
        foreach ($formateursMle as $item) {
            if (!empty($item->mle_presentiel)) {
                $mleList->push($item->mle_presentiel);
            }
            if (!empty($item->mle_syn)) {
                $mleList->push($item->mle_syn);
            }
        }
        $mleList = $mleList->unique()->values();

        // Récupérer les informations des formateurs
        $formateurs = Formateur::whereIn('mle', $mleList)
            ->select('mle', 'nom_formateur')
            ->orderBy('nom_formateur')
            ->get();

        $modules = Module::whereHas('avancements.groupe.formation.etablissement', function($q) use ($complexeId) {
            $q->where('complexe_id', $complexeId);
        })
        ->select('code_module', 'nom_module')
        ->orderBy('nom_module')
        ->get();

        $groupes = Groupe::whereHas('formation.etablissement', function($q) use ($complexeId) {
            $q->where('complexe_id', $complexeId);
        })
        ->select('groupe')
        ->orderBy('groupe')
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
}