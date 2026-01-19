<?php

namespace App\Http\Controllers\AdministrationComplexe;

use App\Http\Controllers\Controller;
use App\Models\HistoriqueAvancement;
use App\Models\Formateur;
use App\Models\Module;
use App\Models\Groupe;
use App\Models\Etablissement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HistoriqueControllerComplex extends Controller
{
    /**
     * Affiche la page d'historique avec filtres
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $complexe = $user->complexe;
        
        // Récupérer les établissements du complexe
        $etablissements = $complexe->etablissements()
            ->orderBy('nom_efp')
            ->get();
        
        // Déterminer le code_efp à utiliser (filtre ou premier établissement)
        $codeEfpFiltre = $request->input('etablissement');
        $codesEfp = [];
        
        if ($codeEfpFiltre && $etablissements->pluck('code_efp')->contains($codeEfpFiltre)) {
            $codesEfp = [$codeEfpFiltre];
        } else {
            $codesEfp = $etablissements->pluck('code_efp')->toArray();
        }
        
        // Récupérer les dates disponibles dans l'historique
        $datesDisponibles = HistoriqueAvancement::whereIn('code_efp', $codesEfp)
            ->selectRaw('DATE(date_capture) as date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->map(function($date) {
                return Carbon::parse($date);
            });
        
        $formateurs = Formateur::whereHas('etablissements', function($q) use ($codesEfp) {
                $q->whereIn('etablissements.code_efp', $codesEfp);
            })
            ->orderBy('nom_complet')
            ->get();
        
        $modules = Module::whereIn('code_efp', $codesEfp)
            ->orderBy('code_module')
            ->get();
        
        $groupes = Groupe::whereIn('code_efp', $codesEfp)
            ->orderBy('code_groupe')
            ->get();
        
        // Construire la requête avec filtres
        $query = HistoriqueAvancement::whereIn('code_efp', $codesEfp)
            ->orderBy('date_capture', 'desc')
            ->orderBy('code_groupe')
            ->orderBy('code_module');
        
        if ($request->filled('date')) {
            $query->whereDate('date_capture', $request->date);
        }
        
        if ($request->filled('formateur')) {
            $mle = $request->formateur;
            $query->where(function($q) use ($mle) {
                $q->where('mle_presentiel', $mle)
                  ->orWhere('mle_syn', $mle);
            });
        }
        
        if ($request->filled('module')) {
            $query->where('code_module', $request->module);
        }
        
        if ($request->filled('groupe')) {
            $query->where('code_groupe', $request->groupe);
        }
        
        $historiques = $query->paginate(50);
        
        return view('administrationcomplexe.historique.index', compact(
            'historiques',
            'datesDisponibles',
            'formateurs',
            'modules',
            'groupes',
            'etablissements',
            'codeEfpFiltre'
        ));
    }
    
    /**
     * Affiche les détails d'un enregistrement d'historique
     */
    public function show($id)
    {
        $user = Auth::user();
        $complexe = $user->complexe;
        
        // Récupérer les codes_efp du complexe
        $codesEfp = $complexe->etablissements()
            ->pluck('code_efp')
            ->toArray();
        
        $historique = HistoriqueAvancement::whereIn('code_efp', $codesEfp)
            ->findOrFail($id);
        
        return view('administrationcomplexe.historique.show', compact('historique'));
    }
    
    /**
     * Compare l'avancement entre deux dates
     * ✅ VERSION CORRIGÉE avec filtres pour complexe
     */
    public function compare(Request $request)
    {
        $request->validate([
            'date1' => 'required|date',
            'date2' => 'required|date|after:date1',
            'formateur' => 'nullable|string',
            'module' => 'nullable|string',
            'groupe' => 'nullable|string',
            'etablissement' => 'nullable|string'
        ]);
        
        $user = Auth::user();
        $complexe = $user->complexe;
        
        // Récupérer les établissements du complexe
        $etablissements = $complexe->etablissements()
            ->orderBy('nom_efp')
            ->get();
        
        // Déterminer le code_efp à utiliser
        $codeEfpFiltre = $request->input('etablissement');
        $codesEfp = [];
        
        if ($codeEfpFiltre && $etablissements->pluck('code_efp')->contains($codeEfpFiltre)) {
            $codesEfp = [$codeEfpFiltre];
        } else {
            $codesEfp = $etablissements->pluck('code_efp')->toArray();
        }
        
        $date1 = Carbon::parse($request->date1);
        $date2 = Carbon::parse($request->date2);
        
        // ✅ Récupérer les filtres
        $formateurMle = $request->input('formateur');
        $moduleCode = $request->input('module');
        $groupeCode = $request->input('groupe');
        
        // ✅ FIX: Utiliser une clé composite basée sur les identifiants métier
        $query1 = HistoriqueAvancement::whereIn('code_efp', $codesEfp)
            ->whereDate('date_capture', $date1);
        
        $query2 = HistoriqueAvancement::whereIn('code_efp', $codesEfp)
            ->whereDate('date_capture', $date2);
        
        // ✅ Appliquer les filtres si présents
        if ($formateurMle) {
            $query1->where(function($q) use ($formateurMle) {
                $q->where('mle_presentiel', $formateurMle)
                  ->orWhere('mle_syn', $formateurMle);
            });
            $query2->where(function($q) use ($formateurMle) {
                $q->where('mle_presentiel', $formateurMle)
                  ->orWhere('mle_syn', $formateurMle);
            });
        }
        
        if ($moduleCode) {
            $query1->where('code_module', $moduleCode);
            $query2->where('code_module', $moduleCode);
        }
        
        if ($groupeCode) {
            $query1->where('code_groupe', $groupeCode);
            $query2->where('code_groupe', $groupeCode);
        }
        
        $historique1 = $query1->get()
            ->mapWithKeys(function($item) {
                // Clé composite: code_groupe + code_module
                $key = $item->code_groupe . '|' . $item->code_module;
                return [$key => $item];
            });
        
        $historique2 = $query2->get()
            ->mapWithKeys(function($item) {
                $key = $item->code_groupe . '|' . $item->code_module;
                return [$key => $item];
            });
        
        // ✅ Vérification si des données existent
        if ($historique1->isEmpty()) {
            $message = "Aucune donnée trouvée pour la date {$date1->format('d/m/Y')}";
            if ($formateurMle) {
                $formateur = Formateur::where('mle', $formateurMle)->first();
                $message .= " pour le formateur " . ($formateur ? $formateur->nom_complet : $formateurMle);
            }
            return redirect()
                ->route('administration.complexe.historique.index')
                ->with('error', $message);
        }
        
        if ($historique2->isEmpty()) {
            $message = "Aucune donnée trouvée pour la date {$date2->format('d/m/Y')}";
            if ($formateurMle) {
                $formateur = Formateur::where('mle', $formateurMle)->first();
                $message .= " pour le formateur " . ($formateur ? $formateur->nom_complet : $formateurMle);
            }
            return redirect()
                ->route('administration.complexe.historique.index')
                ->with('error', $message);
        }
        
        // Calculer les différences
        $comparaisons = [];
        
        foreach ($historique2 as $key => $h2) {
            $h1 = $historique1->get($key);
            
            if ($h1) {
                // ✅ Données trouvées pour les deux dates
                $comparaisons[] = [
                    'formateur_presentiel' => $h2->formateur_presentiel,
                    'formateur_syn' => $h2->formateur_syn,
                    'module' => $h2->nom_module,
                    'code_module' => $h2->code_module,
                    'groupe' => $h2->code_groupe,
                    'filiere' => $h2->nom_filiere,
                    'code_efp' => $h2->code_efp,
                    
                    'mh_realisee_globale_avant' => $h1->mh_realisee_globale,
                    'mh_realisee_globale_apres' => $h2->mh_realisee_globale,
                    'diff_mh' => $h2->mh_realisee_globale - $h1->mh_realisee_globale,
                    
                    'taux_realisation_avant' => $h1->taux_realisation_globale,
                    'taux_realisation_apres' => $h2->taux_realisation_globale,
                    'diff_taux' => $h2->taux_realisation_globale - $h1->taux_realisation_globale,
                    
                    // ✅ Ajout d'informations supplémentaires
                    'mh_affectee_globale' => $h2->mh_affectee_globale,
                    'nb_cc_avant' => $h1->nb_cc,
                    'nb_cc_apres' => $h2->nb_cc,
                    'diff_cc' => $h2->nb_cc - $h1->nb_cc,
                ];
            } else {
                // ✅ Nouvelles affectations (présentes uniquement dans date2)
                $comparaisons[] = [
                    'formateur_presentiel' => $h2->formateur_presentiel,
                    'formateur_syn' => $h2->formateur_syn,
                    'module' => $h2->nom_module,
                    'code_module' => $h2->code_module,
                    'groupe' => $h2->code_groupe,
                    'filiere' => $h2->nom_filiere,
                    'code_efp' => $h2->code_efp,
                    
                    'mh_realisee_globale_avant' => 0,
                    'mh_realisee_globale_apres' => $h2->mh_realisee_globale,
                    'diff_mh' => $h2->mh_realisee_globale,
                    
                    'taux_realisation_avant' => 0,
                    'taux_realisation_apres' => $h2->taux_realisation_globale,
                    'diff_taux' => $h2->taux_realisation_globale,
                    
                    'mh_affectee_globale' => $h2->mh_affectee_globale,
                    'nb_cc_avant' => 0,
                    'nb_cc_apres' => $h2->nb_cc,
                    'diff_cc' => $h2->nb_cc,
                    
                    'nouveau' => true, // ✅ Marqueur pour les nouvelles affectations
                ];
            }
        }
        
        // ✅ Affectations supprimées (présentes uniquement dans date1)
        foreach ($historique1 as $key => $h1) {
            if (!$historique2->has($key)) {
                $comparaisons[] = [
                    'formateur_presentiel' => $h1->formateur_presentiel,
                    'formateur_syn' => $h1->formateur_syn,
                    'module' => $h1->nom_module,
                    'code_module' => $h1->code_module,
                    'groupe' => $h1->code_groupe,
                    'filiere' => $h1->nom_filiere,
                    'code_efp' => $h1->code_efp,
                    
                    'mh_realisee_globale_avant' => $h1->mh_realisee_globale,
                    'mh_realisee_globale_apres' => 0,
                    'diff_mh' => -$h1->mh_realisee_globale,
                    
                    'taux_realisation_avant' => $h1->taux_realisation_globale,
                    'taux_realisation_apres' => 0,
                    'diff_taux' => -$h1->taux_realisation_globale,
                    
                    'mh_affectee_globale' => $h1->mh_affectee_globale,
                    'nb_cc_avant' => $h1->nb_cc,
                    'nb_cc_apres' => 0,
                    'diff_cc' => -$h1->nb_cc,
                    
                    'supprime' => true, // ✅ Marqueur pour les affectations supprimées
                ];
            }
        }
        
        // Trier par différence de taux (du plus grand progrès au plus petit)
        usort($comparaisons, function($a, $b) {
            return $b['diff_taux'] <=> $a['diff_taux'];
        });
        
        // ✅ Récupérer les informations du formateur si filtre appliqué
        $formateurInfo = null;
        if ($formateurMle) {
            $formateurInfo = Formateur::where('mle', $formateurMle)->first();
        }
        
        // ✅ Récupérer les informations du module si filtre appliqué
        $moduleInfo = null;
        if ($moduleCode) {
            $moduleInfo = Module::where('code_module', $moduleCode)
                               ->whereIn('code_efp', $codesEfp)
                               ->first();
        }
        
        // ✅ Récupérer les informations du groupe si filtre appliqué
        $groupeInfo = null;
        if ($groupeCode) {
            $groupeInfo = Groupe::where('code_groupe', $groupeCode)
                               ->whereIn('code_efp', $codesEfp)
                               ->first();
        }
        
        // ✅ Récupérer l'établissement si filtre appliqué
        $etablissementInfo = null;
        if ($codeEfpFiltre) {
            $etablissementInfo = Etablissement::where('code_efp', $codeEfpFiltre)->first();
        }
        
        return view('administrationcomplexe.historique.compare', compact(
            'comparaisons',
            'date1',
            'date2',
            'formateurInfo',
            'moduleInfo',
            'groupeInfo',
            'etablissementInfo',
            'etablissements',
            'codeEfpFiltre'
        ));
    }
    
    /**
     * Exporte l'historique en CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $complexe = $user->complexe;
        
        // Récupérer les codes_efp du complexe
        $codesEfp = $complexe->etablissements()
            ->pluck('code_efp')
            ->toArray();
        
        $query = HistoriqueAvancement::whereIn('code_efp', $codesEfp)
            ->orderBy('date_capture', 'desc')
            ->orderBy('code_groupe')
            ->orderBy('code_module');
        
        if ($request->filled('date')) {
            $query->whereDate('date_capture', $request->date);
        }
        
        if ($request->filled('formateur')) {
            $mle = $request->formateur;
            $query->where(function($q) use ($mle) {
                $q->where('mle_presentiel', $mle)
                  ->orWhere('mle_syn', $mle);
            });
        }
        
        if ($request->filled('module')) {
            $query->where('code_module', $request->module);
        }
        
        if ($request->filled('groupe')) {
            $query->where('code_groupe', $request->groupe);
        }
        
        if ($request->filled('etablissement')) {
            $query->where('code_efp', $request->etablissement);
        }
        
        $historiques = $query->get();
        
        $filename = 'historique_avancement_complexe_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($historiques) {
            $file = fopen('php://output', 'w');
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'Date Capture',
                'Établissement',
                'Formateur Présentiel',
                'MLE Présentiel',
                'Formateur Synchrone',
                'MLE Synchrone',
                'Module',
                'Code Module',
                'Groupe',
                'Filière',
                'MH Affectée Globale',
                'MH Réalisée Globale',
                'Taux Réalisation Globale',
                'Moyenne Absence',
                'Nb CC',
                'Séance EFM',
                'Validation EFM'
            ], ';');
            
            foreach ($historiques as $h) {
                fputcsv($file, [
                    $h->date_capture->format('d/m/Y'),
                    $h->code_efp,
                    $h->formateur_presentiel,
                    $h->mle_presentiel,
                    $h->formateur_syn,
                    $h->mle_syn,
                    $h->nom_module,
                    $h->code_module,
                    $h->code_groupe,
                    $h->nom_filiere,
                    $h->mh_affectee_globale,
                    $h->mh_realisee_globale,
                    $h->taux_realisation_globale . '%',
                    $h->moyenne_absence . '%',
                    $h->nb_cc,
                    $h->seance_efm,
                    $h->validation_efm
                ], ';');
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
