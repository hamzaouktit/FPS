<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Niveau;
use App\Models\Formation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NiveauController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer l'établissement de l'utilisateur connecté
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Vous n\'êtes pas associé à un établissement.');
        }

        // Récupérer TOUS les niveaux avec le nombre de formations pour cet établissement
        $niveaux = Niveau::withCount(['formations' => function($query) use ($etablissement) {
            $query->where('code_efp', $etablissement->code_efp);
        }])
        ->orderBy('created_at', 'desc') // Les plus récents en premier
        ->get();

        return view('administrationetablissement.niveaux.index', compact('niveaux', 'etablissement'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Vous n\'êtes pas associé à un établissement.');
        }

        return view('administrationetablissement.niveaux.create', compact('etablissement'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Vous n\'êtes pas associé à un établissement.');
        }

        $request->validate([
            'niveau' => 'required|string|max:255|unique:niveaux,niveau',
        ], [
            'niveau.required' => 'Le niveau est obligatoire.',
            'niveau.unique' => 'Ce niveau existe déjà.',
        ]);

        try {
            Niveau::create([
                'niveau' => $request->niveau,
            ]);

            return redirect()->route('administration.etablissement.niveaux.index')
                ->with('success', 'Niveau créé avec succès. Il apparaîtra dans les filtres du dashboard une fois qu\'une formation l\'utilisera.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erreur lors de la création du niveau : ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $niveau)
    {
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Vous n\'êtes pas associé à un établissement.');
        }

        $niveauModel = Niveau::where('niveau', $niveau)->firstOrFail();
        
        // Récupérer les formations de ce niveau pour cet établissement
        $formations = Formation::where('niveau', $niveau)
            ->where('code_efp', $etablissement->code_efp)
            ->with(['filiere.secteur', 'groupes'])
            ->get();

        return view('administrationetablissement.niveaux.show', compact('niveauModel', 'formations', 'etablissement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $niveau)
    {
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Vous n\'êtes pas associé à un établissement.');
        }

        $niveauModel = Niveau::where('niveau', $niveau)->firstOrFail();

        return view('administrationetablissement.niveaux.edit', compact('niveauModel', 'etablissement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $niveau)
    {
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Vous n\'êtes pas associé à un établissement.');
        }

        $niveauModel = Niveau::where('niveau', $niveau)->firstOrFail();

        $request->validate([
            'niveau' => 'required|string|max:255|unique:niveaux,niveau,' . $niveauModel->niveau . ',niveau',
        ], [
            'niveau.required' => 'Le niveau est obligatoire.',
            'niveau.unique' => 'Ce niveau existe déjà.',
        ]);

        try {
            $niveauModel->update([
                'niveau' => $request->niveau,
            ]);

            return redirect()->route('administration.etablissement.niveaux.index')
                ->with('success', 'Niveau modifié avec succès.');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Erreur lors de la modification du niveau : ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $niveau)
    {
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Vous n\'êtes pas associé à un établissement.');
        }

        $niveauModel = Niveau::where('niveau', $niveau)->firstOrFail();

        try {
            // Vérifier si le niveau est utilisé dans des formations de cet établissement
            $formationsCount = Formation::where('niveau', $niveau)
                ->where('code_efp', $etablissement->code_efp)
                ->count();

            if ($formationsCount > 0) {
                return back()->with('error', 'Impossible de supprimer ce niveau car il est utilisé dans ' . $formationsCount . ' formation(s) de votre établissement.');
            }

            $niveauModel->delete();

            return redirect()->route('administration.etablissement.niveaux.index')
                ->with('success', 'Niveau supprimé avec succès.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression du niveau : ' . $e->getMessage());
        }
    }
}