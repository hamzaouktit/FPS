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
        
        $filieres = Filiere::with(['secteur', 'etablissement'])
            ->forEtablissement($code_efp)
            ->orderBy('nom_filiere')
            ->paginate(15);

        return view('administrationetablissement.filieres.index', compact('filieres'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $code_efp = $this->getCodeEfp();
        
        // Récupérer uniquement les secteurs de cet établissement
        $secteurs = Secteur::forEtablissement($code_efp)
            ->orderBy('nom_secteur')
            ->get();

        if ($secteurs->isEmpty()) {
            return redirect()->route('administration.etablissement.filieres.index')
                ->with('warning', 'Vous devez d\'abord créer au moins un secteur avant d\'ajouter une filière.');
        }

        return view('administrationetablissement.filieres.create', compact('secteurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $code_efp = $this->getCodeEfp();

        $validated = $request->validate([
            'code_filiere' => [
                'required',
                'string',
                'max:50',
                Rule::unique('filieres')->where(function ($query) use ($code_efp) {
                    return $query->where('code_efp', $code_efp);
                })
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
                }
            ],
        ], [
            'code_filiere.required' => 'Le code de la filière est obligatoire.',
            'code_filiere.unique' => 'Ce code de filière existe déjà dans votre établissement.',
            'nom_filiere.required' => 'Le nom de la filière est obligatoire.',
            'secteur_id.required' => 'Le secteur est obligatoire.',
            'secteur_id.exists' => 'Le secteur sélectionné n\'existe pas.',
        ]);

        // Ajouter automatiquement le code_efp
        $validated['code_efp'] = $code_efp;

        Filiere::create($validated);

        return redirect()->route('administration.etablissement.filieres.index')
            ->with('success', 'Filière créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $code_filiere)
    {
        $code_efp = $this->getCodeEfp();

        $filiere = Filiere::with([
            'secteur',
            'etablissement',
            'formations.niveau',
            'formations.groupes',
            'groupes.avancements',
            'groupes.affectations'
        ])
            ->forEtablissement($code_efp)
            ->where('code_filiere', $code_filiere)
            ->firstOrFail();

        // Statistiques de la filière
        $stats = [
            'total_formations' => $filiere->formations()->count(),
            'total_groupes' => $filiere->groupes()->count(),
            'effectif_total' => $filiere->groupes()->sum('effectif_groupe'),
            'formations_par_niveau' => $filiere->formations()
                ->with('niveau')
                ->get()
                ->groupBy('niveau.niveau')
                ->map->count(),
            'formations_par_type' => $filiere->formations()
                ->get()
                ->groupBy('type_formation')
                ->map->count(),
        ];

        // Récupérer les modules via les formations et groupes
        $modules = \App\Models\Module::whereHas('avancements.groupe.formation', function ($query) use ($filiere) {
            $query->where('filiere_id', $filiere->id);
        })
            ->forEtablissement($code_efp)
            ->distinct()
            ->get();

        // Récupérer les formateurs via les affectations
        $formateurs = \App\Models\Formateur::whereHas('affectations.groupe.formation', function ($query) use ($filiere) {
            $query->where('filiere_id', $filiere->id);
        })
            ->forEtablissement($code_efp)
            ->distinct()
            ->get();

        // Récupérer les niveaux actifs pour cette filière
        $niveaux = \App\Models\Niveau::whereHas('formations', function ($query) use ($filiere) {
            $query->where('filiere_id', $filiere->id);
        })
            ->forEtablissement($code_efp)
            ->distinct()
            ->get();

        return view('administrationetablissement.filieres.show', compact(
            'filiere',
            'stats',
            'modules',
            'formateurs',
            'niveaux'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $code_filiere)
    {
        $code_efp = $this->getCodeEfp();

        $filiere = Filiere::forEtablissement($code_efp)
            ->where('code_filiere', $code_filiere)
            ->firstOrFail();

        $secteurs = Secteur::forEtablissement($code_efp)
            ->orderBy('nom_secteur')
            ->get();

        return view('administrationetablissement.filieres.edit', compact('filiere', 'secteurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $code_filiere)
    {
        $code_efp = $this->getCodeEfp();

        $filiere = Filiere::forEtablissement($code_efp)
            ->where('code_filiere', $code_filiere)
            ->firstOrFail();

        $validated = $request->validate([
            'code_filiere' => [
                'required',
                'string',
                'max:50',
                Rule::unique('filieres')->where(function ($query) use ($code_efp) {
                    return $query->where('code_efp', $code_efp);
                })->ignore($filiere->id)
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
                }
            ],
        ], [
            'code_filiere.required' => 'Le code de la filière est obligatoire.',
            'code_filiere.unique' => 'Ce code de filière existe déjà dans votre établissement.',
            'nom_filiere.required' => 'Le nom de la filière est obligatoire.',
            'secteur_id.required' => 'Le secteur est obligatoire.',
            'secteur_id.exists' => 'Le secteur sélectionné n\'existe pas.',
        ]);

        $filiere->update($validated);

        return redirect()->route('administration.etablissement.filieres.index')
            ->with('success', 'Filière modifiée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $code_filiere)
    {
        $code_efp = $this->getCodeEfp();

        $filiere = Filiere::forEtablissement($code_efp)
            ->where('code_filiere', $code_filiere)
            ->firstOrFail();

        // Vérifier s'il y a des formations liées
        if ($filiere->formations()->count() > 0) {
            return redirect()->route('administration.etablissement.filieres.index')
                ->with('error', 'Impossible de supprimer cette filière car elle contient des formations.');
        }

        $filiere->delete();

        return redirect()->route('administration.etablissement.filieres.index')
            ->with('success', 'Filière supprimée avec succès.');
    }
}