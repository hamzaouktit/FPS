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
    private function getCodeEfp()
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            abort(403, 'Accès non autorisé');
        }
        
        return $user->etablissement->code_efp;
    }

    public function index(Request $request)
    {
        $code_efp = $this->getCodeEfp();
        
        // Construction de la requête avec filtres
        $query = Niveau::where('code_efp', $code_efp)
            ->with(['formations' => function($query) use ($code_efp) {
                $query->where('code_efp', $code_efp);
            }]);

        // Filtre par recherche (nom du niveau)
        if ($request->filled('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%');
        }

        // Filtre par date de création
        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        // Filtre par nombre de formations
        if ($request->filled('formations_min')) {
            $query->has('formations', '>=', $request->formations_min);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['created_at', 'nom', 'id'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'created_at';
        }
        
        $query->orderBy($sortBy, $sortOrder);

        $niveaux = $query->get()->map(function($niveau) use ($code_efp) {
            // Compter les formations
            $niveau->formations_count = Formation::where('niveau_id', $niveau->id)
                ->where('code_efp', $code_efp)
                ->count();
            
            // Compter les groupes
            $niveau->groupes_count = Groupe::where('code_efp', $code_efp)
                ->whereHas('formation', function($q) use ($niveau) {
                    $q->where('niveau_id', $niveau->id);
                })
                ->count();
            
            // Calculer l'effectif total
            $niveau->effectif_total = Groupe::where('code_efp', $code_efp)
                ->whereHas('formation', function($q) use ($niveau) {
                    $q->where('niveau_id', $niveau->id);
                })
                ->sum('effectif_groupe');
            
            return $niveau;
        });

        // Filtre par nombre de groupes
        if ($request->filled('groupes_min')) {
            $niveaux = $niveaux->filter(function($niveau) use ($request) {
                return $niveau->groupes_count >= $request->groupes_min;
            });
        }

        // Statistiques globales pour les filtres
        $stats = [
            'total_niveaux' => $niveaux->count(),
            'total_formations' => $niveaux->sum('formations_count'),
            'total_groupes' => $niveaux->sum('groupes_count'),
            'effectif_total' => $niveaux->sum('effectif_total')
        ];

        // Pagination manuelle
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);
        $offset = ($page - 1) * $perPage;
        
        $niveauxPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $niveaux->slice($offset, $perPage)->values(),
            $niveaux->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('administrationetablissement.niveaux.index', [
            'niveaux' => $niveauxPaginated,
            'stats' => $stats,
            'filters' => $request->all()
        ]);
    }

    public function show(Request $request, string $id)
    {
        $code_efp = $this->getCodeEfp();

        $niveauData = Niveau::where('code_efp', $code_efp)
            ->where('id', $id)
            ->with('etablissement')
            ->firstOrFail();

        // Construction de la requête formations avec filtres
        $formationsQuery = Formation::where('niveau_id', $niveauData->id)
            ->where('code_efp', $code_efp)
            ->with(['filiere.secteur', 'groupes']);

        // Filtres pour formations
        if ($request->filled('annee')) {
            $formationsQuery->where('annee', $request->annee);
        }
        if ($request->filled('type')) {
            $formationsQuery->where('type', $request->type);
        }
        if ($request->filled('mode')) {
            $formationsQuery->where('mode', $request->mode);
        }
        if ($request->filled('creneau')) {
            $formationsQuery->where('creneau', $request->creneau);
        }
        if ($request->filled('secteur_id')) {
            $formationsQuery->whereHas('filiere', function($q) use ($request) {
                $q->where('secteur_id', $request->secteur_id);
            });
        }
        if ($request->filled('filiere_id')) {
            $formationsQuery->where('filiere_id', $request->filiere_id);
        }

        $formations = $formationsQuery->get();

        // Construction de la requête groupes avec filtres
        $groupesQuery = Groupe::where('code_efp', $code_efp)
            ->whereHas('formation', function($q) use ($niveauData) {
                $q->where('niveau_id', $niveauData->id);
            })
            ->with(['formation.filiere.secteur', 'filiere']);

        // Filtres pour groupes
        if ($request->filled('groupe_statut')) {
            $groupesQuery->where('statut', $request->groupe_statut);
        }
        if ($request->filled('groupe_annee')) {
            $groupesQuery->where('annee_formation', $request->groupe_annee);
        }
        if ($request->filled('groupe_filiere')) {
            $groupesQuery->where('filiere_id', $request->groupe_filiere);
        }
        if ($request->filled('groupe_search')) {
            $groupesQuery->where('code_groupe', 'like', '%' . $request->groupe_search . '%');
        }

        $groupesEtablissement = $groupesQuery->get();
        $groupeIds = $groupesEtablissement->pluck('id')->toArray();

        // Statistiques détaillées
        $stats = [
            'total_formations' => $formations->count(),
            'total_groupes' => $groupesEtablissement->count(),
            'effectif_total' => $groupesEtablissement->sum('effectif_groupe'),
            'formations_par_filiere' => $formations->groupBy('filiere.nom_filiere')->map->count()->toArray(),
            'formations_par_type' => $formations->groupBy('type')->map->count()->toArray(),
            'formations_par_annee' => $formations->groupBy('annee')->map->count()->toArray(),
        ];

        // Secteurs concernés
        $secteurs = Secteur::whereHas('filieres.formations', function($q) use ($niveauData, $code_efp) {
            $q->where('niveau_id', $niveauData->id)->where('code_efp', $code_efp);
        })->get();

        // Filières pour les filtres
        $filieres = Filiere::where('code_efp', $code_efp)
            ->whereHas('formations', function($q) use ($niveauData) {
                $q->where('niveau_id', $niveauData->id);
            })
            ->with('secteur')
            ->get();

        // Modules
        $modulesQuery = Module::whereHas('affectations', function($q) use ($groupeIds) {
            $q->whereIn('groupe_id', $groupeIds);
        })
        ->withCount(['affectations' => function($q) use ($groupeIds) {
            $q->whereIn('groupe_id', $groupeIds);
        }]);

        if ($request->filled('module_search')) {
            $modulesQuery->where(function($q) use ($request) {
                $q->where('nom_module', 'like', '%' . $request->module_search . '%')
                  ->orWhere('code_module', 'like', '%' . $request->module_search . '%');
            });
        }

        $modules = $modulesQuery->get();

        // Formateurs
        $formateursQuery = Formateur::where('code_efp', $code_efp)
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
            ->distinct();

        if ($request->filled('formateur_type')) {
            $formateursQuery->where('type', $request->formateur_type);
        }
        if ($request->filled('formateur_search')) {
            $formateursQuery->where('nom_complet', 'like', '%' . $request->formateur_search . '%');
        }

        $formateurs = $formateursQuery->get();

        // Données pour les filtres
        $filterData = [
            'annees' => Formation::where('code_efp', $code_efp)
                ->where('niveau_id', $niveauData->id)
                ->distinct()
                ->pluck('annee')
                ->sort()
                ->values(),
            'types' => ['Diplômante', 'Qualifiante', 'PP'],
            'modes' => ['Résidentiel', 'Alterné'],
            'creneaux' => ['CDJ', 'CDS'],
            'statuts' => ['Actif', 'Inactif']
        ];

        $niveauData->groupes = $groupesEtablissement;
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
            'filieres',
            'modules',
            'formateurs',
            'filterData'
        ));
    }

    public function create()
    {
        $this->getCodeEfp();
        return view('administrationetablissement.niveaux.create');
    }

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

    public function edit(string $id)
    {
        $code_efp = $this->getCodeEfp();

        $niveauData = Niveau::where('code_efp', $code_efp)
            ->where('id', $id)
            ->firstOrFail();

        $niveauData->formations_count = Formation::where('niveau_id', $niveauData->id)
            ->where('code_efp', $code_efp)
            ->count();

        $niveauData->groupes_count = Groupe::where('code_efp', $code_efp)
            ->whereHas('formation', function($q) use ($niveauData) {
                $q->where('niveau_id', $niveauData->id);
            })
            ->count();

        $niveauData->effectif_total = Groupe::where('code_efp', $code_efp)
            ->whereHas('formation', function($q) use ($niveauData) {
                $q->where('niveau_id', $niveauData->id);
            })
            ->sum('effectif_groupe');

        return view('administrationetablissement.niveaux.edit', compact('niveauData'));
    }

    public function update(Request $request, string $id)
    {
        $code_efp = $this->getCodeEfp();

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

    public function destroy(string $id)
    {
        $code_efp = $this->getCodeEfp();

        $niveauData = Niveau::where('code_efp', $code_efp)
            ->where('id', $id)
            ->firstOrFail();

        $hasGroupes = Groupe::where('code_efp', $code_efp)
            ->whereHas('formation', function($q) use ($niveauData) {
                $q->where('niveau_id', $niveauData->id);
            })
            ->exists();

        if ($hasGroupes) {
            return redirect()->route('administration.etablissement.niveaux.index')
                ->with('error', 'Impossible de supprimer ce niveau car il contient des groupes.');
        }

        $hasFormations = Formation::where('niveau_id', $niveauData->id)
            ->where('code_efp', $code_efp)
            ->exists();

        if ($hasFormations) {
            return redirect()->route('administration.etablissement.niveaux.index')
                ->with('error', 'Impossible de supprimer ce niveau car il contient des formations.');
        }

        $niveauData->delete();
        
        return redirect()->route('administration.etablissement.niveaux.index')
            ->with('success', 'Niveau supprimé avec succès.');
    }
}