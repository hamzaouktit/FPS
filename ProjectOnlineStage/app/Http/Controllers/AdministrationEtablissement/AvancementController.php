<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Avancement;
use App\Models\Affectation;
use App\Models\Etablissement;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Formateur;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvancementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Récupérer l'établissement du directeur connecté
        $user = Auth::user();
        $etablissement = $user->etablissement;
        
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
        }

        // Récupérer les données pour les filtres
        $groupes = Groupe::where('code_efp', $etablissement->code_efp)
            ->orderBy('code_groupe')
            ->get();
            
        $modules = Module::where('code_efp', $etablissement->code_efp)
            ->orderBy('code_module')
            ->get();
            
        $formateurs = Formateur::where('code_efp', $etablissement->code_efp)
            ->orderBy('nom_complet')
            ->get();

        // Query de base
        $query = Avancement::with([
                'affectation.groupe',
                'affectation.module',
                'affectation.formateurPresentiel',
                'affectation.formateurSyn'
            ])
            ->where('code_efp', $etablissement->code_efp);

        // Filtrage par recherche (groupe ou module)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('affectation.groupe', function($subQ) use ($search) {
                    $subQ->where('code_groupe', 'LIKE', "%{$search}%");
                })->orWhereHas('affectation.module', function($subQ) use ($search) {
                    $subQ->where('nom_module', 'LIKE', "%{$search}%")
                      ->orWhere('code_module', 'LIKE', "%{$search}%");
                });
            });
        }

        // Filtrage par groupe
        if ($request->filled('groupe_id')) {
            $query->whereHas('affectation', function($q) use ($request) {
                $q->where('groupe_id', $request->groupe_id);
            });
        }

        // Filtrage par module
        if ($request->filled('module_id')) {
            $query->whereHas('affectation', function($q) use ($request) {
                $q->where('module_id', $request->module_id);
            });
        }

        // Filtrage par formateur présentiel
        if ($request->filled('formateur_presentiel')) {
            $query->whereHas('affectation', function($q) use ($request) {
                $q->where('mle_affecte_presentiel', $request->formateur_presentiel);
            });
        }

        // Filtrage par formateur synchrone
        if ($request->filled('formateur_syn')) {
            $query->whereHas('affectation', function($q) use ($request) {
                $q->where('mle_affecte_syn', $request->formateur_syn);
            });
        }

        // Filtrage par taux de réalisation
        if ($request->filled('taux_min')) {
            $query->where('taux_realisation_globale', '>=', $request->taux_min);
        }
        if ($request->filled('taux_max')) {
            $query->where('taux_realisation_globale', '<=', $request->taux_max);
        }

        // Filtrage par date
        if ($request->filled('date_debut')) {
            $query->whereDate('date_maj', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->whereDate('date_maj', '<=', $request->date_fin);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'date_maj');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['date_maj', 'taux_realisation_globale', 'mh_realisee_globale'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Récupérer tous les avancements pour les statistiques
        $avancementsCollection = $query->get();

        // Calculer les statistiques sur tous les avancements
        $stats = [
            'total' => $avancementsCollection->count(),
            'taux_moyen' => $avancementsCollection->count() > 0 
                ? round($avancementsCollection->avg('taux_realisation_globale'), 2) 
                : 0,
            'mh_total' => round($avancementsCollection->sum('mh_realisee_globale'), 2),
            'en_retard' => $avancementsCollection->where('taux_realisation_globale', '<', 50)->count(),
        ];

        // Grouper par groupe-module
        $avancementsGroupes = $avancementsCollection
            ->groupBy(function($avancement) {
                return $avancement->affectation->groupe->code_groupe . ' - ' . $avancement->affectation->module->nom_module;
            })
            ->sortBy(function($avancementsGroupe, $key) {
                $firstAvancement = $avancementsGroupe->first();
                $groupeCode = $firstAvancement->affectation->groupe->code_groupe;
                $moduleNom = $firstAvancement->affectation->module->nom_module;
                return $groupeCode . '|' . $moduleNom;
            }, SORT_NATURAL | SORT_FLAG_CASE);

        // Paginer les groupes
        $perPage = $request->get('per_page', 10); // Nombre de groupes par page
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $avancementsPaginated = $avancementsGroupes->slice($offset, $perPage);
        
        // Créer un objet LengthAwarePaginator
        $avancements = new \Illuminate\Pagination\LengthAwarePaginator(
            $avancementsPaginated,
            $avancementsGroupes->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('administrationetablissement.avancements.index', compact(
            'avancements', 
            'etablissement', 
            'groupes', 
            'modules', 
            'formateurs',
            'stats'
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
            return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
        }

        // Récupérer les affectations de l'établissement qui n'ont pas encore d'avancement
        $affectations = Affectation::with(['groupe', 'module', 'formateurPresentiel', 'formateurSyn'])
            ->where('code_efp', $etablissement->code_efp)
            ->whereDoesntHave('avancement')
            ->get()
            ->sortBy(function($affectation) {
                return $affectation->groupe->code_groupe . '|' . $affectation->module->nom_module;
            });

        return view('administrationetablissement.avancements.create', compact('affectations', 'etablissement'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'affectation_id' => 'required|exists:affectations,id',
            'mh_realisee_presentiel' => 'required|numeric|min:0',
            'mh_realisee_sync' => 'required|numeric|min:0',
            'mh_realisee_globale' => 'required|numeric|min:0',
            'taux_realisation_presentiel' => 'required|numeric|min:0|max:100',
            'taux_realisation_syn' => 'required|numeric|min:0|max:100',
            'taux_realisation_globale' => 'required|numeric|min:0|max:100',
            'moyenne_absence' => 'required|numeric|min:0|max:100',
            'nb_cc' => 'required|integer|min:0',
            'seance_efm' => 'required|in:Oui,Non',
            'validation_efm' => 'required|in:oui,non',
            'classe_teams' => 'nullable|string',
            'date_maj' => 'required|date',
        ]);

        $user = Auth::user();
        $etablissement = $user->etablissement;

        // Vérifier que l'affectation appartient bien à l'établissement du directeur
        $affectation = Affectation::where('id', $request->affectation_id)
            ->where('code_efp', $etablissement->code_efp)
            ->first();

        if (!$affectation) {
            return redirect()->back()->with('error', 'Affectation non trouvée dans votre établissement.');
        }

        Avancement::create([
            'affectation_id' => $request->affectation_id,
            'mh_realisee_presentiel' => $request->mh_realisee_presentiel,
            'mh_realisee_sync' => $request->mh_realisee_sync,
            'mh_realisee_globale' => $request->mh_realisee_globale,
            'taux_realisation_presentiel' => $request->taux_realisation_presentiel,
            'taux_realisation_syn' => $request->taux_realisation_syn,
            'taux_realisation_globale' => $request->taux_realisation_globale,
            'moyenne_absence' => $request->moyenne_absence,
            'nb_cc' => $request->nb_cc,
            'seance_efm' => $request->seance_efm,
            'validation_efm' => $request->validation_efm,
            'classe_teams' => $request->classe_teams,
            'date_maj' => $request->date_maj,
            'code_efp' => $etablissement->code_efp,
        ]);

        return redirect()->route('administration.etablissement.avancements.index')
            ->with('success', 'Avancement créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Avancement $avancement)
    {
        // Vérifier que l'avancement appartient à l'établissement du directeur
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if ($avancement->code_efp !== $etablissement->code_efp) {
            abort(403, 'Accès non autorisé.');
        }

        $avancement->load([
            'affectation.groupe',
            'affectation.module',
            'affectation.formateurPresentiel',
            'affectation.formateurSyn'
        ]);

        return view('administrationetablissement.avancements.show', compact('avancement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Avancement $avancement)
    {
        // Vérifier que l'avancement appartient à l'établissement du directeur
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if ($avancement->code_efp !== $etablissement->code_efp) {
            abort(403, 'Accès non autorisé.');
        }

        $avancement->load(['affectation.groupe', 'affectation.module']);

        return view('administrationetablissement.avancements.edit', compact('avancement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Avancement $avancement)
    {
        // Vérifier que l'avancement appartient à l'établissement du directeur
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if ($avancement->code_efp !== $etablissement->code_efp) {
            abort(403, 'Accès non autorisé.');
        }

        $request->validate([
            'mh_realisee_presentiel' => 'required|numeric|min:0',
            'mh_realisee_sync' => 'required|numeric|min:0',
            'mh_realisee_globale' => 'required|numeric|min:0',
            'taux_realisation_presentiel' => 'required|numeric|min:0|max:100',
            'taux_realisation_syn' => 'required|numeric|min:0|max:100',
            'taux_realisation_globale' => 'required|numeric|min:0|max:100',
            'moyenne_absence' => 'required|numeric|min:0|max:100',
            'nb_cc' => 'required|integer|min:0',
            'seance_efm' => 'required|in:Oui,Non',
            'validation_efm' => 'required|in:oui,non',
            'classe_teams' => 'nullable|string',
            'date_maj' => 'required|date',
        ]);

        $avancement->update($request->all());

        return redirect()->route('administration.etablissement.avancements.index')
            ->with('success', 'Avancement modifié avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Avancement $avancement)
    {
        // Vérifier que l'avancement appartient à l'établissement du directeur
        $user = Auth::user();
        $etablissement = $user->etablissement;

        if ($avancement->code_efp !== $etablissement->code_efp) {
            abort(403, 'Accès non autorisé.');
        }

        $avancement->delete();

        return redirect()->route('administration.etablissement.avancements.index')
            ->with('success', 'Avancement supprimé avec succès.');
    }
}