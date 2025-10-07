<?php

namespace App\Http\Controllers\AdministrationComplexe;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DirecteurController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $directeurs = User::where('role', 'directeur_etablissement')
                          ->with('etablissement') // Pour afficher l'établissement associé si existant
                          ->paginate(10);

        return view('administrationcomplexe.directeurs.index', compact('directeurs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('administrationcomplexe.directeurs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|string|email|max:191|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'directeur_etablissement',
        ]);

        return redirect()->route('administration.complexe.directeurs.index')
                         ->with('success', 'Directeur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $directeur)
    {
        if ($directeur->role !== 'directeur_etablissement') {
            abort(404);
        }

        $directeur->load('etablissement');

        return view('administrationcomplexe.directeurs.show', compact('directeur'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $directeur)
    {
        if ($directeur->role !== 'directeur_etablissement') {
            abort(404);
        }

        return view('administrationcomplexe.directeurs.edit', compact('directeur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $directeur)
    {
        if ($directeur->role !== 'directeur_etablissement') {
            abort(404);
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:191',
                Rule::unique('users')->ignore($directeur->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'nom' => $request->nom,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $directeur->update($data);

        return redirect()->route('administration.complexe.directeurs.index')
                         ->with('success', 'Directeur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $directeur)
    {
        if ($directeur->role !== 'directeur_etablissement') {
            abort(404);
        }

        // Optionnel : Vérifier si un établissement est associé avant suppression
        if ($directeur->etablissement) {
            return redirect()->route('administration.complexe.directeurs.index')
                             ->with('warning', 'Impossible de supprimer : un établissement est associé.');
        }

        $directeur->delete();

        return redirect()->route('administration.complexe.directeurs.index')
                         ->with('success', 'Directeur supprimé avec succès.');
    }
}