<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\Affectation;
use App\Models\Etablissement;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Formateur;
use App\Models\Avancement;
use App\Models\Secteur;
use App\Models\Filiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AffectationController extends Controller
{
    /**
     * Afficher la liste des affectations avec filtrage amélioré
     */
    public function index(Request $request)
    {
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
        }

        $query = Affectation::where('code_efp', $etablissement->code_efp)
            ->with([
                'groupe.filiere.secteur', 
                'groupe.formation',
                'module', 
                'formateurPresentiel', 
                'formateurSyn', 
                'avancement'
            ]);

        if ($request->filled('secteur_id')) {
            $query->whereHas('groupe.filiere.secteur', function($q) use ($request) {
                $q->where('id', $request->secteur_id);
            });
        }

        if ($request->filled('filiere_id')) {
            $query->whereHas('groupe', function($q) use ($request) {
                $q->where('filiere_id', $request->filiere_id);
            });
        }

        if ($request->filled('groupe_id')) {
            $query->where('groupe_id', $request->groupe_id);
        }

        if ($request->filled('module_id')) {
            $query->where('module_id', $request->module_id);
        }

        if ($request->filled('formateur_presentiel')) {
            $query->where('mle_affecte_presentiel', $request->formateur_presentiel);
        }

        if ($request->filled('formateur_syn')) {
            $query->where('mle_affecte_syn', $request->formateur_syn);
        }

        if ($request->filled('fusion_groupe')) {
            $query->where('fusion_groupe', 'LIKE', '%' . $request->fusion_groupe . '%');
        }

        $affectations = $query->orderBy('created_at', 'desc')->paginate(15);

        $secteurs = Secteur::where('code_efp', $etablissement->code_efp)->orderBy('nom_secteur')->get();
        $filieres = Filiere::where('code_efp', $etablissement->code_efp)->orderBy('nom_filiere')->get();
        $groupes = Groupe::where('code_efp', $etablissement->code_efp)->where('statut', 'Actif')->orderBy('code_groupe')->get();
        $modules = Module::where('code_efp', $etablissement->code_efp)->orderBy('code_module')->get();
        $formateurs = $etablissement->formateurs()->orderBy('nom_complet')->get();

        $stats = [
            'total' => $affectations->total(),
            'mh_totale_drif' => Affectation::where('code_efp', $etablissement->code_efp)->sum('mh_totale_drif'),
            'mh_affectee' => Affectation::where('code_efp', $etablissement->code_efp)->sum('mh_affectee_globale'),
        ];

        return view('administrationetablissement.affectations.index', compact(
            'affectations', 
            'etablissement',
            'secteurs',
            'filieres',
            'groupes',
            'modules',
            'formateurs',
            'stats'
        ));
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

        $groupes = Groupe::where('code_efp', $etablissement->code_efp)
            ->where('statut', 'Actif')
            ->with(['filiere', 'formation'])
            ->orderBy('code_groupe')
            ->get();
        
        $modules = Module::where('code_efp', $etablissement->code_efp)
            ->orderBy('code_module')
            ->get();
        
        $formateurs = $etablissement->formateurs()
            ->orderBy('nom_complet')
            ->get();

        return view('administrationetablissement.affectations.create', compact(
            'etablissement', 
            'groupes', 
            'modules', 
            'formateurs'
        ));
    }

    /**
     * Récupérer les modules d'un formateur via AJAX
     * ✅ MÉTHODE CORRIGÉE
     */
    public function getFormateurModules($mle)
    {
        try {
            $etablissement = Auth::user()->etablissement;
            
            if (!$mle) {
                return response()->json([
                    'success' => false,
                    'message' => 'MLE manquant',
                    'modules' => []
                ], 400);
            }

            $formateur = Formateur::where('mle', $mle)
                ->with(['modules' => function($query) use ($etablissement) {
                    $query->where('code_efp', $etablissement->code_efp);
                }])
                ->first();
            
            if (!$formateur) {
                return response()->json([
                    'success' => false,
                    'message' => 'Formateur non trouvé',
                    'modules' => []
                ], 404);
            }

            $modules = $formateur->modules
                ->map(function($module) {
                    return [
                        'id' => $module->id,
                        'code_module' => $module->code_module,
                        'nom_module' => $module->nom_module,
                        'text' => $module->code_module . ' - ' . $module->nom_module
                    ];
                })
                ->sortBy('code_module')
                ->values();

            return response()->json([
                'success' => true,
                'modules' => $modules,
                'count' => $modules->count()
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des modules: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage(),
                'modules' => []
            ], 500);
        }
    }


    /**
     * Enregistrer une nouvelle affectation
     */
    public function store(Request $request)
    {
        $etablissement = Auth::user()->etablissement;
        
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Aucun établissement associé à votre compte.');
        }

        $request->validate([
            'groupe_id' => 'required|exists:groupes,id',
            'module_id' => 'required|exists:modules,id',
            'mle_affecte_presentiel' => 'nullable|exists:formateurs,mle',
            'mle_affecte_syn' => 'nullable|exists:formateurs,mle',
            'fusion_groupe' => 'nullable|string|max:255',
            'code_fusion' => 'nullable|string|max:255',
            'mhp_s1_drif' => 'nullable|numeric|min:0',
            'mhsyn_s1_drif' => 'nullable|numeric|min:0',
            'mhasyn_s1_drif' => 'nullable|numeric|min:0',
            'mhp_s2_drif' => 'nullable|numeric|min:0',
            'mhsyn_s2_drif' => 'nullable|numeric|min:0',
            'mhasyn_s2_drif' => 'nullable|numeric|min:0',
            'mh_affectee_presentiel' => 'nullable|numeric|min:0',
            'mh_affectee_sync' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Vérifier si l'affectation existe déjà
            $existingAffectation = Affectation::where('groupe_id', $request->groupe_id)
                ->where('module_id', $request->module_id)
                ->first();

            if ($existingAffectation) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Cette affectation existe déjà pour ce groupe et ce module.')
                    ->withInput();
            }

            // Vérifier que le module appartient bien aux formateurs sélectionnés
            if ($request->mle_affecte_presentiel) {
                $formateurPresentiel = Formateur::where('mle', $request->mle_affecte_presentiel)->first();
                $hasModule = $formateurPresentiel->modules()->where('modules.id', $request->module_id)->exists();
                
                if (!$hasModule) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Le module sélectionné n\'est pas assigné au formateur présentiel.')
                        ->withInput();
                }
            }

            if ($request->mle_affecte_syn) {
                $formateurSyn = Formateur::where('mle', $request->mle_affecte_syn)->first();
                $hasModule = $formateurSyn->modules()->where('modules.id', $request->module_id)->exists();
                
                if (!$hasModule) {
                    DB::rollBack();
                    return redirect()->back()
                        ->with('error', 'Le module sélectionné n\'est pas assigné au formateur synchrone.')
                        ->withInput();
                }
            }

            // Récupérer les noms des formateurs
            $formateurPresentiel = null;
            $formateurSyn = null;

            if ($request->mle_affecte_presentiel) {
                $formateurPresentiel = Formateur::where('mle', $request->mle_affecte_presentiel)->first();
            }

            if ($request->mle_affecte_syn) {
                $formateurSyn = Formateur::where('mle', $request->mle_affecte_syn)->first();
            }

            // Calculs automatiques
            $mhpS1 = $request->mhp_s1_drif ?? 0;
            $mhsynS1 = $request->mhsyn_s1_drif ?? 0;
            $mhasynS1 = $request->mhasyn_s1_drif ?? 0;
            $mh_totale_s1_drif = $mhpS1 + $mhsynS1 + $mhasynS1;
            
            $mhpS2 = $request->mhp_s2_drif ?? 0;
            $mhsynS2 = $request->mhsyn_s2_drif ?? 0;
            $mhasynS2 = $request->mhasyn_s2_drif ?? 0;
            $mh_totale_s2_drif = $mhpS2 + $mhsynS2 + $mhasynS2;

            $mhp_totale_drif = $mhpS1 + $mhpS2;
            $mhsyn_totale_drif = $mhsynS1 + $mhsynS2;
            $mhasyn_totale_drif = $mhasynS1 + $mhasynS2;
            $mh_totale_drif = $mh_totale_s1_drif + $mh_totale_s2_drif;

            $mhPresentiel = $request->mh_affectee_presentiel ?? 0;
            $mhSync = $request->mh_affectee_sync ?? 0;
            $mh_affectee_globale = $mhPresentiel + $mhSync;

            // Créer l'affectation
            $affectation = Affectation::create([
                'groupe_id' => $request->groupe_id,
                'module_id' => $request->module_id,
                'code_efp' => $etablissement->code_efp,
                'fusion_groupe' => $request->fusion_groupe,
                'code_fusion' => $request->code_fusion,
                'mle_affecte_presentiel' => $request->mle_affecte_presentiel,
                'formateur_affecte_presentiel' => $formateurPresentiel ? $formateurPresentiel->nom_complet : null,
                'mle_affecte_syn' => $request->mle_affecte_syn,
                'formateur_affecte_syn' => $formateurSyn ? $formateurSyn->nom_complet : null,
                'mhp_s1_drif' => $mhpS1,
                'mhsyn_s1_drif' => $mhsynS1,
                'mhasyn_s1_drif' => $mhasynS1,
                'mh_totale_s1_drif' => $mh_totale_s1_drif,
                'mhp_s2_drif' => $mhpS2,
                'mhsyn_s2_drif' => $mhsynS2,
                'mhasyn_s2_drif' => $mhasynS2,
                'mh_totale_s2_drif' => $mh_totale_s2_drif,
                'mhp_totale_drif' => $mhp_totale_drif,
                'mhsyn_totale_drif' => $mhsyn_totale_drif,
                'mhasyn_totale_drif' => $mhasyn_totale_drif,
                'mh_totale_drif' => $mh_totale_drif,
                'mh_affectee_presentiel' => $mhPresentiel,
                'mh_affectee_sync' => $mhSync,
                'mh_affectee_globale' => $mh_affectee_globale,
            ]);

            // Créer automatiquement un enregistrement d'avancement vide
            Avancement::create([
                'affectation_id' => $affectation->id,
                'code_efp' => $etablissement->code_efp,
                'mh_realisee_presentiel' => 0,
                'mh_realisee_sync' => 0,
                'mh_realisee_globale' => 0,
                'taux_realisation_presentiel' => 0,
                'taux_realisation_syn' => 0,
                'taux_realisation_globale' => 0,
                'moyenne_absence' => 0,
                'nb_cc' => 0,
                'seance_efm' => 'Non',
                'validation_efm' => 'non',
            ]);

            DB::commit();

            return redirect()->route('administration.etablissement.affectations.index')
                ->with('success', 'Affectation créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur création affectation: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la création: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Afficher les détails d'une affectation
     */
    public function show($id)
    {
        $etablissement = Auth::user()->etablissement;
        $affectation = Affectation::where('code_efp', $etablissement->code_efp)
            ->with([
                'groupe.filiere.secteur', 
                'groupe.formation',
                'module', 
                'formateurPresentiel', 
                'formateurSyn',
                'avancement'
            ])
            ->findOrFail($id);

        return view('administrationetablissement.affectations.show', compact('affectation', 'etablissement'));
    }

    /**
     * Afficher le formulaire d'édition
     */
     public function edit($id)
    {
        $etablissement = Auth::user()->etablissement;
        $affectation = Affectation::where('code_efp', $etablissement->code_efp)
            ->with(['groupe.filiere.secteur', 'groupe.formation', 'module', 'formateurPresentiel', 'formateurSyn'])
            ->findOrFail($id);

        $groupes = Groupe::where('code_efp', $etablissement->code_efp)
            ->where('statut', 'Actif')
            ->with(['filiere', 'formation'])
            ->orderBy('code_groupe')
            ->get();
        
        // Récupérer tous les modules de l'établissement
        $allModules = Module::where('code_efp', $etablissement->code_efp)
            ->orderBy('code_module')
            ->get();
        
        $formateurs = $etablissement->formateurs()
            ->orderBy('nom_complet')
            ->get();

        // Récupérer les modules du formateur présentiel actuel
        $currentModules = collect([]);
        if ($affectation->mle_affecte_presentiel) {
            $formateur = Formateur::where('mle', $affectation->mle_affecte_presentiel)
                ->with('modules')
                ->first();
            if ($formateur) {
                $currentModules = $formateur->modules()
                    ->where('code_efp', $etablissement->code_efp)
                    ->orderBy('code_module')
                    ->get();
            }
        }

        return view('administrationetablissement.affectations.edit', compact(
            'affectation',
            'etablissement', 
            'groupes', 
            'allModules',
            'currentModules',
            'formateurs'
        ));
    }


    /**
     * Mettre à jour une affectation
     */
    public function update(Request $request, $id)
    {
        $etablissement = Auth::user()->etablissement;
        $affectation = Affectation::where('code_efp', $etablissement->code_efp)->findOrFail($id);

        $request->validate([
            'groupe_id' => 'required|exists:groupes,id',
            'module_id' => 'required|exists:modules,id',
            'mle_affecte_presentiel' => 'nullable|exists:formateurs,mle',
            'mle_affecte_syn' => 'nullable|exists:formateurs,mle',
            'fusion_groupe' => 'nullable|string|max:255',
            'code_fusion' => 'nullable|string|max:255',
            'mhp_s1_drif' => 'nullable|numeric|min:0',
            'mhsyn_s1_drif' => 'nullable|numeric|min:0',
            'mhasyn_s1_drif' => 'nullable|numeric|min:0',
            'mhp_s2_drif' => 'nullable|numeric|min:0',
            'mhsyn_s2_drif' => 'nullable|numeric|min:0',
            'mhasyn_s2_drif' => 'nullable|numeric|min:0',
            'mh_affectee_presentiel' => 'nullable|numeric|min:0',
            'mh_affectee_sync' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Vérifier si l'affectation existe déjà (sauf celle qu'on modifie)
            $existingAffectation = Affectation::where('groupe_id', $request->groupe_id)
                ->where('module_id', $request->module_id)
                ->where('id', '!=', $id)
                ->where('code_efp', $etablissement->code_efp)
                ->first();

            if ($existingAffectation) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Cette affectation existe déjà pour ce groupe et ce module.')
                    ->withInput();
            }

            // Vérifier que le module appartient bien aux formateurs sélectionnés
            if ($request->mle_affecte_presentiel) {
                $formateurPresentiel = Formateur::where('mle', $request->mle_affecte_presentiel)->first();
                if ($formateurPresentiel) {
                    $hasModule = $formateurPresentiel->modules()
                        ->where('modules.id', $request->module_id)
                        ->exists();
                    
                    if (!$hasModule) {
                        DB::rollBack();
                        return redirect()->back()
                            ->with('error', 'Le module sélectionné n\'est pas assigné au formateur présentiel.')
                            ->withInput();
                    }
                }
            }

            if ($request->mle_affecte_syn) {
                $formateurSyn = Formateur::where('mle', $request->mle_affecte_syn)->first();
                if ($formateurSyn) {
                    $hasModule = $formateurSyn->modules()
                        ->where('modules.id', $request->module_id)
                        ->exists();
                    
                    if (!$hasModule) {
                        DB::rollBack();
                        return redirect()->back()
                            ->with('error', 'Le module sélectionné n\'est pas assigné au formateur synchrone.')
                            ->withInput();
                    }
                }
            }

            // Récupérer les noms des formateurs
            $formateurPresentiel = null;
            $formateurSyn = null;

            if ($request->mle_affecte_presentiel) {
                $formateurPresentiel = Formateur::where('mle', $request->mle_affecte_presentiel)->first();
            }

            if ($request->mle_affecte_syn) {
                $formateurSyn = Formateur::where('mle', $request->mle_affecte_syn)->first();
            }

            // Calculs automatiques
            $mhpS1 = $request->mhp_s1_drif ?? 0;
            $mhsynS1 = $request->mhsyn_s1_drif ?? 0;
            $mhasynS1 = $request->mhasyn_s1_drif ?? 0;
            $mh_totale_s1_drif = $mhpS1 + $mhsynS1 + $mhasynS1;
            
            $mhpS2 = $request->mhp_s2_drif ?? 0;
            $mhsynS2 = $request->mhsyn_s2_drif ?? 0;
            $mhasynS2 = $request->mhasyn_s2_drif ?? 0;
            $mh_totale_s2_drif = $mhpS2 + $mhsynS2 + $mhasynS2;

            $mhp_totale_drif = $mhpS1 + $mhpS2;
            $mhsyn_totale_drif = $mhsynS1 + $mhsynS2;
            $mhasyn_totale_drif = $mhasynS1 + $mhasynS2;
            $mh_totale_drif = $mh_totale_s1_drif + $mh_totale_s2_drif;

            $mhPresentiel = $request->mh_affectee_presentiel ?? 0;
            $mhSync = $request->mh_affectee_sync ?? 0;
            $mh_affectee_globale = $mhPresentiel + $mhSync;

            // Mettre à jour l'affectation
            $affectation->update([
                'groupe_id' => $request->groupe_id,
                'module_id' => $request->module_id,
                'fusion_groupe' => $request->fusion_groupe,
                'code_fusion' => $request->code_fusion,
                'mle_affecte_presentiel' => $request->mle_affecte_presentiel,
                'formateur_affecte_presentiel' => $formateurPresentiel ? $formateurPresentiel->nom_complet : null,
                'mle_affecte_syn' => $request->mle_affecte_syn,
                'formateur_affecte_syn' => $formateurSyn ? $formateurSyn->nom_complet : null,
                'mhp_s1_drif' => $mhpS1,
                'mhsyn_s1_drif' => $mhsynS1,
                'mhasyn_s1_drif' => $mhasynS1,
                'mh_totale_s1_drif' => $mh_totale_s1_drif,
                'mhp_s2_drif' => $mhpS2,
                'mhsyn_s2_drif' => $mhsynS2,
                'mhasyn_s2_drif' => $mhasynS2,
                'mh_totale_s2_drif' => $mh_totale_s2_drif,
                'mhp_totale_drif' => $mhp_totale_drif,
                'mhsyn_totale_drif' => $mhsyn_totale_drif,
                'mhasyn_totale_drif' => $mhasyn_totale_drif,
                'mh_totale_drif' => $mh_totale_drif,
                'mh_affectee_presentiel' => $mhPresentiel,
                'mh_affectee_sync' => $mhSync,
                'mh_affectee_globale' => $mh_affectee_globale,
            ]);

            DB::commit();

            return redirect()->route('administration.etablissement.affectations.index')
                ->with('success', 'Affectation modifiée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur modification affectation: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la modification: ' . $e->getMessage())
                ->withInput();
        }
    }


    /**
     * Supprimer une affectation
     */
    public function destroy($id)
    {
        $etablissement = Auth::user()->etablissement;
        $affectation = Affectation::where('code_efp', $etablissement->code_efp)->findOrFail($id);

        try {
            DB::beginTransaction();

            if ($affectation->avancement) {
                $affectation->avancement->delete();
            }

            $affectation->delete();

            DB::commit();

            return redirect()->route('administration.etablissement.affectations.index')
                ->with('success', 'Affectation supprimée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }
}