<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\Niveau;
use App\Models\Filiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormationController extends Controller
{
    /**
     * Display a listing of the formations.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est bien un directeur d'établissement
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }

        $code_efp = $user->etablissement->code_efp;

        // Récupérer les formations de l'établissement avec leurs relations
        $formations = Formation::forEtablissement($code_efp)
            ->with(['niveau', 'filiere.secteur', 'etablissement', 'groupes'])
            ->orderBy('annee', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('administrationetablissement.formations.index', compact('formations'));
    }

    /**
     * Show the form for creating a new formation.
     */
    public function create()
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }

        $code_efp = $user->etablissement->code_efp;

        // Récupérer les niveaux et filières de l'établissement
        $niveaux = Niveau::forEtablissement($code_efp)->orderBy('niveau')->get();
        $filieres = Filiere::forEtablissement($code_efp)
            ->with('secteur')
            ->orderBy('nom_filiere')
            ->get();

        return view('administrationetablissement.formations.create', compact('niveaux', 'filieres'));
    }

    /**
     * Store a newly created formation in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }

        $code_efp = $user->etablissement->code_efp;

        $validated = $request->validate([
            'annee' => 'required|integer|min:2000|max:2100',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'required|exists:filieres,id',
            'type_formation' => 'nullable|string|max:255',
            'creneau' => 'nullable|string|max:255',
        ], [
            'annee.required' => 'L\'année est obligatoire.',
            'annee.integer' => 'L\'année doit être un nombre entier.',
            'annee.min' => 'L\'année doit être supérieure ou égale à 2000.',
            'annee.max' => 'L\'année doit être inférieure ou égale à 2100.',
            'niveau_id.required' => 'Le niveau est obligatoire.',
            'niveau_id.exists' => 'Le niveau sélectionné n\'existe pas.',
            'filiere_id.required' => 'La filière est obligatoire.',
            'filiere_id.exists' => 'La filière sélectionnée n\'existe pas.',
        ]);

        // Vérifier que le niveau et la filière appartiennent à l'établissement
        $niveau = Niveau::forEtablissement($code_efp)->find($validated['niveau_id']);
        $filiere = Filiere::forEtablissement($code_efp)->find($validated['filiere_id']);

        if (!$niveau || !$filiere) {
            return back()->with('error', 'Le niveau ou la filière sélectionné n\'appartient pas à votre établissement.')
                ->withInput();
        }

        // Vérifier si une formation similaire existe déjà
        $existingFormation = Formation::forEtablissement($code_efp)
            ->where('annee', $validated['annee'])
            ->where('niveau_id', $validated['niveau_id'])
            ->where('filiere_id', $validated['filiere_id'])
            ->where('type_formation', $validated['type_formation'])
            ->where('creneau', $validated['creneau'])
            ->first();

        if ($existingFormation) {
            return back()->with('warning', 'Une formation identique existe déjà.')
                ->withInput();
        }

        // Ajouter automatiquement le code_efp
        $validated['code_efp'] = $code_efp;

        Formation::create($validated);

        return redirect()->route('administration.etablissement.formations.index')
            ->with('success', 'Formation créée avec succès.');
    }

    /**
     * Display the specified formation.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }

        $code_efp = $user->etablissement->code_efp;

        // Récupérer la formation avec toutes ses relations
        $formation = Formation::forEtablissement($code_efp)
            ->with([
                'niveau',
                'filiere.secteur',
                'etablissement',
                'groupes.avancements.module',
                'groupes.avancements.formateurPresentiel',
                'groupes.avancements.formateurSynchrone',
                'groupes.affectations.formateur',
                'groupes.affectations.module'
            ])
            ->findOrFail($id);

        // Récupérer tous les modules liés via affectations ET avancements
        $modulesFromAffectations = $formation->groupes->flatMap(function($groupe) {
            return $groupe->affectations->pluck('module')->filter();
        });

        $modulesFromAvancements = $formation->groupes->flatMap(function($groupe) {
            return $groupe->avancements->pluck('module')->filter();
        });

        $modules = $modulesFromAffectations->merge($modulesFromAvancements)->unique('id');

        // Récupérer tous les formateurs liés via affectations ET avancements
        $formateursFromAffectations = $formation->groupes->flatMap(function($groupe) {
            return $groupe->affectations->pluck('formateur')->filter();
        });

        $formateursFromAvancements = $formation->groupes->flatMap(function($groupe) {
            $formateursPresentiel = $groupe->avancements->pluck('formateurPresentiel')->filter();
            $formateursSynchrone = $groupe->avancements->pluck('formateurSynchrone')->filter();
            return $formateursPresentiel->merge($formateursSynchrone);
        });

        $formateurs = $formateursFromAffectations->merge($formateursFromAvancements)->unique('mle')->filter();

        // Statistiques
        $stats = [
            'total_groupes' => $formation->groupes->count(),
            'total_stagiaires' => $formation->groupes->sum('effectif_groupe'),
            'total_modules' => $modules->count(),
            'total_formateurs' => $formateurs->count(),
        ];

        return view('administrationetablissement.formations.show', compact('formation', 'modules', 'formateurs', 'stats'));
    }

    /**
     * Show the form for editing the specified formation.
     */
    public function edit($id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }

        $code_efp = $user->etablissement->code_efp;

        $formation = Formation::forEtablissement($code_efp)->findOrFail($id);

        $niveaux = Niveau::forEtablissement($code_efp)->orderBy('niveau')->get();
        $filieres = Filiere::forEtablissement($code_efp)
            ->with('secteur')
            ->orderBy('nom_filiere')
            ->get();

        return view('administrationetablissement.formations.edit', compact('formation', 'niveaux', 'filieres'));
    }

    /**
     * Update the specified formation in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }

        $code_efp = $user->etablissement->code_efp;

        $formation = Formation::forEtablissement($code_efp)->findOrFail($id);

        $validated = $request->validate([
            'annee' => 'required|integer|min:2000|max:2100',
            'niveau_id' => 'required|exists:niveaux,id',
            'filiere_id' => 'required|exists:filieres,id',
            'type_formation' => 'nullable|string|max:255',
            'creneau' => 'nullable|string|max:255',
        ], [
            'annee.required' => 'L\'année est obligatoire.',
            'annee.integer' => 'L\'année doit être un nombre entier.',
            'niveau_id.required' => 'Le niveau est obligatoire.',
            'filiere_id.required' => 'La filière est obligatoire.',
        ]);

        // Vérifier que le niveau et la filière appartiennent à l'établissement
        $niveau = Niveau::forEtablissement($code_efp)->find($validated['niveau_id']);
        $filiere = Filiere::forEtablissement($code_efp)->find($validated['filiere_id']);

        if (!$niveau || !$filiere) {
            return back()->with('error', 'Le niveau ou la filière sélectionné n\'appartient pas à votre établissement.')
                ->withInput();
        }

        $formation->update($validated);

        return redirect()->route('administration.etablissement.formations.index')
            ->with('success', 'Formation mise à jour avec succès.');
    }

    /**
     * Remove the specified formation from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_etablissement' || !$user->etablissement) {
            return redirect()->route('login')->with('error', 'Accès non autorisé.');
        }

        $code_efp = $user->etablissement->code_efp;

        $formation = Formation::forEtablissement($code_efp)->findOrFail($id);

        // Vérifier s'il y a des groupes associés
        if ($formation->groupes()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer cette formation car elle contient des groupes.');
        }

        $formation->delete();

        return redirect()->route('administration.etablissement.formations.index')
            ->with('success', 'Formation supprimée avec succès.');
    }
}