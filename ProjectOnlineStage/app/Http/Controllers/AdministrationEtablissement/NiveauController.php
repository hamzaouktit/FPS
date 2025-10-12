<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        
        $niveaux = Niveau::with(['etablissement'])
            ->forEtablissement($code_efp)
            ->withCount(['formations', 'groupes'])
            ->orderBy('niveau')
            ->paginate(15);

        return view('administrationetablissement.niveaux.index', compact('niveaux'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->getCodeEfp(); // Vérification de l'accès
        
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
                Rule::unique('niveaux')->where(function ($query) use ($code_efp) {
                    return $query->where('code_efp', $code_efp);
                })
            ],
        ], [
            'niveau.required' => 'Le niveau est obligatoire.',
            'niveau.unique' => 'Ce niveau existe déjà dans votre établissement.',
            'niveau.max' => 'Le niveau ne peut pas dépasser 100 caractères.',
        ]);

        // Ajouter automatiquement le code_efp
        $validated['code_efp'] = $code_efp;

        Niveau::create($validated);

        return redirect()->route('administration.etablissement.niveaux.index')
            ->with('success', 'Niveau créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $niveau)
    {
        $code_efp = $this->getCodeEfp();

        $niveauData = Niveau::with([
            'etablissement',
            'formations.filiere.secteur',
            'formations.groupes',
            'groupes'
        ])
            ->forEtablissement($code_efp)
            ->where('niveau', $niveau)
            ->firstOrFail();

        // Statistiques du niveau
        $stats = [
            'total_formations' => $niveauData->formations()->count(),
            'total_groupes' => $niveauData->groupes()->count(),
            'effectif_total' => $niveauData->groupes()->sum('effectif_groupe'),
            'formations_par_filiere' => $niveauData->formations()
                ->with('filiere')
                ->get()
                ->groupBy('filiere.nom_filiere')
                ->map->count(),
            'formations_par_type' => $niveauData->formations()
                ->get()
                ->groupBy('type_formation')
                ->map->count(),
            'formations_par_annee' => $niveauData->formations()
                ->get()
                ->groupBy('annee')
                ->map->count(),
        ];

        // Récupérer les filières associées via les formations
        $filieres = \App\Models\Filiere::whereHas('formations', function ($query) use ($niveauData) {
            $query->where('niveau_id', $niveauData->id);
        })
            ->forEtablissement($code_efp)
            ->with('secteur')
            ->distinct()
            ->get();

        // Récupérer les secteurs via les filières
        $secteurs = \App\Models\Secteur::whereHas('filieres.formations', function ($query) use ($niveauData) {
            $query->where('niveau_id', $niveauData->id);
        })
            ->forEtablissement($code_efp)
            ->distinct()
            ->get();

        // Récupérer les modules via les avancements des groupes
        $modules = \App\Models\Module::whereHas('avancements.groupe.formation', function ($query) use ($niveauData) {
            $query->where('niveau_id', $niveauData->id);
        })
            ->forEtablissement($code_efp)
            ->distinct()
            ->withCount('avancements')
            ->get();

        // Récupérer les formateurs via les affectations
        $formateurs = \App\Models\Formateur::whereHas('affectations.groupe.formation', function ($query) use ($niveauData) {
            $query->where('niveau_id', $niveauData->id);
        })
            ->forEtablissement($code_efp)
            ->distinct()
            ->get();

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
    public function edit(string $niveau)
    {
        $code_efp = $this->getCodeEfp();

        $niveauData = Niveau::forEtablissement($code_efp)
            ->where('niveau', $niveau)
            ->firstOrFail();

        return view('administrationetablissement.niveaux.edit', compact('niveauData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $niveau)
    {
        $code_efp = $this->getCodeEfp();

        $niveauData = Niveau::forEtablissement($code_efp)
            ->where('niveau', $niveau)
            ->firstOrFail();

        $validated = $request->validate([
            'niveau' => [
                'required',
                'string',
                'max:100',
                Rule::unique('niveaux')->where(function ($query) use ($code_efp) {
                    return $query->where('code_efp', $code_efp);
                })->ignore($niveauData->id)
            ],
        ], [
            'niveau.required' => 'Le niveau est obligatoire.',
            'niveau.unique' => 'Ce niveau existe déjà dans votre établissement.',
            'niveau.max' => 'Le niveau ne peut pas dépasser 100 caractères.',
        ]);

        $niveauData->update($validated);

        return redirect()->route('administration.etablissement.niveaux.index')
            ->with('success', 'Niveau modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $niveau)
    {
        $code_efp = $this->getCodeEfp();

        $niveauData = Niveau::forEtablissement($code_efp)
            ->where('niveau', $niveau)
            ->firstOrFail();

        // Vérifier s'il y a des formations liées
        if ($niveauData->formations()->count() > 0) {
            return redirect()->route('administration.etablissement.niveaux.index')
                ->with('error', 'Impossible de supprimer ce niveau car il contient des formations.');
        }

        $niveauData->delete();

        return redirect()->route('administration.etablissement.niveaux.index')
            ->with('success', 'Niveau supprimé avec succès.');
    }
}