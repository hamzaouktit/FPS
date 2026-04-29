<?php

namespace App\Http\Controllers\AdministrationComplexe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\{Etablissement, Formateur, Formation, Module, Groupe, Filiere, Secteur, Affectation, Avancement};

class RapportGlobalController extends Controller
{
    public function index(Request $request)
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

        // Récupérer les filtres
        $filters = [
            'etablissement' => $request->input('etablissement'),
            'formateur' => $request->input('formateur'),
            'module' => $request->input('module'),
            'groupe' => $request->input('groupe'),
            'filiere' => $request->input('filiere'),
            'secteur' => $request->input('secteur'),
        ];

        // On récupère les mêmes données que le Dashboard Complexe
        $statistics = $this->calculateStatistics($complexe->id, $filters);
        $chartData = $this->getChartData($complexe->id, $filters);
        $filterOptions = $this->getFilterOptions($complexe->id);
        $etablissementsStats = $this->getEtablissementsStats($complexe->id);

        return view('administrationcomplexe.rapports.index', compact(
            'complexe', 
            'filters',
            'filterOptions',
            'statistics',
            'chartData',
            'etablissementsStats'
        ));
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

        // Appliquer les filtres
        if (!empty($filters['etablissement'])) $query->where('etablissements.code_efp', $filters['etablissement']);
        if (!empty($filters['secteur'])) $query->where('secteurs.nom_secteur', $filters['secteur']);
        if (!empty($filters['filiere'])) $query->where('filieres.code_filiere', $filters['filiere']);
        if (!empty($filters['formateur'])) {
            $query->where(function($q) use ($filters) {
                $q->where('affectations.mle_affecte_presentiel', $filters['formateur'])
                  ->orWhere('affectations.mle_affecte_syn', $filters['formateur']);
            });
        }
        if (!empty($filters['module'])) $query->where('modules.code_module', $filters['module']);
        if (!empty($filters['groupe'])) $query->where('groupes.code_groupe', $filters['groupe']);

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
        $heuresRealisees = $affectations->sum(function($item) { return $item->mh_realisee_globale ?? 0; });
        $difference = $heuresRequises - $heuresRealisees;

        $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;
        $tauxAffectation = $heuresRequises > 0 ? ($heuresAffectees / $heuresRequises) * 100 : 0;

        return [
            'nb_formations' => $affectations->pluck('formation_id')->unique()->filter()->count(),
            'nb_filieres' => $affectations->pluck('filiere_id')->unique()->filter()->count(),
            'nb_secteurs' => $affectations->pluck('secteur_id')->unique()->filter()->count(),
            'nb_groupes' => $affectations->pluck('groupe_id')->unique()->filter()->count(),
            'nb_modules' => $affectations->pluck('module_id')->unique()->filter()->count(),
            'nb_formateurs' => $affectations->pluck('mle_affecte_presentiel')->filter()
                                ->merge($affectations->pluck('mle_affecte_syn')->filter())->unique()->count(),
            'nb_apprenants' => $affectations->groupBy('groupe_id')->map(function($items) { return $items->first()->effectif_groupe ?? 0; })->sum(),
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
            ->join('modules', 'affectations.module_id', '=', 'modules.id')
            ->join('etablissements', 'affectations.code_efp', '=', 'etablissements.code_efp')
            ->leftJoin('avancements', 'affectations.id', '=', 'avancements.affectation_id')
            ->where('etablissements.complexe_id', $complexeId);

        if (!empty($filters['etablissement'])) $query->where('etablissements.code_efp', $filters['etablissement']);
        if (!empty($filters['secteur'])) $query->where('secteurs.nom_secteur', $filters['secteur']);
        if (!empty($filters['filiere'])) $query->where('filieres.code_filiere', $filters['filiere']);
        if (!empty($filters['formateur'])) {
            $query->where(function($q) use ($filters) {
                $q->where('affectations.mle_affecte_presentiel', $filters['formateur'])
                  ->orWhere('affectations.mle_affecte_syn', $filters['formateur']);
            });
        }
        if (!empty($filters['module'])) $query->where('modules.code_module', $filters['module']);
        if (!empty($filters['groupe'])) $query->where('groupes.code_groupe', $filters['groupe']);

        $affectations = $query->select(
            'affectations.mhp_s1_drif', 'affectations.mhsyn_s1_drif', 'affectations.mhasyn_s1_drif',
            'affectations.mhp_s2_drif', 'affectations.mhsyn_s2_drif', 'affectations.mhasyn_s2_drif',
            'affectations.mh_affectee_presentiel', 'affectations.mh_affectee_sync',
            'avancements.mh_realisee_presentiel', 'avancements.mh_realisee_sync'
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
                'presentiel' => round($affectations->sum(function($item) { return $item->mh_realisee_presentiel ?? 0; }), 2),
                'synchrone' => round($affectations->sum(function($item) { return $item->mh_realisee_sync ?? 0; }), 2),
            ],
            'taux_par_mode' => [
                'presentiel' => [
                    'affectee' => round($affectations->sum('mh_affectee_presentiel'), 2),
                    'realisee' => round($affectations->sum(function($item) { return $item->mh_realisee_presentiel ?? 0; }), 2),
                ],
                'synchrone' => [
                    'affectee' => round($affectations->sum('mh_affectee_sync'), 2),
                    'realisee' => round($affectations->sum(function($item) { return $item->mh_realisee_sync ?? 0; }), 2),
                ],
            ],
        ];
    }

    private function getFilterOptions($complexeId)
    {
        $etablissements = Etablissement::where('complexe_id', $complexeId)->select('code_efp', 'nom_efp')->orderBy('nom_efp')->get();
        $subQuery = function($query) use ($complexeId) { $query->select('code_efp')->from('etablissements')->where('complexe_id', $complexeId); };
        
        return [
            'etablissements' => $etablissements,
            'secteurs' => Secteur::whereIn('code_efp', $subQuery)->select('id', 'nom_secteur')->distinct()->orderBy('nom_secteur')->get(),
            'filieres' => Filiere::whereIn('code_efp', $subQuery)->select('id', 'code_filiere', 'nom_filiere')->distinct()->orderBy('nom_filiere')->get(),
            'formateurs' => Formateur::whereIn('id', DB::table('etablissement_formateur')->join('etablissements', 'etablissement_formateur.code_efp', '=', 'etablissements.code_efp')->where('etablissements.complexe_id', $complexeId)->pluck('etablissement_formateur.formateur_id')->unique())->select('id', 'mle', 'nom_complet')->orderBy('nom_complet')->get(),
            'modules' => Module::whereIn('code_efp', $subQuery)->select('id', 'code_module', 'nom_module')->distinct()->orderBy('nom_module')->get(),
            'groupes' => Groupe::whereIn('code_efp', $subQuery)->select('id', 'code_groupe')->distinct()->orderBy('code_groupe')->get(),
        ];
    }

    private function getEtablissementsStats($complexeId)
    {
        $etablissements = Etablissement::where('complexe_id', $complexeId)->get()->keyBy('code_efp');
        
        $allAffectations = Affectation::query()
            ->join('etablissements', 'affectations.code_efp', '=', 'etablissements.code_efp')
            ->join('groupes', 'affectations.groupe_id', '=', 'groupes.id')
            ->join('formations', 'groupes.formation_id', '=', 'formations.id')
            ->leftJoin('avancements', 'affectations.id', '=', 'avancements.affectation_id')
            ->where('etablissements.complexe_id', $complexeId)
            ->select('affectations.code_efp', 'affectations.mh_totale_drif', 'avancements.mh_realisee_globale', 'groupes.id as groupe_id', 'groupes.effectif_groupe', 'formations.id as formation_id', 'affectations.mle_affecte_presentiel', 'affectations.mle_affecte_syn')
            ->get()->groupBy('code_efp');

        $stats = [];
        foreach ($etablissements as $code_efp => $etablissement) {
            $affectations = $allAffectations->get($code_efp, collect([]));
            $heuresRequises = $affectations->sum('mh_totale_drif') ?: 0;
            $heuresRealisees = $affectations->sum(function($item) { return $item->mh_realisee_globale ?? 0; });
            $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;

            $stats[] = [
                'code_efp' => $code_efp,
                'nom_efp' => $etablissement->nom_efp,
                'heures_requises' => round($heuresRequises, 2),
                'heures_realisees' => round($heuresRealisees, 2),
                'taux_realisation' => round($tauxRealisation, 2),
            ];
        }

        return collect($stats);
    }
}
