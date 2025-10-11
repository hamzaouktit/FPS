<?php

namespace App\Imports;

use App\Models\Secteur;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Models\Formation;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Formateur;
use App\Models\Avancement;
use App\Models\Etablissement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class DataImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $etablissement;
    protected $errors = [];
    protected $imported = 0;
    protected $skipped = 0;
    protected $updated = 0;

    public function __construct()
    {
        $this->etablissement = Auth::user()->etablissement;
        
        if (!$this->etablissement) {
            throw new \Exception('Aucun établissement associé à votre compte.');
        }
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        
        try {
            foreach ($rows as $index => $row) {
                $this->processRow($row, $index + 2);
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de l\'importation: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function processRow($row, $lineNumber)
    {
        try {
            // Vérifier si cette ligne concerne l'établissement du directeur connecté
            $codeEfp = trim($row['code_efp'] ?? '');
            
            if (empty($codeEfp) || $codeEfp !== $this->etablissement->code_efp) {
                $this->skipped++;
                return;
            }

            // 1. Traiter le secteur
            $nomSecteur = trim($row['secteur'] ?? '');
            if (!empty($nomSecteur)) {
                Secteur::firstOrCreate(
                    ['nom_secteur' => $nomSecteur, 'code_efp' => $codeEfp]
                );
            }

            // 2. Traiter le niveau
            $niveau = trim($row['niveau'] ?? '');
            if (!empty($niveau)) {
                Niveau::firstOrCreate(
                    ['niveau' => $niveau, 'code_efp' => $codeEfp]
                );
            }

            // 3. Traiter la filière
            $codeFiliere = trim($row['code_filiere'] ?? '');
            $nomFiliere = trim($row['filiere'] ?? '');
            if (!empty($codeFiliere) && !empty($nomFiliere) && !empty($nomSecteur)) {
                Filiere::firstOrCreate(
                    ['code_filiere' => $codeFiliere],
                    [
                        'nom_filiere' => $nomFiliere,
                        'nom_secteur' => $nomSecteur,
                        'code_efp' => $codeEfp
                    ]
                );
            }

            // 4. Traiter la formation
            $annee = intval($row['annee'] ?? date('Y'));
            $typeFormation = trim($row['type_de_formation'] ?? '');
            $creneau = trim($row['creneau'] ?? '');

            $formation = null;
            if (!empty($codeFiliere) && !empty($niveau)) {
                $formation = Formation::firstOrCreate(
                    [
                        'annee' => $annee,
                        'code_efp' => $codeEfp,
                        'niveau' => $niveau,
                        'code_filiere' => $codeFiliere
                    ],
                    [
                        'type_formation' => $typeFormation,
                        'creneau' => $creneau
                    ]
                );
            }

            // 5. Traiter le groupe
            $groupe = trim($row['groupe'] ?? '');
            $effectifGroupe = intval($row['effectif_groupe'] ?? 0);
            $sousGroupe = trim($row['sous_groupe'] ?? '');
            $statutSousGroupe = trim($row['statut_sous_groupe'] ?? '');
            $fusionGroupe = trim($row['fusiongroupe'] ?? '');
            $codeFusion = trim($row['code_fusion'] ?? '');
            $anneeFormation = intval($row['annee_de_formation'] ?? 1);

            if (!empty($groupe) && $formation) {
                Groupe::firstOrCreate(
                    ['groupe' => $groupe],
                    [
                        'id_formation' => $formation->id,
                        'effectif_groupe' => $effectifGroupe,
                        'sous_groupe' => $sousGroupe,
                        'statut_sous_groupe' => $statutSousGroupe,
                        'fusion_groupe' => $fusionGroupe,
                        'code_fusion' => $codeFusion,
                        'annee_formation' => $anneeFormation,
                        'code_efp' => $codeEfp
                    ]
                );
            }

            // 6. Traiter le module
            $codeModule = trim($row['code_module'] ?? '');
            $nomModule = trim($row['module'] ?? '');
            $regional = trim($row['regional'] ?? '');

            if (!empty($codeModule) && !empty($nomModule)) {
                Module::firstOrCreate(
                    ['code_module' => $codeModule],
                    [
                        'nom_module' => $nomModule,
                        'regional' => $regional,
                        'code_efp' => $codeEfp
                    ]
                );
            }

            // 7. Traiter les formateurs
            $mleAffectePresentiel = trim($row['mle_affecte_presentiel_actif'] ?? '');
            $formateurPresentiel = trim($row['formateur_affecte_presentiel_actif'] ?? '');
            $mleAffecteSyn = trim($row['mle_affecte_syn_actif'] ?? '');
            $formateurSyn = trim($row['formateur_affecte_syn_actif'] ?? '');

            if (!empty($mleAffectePresentiel) && !empty($formateurPresentiel)) {
                Formateur::firstOrCreate(
                    ['mle' => $mleAffectePresentiel],
                    [
                        'nom_formateur' => $formateurPresentiel,
                        'code_efp' => $codeEfp
                    ]
                );
            }

            if (!empty($mleAffecteSyn) && !empty($formateurSyn) && $mleAffecteSyn !== $mleAffectePresentiel) {
                Formateur::firstOrCreate(
                    ['mle' => $mleAffecteSyn],
                    [
                        'nom_formateur' => $formateurSyn,
                        'code_efp' => $codeEfp
                    ]
                );
            }

            // 8. Traiter l'avancement
            if (!empty($groupe) && !empty($codeModule)) {
                $dateMaj = $this->parseDate($row['date_maj'] ?? null);
                $mode = trim($row['mode'] ?? '');

                $avancementData = [
                    'groupe' => $groupe,
                    'code_module' => $codeModule
                ];

                $avancementValues = [
                    'date_maj' => $dateMaj,
                    'mode' => $mode,
                    'mle_presentiel' => !empty($mleAffectePresentiel) ? $mleAffectePresentiel : null,
                    'mle_syn' => !empty($mleAffecteSyn) ? $mleAffecteSyn : null,
                    
                    // Heures S1
                    'mhp_s1_drif' => $this->parseDecimal($row['mhp_s1_drif'] ?? 0),
                    'mhsyn_s1_drif' => $this->parseDecimal($row['mhsyn_s1_drif'] ?? 0),
                    'mhasyn_s1_drif' => $this->parseDecimal($row['mhasyn_s1_drif'] ?? 0),
                    'mh_totale_s1_drif' => $this->parseDecimal($row['mh_totale_s1_drif'] ?? 0),
                    
                    // Heures S2
                    'mhp_s2_drif' => $this->parseDecimal($row['mhp_s2_drif'] ?? 0),
                    'mhsyn_s2_drif' => $this->parseDecimal($row['mhsyn_s2_drif'] ?? 0),
                    'mhasyn_s2_drif' => $this->parseDecimal($row['mhasyn_s2_drif'] ?? 0),
                    'mh_totale_s2_drif' => $this->parseDecimal($row['mh_totale_s2_drif'] ?? 0),
                    
                    // Totaux DRIF
                    'mhp_totale_drif' => $this->parseDecimal($row['mhp_totale_drif'] ?? 0),
                    'mhsyn_totale_drif' => $this->parseDecimal($row['mhsyn_totale_drif'] ?? 0),
                    'mhasyn_totale_drif' => $this->parseDecimal($row['mhasyn_totale_drif'] ?? 0),
                    'mh_totale_drif' => $this->parseDecimal($row['mh_totale_drif'] ?? 0),
                    
                    // Affectées et réalisées
                    'mh_affectee_presentiel' => $this->parseDecimal($row['mh_affectee_presentiel'] ?? 0),
                    'mh_affectee_sync' => $this->parseDecimal($row['mh_affectee_sync'] ?? 0),
                    'mh_affectee_globale' => $this->parseDecimal($row['mh_affectee_globale_p_syn'] ?? 0),
                    'mh_realisee_presentiel' => $this->parseDecimal($row['mh_realisee_presentiel'] ?? 0),
                    'mh_realisee_sync' => $this->parseDecimal($row['mh_realisee_sync'] ?? 0),
                    'mh_realisee_globale' => $this->parseDecimal($row['mh_realisee_globale'] ?? 0),
                    
                    // Taux et autres
                    'taux_realisation_presentiel' => $this->parseDecimal($row['taux_realisation_presentiel'] ?? 0),
                    'taux_realisation_syn' => $this->parseDecimal($row['taux_realisation_syn'] ?? 0),
                    'taux_realisation_global' => $this->parseDecimal($row['taux_realisation_p_syn'] ?? 0),
                    'moy_absence' => $this->parseDecimal($row['moy_absence'] ?? 0),
                    'nb_cc' => intval($row['nb_cc'] ?? 0),
                    'seance_efm' => trim($row['seance_efm'] ?? ''),
                    'validation_efm' => trim($row['validation_efm'] ?? ''),
                    'classe_teams' => trim($row['classe_teams'] ?? ''),
                    'module_pie' => trim($row['module_pie'] ?? ''),
                    'efp_pie' => trim($row['efp_pie'] ?? '')
                ];

                $avancement = Avancement::updateOrCreate($avancementData, $avancementValues);
                
                if ($avancement->wasRecentlyCreated) {
                    $this->imported++;
                } else {
                    $this->updated++;
                }
            }

        } catch (\Exception $e) {
            $this->errors[] = "Ligne {$lineNumber}: " . $e->getMessage();
            Log::error("Erreur ligne {$lineNumber}: " . $e->getMessage());
            Log::error("Données de la ligne: " . json_encode($row->toArray()));
        }
    }

    protected function parseDate($dateString)
    {
        if (empty($dateString)) {
            return now();
        }

        try {
            // Essayer différents formats de date
            $formats = [
                'd/m/Y H:i:s',
                'd/m/Y H:i',
                'd/m/Y',
                'Y-m-d H:i:s',
                'Y-m-d',
                'd-m-Y H:i:s',
                'd-m-Y',
                'm/d/Y H:i:s',
                'm/d/Y'
            ];
            
            foreach ($formats as $format) {
                try {
                    $date = Carbon::createFromFormat($format, trim($dateString));
                    if ($date) {
                        return $date;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            // Si aucun format ne marche, essayer le parsing automatique
            return Carbon::parse($dateString);
            
        } catch (\Exception $e) {
            Log::warning("Impossible de parser la date: {$dateString}. Utilisation de la date actuelle.");
            return now();
        }
    }

    protected function parseDecimal($value)
    {
        if (empty($value)) {
            return 0;
        }
        
        // Remplacer la virgule par un point pour les nombres décimaux
        $value = str_replace(',', '.', trim($value));
        
        // Supprimer les espaces
        $value = str_replace(' ', '', $value);
        
        return floatval($value);
    }

    public function rules(): array
    {
        return [
            'code_efp' => 'required|string',
            'annee' => 'nullable|integer',
            'niveau' => 'nullable|string',
            'secteur' => 'nullable|string',
            'code_filiere' => 'nullable|string',
            'filiere' => 'nullable|string',
            'groupe' => 'nullable|string',
            'code_module' => 'nullable|string',
            'module' => 'nullable|string'
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getImported()
    {
        return $this->imported;
    }

    public function getSkipped()
    {
        return $this->skipped;
    }

    public function getUpdated()
    {
        return $this->updated;
    }
}