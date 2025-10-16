<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use App\Models\Secteur;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Formateur;
use App\Models\Affectation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FiliereController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            abort(403, 'Accès non autorisé');
        }

        // Filtrer uniquement les filières de l'établissement du directeur connecté
        $query = Filiere::with(['secteur'])
            ->where('code_efp', $user->etablissement->code_efp);

        // Récupérer les secteurs utilisés dans les filières de cet établissement
        $secteursUtilises = Filiere::where('code_efp', $user->etablissement->code_efp)
            ->distinct()
            ->pluck('secteur_id')
            ->toArray();

        // Filtres
        if ($request->filled('secteur_id')) {
            $query->where('secteur_id', $request->secteur_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom_filiere', 'like', "%{$search}%")
                  ->orWhere('code_filiere', 'like', "%{$search}%");
            });
        }

        $filieres = $query->orderBy('nom_filiere')->paginate(15);
        
        // Récupérer uniquement les secteurs utilisés dans cet établissement
        $secteurs = Secteur::whereIn('id', $secteursUtilises)
            ->where('code_efp', $user->etablissement->code_efp)
            ->orderBy('nom_secteur')
            ->get();

        return view('administrationetablissement.filieres.index', compact('filieres', 'secteurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            abort(403, 'Accès non autorisé');
        }
        
        // Récupérer les secteurs de l'établissement
        $secteurs = Secteur::where('code_efp', $user->etablissement->code_efp)
            ->orderBy('nom_secteur')
            ->get();

        return view('administrationetablissement.filieres.create', compact('secteurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            abort(403, 'Accès non autorisé');
        }

        $validated = $request->validate([
            'code_filiere' => 'required|string|max:255',
            'nom_filiere' => 'required|string|max:255',
            'secteur_id' => 'required|exists:secteurs,id',
        ], [
            'code_filiere.required' => 'Le code de la filière est obligatoire.',
            'nom_filiere.required' => 'Le nom de la filière est obligatoire.',
            'secteur_id.required' => 'Le secteur est obligatoire.',
            'secteur_id.exists' => 'Le secteur sélectionné n\'existe pas.',
        ]);

        // Ajouter automatiquement le code_efp de l'établissement du directeur
        $validated['code_efp'] = $user->etablissement->code_efp;

        Filiere::create($validated);

        return redirect()
            ->route('administration.etablissement.filieres.index')
            ->with('success', 'Filière créée avec succès.');
    }

    /**
     * Display the specified resource
     */
    public function show(string $id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que la filière appartient à l'établissement du directeur
        $filiere = Filiere::with([
            'secteur',
            'groupes.formation',
            'modules'
        ])
        ->where('code_efp', $user->etablissement->code_efp)
        ->findOrFail($id);

        // 1. STATISTIQUES GÉNÉRALES
        $stats = [
            'total_groupes' => $filiere->groupes()->count(),
            'groupes_actifs' => $filiere->groupes()->where('statut', 'Actif')->count(),
            'effectif_total' => $filiere->groupes()->sum('effectif_groupe'),
            'total_modules' => $filiere->modules()->count(),
            'modules_regionaux' => $filiere->modules()->where('regional', 'O')->count(),
            'modules_pie' => $filiere->modules()->where('module_pie', 'O')->count(),
        ];

        // 2. GROUPES PAR ANNÉE DE FORMATION
        $groupesParAnnee = $filiere->groupes()
            ->select('groupes.annee_formation', DB::raw('COUNT(*) as total'), DB::raw('SUM(groupes.effectif_groupe) as effectif_total'))
            ->groupBy('groupes.annee_formation')
            ->orderBy('groupes.annee_formation')
            ->get();

        // 3. GROUPES PAR TYPE DE FORMATION
        $groupesParType = $filiere->groupes()
            ->join('formations', 'groupes.formation_id', '=', 'formations.id')
            ->select('formations.type', DB::raw('COUNT(*) as total'), DB::raw('SUM(groupes.effectif_groupe) as effectif_total'))
            ->groupBy('formations.type')
            ->get();

        // 4. GROUPES PAR MODE DE FORMATION
        $groupesParMode = $filiere->groupes()
            ->join('formations', 'groupes.formation_id', '=', 'formations.id')
            ->select('formations.mode', DB::raw('COUNT(*) as total'), DB::raw('SUM(groupes.effectif_groupe) as effectif_total'))
            ->groupBy('formations.mode')
            ->get();

        // 5. LISTE DES GROUPES DÉTAILLÉE
        $groupes = $filiere->groupes()
            ->with(['formation'])
            ->orderBy('annee_formation')
            ->orderBy('code_groupe')
            ->get();

        // 6. MODULES DE LA FILIÈRE
        $modules = $filiere->modules()
            ->select('modules.id', 'modules.code_module', 'modules.nom_module', 'modules.regional', 'modules.module_pie', 'modules.code_efp')
            ->orderBy('modules.code_module')
            ->get()
            ->map(function($module) use ($filiere) {
                $groupeIds = $filiere->groupes()->pluck('id')->toArray();
                $nbAffectations = Affectation::where('module_id', $module->id)
                    ->whereIn('groupe_id', $groupeIds)
                    ->count();
                $module->nb_affectations = $nbAffectations;
                return $module;
            });

        // 7. FORMATEURS INTERVENANT DANS CETTE FILIÈRE
        $groupeIds = $filiere->groupes()->pluck('id')->toArray();
        
        $affectations = Affectation::whereIn('groupe_id', $groupeIds)
            ->with(['module', 'groupe', 'formateurPresentiel', 'formateurSyn'])
            ->get();

        $formateurIds = collect();
        foreach ($affectations as $affectation) {
            if ($affectation->mle_affecte_presentiel) {
                $formateurIds->push($affectation->mle_affecte_presentiel);
            }
            if ($affectation->mle_affecte_syn) {
                $formateurIds->push($affectation->mle_affecte_syn);
            }
        }
        $formateurIds = $formateurIds->unique();

        $formateurs = Formateur::whereIn('mle', $formateurIds)
            ->with(['secteurs', 'modules'])
            ->get()
            ->map(function($formateur) use ($affectations) {
                $nbAffectations = $affectations->filter(function($aff) use ($formateur) {
                    return $aff->mle_affecte_presentiel === $formateur->mle || 
                           $aff->mle_affecte_syn === $formateur->mle;
                })->count();
                
                $formateur->nb_affectations = $nbAffectations;
                return $formateur;
            })
            ->sortByDesc('nb_affectations');

        // 8. STATISTIQUES D'AVANCEMENT GLOBAL
        $avancementStats = null;
        if (count($groupeIds) > 0) {
            $avancementStats = DB::table('affectations')
                ->join('avancements', 'affectations.id', '=', 'avancements.affectation_id')
                ->whereIn('affectations.groupe_id', $groupeIds)
                ->select(
                    DB::raw('SUM(affectations.mh_affectee_globale) as total_mh_affectees'),
                    DB::raw('SUM(avancements.mh_realisee_globale) as total_mh_realisees'),
                    DB::raw('AVG(avancements.taux_realisation_globale) as taux_moyen'),
                    DB::raw('AVG(avancements.moyenne_absence) as moyenne_absence_globale'),
                    DB::raw('SUM(avancements.nb_cc) as total_cc'),
                    DB::raw('SUM(CASE WHEN avancements.seance_efm = "Non" THEN 1 ELSE 0 END) as total_efm_passes'),
                    DB::raw('SUM(CASE WHEN avancements.validation_efm = "oui" THEN 1 ELSE 0 END) as total_efm_valides')
                )
                ->first();
        }

        // 9. PROGRESSION PAR MODULE
        $moduleIdsDeLaFiliere = $filiere->modules()->pluck('modules.id')->toArray();
        
        if (count($moduleIdsDeLaFiliere) > 0 && count($groupeIds) > 0) {
            $progressionModules = DB::table('affectations')
                ->join('avancements', 'affectations.id', '=', 'avancements.affectation_id')
                ->join('modules', 'affectations.module_id', '=', 'modules.id')
                ->join('groupes', 'affectations.groupe_id', '=', 'groupes.id')
                ->where('groupes.filiere_id', $filiere->id)
                ->whereIn('modules.id', $moduleIdsDeLaFiliere)
                ->select(
                    'modules.id',
                    'modules.code_module',
                    'modules.nom_module',
                    DB::raw('COUNT(DISTINCT affectations.groupe_id) as nb_groupes'),
                    DB::raw('AVG(avancements.taux_realisation_globale) as taux_moyen'),
                    DB::raw('SUM(avancements.mh_realisee_globale) as total_mh_realisees'),
                    DB::raw('SUM(affectations.mh_affectee_globale) as total_mh_affectees')
                )
                ->groupBy('modules.id', 'modules.code_module', 'modules.nom_module')
                ->orderByDesc('taux_moyen')
                ->get();
        } else {
            $progressionModules = collect();
        }

        return view('administrationetablissement.filieres.show', compact(
            'filiere',
            'stats',
            'groupesParAnnee',
            'groupesParType',
            'groupesParMode',
            'groupes',
            'modules',
            'formateurs',
            'avancementStats',
            'progressionModules'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que la filière appartient à l'établissement du directeur
        $filiere = Filiere::where('code_efp', $user->etablissement->code_efp)
            ->findOrFail($id);
        
        // Récupérer les secteurs de l'établissement
        $secteurs = Secteur::where('code_efp', $user->etablissement->code_efp)
            ->orderBy('nom_secteur')
            ->get();

        return view('administrationetablissement.filieres.edit', compact('filiere', 'secteurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que la filière appartient à l'établissement du directeur
        $filiere = Filiere::where('code_efp', $user->etablissement->code_efp)
            ->findOrFail($id);

        $validated = $request->validate([
            'code_filiere' => 'required|string|max:255',
            'nom_filiere' => 'required|string|max:255',
            'secteur_id' => 'required|exists:secteurs,id',
        ], [
            'code_filiere.required' => 'Le code de la filière est obligatoire.',
            'nom_filiere.required' => 'Le nom de la filière est obligatoire.',
            'secteur_id.required' => 'Le secteur est obligatoire.',
            'secteur_id.exists' => 'Le secteur sélectionné n\'existe pas.',
        ]);

        // S'assurer que le code_efp ne change pas
        $validated['code_efp'] = $user->etablissement->code_efp;

        $filiere->update($validated);

        return redirect()
            ->route('administration.etablissement.filieres.index')
            ->with('success', 'Filière modifiée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que la filière appartient à l'établissement du directeur
        $filiere = Filiere::where('code_efp', $user->etablissement->code_efp)
            ->findOrFail($id);

        // Vérifier s'il y a des groupes liés
        if ($filiere->groupes()->count() > 0) {
            return redirect()
                ->route('administration.etablissement.filieres.index')
                ->with('error', 'Impossible de supprimer cette filière car elle contient des groupes.');
        }

        $filiere->delete();

        return redirect()
            ->route('administration.etablissement.filieres.index')
            ->with('success', 'Filière supprimée avec succès.');
    }
}