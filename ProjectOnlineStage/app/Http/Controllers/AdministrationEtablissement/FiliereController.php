<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use App\Models\Secteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FiliereController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Filiere::with('secteur')->withCount('formations');

        // Filtre par secteur
        if ($request->filled('secteur')) {
            $query->where('nom_secteur', $request->secteur);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code_filiere', 'like', "%{$search}%")
                  ->orWhere('nom_filiere', 'like', "%{$search}%");
            });
        }

        $filieres = $query->orderBy('nom_filiere')->paginate(15);
        $secteurs = Secteur::orderBy('nom_secteur')->get();

        return view('administrationetablissement.filieres.index', compact('filieres', 'secteurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $secteurs = Secteur::orderBy('nom_secteur')->get();
        return view('administrationetablissement.filieres.create', compact('secteurs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code_filiere' => 'required|string|max:50|unique:filieres,code_filiere',
            'nom_filiere' => 'required|string|max:255',
            'nom_secteur' => 'required|string|exists:secteurs,nom_secteur'
        ], [
            'code_filiere.required' => 'Le code de la filière est obligatoire.',
            'code_filiere.unique' => 'Ce code de filière existe déjà.',
            'nom_filiere.required' => 'Le nom de la filière est obligatoire.',
            'nom_secteur.required' => 'Le secteur est obligatoire.',
            'nom_secteur.exists' => 'Le secteur sélectionné n\'existe pas.'
        ]);

        try {
            Filiere::create([
                'code_filiere' => strtoupper($request->code_filiere),
                'nom_filiere' => $request->nom_filiere,
                'nom_secteur' => $request->nom_secteur
            ]);

            return redirect()
                ->route('administration.etablissement.filieres.index')
                ->with('success', 'Filière créée avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création de la filière: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $code_filiere)
    {
        $filiere = Filiere::where('code_filiere', $code_filiere)
            ->with('secteur')
            ->withCount('formations')
            ->firstOrFail();

        // Récupérer les formations associées
        $formations = $filiere->formations()
            ->with(['etablissement', 'niveau'])
            ->withCount('groupes')
            ->orderBy('annee', 'desc')
            ->paginate(10);

        // Statistiques de la filière
        $stats = [
            'total_formations' => $filiere->formations()->count(),
            'total_groupes' => DB::table('groupes')
                ->join('formations', 'groupes.id_formation', '=', 'formations.id')
                ->where('formations.code_filiere', $code_filiere)
                ->count(),
            'effectif_total' => DB::table('groupes')
                ->join('formations', 'groupes.id_formation', '=', 'formations.id')
                ->where('formations.code_filiere', $code_filiere)
                ->sum('groupes.effectif_groupe'),
            'etablissements' => DB::table('formations')
                ->where('code_filiere', $code_filiere)
                ->distinct('code_efp')
                ->count('code_efp')
        ];

        return view('administrationetablissement.filieres.show', compact('filiere', 'formations', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $code_filiere)
    {
        $filiere = Filiere::where('code_filiere', $code_filiere)->firstOrFail();
        $secteurs = Secteur::orderBy('nom_secteur')->get();
        return view('administrationetablissement.filieres.edit', compact('filiere', 'secteurs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $code_filiere)
    {
        $filiere = Filiere::where('code_filiere', $code_filiere)->firstOrFail();

        $request->validate([
            'code_filiere' => 'required|string|max:50|unique:filieres,code_filiere,' . $code_filiere . ',code_filiere',
            'nom_filiere' => 'required|string|max:255',
            'nom_secteur' => 'required|string|exists:secteurs,nom_secteur'
        ], [
            'code_filiere.required' => 'Le code de la filière est obligatoire.',
            'code_filiere.unique' => 'Ce code de filière existe déjà.',
            'nom_filiere.required' => 'Le nom de la filière est obligatoire.',
            'nom_secteur.required' => 'Le secteur est obligatoire.',
            'nom_secteur.exists' => 'Le secteur sélectionné n\'existe pas.'
        ]);

        try {
            DB::transaction(function () use ($filiere, $request, $code_filiere) {
                $oldCode = $filiere->code_filiere;
                $newCode = strtoupper($request->code_filiere);

                if ($oldCode !== $newCode) {
                    // Mettre à jour les formations associées
                    DB::table('formations')
                        ->where('code_filiere', $oldCode)
                        ->update(['code_filiere' => $newCode]);

                    // Supprimer l'ancienne filière et créer la nouvelle
                    Filiere::where('code_filiere', $oldCode)->delete();
                    Filiere::create([
                        'code_filiere' => $newCode,
                        'nom_filiere' => $request->nom_filiere,
                        'nom_secteur' => $request->nom_secteur
                    ]);
                } else {
                    // Simple mise à jour
                    $filiere->update([
                        'nom_filiere' => $request->nom_filiere,
                        'nom_secteur' => $request->nom_secteur
                    ]);
                }
            });

            return redirect()
                ->route('administration.etablissement.filieres.index')
                ->with('success', 'Filière modifiée avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la modification de la filière: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $code_filiere)
    {
        try {
            $filiere = Filiere::where('code_filiere', $code_filiere)->firstOrFail();
            
            // Vérifier s'il y a des formations associées
            if ($filiere->formations()->count() > 0) {
                return back()->with('error', 'Impossible de supprimer cette filière car elle contient des formations.');
            }

            $filiere->delete();

            return redirect()
                ->route('administration.etablissement.filieres.index')
                ->with('success', 'Filière supprimée avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression de la filière: ' . $e->getMessage());
        }
    }
}