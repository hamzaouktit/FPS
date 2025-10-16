<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Etablissement;
use App\Models\Filiere;
use App\Models\Formateur;
use App\Models\Affectation;
use App\Models\Avancement;
use App\Models\Groupe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    /**
     * Afficher la liste des modules de l'établissement
     */
    public function index()
    {
        // Récupérer l'établissement de l'utilisateur connecté
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
        }

        $modules = Module::where('code_efp', $etablissement->code_efp)
                        ->with(['filieres', 'formateurs'])
                        ->orderBy('code_module')
                        ->get();

        return view('administrationetablissement.modules.index', compact('modules', 'etablissement'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
        }

        // Récupérer les filières de l'établissement pour la sélection
        $filieres = Filiere::where('code_efp', $etablissement->code_efp)->get();
        
        return view('administrationetablissement.modules.create', compact('etablissement', 'filieres'));
    }

    /**
     * Enregistrer un nouveau module
     */
    public function store(Request $request)
    {
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
        }

        $request->validate([
            'code_module' => 'required|string|max:50|',
            'nom_module' => 'required|string|max:255',
            'regional' => 'required|in:O,N',
            'module_pie' => 'required|in:O,N',
            'efp_pie' => 'nullable|string|max:255',
            'filieres' => 'nullable|array',
            'filieres.*' => 'exists:filieres,id'
        ]);

        try {
            $module = Module::create([
                'code_module' => $request->code_module,
                'nom_module' => $request->nom_module,
                'regional' => $request->regional,
                'module_pie' => $request->module_pie,
                'efp_pie' => $request->efp_pie,
                'code_efp' => $etablissement->code_efp
            ]);

            // Associer les filières sélectionnées
            if ($request->has('filieres')) {
                $module->filieres()->sync($request->filieres);
            }

            return redirect()->route('administration.etablissement.modules.index')
                           ->with('success', 'Module créé avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la création du module: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Afficher les détails d'un module
     */
    public function show($id)
    {
        $etablissement = Auth::user()->etablissement;
        $module = Module::where('code_efp', $etablissement->code_efp)
                       ->with(['filieres', 'formateurs', 'affectations.groupe.filiere'])
                       ->findOrFail($id);

        // Récupérer les avancements par groupe
        $avancementsParGroupe = Affectation::where('module_id', $module->id)
            ->with(['groupe.filiere', 'avancement', 'formateurPresentiel', 'formateurSyn'])
            ->get()
            ->groupBy('groupe.code_groupe');

        // Récupérer les avancements par filière
        $avancementsParFiliere = Affectation::where('module_id', $module->id)
            ->with(['groupe.filiere', 'avancement'])
            ->get()
            ->groupBy('groupe.filiere.nom_filiere');

        // Récupérer les avancements par formateur
        $avancementsParFormateur = collect();
        
        // Formateurs présentiel
        $affectationsPresentiel = Affectation::where('module_id', $module->id)
            ->whereNotNull('mle_affecte_presentiel')
            ->with(['formateurPresentiel', 'avancement', 'groupe'])
            ->get();
        
        // Formateurs synchrone
        $affectationsSyn = Affectation::where('module_id', $module->id)
            ->whereNotNull('mle_affecte_syn')
            ->with(['formateurSyn', 'avancement', 'groupe'])
            ->get();

        // Fusionner les deux collections
        $avancementsParFormateur = $affectationsPresentiel->merge($affectationsSyn)
            ->groupBy(function($affectation) {
                if ($affectation->mle_affecte_presentiel) {
                    return $affectation->formateurPresentiel->nom_complet ?? 'Non assigné';
                } else {
                    return $affectation->formateurSyn->nom_complet ?? 'Non assigné';
                }
            });

        return view('administrationetablissement.modules.show', compact(
            'module', 
            'avancementsParGroupe',
            'avancementsParFiliere',
            'avancementsParFormateur'
        ));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit($id)
    {
        $etablissement = Auth::user()->etablissement;
        $module = Module::where('code_efp', $etablissement->code_efp)
                       ->with('filieres')
                       ->findOrFail($id);

        $filieres = Filiere::where('code_efp', $etablissement->code_efp)->get();

        return view('administrationetablissement.modules.edit', compact('module', 'etablissement', 'filieres'));
    }

    /**
     * Mettre à jour un module
     */
    public function update(Request $request, $id)
    {
        $etablissement = Auth::user()->etablissement;
        $module = Module::where('code_efp', $etablissement->code_efp)->findOrFail($id);

        $request->validate([
            'code_module' => 'required|string|max:50|',
            'nom_module' => 'required|string|max:255',
            'regional' => 'required|in:O,N',
            'module_pie' => 'required|in:O,N',
            'efp_pie' => 'nullable|string|max:255',
            'filieres' => 'nullable|array',
            'filieres.*' => 'exists:filieres,id'
        ]);

        try {
            $module->update([
                'code_module' => $request->code_module,
                'nom_module' => $request->nom_module,
                'regional' => $request->regional,
                'module_pie' => $request->module_pie,
                'efp_pie' => $request->efp_pie
            ]);

            // Mettre à jour les filières associées
            $module->filieres()->sync($request->filieres ?? []);

            return redirect()->route('administration.etablissement.modules.index')
                           ->with('success', 'Module modifié avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la modification du module: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Supprimer un module
     */
    public function destroy($id)
    {
        $etablissement = Auth::user()->etablissement;
        $module = Module::where('code_efp', $etablissement->code_efp)->findOrFail($id);

        try {
            // Vérifier s'il y a des affectations liées à ce module
            if ($module->affectations()->count() > 0) {
                return redirect()->back()
                               ->with('error', 'Impossible de supprimer ce module car il est lié à des affectations.');
            }

            $module->delete();

            return redirect()->route('administration.etablissement.modules.index')
                           ->with('success', 'Module supprimé avec succès.');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Erreur lors de la suppression du module: ' . $e->getMessage());
        }
    }
}