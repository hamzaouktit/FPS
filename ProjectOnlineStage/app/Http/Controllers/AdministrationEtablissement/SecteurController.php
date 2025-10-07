<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Secteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SecteurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $secteurs = Secteur::withCount('filieres')->orderBy('nom_secteur')->paginate(15);
        return view('administrationetablissement.secteurs.index', compact('secteurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrationetablissement.secteurs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_secteur' => 'required|string|max:255|unique:secteurs,nom_secteur'
        ], [
            'nom_secteur.required' => 'Le nom du secteur est obligatoire.',
            'nom_secteur.unique' => 'Ce secteur existe déjà.',
            'nom_secteur.max' => 'Le nom du secteur ne doit pas dépasser 255 caractères.'
        ]);

        try {
            Secteur::create([
                'nom_secteur' => $request->nom_secteur
            ]);

            return redirect()
                ->route('administration.etablissement.secteurs.index')
                ->with('success', 'Secteur créé avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du secteur: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $nom_secteur)
    {
        $secteur = Secteur::where('nom_secteur', $nom_secteur)
            ->withCount('filieres')
            ->firstOrFail();

        // Récupérer les filières associées au secteur
        $filieres = $secteur->filieres()
            ->withCount('formations')
            ->orderBy('nom_filiere')
            ->paginate(10);

        // Statistiques du secteur
        $stats = [
            'total_filieres' => $secteur->filieres()->count(),
            'total_formations' => DB::table('formations')
                ->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere')
                ->where('filieres.nom_secteur', $nom_secteur)
                ->count(),
            'total_groupes' => DB::table('groupes')
                ->join('formations', 'groupes.id_formation', '=', 'formations.id')
                ->join('filieres', 'formations.code_filiere', '=', 'filieres.code_filiere')
                ->where('filieres.nom_secteur', $nom_secteur)
                ->count()
        ];

        return view('administrationetablissement.secteurs.show', compact('secteur', 'filieres', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $nom_secteur)
    {
        $secteur = Secteur::where('nom_secteur', $nom_secteur)->firstOrFail();
        return view('administrationetablissement.secteurs.edit', compact('secteur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $nom_secteur)
    {
        $secteur = Secteur::where('nom_secteur', $nom_secteur)->firstOrFail();

        $request->validate([
            'nom_secteur' => 'required|string|max:255|unique:secteurs,nom_secteur,' . $nom_secteur . ',nom_secteur'
        ], [
            'nom_secteur.required' => 'Le nom du secteur est obligatoire.',
            'nom_secteur.unique' => 'Ce secteur existe déjà.',
            'nom_secteur.max' => 'Le nom du secteur ne doit pas dépasser 255 caractères.'
        ]);

        try {
            // Mise à jour du secteur et des clés étrangères
            DB::transaction(function () use ($secteur, $request) {
                $oldNom = $secteur->nom_secteur;
                $newNom = $request->nom_secteur;

                if ($oldNom !== $newNom) {
                    // Mettre à jour les filières associées
                    DB::table('filieres')
                        ->where('nom_secteur', $oldNom)
                        ->update(['nom_secteur' => $newNom]);

                    // Supprimer l'ancien secteur et créer le nouveau
                    Secteur::where('nom_secteur', $oldNom)->delete();
                    Secteur::create(['nom_secteur' => $newNom]);
                }
            });

            return redirect()
                ->route('administration.etablissement.secteurs.index')
                ->with('success', 'Secteur modifié avec succès.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la modification du secteur: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $nom_secteur)
    {
        try {
            $secteur = Secteur::where('nom_secteur', $nom_secteur)->firstOrFail();
            
            // Vérifier s'il y a des filières associées
            if ($secteur->filieres()->count() > 0) {
                return back()->with('error', 'Impossible de supprimer ce secteur car il contient des filières.');
            }

            $secteur->delete();

            return redirect()
                ->route('administration.etablissement.secteurs.index')
                ->with('success', 'Secteur supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression du secteur: ' . $e->getMessage());
        }
    }
}