<?php

namespace App\Http\Controllers\AdministrationComplexe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\{Etablissement, Groupe};

class EffectifsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role !== 'directeur_complexe') {
            return redirect()->route('welcome')->with('error', 'Accès refusé.');
        }
        
        $complexe = $user->complexe;
        if (!$complexe) {
            return redirect()->route('welcome')->with('error', 'Aucun complexe associé.');
        }

        // Effectifs par établissement (avec stats complètes)
        $effectifsParEtablissement = DB::table('groupes')
            ->join('etablissements', 'groupes.code_efp', '=', 'etablissements.code_efp')
            ->where('etablissements.complexe_id', $complexe->id)
            ->select(
                'etablissements.nom_efp',
                'etablissements.code_efp',
                DB::raw('COUNT(DISTINCT groupes.id) as nombre_groupes'),
                DB::raw('SUM(groupes.effectif_groupe) as effectif_total'),
                DB::raw('SUM(CASE WHEN groupes.statut = "Actif" THEN groupes.effectif_groupe ELSE 0 END) as effectif_actif'),
                DB::raw('COUNT(DISTINCT groupes.annee_formation) as nb_annees'),
                DB::raw('COUNT(DISTINCT groupes.filiere_id) as nb_filieres'),
                DB::raw('AVG(groupes.effectif_groupe) as effectif_moyen')
            )
            ->groupBy('etablissements.code_efp', 'etablissements.nom_efp')
            ->orderByDesc('effectif_total')
            ->get();

        // Détail par établissement et par filière
        $detailParEtabFiliere = DB::table('groupes')
            ->join('etablissements', 'groupes.code_efp', '=', 'etablissements.code_efp')
            ->join('filieres', 'groupes.filiere_id', '=', 'filieres.id')
            ->join('secteurs', 'filieres.secteur_id', '=', 'secteurs.id')
            ->where('etablissements.complexe_id', $complexe->id)
            ->select(
                'etablissements.nom_efp',
                'etablissements.code_efp',
                'secteurs.nom_secteur',
                'filieres.nom_filiere',
                'filieres.code_filiere',
                DB::raw('COUNT(DISTINCT groupes.id) as nombre_groupes'),
                DB::raw('SUM(groupes.effectif_groupe) as effectif_total'),
                DB::raw('GROUP_CONCAT(DISTINCT groupes.annee_formation ORDER BY groupes.annee_formation SEPARATOR ", ") as annees')
            )
            ->groupBy('etablissements.code_efp', 'etablissements.nom_efp', 'filieres.id', 'filieres.nom_filiere', 'filieres.code_filiere', 'secteurs.nom_secteur')
            ->orderBy('etablissements.nom_efp')
            ->orderByDesc('effectif_total')
            ->get();

        // Effectifs par année de formation
        $effectifsParAnnee = DB::table('groupes')
            ->join('etablissements', 'groupes.code_efp', '=', 'etablissements.code_efp')
            ->where('etablissements.complexe_id', $complexe->id)
            ->select(
                'groupes.annee_formation',
                DB::raw('SUM(groupes.effectif_groupe) as effectif_total'),
                DB::raw('COUNT(DISTINCT groupes.id) as nombre_groupes')
            )
            ->groupBy('groupes.annee_formation')
            ->orderBy('groupes.annee_formation')
            ->get();

        // Effectifs par filière (Top 10)
        $effectifsParFiliere = DB::table('groupes')
            ->join('etablissements', 'groupes.code_efp', '=', 'etablissements.code_efp')
            ->join('filieres', 'groupes.filiere_id', '=', 'filieres.id')
            ->where('etablissements.complexe_id', $complexe->id)
            ->select(
                'filieres.nom_filiere',
                'filieres.code_filiere',
                DB::raw('SUM(groupes.effectif_groupe) as effectif_total'),
                DB::raw('COUNT(DISTINCT groupes.id) as nombre_groupes')
            )
            ->groupBy('filieres.id', 'filieres.nom_filiere', 'filieres.code_filiere')
            ->orderByDesc('effectif_total')
            ->limit(10)
            ->get();

        // Effectifs par secteur
        $effectifsParSecteur = DB::table('groupes')
            ->join('etablissements', 'groupes.code_efp', '=', 'etablissements.code_efp')
            ->join('filieres', 'groupes.filiere_id', '=', 'filieres.id')
            ->join('secteurs', 'filieres.secteur_id', '=', 'secteurs.id')
            ->where('etablissements.complexe_id', $complexe->id)
            ->select(
                'secteurs.nom_secteur',
                DB::raw('SUM(groupes.effectif_groupe) as effectif_total'),
                DB::raw('COUNT(DISTINCT groupes.id) as nombre_groupes'),
                DB::raw('COUNT(DISTINCT filieres.id) as nombre_filieres')
            )
            ->groupBy('secteurs.id', 'secteurs.nom_secteur')
            ->orderByDesc('effectif_total')
            ->get();

        $totalApprenants = $effectifsParEtablissement->sum('effectif_total');
        $totalGroupes = $effectifsParEtablissement->sum('nombre_groupes');
        $totalFilieres = DB::table('filieres')
            ->join('etablissements', 'filieres.code_efp', '=', 'etablissements.code_efp')
            ->where('etablissements.complexe_id', $complexe->id)
            ->distinct('filieres.id')->count('filieres.id');
        $totalSecteurs = DB::table('secteurs')
            ->join('etablissements', 'secteurs.code_efp', '=', 'etablissements.code_efp')
            ->where('etablissements.complexe_id', $complexe->id)
            ->distinct('secteurs.id')->count('secteurs.id');

        return view('administrationcomplexe.effectifs.index', compact(
            'complexe',
            'effectifsParEtablissement',
            'detailParEtabFiliere',
            'effectifsParAnnee',
            'effectifsParFiliere',
            'effectifsParSecteur',
            'totalApprenants',
            'totalGroupes',
            'totalFilieres',
            'totalSecteurs'
        ));
    }
}
