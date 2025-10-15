<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Models\Secteur;
use App\Models\Module;
use App\Models\Formateur;
use App\Models\Groupe;
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
        
        // Récupérer uniquement les niveaux de cet établissement avec leurs relations
        $niveaux = Niveau::where('code_efp', $code_efp)
            ->with(['filieres' => function($query) use ($code_efp) {
                $query->where('code_efp', $code_efp);
            }])
            ->get()
            ->map(function($niveau) use ($code_efp) {
                // Compter les filières uniques de cet établissement
                $niveau->filieres_count = Filiere::where('niveau_id', $niveau->id)
                    ->where('code_efp', $code_efp)
                    ->count();
                
                // Compter les groupes de cet établissement pour ce niveau
                $niveau->groupes_count = Groupe::where('code_efp', $code_efp)
                    ->whereHas('filiere', function($q) use ($niveau) {
                        $q->where('niveau_id', $niveau->id);
                    })
                    ->count();
                
                // Adapter le nom pour la vue (formations_count = filieres_count)
                $niveau->formations_count = $niveau->filieres_count;
                
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
            'code' => strtoupper(substr($validated['niveau'], 0, 3)),
            'nom' => $validated['niveau'],
            'code_efp' => $code_efp
        ]);

        return redirect()->route('administration.etablissement.niveaux.index')
            ->with('success', 'Niveau créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $code)
    {
        $code_efp = $this->getCodeEfp();

        // Récupérer le niveau de cet établissement avec ses relations
        $niveauData = Niveau::where('code_efp', $code_efp)
            ->where(function($query) use ($code) {
                $query->where('code', $code)->orWhere('nom', $code);
            })
            ->with('etablissement')
            ->firstOrFail();

        // Récupérer toutes les filières de ce niveau dans cet établissement
        $filieres = Filiere::where('niveau_id', $niveauData->id)
            ->where('code_efp', $code_efp)
            ->with(['secteur', 'groupes' => function($q) use ($code_efp) {
                $q->where('code_efp', $code_efp)
                  ->with('formation');
            }])
            ->get();

        // Récupérer tous les groupes de cet établissement pour ce niveau
        $groupesEtablissement = Groupe::where('code_efp', $code_efp)
            ->whereHas('filiere', function($q) use ($niveauData) {
                $q->where('niveau_id', $niveauData->id);
            })
            ->with(['filiere.secteur', 'formation'])
            ->get();

        $groupeIds = $groupesEtablissement->pluck('id')->toArray();

        // Statistiques détaillées
        $stats = [
            'total_formations' => $filieres->count(),
            'total_groupes' => $groupesEtablissement->count(),
            'effectif_total' => $groupesEtablissement->sum('effectif'),
            'formations_par_filiere' => $filieres->mapWithKeys(function($filiere) {
                return [$filiere->nom => $filiere->groupes->count()];
            })->toArray(),
            'formations_par_type' => $groupesEtablissement
                ->groupBy('formation.type')
                ->map->count()
                ->toArray(),
            'formations_par_annee' => $groupesEtablissement
                ->groupBy('annee_formation')
                ->map->count()
                ->toArray(),
        ];

        // Secteurs concernés
        $secteurs = Secteur::whereHas('filieres', function($q) use ($niveauData, $code_efp) {
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
        ->get()
        ->map(function($module) {
            $module->avancements_count = $module->affectations_count;
            return $module;
        });

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
        
        // Structurer les formations par filière et année
        $niveauData->formations = $filieres->flatMap(function($filiere) {
            return $filiere->groupes->map(function($groupe) use ($filiere) {
                return (object)[
                    'annee' => $groupe->annee_formation,
                    'filiere' => $filiere,
                    'secteur' => $filiere->secteur,
                    'type_formation' => $groupe->formation->type ?? 'N/A',
                    'mode' => $groupe->formation->mode ?? 'N/A',
                    'creneau' => $groupe->formation->creneau ?? 'N/A',
                    'groupe' => $groupe,
                    'effectif' => $groupe->effectif
                ];
            });
        })->sortByDesc('annee');

        return view('administrationetablissement.niveaux.show', compact(
            'niveauData',
            'stats',
            'filieres',
            'secteurs',
            'modules',
            'formateurs'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $code)
    {
        $code_efp = $this->getCodeEfp();

        // Récupérer le niveau de cet établissement uniquement
        $niveauData = Niveau::where('code_efp', $code_efp)
            ->where(function($query) use ($code) {
                $query->where('code', $code)->orWhere('nom', $code);
            })
            ->firstOrFail();

        // Compter les filières et groupes
        $niveauData->formations_count = Filiere::where('niveau_id', $niveauData->id)
            ->where('code_efp', $code_efp)
            ->count();

        $niveauData->groupes_count = Groupe::where('code_efp', $code_efp)
            ->whereHas('filiere', function($q) use ($niveauData) {
                $q->where('niveau_id', $niveauData->id);
            })
            ->count();

        // Calculer l'effectif total
        $niveauData->effectif_total = Groupe::where('code_efp', $code_efp)
            ->whereHas('filiere', function($q) use ($niveauData) {
                $q->where('niveau_id', $niveauData->id);
            })
            ->sum('effectif');

        return view('administrationetablissement.niveaux.edit', compact('niveauData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $code)
    {
        $code_efp = $this->getCodeEfp();

        // Récupérer le niveau de cet établissement uniquement
        $niveauData = Niveau::where('code_efp', $code_efp)
            ->where(function($query) use ($code) {
                $query->where('code', $code)->orWhere('nom', $code);
            })
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
            'nom' => $validated['niveau'],
            'code' => strtoupper(substr($validated['niveau'], 0, 3))
        ]);

        return redirect()->route('administration.etablissement.niveaux.index')
            ->with('success', 'Niveau modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $code)
    {
        $code_efp = $this->getCodeEfp();

        // Récupérer le niveau de cet établissement uniquement
        $niveauData = Niveau::where('code_efp', $code_efp)
            ->where(function($query) use ($code) {
                $query->where('code', $code)->orWhere('nom', $code);
            })
            ->firstOrFail();

        // Vérifier s'il y a des groupes dans cet établissement
        $hasGroupes = Groupe::where('code_efp', $code_efp)
            ->whereHas('filiere', function($q) use ($niveauData) {
                $q->where('niveau_id', $niveauData->id);
            })
            ->exists();

        if ($hasGroupes) {
            return redirect()->route('administration.etablissement.niveaux.index')
                ->with('error', 'Impossible de supprimer ce niveau car il contient des groupes.');
        }

        // Vérifier s'il y a des filières liées dans cet établissement
        $hasFilieres = Filiere::where('niveau_id', $niveauData->id)
            ->where('code_efp', $code_efp)
            ->exists();

        if ($hasFilieres) {
            return redirect()->route('administration.etablissement.niveaux.index')
                ->with('error', 'Impossible de supprimer ce niveau car il contient des filières.');
        }

        // Supprimer le niveau
        $niveauData->delete();
        
        return redirect()->route('administration.etablissement.niveaux.index')
            ->with('success', 'Niveau supprimé avec succès.');
    }
}