<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use App\Models\Secteur;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FiliereController extends Controller
{
    public function index()
    {
        $filieres = Filiere::forUser(Auth::user())->with(['secteur', 'etablissement'])->paginate(10);
        return view('administrationetablissement.filieres.index', compact('filieres'));
    }

    public function create()
    {
        $secteurs = Secteur::forUser(Auth::user())->get();
        return view('administrationetablissement.filieres.create', compact('secteurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code_filiere' => 'required|string|unique:filieres,code_filiere',
            'nom_filiere' => 'required|string|max:255',
            'nom_secteur' => 'required|string|exists:secteurs,nom_secteur',
        ]);

        $filiere = new Filiere($request->only(['code_filiere', 'nom_filiere', 'nom_secteur']));
        $filiere->code_efp = Auth::user()->etablissement->code_efp;
        $filiere->save();

        return redirect()->route('administration.etablissement.filieres.index')
            ->with('success', 'Filière créée avec succès.');
    }

    public function show($code_filiere)
{
    $filiere = Filiere::forUser(Auth::user())
        ->with(['secteur', 'etablissement', 'formations.niveau', 'groupes'])
        ->where('code_filiere', $code_filiere)
        ->firstOrFail();

    $modules = Module::whereHas('groupes', function ($query) use ($filiere) {
        $query->whereIn('groupes.groupe', $filiere->groupes->pluck('groupe'));
    })->get();

    return view('administrationetablissement.filieres.show', compact('filiere', 'modules'));
}

    public function edit($code_filiere)
    {
        $filiere = Filiere::forUser(Auth::user())->where('code_filiere', $code_filiere)->firstOrFail();
        $secteurs = Secteur::forUser(Auth::user())->get();
        return view('administrationetablissement.filieres.edit', compact('filiere', 'secteurs'));
    }

    public function update(Request $request, $code_filiere)
    {
        $filiere = Filiere::forUser(Auth::user())->where('code_filiere', $code_filiere)->firstOrFail();

        $request->validate([
            'nom_filiere' => 'required|string|max:255',
            'nom_secteur' => 'required|string|exists:secteurs,nom_secteur',
        ]);

        $filiere->update($request->only(['nom_filiere', 'nom_secteur']));

        return redirect()->route('administration.etablissement.filieres.index')
            ->with('success', 'Filière mise à jour avec succès.');
    }

    public function destroy($code_filiere)
    {
        $filiere = Filiere::forUser(Auth::user())->where('code_filiere', $code_filiere)->firstOrFail();
        $filiere->delete();

        return redirect()->route('administration.etablissement.filieres.index')
            ->with('success', 'Filière supprimée avec succès.');
    }
}