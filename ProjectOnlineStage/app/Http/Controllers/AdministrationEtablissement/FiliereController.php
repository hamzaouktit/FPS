<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use App\Models\Secteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FiliereController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est directeur d'établissement
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            abort(403, 'Accès non autorisé');
        }

        $code_efp = $user->etablissement->code_efp;
        
        // Récupérer les filières avec leurs relations
        $query = Filiere::with(['secteur', 'etablissement'])
            ->where('code_efp', $code_efp);

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
        
        // Récupérer les secteurs pour le filtre
        $secteurs = Secteur::where('code_efp', $code_efp)
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

        $code_efp = $user->etablissement->code_efp;
        
        // Récupérer les secteurs de l'établissement
        $secteurs = Secteur::where('code_efp', $code_efp)
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

        $code_efp = $user->etablissement->code_efp;

        $validated = $request->validate([
            'code_filiere' => [
                'required',
                'string',
                'max:255',
                Rule::unique('filieres')->where('code_efp', $code_efp)
            ],
            'nom_filiere' => 'required|string|max:255',
            'secteur_id' => [
                'required',
                'exists:secteurs,id',
                function ($attribute, $value, $fail) use ($code_efp) {
                    $secteur = Secteur::find($value);
                    if (!$secteur || $secteur->code_efp !== $code_efp) {
                        $fail('Le secteur sélectionné n\'appartient pas à votre établissement.');
                    }
                },
            ],
        ], [
            'code_filiere.required' => 'Le code de la filière est obligatoire.',
            'code_filiere.unique' => 'Ce code de filière existe déjà dans votre établissement.',
            'nom_filiere.required' => 'Le nom de la filière est obligatoire.',
            'secteur_id.required' => 'Le secteur est obligatoire.',
            'secteur_id.exists' => 'Le secteur sélectionné n\'existe pas.',
        ]);

        $validated['code_efp'] = $code_efp;

        Filiere::create($validated);

        return redirect()
            ->route('administration.etablissement.filieres.index')
            ->with('success', 'Filière créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    /**
 * Display the specified resource.
 */
public function show(string $code_filiere)
{
    $user = Auth::user();
    
    if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
        abort(403, 'Accès non autorisé');
    }

    $code_efp = $user->etablissement->code_efp;

    // Charger la filière avec toutes ses relations
    $filiere = Filiere::with([
        'secteur',
        'etablissement',
        'formations.niveau',
        'formations.groupes',
        'groupes.formation'
    ])
        ->where('code_efp', $code_efp)
        ->where('code_filiere', $code_filiere)
        ->firstOrFail();

    // Calculer les statistiques
    $stats = [
        'total_formations' => $filiere->formations()->count(),
        'total_groupes' => $filiere->groupes()->count(),
        'effectif_total' => $filiere->groupes()->sum('effectif_groupe'),
        'formations_par_niveau' => $filiere->formations()
            ->with('niveau')
            ->get()
            ->groupBy(function($formation) {
                return $formation->niveau->niveau ?? 'Non défini';
            })
            ->map->count(),
        'formations_par_type' => $filiere->formations()
            ->get()
            ->groupBy(function($formation) {
                return $formation->type_formation ?? 'Non défini';
            })
            ->map->count(),
    ];

    // Récupérer les niveaux actifs pour cette filière
    $niveaux = \App\Models\Niveau::where('code_efp', $code_efp)
        ->whereHas('formations', function($q) use ($filiere) {
            $q->where('filiere_id', $filiere->id);
        })
        ->get();

    // CORRECTION : Récupérer UNIQUEMENT les modules de cette filière
    // Étape 1: Récupérer les IDs des formations de cette filière
    $formationIds = $filiere->formations()->pluck('id')->toArray();

    // Étape 2: Récupérer les IDs des groupes de ces formations
    $groupeIds = \App\Models\Groupe::whereIn('formation_id', $formationIds)
        ->pluck('id')
        ->toArray();

    // Étape 3: Récupérer les modules via les avancements, en s'assurant que code_efp correspond
    $modules = \App\Models\Module::where('code_efp', $code_efp)
        ->whereIn('id', function ($query) use ($groupeIds) {
            $query->select('module_id')
                ->from('avancements')
                ->whereIn('groupe_id', $groupeIds)
                ->distinct();
        })
        ->with(['avancements' => function($q) use ($groupeIds) {
            $q->whereIn('groupe_id', $groupeIds);
        }])
        ->orderBy('code_module')
        ->get();

    // CORRECTION FORMATEURS
    $formateursPresentielIds = \App\Models\Avancement::whereIn('groupe_id', $groupeIds)
        ->whereNotNull('mle_presentiel')
        ->distinct()
        ->pluck('mle_presentiel')
        ->filter()
        ->toArray();

    $formateursSynchroneIds = \App\Models\Avancement::whereIn('groupe_id', $groupeIds)
        ->whereNotNull('mle_syn')
        ->distinct()
        ->pluck('mle_syn')
        ->filter()
        ->toArray();

    $allFormateurIds = array_unique(array_merge($formateursPresentielIds, $formateursSynchroneIds));

    $formateurs = \App\Models\Formateur::where('code_efp', $code_efp)
        ->whereIn('mle', $allFormateurIds)
        ->with([
            'avancementsPresentiel' => function($q) use ($groupeIds) {
                $q->whereIn('groupe_id', $groupeIds)
                  ->with(['module', 'groupe.formation.niveau']);
            }, 
            'avancementsSynchrone' => function($q) use ($groupeIds) {
                $q->whereIn('groupe_id', $groupeIds)
                  ->with(['module', 'groupe.formation.niveau']);
            },
            'etablissement'
        ])
        ->orderBy('nom_formateur')
        ->get();

    return view('administrationetablissement.filieres.show', compact('filiere', 'stats', 'niveaux', 'modules', 'formateurs'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $code_filiere)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            abort(403, 'Accès non autorisé');
        }

        $code_efp = $user->etablissement->code_efp;

        $filiere = Filiere::where('code_efp', $code_efp)
            ->where('code_filiere', $code_filiere)
            ->firstOrFail();

        $secteurs = Secteur::where('code_efp', $code_efp)
            ->orderBy('nom_secteur')
            ->get();

        return view('administrationetablissement.filieres.edit', compact('filiere', 'secteurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $code_filiere)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            abort(403, 'Accès non autorisé');
        }

        $code_efp = $user->etablissement->code_efp;

        $filiere = Filiere::where('code_efp', $code_efp)
            ->where('code_filiere', $code_filiere)
            ->firstOrFail();

        $validated = $request->validate([
            'code_filiere' => [
                'required',
                'string',
                'max:255',
                Rule::unique('filieres')->where('code_efp', $code_efp)->ignore($filiere->id)
            ],
            'nom_filiere' => 'required|string|max:255',
            'secteur_id' => [
                'required',
                'exists:secteurs,id',
                function ($attribute, $value, $fail) use ($code_efp) {
                    $secteur = Secteur::find($value);
                    if (!$secteur || $secteur->code_efp !== $code_efp) {
                        $fail('Le secteur sélectionné n\'appartient pas à votre établissement.');
                    }
                },
            ],
        ], [
            'code_filiere.required' => 'Le code de la filière est obligatoire.',
            'code_filiere.unique' => 'Ce code de filière existe déjà dans votre établissement.',
            'nom_filiere.required' => 'Le nom de la filière est obligatoire.',
            'secteur_id.required' => 'Le secteur est obligatoire.',
            'secteur_id.exists' => 'Le secteur sélectionné n\'existe pas.',
        ]);

        $filiere->update($validated);

        return redirect()
            ->route('administration.etablissement.filieres.index')
            ->with('success', 'Filière modifiée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $code_filiere)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            abort(403, 'Accès non autorisé');
        }

        $code_efp = $user->etablissement->code_efp;

        $filiere = Filiere::where('code_efp', $code_efp)
            ->where('code_filiere', $code_filiere)
            ->firstOrFail();

        // Vérifier s'il y a des formations liées
        if ($filiere->formations()->count() > 0) {
            return redirect()
                ->route('administration.etablissement.filieres.index')
                ->with('error', 'Impossible de supprimer cette filière car elle contient des formations.');
        }

        $filiere->delete();

        return redirect()
            ->route('administration.etablissement.filieres.index')
            ->with('success', 'Filière supprimée avec succès.');
    }
}