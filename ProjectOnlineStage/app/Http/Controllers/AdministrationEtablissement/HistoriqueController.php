<?php

namespace App\Http\Controllers\AdministrationEtablissement;

use App\Http\Controllers\Controller;
use App\Models\HistoriqueAvancement;
use App\Models\Formateur;
use App\Models\Module;
use App\Models\Groupe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class HistoriqueController extends Controller
{
    /**
     * Affiche la page d'historique avec filtres
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $codeEfp = $user->etablissement->code_efp;
        
        // Récupérer les dates disponibles dans l'historique
        $datesDisponibles = HistoriqueAvancement::where('code_efp', $codeEfp)
            ->selectRaw('DATE(date_capture) as date')
            ->distinct()
            ->orderBy('date', 'desc')
            ->pluck('date')
            ->map(function($date) {
                return Carbon::parse($date);
            });
        
        // ✅ FIX: Spécifier la table pour éviter l'ambiguïté
        $formateurs = Formateur::whereHas('etablissements', function($q) use ($codeEfp) {
                $q->where('etablissements.code_efp', $codeEfp);
            })
            ->orderBy('nom_complet')
            ->get();
        
        // Récupérer les modules de l'établissement
        $modules = Module::where('code_efp', $codeEfp)
            ->orderBy('code_module')
            ->get();
        
        // Récupérer les groupes de l'établissement
        $groupes = Groupe::where('code_efp', $codeEfp)
            ->orderBy('code_groupe')
            ->get();
        
        // Construire la requête avec filtres
        $query = HistoriqueAvancement::where('code_efp', $codeEfp)
            ->orderBy('date_capture', 'desc')
            ->orderBy('code_groupe')
            ->orderBy('code_module');
        
        // Filtre par date
        if ($request->filled('date')) {
            $query->whereDate('date_capture', $request->date);
        }
        
        // Filtre par formateur (présentiel ou synchrone)
        if ($request->filled('formateur')) {
            $mle = $request->formateur;
            $query->where(function($q) use ($mle) {
                $q->where('mle_presentiel', $mle)
                  ->orWhere('mle_syn', $mle);
            });
        }
        
        // Filtre par module
        if ($request->filled('module')) {
            $query->where('code_module', $request->module);
        }
        
        // Filtre par groupe
        if ($request->filled('groupe')) {
            $query->where('code_groupe', $request->groupe);
        }
        
        $historiques = $query->paginate(50);
        
        return view('administrationetablissement.historique.index', compact(
            'historiques',
            'datesDisponibles',
            'formateurs',
            'modules',
            'groupes'
        ));
    }
    
    /**
     * Affiche les détails d'un enregistrement d'historique
     */
    public function show($id)
    {
        $user = Auth::user();
        $codeEfp = $user->etablissement->code_efp;
        
        $historique = HistoriqueAvancement::where('code_efp', $codeEfp)
            ->findOrFail($id);
        
        return view('administrationetablissement.historique.show', compact('historique'));
    }
    
    /**
     * Compare l'avancement entre deux dates
     */
    public function compare(Request $request)
    {
        $request->validate([
            'date1' => 'required|date',
            'date2' => 'required|date|after:date1'
        ]);
        
        $user = Auth::user();
        $codeEfp = $user->etablissement->code_efp;
        
        $date1 = Carbon::parse($request->date1);
        $date2 = Carbon::parse($request->date2);
        
        // Récupérer les données de la première date
        $historique1 = HistoriqueAvancement::where('code_efp', $codeEfp)
            ->whereDate('date_capture', $date1)
            ->get()
            ->keyBy('affectation_id');
        
        // Récupérer les données de la deuxième date
        $historique2 = HistoriqueAvancement::where('code_efp', $codeEfp)
            ->whereDate('date_capture', $date2)
            ->get()
            ->keyBy('affectation_id');
        
        // Calculer les différences
        $comparaisons = [];
        
        foreach ($historique2 as $affectationId => $h2) {
            $h1 = $historique1->get($affectationId);
            
            if ($h1) {
                $comparaisons[] = [
                    'formateur_presentiel' => $h2->formateur_presentiel,
                    'formateur_syn' => $h2->formateur_syn,
                    'module' => $h2->nom_module,
                    'code_module' => $h2->code_module,
                    'groupe' => $h2->code_groupe,
                    'filiere' => $h2->nom_filiere,
                    
                    'mh_realisee_globale_avant' => $h1->mh_realisee_globale,
                    'mh_realisee_globale_apres' => $h2->mh_realisee_globale,
                    'diff_mh' => $h2->mh_realisee_globale - $h1->mh_realisee_globale,
                    
                    'taux_realisation_avant' => $h1->taux_realisation_globale,
                    'taux_realisation_apres' => $h2->taux_realisation_globale,
                    'diff_taux' => $h2->taux_realisation_globale - $h1->taux_realisation_globale,
                ];
            }
        }
        
        // Trier par différence de taux (du plus grand progrès au plus petit)
        usort($comparaisons, function($a, $b) {
            return $b['diff_taux'] <=> $a['diff_taux'];
        });
        
        return view('administrationetablissement.historique.compare', compact(
            'comparaisons',
            'date1',
            'date2'
        ));
    }
    
    /**
     * Exporte l'historique en CSV
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $codeEfp = $user->etablissement->code_efp;
        
        $query = HistoriqueAvancement::where('code_efp', $codeEfp)
            ->orderBy('date_capture', 'desc')
            ->orderBy('code_groupe')
            ->orderBy('code_module');
        
        // Appliquer les mêmes filtres que l'index
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
        
        $historiques = $query->get();
        
        $filename = 'historique_avancement_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($historiques) {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8 pour Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // En-têtes
            fputcsv($file, [
                'Date Capture',
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
            
            // Données
            foreach ($historiques as $h) {
                fputcsv($file, [
                    $h->date_capture->format('d/m/Y'),
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