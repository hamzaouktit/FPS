<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Groupe;
use App\Models\Filiere;
use App\Models\Formation;
use App\Models\Niveau;
use App\Models\Secteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GroupeController extends Controller
{
    /**
     * Récupérer le code EFP de l'utilisateur connecté
     */
    private function getCodeEfp()
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            abort(403, 'Accès non autorisé');
        }
        
        return $user->etablissement->code_efp;
    }

    /**
     * Display a listing of the resource with filters.
     */
    public function index(Request $request)
    {
        $code_efp = $this->getCodeEfp();
        
        // Construction de la requête de base
        $query = Groupe::where('code_efp', $code_efp)
            ->with(['filiere.secteur', 'formation.niveau', 'formation.filiere']);
        
        // Filtrage par code groupe
        if ($request->filled('code_groupe')) {
            $query->where('code_groupe', 'like', '%' . $request->code_groupe . '%');
        }
        
        // Filtrage par secteur
        if ($request->filled('secteur_id')) {
            $query->whereHas('filiere.secteur', function($q) use ($request) {
                $q->where('id', $request->secteur_id);
            });
        }
        
        // Filtrage par filière
        if ($request->filled('filiere_id')) {
            $query->where('filiere_id', $request->filiere_id);
        }
        
        // Filtrage par niveau
        if ($request->filled('niveau_id')) {
            $query->whereHas('formation', function($q) use ($request) {
                $q->where('niveau_id', $request->niveau_id);
            });
        }
        
        // Filtrage par année de formation
        if ($request->filled('annee_formation')) {
            $query->where('annee_formation', $request->annee_formation);
        }
        
        // Filtrage par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        
        // Filtrage par type de formation
        if ($request->filled('type_formation')) {
            $query->whereHas('formation', function($q) use ($request) {
                $q->where('type', $request->type_formation);
            });
        }
        
        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['code_groupe', 'annee_formation', 'effectif_groupe', 'statut', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $groupes = $query->paginate(15)->withQueryString();
        
        // Récupération des données pour les filtres
        $secteurs = Secteur::where('code_efp', $code_efp)->orderBy('nom_secteur')->get();
        $filieres = Filiere::where('code_efp', $code_efp)->orderBy('nom_filiere')->get();
        $niveaux = Niveau::where('code_efp', $code_efp)->orderBy('nom')->get();
        
        // Types de formation uniques
        $typesFormation = Formation::where('code_efp', $code_efp)
            ->distinct()
            ->pluck('type')
            ->filter()
            ->sort()
            ->values();
        
        return view('administrationetablissement.groupes.index', compact(
            'groupes', 
            'secteurs', 
            'filieres', 
            'niveaux', 
            'typesFormation'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $code_efp = $this->getCodeEfp();
        
        $filieres = Filiere::where('code_efp', $code_efp)->get();
        $formations = Formation::where('code_efp', $code_efp)->get();
        $niveaux = Niveau::where('code_efp', $code_efp)->get();
        $secteurs = Secteur::where('code_efp', $code_efp)->get();

        return view('administrationetablissement.groupes.create', compact('filieres', 'formations', 'niveaux', 'secteurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $code_efp = $this->getCodeEfp();

        $validated = $request->validate([
            'code_groupe' => [
                'required',
                'string',
                'max:255',
                Rule::unique('groupes', 'code_groupe')->where(function ($query) use ($code_efp) {
                    return $query->where('code_efp', $code_efp);
                })
            ],
            'effectif_groupe' => 'required|integer|min:0',
            'statut' => 'required|string|in:Actif,Inactif',
            'sous_groupe' => 'required|string|max:255',
            'statut_sous_groupe' => 'required|string|in:Actif,Inactif',
            'annee_formation' => 'required|integer|min:1|max:2',
            'filiere_id' => 'required|exists:filieres,id',
            'formation_id' => 'required|exists:formations,id',
        ], [
            'code_groupe.required' => 'Le code du groupe est obligatoire.',
            'code_groupe.unique' => 'Ce code de groupe existe déjà dans votre établissement.',
            'effectif_groupe.required' => 'L\'effectif du groupe est obligatoire.',
            'sous_groupe.required' => 'Le sous-groupe est obligatoire.',
        ]);

        // Vérifier que la filière et la formation appartiennent à l'établissement
        $filiere = Filiere::where('id', $validated['filiere_id'])->where('code_efp', $code_efp)->first();
        $formation = Formation::where('id', $validated['formation_id'])->where('code_efp', $code_efp)->first();

        if (!$filiere || !$formation) {
            return redirect()->back()
                ->with('error', 'La filière ou la formation sélectionnée n\'appartient pas à votre établissement.')
                ->withInput();
        }

        Groupe::create(array_merge($validated, [
            'code_efp' => $code_efp
        ]));

        return redirect()->route('administration.etablissement.groupes.index')
            ->with('success', 'Groupe créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $code_efp = $this->getCodeEfp();

        $groupe = Groupe::where('code_efp', $code_efp)
            ->where('id', $id)
            ->with([
                'filiere.secteur', 
                'formation.niveau', 
                'formation.filiere',
                'affectations.module',
                'affectations.formateurPresentiel',
                'affectations.formateurSyn',
                'affectations.avancement'
            ])
            ->firstOrFail();

        // Calculer les statistiques
        $stats = [
            'total_affectations' => $groupe->affectations->count(),
            'total_modules' => $groupe->affectations->unique('module_id')->count(),
            'total_formateurs' => $groupe->affectations->filter(function($affectation) {
                return $affectation->mle_affecte_presentiel || $affectation->mle_affecte_syn;
            })->unique(function($affectation) {
                return $affectation->mle_affecte_presentiel . '-' . $affectation->mle_affecte_syn;
            })->count(),
            'mh_totale_affectee' => $groupe->affectations->sum('mh_affectee_globale'),
            'mh_totale_realisee' => $groupe->affectations->sum(function($affectation) {
                return $affectation->avancement->mh_realisee_globale ?? 0;
            })
        ];

        return view('administrationetablissement.groupes.show', compact('groupe', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $code_efp = $this->getCodeEfp();

        $groupe = Groupe::where('code_efp', $code_efp)
            ->where('id', $id)
            ->firstOrFail();

        $filieres = Filiere::where('code_efp', $code_efp)->get();
        $formations = Formation::where('code_efp', $code_efp)->get();
        $niveaux = Niveau::where('code_efp', $code_efp)->get();
        $secteurs = Secteur::where('code_efp', $code_efp)->get();

        return view('administrationetablissement.groupes.edit', compact('groupe', 'filieres', 'formations', 'niveaux', 'secteurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $code_efp = $this->getCodeEfp();

        $groupe = Groupe::where('code_efp', $code_efp)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'code_groupe' => [
                'required',
                'string',
                'max:255',
                Rule::unique('groupes', 'code_groupe')
                    ->ignore($groupe->id)
                    ->where(function ($query) use ($code_efp) {
                        return $query->where('code_efp', $code_efp);
                    })
            ],
            'effectif_groupe' => 'required|integer|min:0',
            'statut' => 'required|string|in:Actif,Inactif',
            'sous_groupe' => 'required|string|max:255',
            'statut_sous_groupe' => 'required|string|in:Actif,Inactif',
            'annee_formation' => 'required|integer|min:1|max:2',
            'filiere_id' => 'required|exists:filieres,id',
            'formation_id' => 'required|exists:formations,id',
        ], [
            'code_groupe.required' => 'Le code du groupe est obligatoire.',
            'code_groupe.unique' => 'Ce code de groupe existe déjà dans votre établissement.',
            'effectif_groupe.required' => 'L\'effectif du groupe est obligatoire.',
            'sous_groupe.required' => 'Le sous-groupe est obligatoire.',
        ]);

        // Vérifier que la filière et la formation appartiennent à l'établissement
        $filiere = Filiere::where('id', $validated['filiere_id'])->where('code_efp', $code_efp)->first();
        $formation = Formation::where('id', $validated['formation_id'])->where('code_efp', $code_efp)->first();

        if (!$filiere || !$formation) {
            return redirect()->back()
                ->with('error', 'La filière ou la formation sélectionnée n\'appartient pas à votre établissement.')
                ->withInput();
        }

        $groupe->update($validated);

        return redirect()->route('administration.etablissement.groupes.index')
            ->with('success', 'Groupe modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $code_efp = $this->getCodeEfp();

        $groupe = Groupe::where('code_efp', $code_efp)
            ->where('id', $id)
            ->firstOrFail();

        // Vérifier s'il y a des affectations liées
        if ($groupe->affectations()->exists()) {
            return redirect()->route('administration.etablissement.groupes.index')
                ->with('error', 'Impossible de supprimer ce groupe car il contient des affectations.');
        }

        $groupe->delete();

        return redirect()->route('administration.etablissement.groupes.index')
            ->with('success', 'Groupe supprimé avec succès.');
    }
}