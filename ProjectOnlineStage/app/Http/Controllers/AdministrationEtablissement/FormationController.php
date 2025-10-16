<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FormationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Aucun établissement associé à votre compte.');
        }

        // Récupérer les formations avec les relations
        $query = Formation::with(['filiere', 'niveau', 'etablissement'])
            ->where('code_efp', $etablissement->code_efp);

        // Filtre par année
        if ($request->filled('annee')) {
            $query->where('annee', $request->annee);
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par mode
        if ($request->filled('mode')) {
            $query->where('mode', $request->mode);
        }

        // Filtre par créneau
        if ($request->filled('creneau')) {
            $query->where('creneau', $request->creneau);
        }

        // Filtre par filière
        if ($request->filled('filiere_id')) {
            $query->where('filiere_id', $request->filiere_id);
        }

        // Filtre par niveau
        if ($request->filled('niveau_id')) {
            $query->where('niveau_id', $request->niveau_id);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                  ->orWhere('mode', 'like', "%{$search}%")
                  ->orWhere('creneau', 'like', "%{$search}%")
                  ->orWhere('annee', 'like', "%{$search}%")
                  ->orWhereHas('filiere', function($q) use ($search) {
                      $q->where('nom_filiere', 'like', "%{$search}%")
                        ->orWhere('code_filiere', 'like', "%{$search}%");
                  })
                  ->orWhereHas('niveau', function($q) use ($search) {
                      $q->where('nom', 'like', "%{$search}%");
                  });
            });
        }

        $formations = $query->orderBy('annee', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Récupérer les filières et niveaux pour les filtres
        $filieres = Filiere::where('code_efp', $etablissement->code_efp)->get();
        $niveaux = Niveau::where('code_efp', $etablissement->code_efp)->get();

        // Statistiques
        $stats = [
            'total' => Formation::where('code_efp', $etablissement->code_efp)->count(),
            'diplomante' => Formation::where('code_efp', $etablissement->code_efp)->where('type', 'Diplômante')->count(),
            'qualifiante' => Formation::where('code_efp', $etablissement->code_efp)->where('type', 'Qualifiante')->count(),
            'pp' => Formation::where('code_efp', $etablissement->code_efp)->where('type', 'PP')->count(),
            'annees' => Formation::where('code_efp', $etablissement->code_efp)->distinct()->pluck('annee'),
        ];

        return view('administrationetablissement.formations.index', compact(
            'formations', 
            'stats', 
            'etablissement',
            'filieres',
            'niveaux'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Aucun établissement associé à votre compte.');
        }

        // Récupérer les filières et niveaux de l'établissement
        $filieres = Filiere::where('code_efp', $etablissement->code_efp)->get();
        $niveaux = Niveau::where('code_efp', $etablissement->code_efp)->get();

        return view('administrationetablissement.formations.create', compact(
            'etablissement',
            'filieres',
            'niveaux'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Aucun établissement associé à votre compte.');
        }

        $validator = Validator::make($request->all(), [
            'annee' => 'required|integer|min:2020|max:2030',
            'filiere_id' => 'required|exists:filieres,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'type' => 'required|in:Diplômante,Qualifiante,PP',
            'mode' => 'required|in:Résidentiel,Alterné',
            'creneau' => 'required|in:CDJ,CDS',
        ], [
            'annee.required' => 'L\'année est obligatoire.',
            'annee.integer' => 'L\'année doit être un nombre.',
            'filiere_id.required' => 'La filière est obligatoire.',
            'filiere_id.exists' => 'La filière sélectionnée n\'existe pas.',
            'niveau_id.required' => 'Le niveau est obligatoire.',
            'niveau_id.exists' => 'Le niveau sélectionné n\'existe pas.',
            'type.required' => 'Le type de formation est obligatoire.',
            'type.in' => 'Le type doit être: Diplômante, Qualifiante ou PP.',
            'mode.required' => 'Le mode de formation est obligatoire.',
            'mode.in' => 'Le mode doit être: Résidentiel ou Alterné.',
            'creneau.required' => 'Le créneau est obligatoire.',
            'creneau.in' => 'Le créneau doit être: CDJ ou CDS.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier si une formation identique existe déjà
        $exists = Formation::where('code_efp', $etablissement->code_efp)
            ->where('annee', $request->annee)
            ->where('filiere_id', $request->filiere_id)
            ->where('niveau_id', $request->niveau_id)
            ->where('type', $request->type)
            ->where('mode', $request->mode)
            ->where('creneau', $request->creneau)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Cette formation existe déjà pour cette année.')
                ->withInput();
        }

        Formation::create([
            'annee' => $request->annee,
            'filiere_id' => $request->filiere_id,
            'niveau_id' => $request->niveau_id,
            'type' => $request->type,
            'mode' => $request->mode,
            'creneau' => $request->creneau,
            'code_efp' => $etablissement->code_efp,
        ]);

        return redirect()->route('administration.etablissement.formations.index')
            ->with('success', 'Formation créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Aucun établissement associé à votre compte.');
        }

        $formation = Formation::with(['filiere.secteur', 'niveau', 'etablissement', 'groupes'])
            ->where('id', $id)
            ->where('code_efp', $etablissement->code_efp)
            ->firstOrFail();

        // Statistiques des groupes liés
        $groupes = $formation->groupes;
        $stats = [
            'total_groupes' => $groupes->count(),
            'effectif_total' => $groupes->sum('effectif_groupe'),
            'groupes_actifs' => $groupes->where('statut', 'Actif')->count(),
            'groupes_inactifs' => $groupes->where('statut', 'Inactif')->count(),
        ];

        return view('administrationetablissement.formations.show', compact(
            'formation', 
            'groupes', 
            'stats', 
            'etablissement'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Aucun établissement associé à votre compte.');
        }

        $formation = Formation::with(['filiere', 'niveau'])
            ->where('id', $id)
            ->where('code_efp', $etablissement->code_efp)
            ->firstOrFail();

        // Récupérer les filières et niveaux de l'établissement
        $filieres = Filiere::where('code_efp', $etablissement->code_efp)->get();
        $niveaux = Niveau::where('code_efp', $etablissement->code_efp)->get();

        return view('administrationetablissement.formations.edit', compact(
            'formation', 
            'etablissement',
            'filieres',
            'niveaux'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if (!$etablissement) {
            return redirect()->route('administration.etablissement.dashboard')
                ->with('error', 'Aucun établissement associé à votre compte.');
        }

        $formation = Formation::where('id', $id)
            ->where('code_efp', $etablissement->code_efp)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'annee' => 'required|integer|min:2020|max:2030',
            'filiere_id' => 'required|exists:filieres,id',
            'niveau_id' => 'required|exists:niveaux,id',
            'type' => 'required|in:Diplômante,Qualifiante,PP',
            'mode' => 'required|in:Résidentiel,Alterné',
            'creneau' => 'required|in:CDJ,CDS',
        ], [
            'annee.required' => 'L\'année est obligatoire.',
            'annee.integer' => 'L\'année doit être un nombre.',
            'filiere_id.required' => 'La filière est obligatoire.',
            'filiere_id.exists' => 'La filière sélectionnée n\'existe pas.',
            'niveau_id.required' => 'Le niveau est obligatoire.',
            'niveau_id.exists' => 'Le niveau sélectionné n\'existe pas.',
            'type.required' => 'Le type de formation est obligatoire.',
            'type.in' => 'Le type doit être: Diplômante, Qualifiante ou PP.',
            'mode.required' => 'Le mode de formation est obligatoire.',
            'mode.in' => 'Le mode doit être: Résidentiel ou Alterné.',
            'creneau.required' => 'Le créneau est obligatoire.',
            'creneau.in' => 'Le créneau doit être: CDJ ou CDS.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier si une formation identique existe (sauf celle en cours de modification)
        $exists = Formation::where('code_efp', $etablissement->code_efp)
            ->where('annee', $request->annee)
            ->where('filiere_id', $request->filiere_id)
            ->where('niveau_id', $request->niveau_id)
            ->where('type', $request->type)
            ->where('mode', $request->mode)
            ->where('creneau', $request->creneau)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Cette formation existe déjà pour cette année.')
                ->withInput();
        }

        $formation->update([
            'annee' => $request->annee,
            'filiere_id' => $request->filiere_id,
            'niveau_id' => $request->niveau_id,
            'type' => $request->type,
            'mode' => $request->mode,
            'creneau' => $request->creneau,
        ]);

        return redirect()->route('administration.etablissement.formations.index')
            ->with('success', 'Formation modifiée avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if (!$etablissement) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun établissement associé à votre compte.'
            ], 403);
        }

        $formation = Formation::where('id', $id)
            ->where('code_efp', $etablissement->code_efp)
            ->firstOrFail();

        // Vérifier si la formation est utilisée par des groupes
        $groupesCount = $formation->groupes()->count();

        if ($groupesCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Impossible de supprimer cette formation car elle est utilisée par {$groupesCount} groupe(s)."
            ], 400);
        }

        $formation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Formation supprimée avec succès.'
        ]);
    }
}