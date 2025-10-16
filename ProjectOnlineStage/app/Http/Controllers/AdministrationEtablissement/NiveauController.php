<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Models\Secteur;
use App\Models\Module;
use App\Models\Formateur;
use App\Models\Groupe;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NiveauController extends Controller
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
     * Display a listing of the resource.
     */
    public function index()
    {
        $code_efp = $this->getCodeEfp();
        
        // Récupérer uniquement les niveaux de cet établissement
        $niveaux = Niveau::where('code_efp', $code_efp)
            ->with(['formations' => function($query) use ($code_efp) {
                $query->where('code_efp', $code_efp);
            }])
            ->get()
            ->map(function($niveau) use ($code_efp) {
                // Compter les formations de cet établissement
                $niveau->formations_count = Formation::where('niveau_id', $niveau->id)
                    ->where('code_efp', $code_efp)
                    ->count();
                
                // Compter les groupes de cet établissement pour ce niveau
                $niveau->groupes_count = Groupe::where('code_efp', $code_efp)
                    ->whereHas('formation', function($q) use ($niveau) {
                        $q->where('niveau_id', $niveau->id);
                    })
                    ->count();
                
                return $niveau;
            })
            ->sortByDesc('created_at');

        // Paginer manuellement
        $page = request()->get('page', 1);
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        
        $niveauxPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $niveaux->slice($offset, $perPage)->values(),
            $niveaux->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('administrationetablissement.niveaux.index', ['niveaux' => $niveauxPaginated]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->getCodeEfp();
        return view('administrationetablissement.niveaux.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $code_efp = $this->getCodeEfp();

        $validated = $request->validate([
            'niveau' => [
                'required',
                'string',
                'max:100',
                Rule::unique('niveaux', 'nom')->where(function ($query) use ($code_efp) {
                    return $query->where('code_efp', $code_efp);
                })
            ],
        ], [
            'niveau.required' => 'Le niveau est obligatoire.',
            'niveau.unique' => 'Ce niveau existe déjà dans votre établissement.',
            'niveau.max' => 'Le niveau ne peut pas dépasser 100 caractères.',
        ]);

        Niveau::create([
            'nom' => $validated['niveau'],
            'code_efp' => $code_efp
        ]);

        return redirect()->route('administration.etablissement.niveaux.index')
            ->with('success', 'Niveau créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $code_efp = $this->getCodeEfp();

        // Récupérer le niveau de cet établissement avec ses relations
        $niveauData = Niveau::where('code_efp', $code_efp)
            ->where('id', $id)
            ->with('etablissement')
            ->firstOrFail();

        // Récupérer toutes les formations de ce niveau dans cet établissement
        $formations = Formation::where('niveau_id', $niveauData->id)
            ->where('code_efp', $code_efp)
            ->with(['filiere.secteur', 'groupes'])
            ->get();

        // Récupérer tous les groupes de cet établissement pour ce niveau
        $groupesEtablissement = Groupe::where('code_efp', $code_efp)
            ->whereHas('formation', function($q) use ($niveauData) {
                $q->where('niveau_id', $niveauData->id);
            })
            ->with(['formation.filiere.secteur', 'filiere'])
            ->get();

        $groupeIds = $groupesEtablissement->pluck('id')->toArray();

        // Statistiques détaillées
        $stats = [
            'total_formations' => $formations->count(),
            'total_groupes' => $groupesEtablissement->count(),
            'effectif_total' => $groupesEtablissement->sum('effectif_groupe'),
            'formations_par_filiere' => $formations->groupBy('filiere.nom')
                ->map->count()
                ->toArray(),
            'formations_par_type' => $formations->groupBy('type')
                ->map->count()
                ->toArray(),
            'formations_par_annee' => $formations->groupBy('annee')
                ->map->count()
                ->toArray(),
        ];

        // Secteurs concernés
        $secteurs = Secteur::whereHas('filieres.formations', function($q) use ($niveauData, $code_efp) {
            $q->where('niveau_id', $niveauData->id)
              ->where('code_efp', $code_efp);
        })->get();

        // Modules utilisés dans ce niveau
        $modules = Module::whereHas('affectations', function($q) use ($groupeIds) {
            $q->whereIn('groupe_id', $groupeIds);
        })
        ->withCount(['affectations' => function($q) use ($groupeIds) {
            $q->whereIn('groupe_id', $groupeIds);
        }])
        ->get();

        // Formateurs intervenant dans ce niveau
        $formateurs = Formateur::where('code_efp', $code_efp)
            ->where(function($query) use ($groupeIds) {
                $query->whereIn('mle', function($q) use ($groupeIds) {
                    $q->select('mle_affecte_presentiel')
                        ->from('affectations')
                        ->whereIn('groupe_id', $groupeIds)
                        ->whereNotNull('mle_affecte_presentiel');
                })->orWhereIn('mle', function($q) use ($groupeIds) {
                    $q->select('mle_affecte_syn')
                        ->from('affectations')
                        ->whereIn('groupe_id', $groupeIds)
                        ->whereNotNull('mle_affecte_syn');
                });
            })
            ->distinct()
            ->get();

        // Préparer les données structurées pour la vue
        $niveauData->groupes = $groupesEtablissement;
        
        // Structurer les formations avec leurs groupes
        $niveauData->formations_structured = $formations->map(function($formation) {
            return (object)[
                'formation' => $formation,
                'filiere' => $formation->filiere,
                'secteur' => $formation->filiere->secteur,
                'groupes' => $formation->groupes,
                'total_effectif' => $formation->groupes->sum('effectif_groupe')
            ];
        });

        return view('administrationetablissement.niveaux.show', compact(
            'niveauData',
            'stats',
            'formations',
            'secteurs',
            'modules',
            'formateurs'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $code_efp = $this->getCodeEfp();

        // Récupérer le niveau de cet établissement uniquement
        $niveauData = Niveau::where('code_efp', $code_efp)
            ->where('id', $id)
            ->firstOrFail();

        // Compter les formations et groupes
        $niveauData->formations_count = Formation::where('niveau_id', $niveauData->id)
            ->where('code_efp', $code_efp)
            ->count();

        $niveauData->groupes_count = Groupe::where('code_efp', $code_efp)
            ->whereHas('formation', function($q) use ($niveauData) {
                $q->where('niveau_id', $niveauData->id);
            })
            ->count();

        // Calculer l'effectif total
        $niveauData->effectif_total = Groupe::where('code_efp', $code_efp)
            ->whereHas('formation', function($q) use ($niveauData) {
                $q->where('niveau_id', $niveauData->id);
            })
            ->sum('effectif_groupe');

        return view('administrationetablissement.niveaux.edit', compact('niveauData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $code_efp = $this->getCodeEfp();

        // Récupérer le niveau de cet établissement uniquement
        $niveauData = Niveau::where('code_efp', $code_efp)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'niveau' => [
                'required',
                'string',
                'max:100',
                Rule::unique('niveaux', 'nom')
                    ->ignore($niveauData->id)
                    ->where(function ($query) use ($code_efp) {
                        return $query->where('code_efp', $code_efp);
                    })
            ],
        ], [
            'niveau.required' => 'Le niveau est obligatoire.',
            'niveau.unique' => 'Ce niveau existe déjà dans votre établissement.',
            'niveau.max' => 'Le niveau ne peut pas dépasser 100 caractères.',
        ]);

        $niveauData->update([
            'nom' => $validated['niveau']
        ]);

        return redirect()->route('administration.etablissement.niveaux.index')
            ->with('success', 'Niveau modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $code_efp = $this->getCodeEfp();

        // Récupérer le niveau de cet établissement uniquement
        $niveauData = Niveau::where('code_efp', $code_efp)
            ->where('id', $id)
            ->firstOrFail();

        // Vérifier s'il y a des groupes dans cet établissement
        $hasGroupes = Groupe::where('code_efp', $code_efp)
            ->whereHas('formation', function($q) use ($niveauData) {
                $q->where('niveau_id', $niveauData->id);
            })
            ->exists();

        if ($hasGroupes) {
            return redirect()->route('administration.etablissement.niveaux.index')
                ->with('error', 'Impossible de supprimer ce niveau car il contient des groupes.');
        }

        // Vérifier s'il y a des formations liées dans cet établissement
        $hasFormations = Formation::where('niveau_id', $niveauData->id)
            ->where('code_efp', $code_efp)
            ->exists();

        if ($hasFormations) {
            return redirect()->route('administration.etablissement.niveaux.index')
                ->with('error', 'Impossible de supprimer ce niveau car il contient des formations.');
        }

        // Supprimer le niveau
        $niveauData->delete();
        
        return redirect()->route('administration.etablissement.niveaux.index')
            ->with('success', 'Niveau supprimé avec succès.');
    }
}