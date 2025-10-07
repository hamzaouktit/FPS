<?php

namespace App\Http\Controllers\AdministrationComplexe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\{Etablissement, Formateur, Formation, Module, Groupe, Filiere, Secteur, Avancement, User};

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

        // Récupérer les établissements avec leurs statistiques
        $etablissements = Etablissement::where('complexe_id', $complexe->id)
            ->with(['user', 'formations'])
            ->withCount('formations')
            ->paginate(10);

        // Calculer les statistiques pour chaque établissement
        foreach ($etablissements as $etablissement) {
            $stats = $this->getEtablissementStats($etablissement->code_efp);
            $etablissement->stats = $stats;
        }

        return view('administrationcomplexe.etablissements.index', compact(
            'user',
            'complexe',
            'etablissements'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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

        // Récupérer les utilisateurs sans établissement et avec le rôle directeur_etablissement
        $directeurs = User::where('role', 'directeur_etablissement')
            ->whereDoesntHave('etablissement')
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
                ->with('error', 'Accès refusé. Vous n\'êtes pas directeur de complexe.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé à votre compte.');
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

        Etablissement::create($validated);

        return redirect()->route('administration.complexe.etablissements.index')
            ->with('success', 'Établissement créé avec succès.');
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

        // Récupérer l'établissement
        $etablissement = Etablissement::where('code_efp', $code_efp)
            ->where('complexe_id', $complexe->id)
            ->firstOrFail();

        // Récupérer les filtres
        $filters = [
            'formateur' => $request->input('formateur'),
            'module' => $request->input('module'),
            'groupe' => $request->input('groupe'),
            'filiere' => $request->input('filiere'),
            'secteur' => $request->input('secteur'),
        ];

        // Construire la requête de base
        $query = $this->buildDetailedQuery($code_efp, $filters);
        
        // Récupérer les données avec pagination
        $detailedData = $query->paginate(20)->appends($request->except('page'));

        // Calculer les statistiques
        $statistics = $this->calculateStatistics($code_efp, $filters);

        // Récupérer les données pour les graphiques
        $chartData = $this->getChartData($code_efp, $filters);

        // Récupérer les options de filtrage
        $filterOptions = $this->getFilterOptions($code_efp);

        return view('administrationcomplexe.etablissements.show', compact(
            'user', 
            'complexe', 
            'etablissement',
            'detailedData', 
            'statistics', 
            'chartData', 
            'filterOptions',
            'filters'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($code_efp)
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
            ->firstOrFail();

        // Récupérer les directeurs disponibles (sans établissement) + le directeur actuel
        $directeurs = User::where('role', 'directeur_etablissement')
            ->where(function($query) use ($etablissement) {
                $query->whereDoesntHave('etablissement')
                      ->orWhere('id', $etablissement->user_id);
            })
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
                ->with('error', 'Accès refusé. Vous n\'êtes pas directeur de complexe.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')
                ->with('error', 'Aucun complexe associé à votre compte.');
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

        $etablissement->update($validated);

        return redirect()->route('administration.complexe.etablissements.index')
            ->with('success', 'Établissement modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($code_efp)
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
            ->firstOrFail();

        // Vérifier s'il y a des formations associées
        if ($etablissement->formations()->count() > 0) {
            return redirect()->route('administration.complexe.etablissements.index')
                ->with('error', 'Impossible de supprimer cet établissement car il contient des formations.');
        }

        $etablissement->delete();

        return redirect()->route('administration.complexe.etablissements.index')
            ->with('success', 'Établissement supprimé avec succès.');
    }

    // ========== Méthodes privées existantes ==========

    private function getEtablissementStats($code_efp)
    {
        $query = Avancement::query()
            ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
            ->join('formations', 'groupes.id_formation', '=', 'formations.id')
            ->where('formations.code_efp', $code_efp);

        $avancements = $query->get();

        $nbGroupes = $avancements->pluck('groupe')->unique()->count();
        $groupesUniques = $avancements->groupBy('groupe')->map(function($items) {
            return $items->first()->effectif_groupe ?? 0;
        });
        $nbApprenants = $groupesUniques->sum();

        $heuresRequises = $avancements->sum('mh_totale_drif') ?: 0;
        $heuresRealisees = $avancements->sum('mh_realisee_globale') ?: 0;
        $tauxRealisation = $heuresRequises > 0 ? ($heuresRealisees / $heuresRequises) * 100 : 0;

        return [
            'nb_groupes' => $nbGroupes,
            'nb_apprenants' => $nbApprenants,
            'taux_realisation' => round($tauxRealisation, 2),
        ];
    }

    private function buildDetailedQuery($code_efp, $filters)
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
            ->where('etablissements.code_efp', $code_efp)
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

    private function calculateStatistics($code_efp, $filters)
    {
        $query = Avancement::query()
            ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
            ->join('formations', 'groupes.id_formation', '=', 'formations.id')
            ->join('etablissements', 'formations.code_efp', '=', 'etablissements.code_efp')
            ->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere')
            ->join('secteurs', 'filieres.nom_secteur', '=', 'secteurs.nom_secteur')
            ->where('etablissements.code_efp', $code_efp);

        // Appliquer les filtres
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
        
        // Calculer l'effectif total
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

    private function getChartData($code_efp, $filters)
    {
        $query = Avancement::query()
            ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
            ->join('formations', 'groupes.id_formation', '=', 'formations.id')
            ->join('etablissements', 'formations.code_efp', '=', 'etablissements.code_efp')
            ->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere')
            ->join('secteurs', 'filieres.nom_secteur', '=', 'secteurs.nom_secteur')
            ->where('etablissements.code_efp', $code_efp);

        // Appliquer les filtres
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

    private function getFilterOptions($code_efp)
    {
        $secteurs = Secteur::whereHas('filieres.formations.etablissement', function($q) use ($code_efp) {
            $q->where('code_efp', $code_efp);
        })
        ->select('nom_secteur')
        ->distinct()
        ->orderBy('nom_secteur')
        ->get();

        $filieres = Filiere::whereHas('formations.etablissement', function($q) use ($code_efp) {
            $q->where('code_efp', $code_efp);
        })
        ->select('code_filiere', 'nom_filiere')
        ->orderBy('nom_filiere')
        ->get();

        // Récupérer les formateurs via la table avancements
        $formateursMle = DB::table('avancements')
            ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
            ->join('formations', 'groupes.id_formation', '=', 'formations.id')
            ->where('formations.code_efp', $code_efp)
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

        $modules = Module::whereHas('avancements.groupe.formation.etablissement', function($q) use ($code_efp) {
            $q->where('code_efp', $code_efp);
        })
        ->select('code_module', 'nom_module')
        ->orderBy('nom_module')
        ->get();

        $groupes = Groupe::whereHas('formation.etablissement', function($q) use ($code_efp) {
            $q->where('code_efp', $code_efp);
        })
        ->select('groupe')
        ->orderBy('groupe')
        ->get();

        return [
            'secteurs' => $secteurs,
            'filieres' => $filieres,
            'formateurs' => $formateurs,
            'modules' => $modules,
            'groupes' => $groupes,
        ];
    }
}