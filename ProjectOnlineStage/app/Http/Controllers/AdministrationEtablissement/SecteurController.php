<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Secteur;
use App\Models\Etablissement;
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
        
        // Récupérer les secteurs selon le rôle
        if ($user->role === 'directeur_etablissement') {
            $secteurs = Secteur::with(['etablissement', 'filieres'])
                ->where('code_efp', $user->etablissement->code_efp)
                ->withCount(['filieres'])
                ->latest()
                ->paginate(10);
        } elseif ($user->role === 'directeur_complexe') {
            $secteurs = Secteur::with(['etablissement', 'filieres'])
                ->whereHas('etablissement', function ($q) use ($user) {
                    $q->where('complexe_id', $user->complexe->id);
                })
                ->withCount(['filieres'])
                ->latest()
                ->paginate(10);
        } else {
            $secteurs = Secteur::with(['etablissement', 'filieres'])
                ->withCount(['filieres'])
                ->latest()
                ->paginate(10);
        }

        return view('administrationetablissement.secteurs.index', compact('secteurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Vérifier les permissions
        if ($user->role === 'directeur_etablissement') {
            $etablissements = Etablissement::where('code_efp', $user->etablissement->code_efp)->get();
        } elseif ($user->role === 'directeur_complexe') {
            $etablissements = Etablissement::where('complexe_id', $user->complexe->id)->get();
        } else {
            $etablissements = Etablissement::all();
        }

        return view('administrationetablissement.secteurs.create', compact('etablissements'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nom_secteur' => 'required|string|max:255',
        ]);

        // Récupérer automatiquement le code_efp selon le rôle
        $code_efp = null;
        if ($user->role === 'directeur_etablissement') {
            $code_efp = $user->etablissement->code_efp;
        } elseif ($user->role === 'directeur_complexe') {
            // Pour un directeur de complexe, il peut choisir l'établissement
            $request->validate([
                'code_efp' => 'required|exists:etablissements,code_efp',
            ]);
            
            // Vérifier que l'établissement appartient bien au complexe
            $etablissement = Etablissement::where('code_efp', $request->code_efp)
                ->where('complexe_id', $user->complexe->id)
                ->first();
            
            if (!$etablissement) {
                return back()->with('error', 'Établissement non autorisé')->withInput();
            }
            
            $code_efp = $request->code_efp;
        } else {
            $request->validate([
                'code_efp' => 'required|exists:etablissements,code_efp',
            ]);
            $code_efp = $request->code_efp;
        }

        // Vérifier si le secteur existe déjà pour cet établissement
        $exists = Secteur::where('nom_secteur', $request->nom_secteur)
            ->where('code_efp', $code_efp)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ce secteur existe déjà pour cet établissement')->withInput();
        }

        Secteur::create([
            'nom_secteur' => $request->nom_secteur,
            'code_efp' => $code_efp,
        ]);

        return redirect()->route('administration.etablissement.secteurs.index')
            ->with('success', 'Secteur créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        
        $secteur = Secteur::with([
            'etablissement.complexe',
            'filieres' => function ($query) {
                $query->withCount('formations');
            },
            'filieres.formations.groupes' => function ($query) {
                $query->withCount('avancements');
            }
        ])->findOrFail($id);

        // Vérifier les permissions
        if ($user->role === 'directeur_etablissement' && $secteur->code_efp !== $user->etablissement->code_efp) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->role === 'directeur_complexe' && $secteur->etablissement->complexe_id !== $user->complexe->id) {
            abort(403, 'Accès non autorisé');
        }

        // Calculer les statistiques
        $stats = [
            'total_filieres' => $secteur->filieres->count(),
            'total_formations' => $secteur->formations->count(),
            'total_groupes' => $secteur->filieres->sum(function ($filiere) {
                return $filiere->formations->sum(function ($formation) {
                    return $formation->groupes->count();
                });
            }),
            'total_stagiaires' => $secteur->filieres->sum(function ($filiere) {
                return $filiere->formations->sum(function ($formation) {
                    return $formation->groupes->sum('effectif_groupe');
                });
            }),
        ];

        return view('administrationetablissement.secteurs.show', compact('secteur', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        
        $secteur = Secteur::with('etablissement')->findOrFail($id);

        // Vérifier les permissions
        if ($user->role === 'directeur_etablissement' && $secteur->code_efp !== $user->etablissement->code_efp) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->role === 'directeur_complexe' && $secteur->etablissement->complexe_id !== $user->complexe->id) {
            abort(403, 'Accès non autorisé');
        }

        // Récupérer les établissements selon le rôle
        if ($user->role === 'directeur_etablissement') {
            $etablissements = Etablissement::where('code_efp', $user->etablissement->code_efp)->get();
        } elseif ($user->role === 'directeur_complexe') {
            $etablissements = Etablissement::where('complexe_id', $user->complexe->id)->get();
        } else {
            $etablissements = Etablissement::all();
        }

        return view('administrationetablissement.secteurs.edit', compact('secteur', 'etablissements'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        
        $secteur = Secteur::findOrFail($id);

        // Vérifier les permissions
        if ($user->role === 'directeur_etablissement' && $secteur->code_efp !== $user->etablissement->code_efp) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->role === 'directeur_complexe' && $secteur->etablissement->complexe_id !== $user->complexe->id) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'nom_secteur' => 'required|string|max:255',
        ]);

        // Vérifier si le nouveau nom existe déjà pour cet établissement (sauf pour le secteur actuel)
        $exists = Secteur::where('nom_secteur', $request->nom_secteur)
            ->where('code_efp', $secteur->code_efp)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ce nom de secteur existe déjà pour cet établissement')->withInput();
        }

        $secteur->update([
            'nom_secteur' => $request->nom_secteur,
        ]);

        return redirect()->route('administration.etablissement.secteurs.index')
            ->with('success', 'Secteur modifié avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        
        $secteur = Secteur::findOrFail($id);

        // Vérifier les permissions
        if ($user->role === 'directeur_etablissement' && $secteur->code_efp !== $user->etablissement->code_efp) {
            abort(403, 'Accès non autorisé');
        }

        if ($user->role === 'directeur_complexe' && $secteur->etablissement->complexe_id !== $user->complexe->id) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier s'il y a des filières associées
        if ($secteur->filieres()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer ce secteur car il contient des filières');
        }

        $secteur->delete();

        return redirect()->route('administration.etablissement.secteurs.index')
            ->with('success', 'Secteur supprimé avec succès');
    }
}