<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Secteur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecteurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les secteurs en fonction du rôle
        $secteurs = Secteur::with(['etablissement', 'filieres'])
            ->forUser($user)
            ->orderBy('nom_secteur')
            ->paginate(10);
        
        return view('administrationetablissement.secteurs.index', compact('secteurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur a un établissement
        if (!$user->etablissement) {
            return redirect()->route('administration.etablissement.secteurs.index')
                ->with('error', 'Vous devez être rattaché à un établissement.');
        }
        
        return view('administrationetablissement.secteurs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->etablissement) {
            return redirect()->route('administration.etablissement.secteurs.index')
                ->with('error', 'Vous devez être rattaché à un établissement.');
        }
        
        $request->validate([
            'nom_secteur' => 'required|string|max:255|unique:secteurs,nom_secteur',
        ], [
            'nom_secteur.required' => 'Le nom du secteur est obligatoire.',
            'nom_secteur.unique' => 'Ce secteur existe déjà.',
        ]);
        
        Secteur::create([
            'nom_secteur' => $request->nom_secteur,
            'code_efp' => $user->etablissement->code_efp,
        ]);
        
        return redirect()->route('administration.etablissement.secteurs.index')
            ->with('success', 'Secteur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $nom_secteur)
    {
        $user = Auth::user();
        
        $secteur = Secteur::with([
            'etablissement',
            'filieres.formations.groupes',
            'filieres' => function($query) {
                $query->withCount('formations');
            },
            'formations.groupes' => function($query) {
                $query->withCount('avancements');
            }
        ])
        ->forUser($user)
        ->where('nom_secteur', $nom_secteur)
        ->firstOrFail();
        
        // Statistiques
        $stats = [
            'total_filieres' => $secteur->filieres->count(),
            'total_formations' => $secteur->formations->count(),
            'total_groupes' => $secteur->formations->sum(function($formation) {
                return $formation->groupes->count();
            }),
            'total_stagiaires' => $secteur->formations->sum(function($formation) {
                return $formation->groupes->sum('effectif_groupe');
            })
        ];
        
        return view('administrationetablissement.secteurs.show', compact('secteur', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $nom_secteur)
    {
        $user = Auth::user();
        
        $secteur = Secteur::forUser($user)
            ->where('nom_secteur', $nom_secteur)
            ->firstOrFail();
        
        return view('administrationetablissement.secteurs.edit', compact('secteur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $nom_secteur)
    {
        $user = Auth::user();
        
        $secteur = Secteur::forUser($user)
            ->where('nom_secteur', $nom_secteur)
            ->firstOrFail();
        
        $request->validate([
            'nom_secteur' => 'required|string|max:255|unique:secteurs,nom_secteur,' . $nom_secteur . ',nom_secteur',
        ], [
            'nom_secteur.required' => 'Le nom du secteur est obligatoire.',
            'nom_secteur.unique' => 'Ce secteur existe déjà.',
        ]);
        
        // Si le nom change, on doit gérer la clé primaire
        if ($request->nom_secteur !== $nom_secteur) {
            // Créer un nouveau secteur
            $newSecteur = Secteur::create([
                'nom_secteur' => $request->nom_secteur,
                'code_efp' => $secteur->code_efp,
            ]);
            
            // Mettre à jour les filières associées
            $secteur->filieres()->update(['nom_secteur' => $request->nom_secteur]);
            
            // Supprimer l'ancien secteur
            $secteur->delete();
            
            return redirect()->route('administration.etablissement.secteurs.show', $newSecteur->nom_secteur)
                ->with('success', 'Secteur modifié avec succès.');
        }
        
        return redirect()->route('administration.etablissement.secteurs.show', $secteur->nom_secteur)
            ->with('success', 'Secteur modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $nom_secteur)
    {
        $user = Auth::user();
        
        $secteur = Secteur::forUser($user)
            ->where('nom_secteur', $nom_secteur)
            ->firstOrFail();
        
        // Vérifier s'il y a des filières associées
        if ($secteur->filieres()->count() > 0) {
            return redirect()->route('administration.etablissement.secteurs.index')
                ->with('error', 'Impossible de supprimer ce secteur car il contient des filières.');
        }
        
        $secteur->delete();
        
        return redirect()->route('administration.etablissement.secteurs.index')
            ->with('success', 'Secteur supprimé avec succès.');
    }
}