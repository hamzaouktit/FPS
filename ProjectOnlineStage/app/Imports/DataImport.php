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
use App\Models\Affectation;
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
    protected $codeEfp;
    protected $efpNom;
    protected $errors = [];
    protected $imported = 0;
    protected $skipped = 0;
    protected $updated = 0;

    public function __construct()
    {
        // Si vous avez un système d'authentification avec établissement
        if (Auth::check() && Auth::user()->etablissement) {
            $this->codeEfp = Auth::user()->etablissement->code_efp;
            $this->efpNom = Auth::user()->etablissement->nom_efp;
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
            
            Log::info("Importation terminée: {$this->imported} importés, {$this->updated} mis à jour, {$this->skipped} ignorés");
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de l\'importation: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function processRow($row, $lineNumber)
    {
        try {
            // Récupérer les données de base
            $codeEfp = trim($row['code_efp'] ?? '');
            $efpNom = trim($row['efp'] ?? '');
            
            // Vérifier si cette ligne concerne l'établissement (si filtrage activé)
            if ($this->codeEfp && $codeEfp !== $this->codeEfp) {
                $this->skipped++;
                return;
            }

            // 1. Traiter le SECTEUR avec code_efp
            $nomSecteur = trim($row['secteur'] ?? '');
            $secteur = null;
            if (!empty($nomSecteur)) {
                $secteur = Secteur::updateOrCreate(
                    [
                        'nom' => $nomSecteur,
                        'code_efp' => $codeEfp
                    ],
                    [
                        'code' => $this->generateCode($nomSecteur)
                    ]
                );
            }

            // 2. Traiter le NIVEAU avec code_efp
            $niveauCode = trim($row['niveau'] ?? '');
            $niveau = null;
            if (!empty($niveauCode)) {
                $niveauNom = $this->getNiveauNom($niveauCode);
                $niveau = Niveau::updateOrCreate(
                    [
                        'code' => $niveauCode,
                        'code_efp' => $codeEfp
                    ],
                    [
                        'nom' => $niveauNom
                    ]
                );
            }

            // 3. Traiter la FILIERE avec code_efp
            $codeFiliere = trim($row['code_filiere'] ?? '');
            $nomFiliere = trim($row['filiere'] ?? '');
            $filiere = null;
            
            if (!empty($codeFiliere) && !empty($nomFiliere) && $secteur && $niveau) {
                $filiere = Filiere::updateOrCreate(
                    [
                        'code' => $codeFiliere,
                        'code_efp' => $codeEfp
                    ],
                    [
                        'nom' => $nomFiliere,
                        'secteur_id' => $secteur->id,
                        'niveau_id' => $niveau->id
                    ]
                );
            }

            // 4. Traiter la FORMATION avec code_efp
            $typeFormation = trim($row['type_de_formation'] ?? 'Diplômante');
            $mode = trim($row['mode'] ?? 'Résidentiel');
            $creneau = trim($row['creneau'] ?? 'CDJ');

            $formation = Formation::updateOrCreate(
                [
                    'type' => $typeFormation,
                    'mode' => $mode,
                    'creneau' => $creneau,
                    'code_efp' => $codeEfp
                ]
            );

            // 5. Traiter le GROUPE avec code_efp
            $nomGroupe = trim($row['groupe'] ?? '');
            $effectifGroupe = intval($row['effectif_groupe'] ?? 0);
            $sousGroupe = trim($row['sous_groupe'] ?? '');
            $statutSousGroupe = trim($row['statut_sous_groupe'] ?? 'Actif');
            $fusionGroupe = trim($row['fusiongroupe'] ?? '');
            $codeFusion = trim($row['code_fusion'] ?? '');
            $anneeFormation = intval($row['annee_de_formation'] ?? 1);
            $annee = intval($row['annee'] ?? date('Y'));

            $groupe = null;
            if (!empty($nomGroupe) && $filiere && $formation) {
                $groupe = Groupe::updateOrCreate(
                    [
                        'code' => $nomGroupe,
                        'code_efp' => $codeEfp
                    ],
                    [
                        'efp_code' => $codeEfp,
                        'efp_nom' => $efpNom,
                        'effectif' => $effectifGroupe,
                        'statut' => $statutSousGroupe,
                        'fusion_groupe' => $fusionGroupe,
                        'code_fusion' => $codeFusion,
                        'annee_formation' => $anneeFormation,
                        'annee' => $annee,
                        'filiere_id' => $filiere->id,
                        'formation_id' => $formation->id
                    ]
                );
            }

            // 6. Traiter le MODULE avec code_efp
            $codeModule = trim($row['code_module'] ?? '');
            $nomModule = trim($row['module'] ?? '');
            $regional = trim($row['regional'] ?? 'N');
            $modulePie = trim($row['module_pie'] ?? '');
            $efpPie = trim($row['efp_pie'] ?? '');

            $module = null;
            if (!empty($codeModule) && !empty($nomModule)) {
                // Chercher le module par CODE + NOM + FILIERE + CODE_EFP
                $searchCriteria = [
                    'code' => $codeModule,
                    'nom' => $nomModule,
                    'code_efp' => $codeEfp
                ];
                
                // Si on a une filière, on l'ajoute aux critères de recherche
                if ($filiere) {
                    $searchCriteria['filiere_id'] = $filiere->id;
                }
                
                // Recherche avec tous les critères
                $module = Module::where($searchCriteria)->first();
                
                // Si le module n'existe pas, on le crée
                if (!$module) {
                    $module = Module::create([
                        'code' => $codeModule,
                        'nom' => $nomModule,
                        'regional' => $regional,
                        'module_pie' => !empty($modulePie) && strtolower($modulePie) === 'o',
                        'efp_pie' => $efpPie,
                        'filiere_id' => $filiere ? $filiere->id : null,
                        'code_efp' => $codeEfp
                    ]);
                    
                    Log::info("Nouveau module créé: {$codeModule} - {$nomModule} (EFP: {$codeEfp})");
                } else {
                    // Mettre à jour les informations du module si nécessaire
                    $module->update([
                        'regional' => $regional,
                        'module_pie' => !empty($modulePie) && strtolower($modulePie) === 'o',
                        'efp_pie' => $efpPie,
                    ]);
                    
                    Log::debug("Module existant trouvé: {$codeModule} - {$nomModule} (ID: {$module->id})");
                }
            }

            // 7. Traiter les FORMATEURS avec code_efp
            $mleAffectePresentiel = trim($row['mle_affecte_presentiel_actif'] ?? '');
            $formateurPresentiel = trim($row['formateur_affecte_presentiel_actif'] ?? '');
            $mleAffecteSyn = trim($row['mle_affecte_syn_actif'] ?? '');
            $formateurSyn = trim($row['formateur_affecte_syn_actif'] ?? '');

            $formateurPresentielObj = null;
            if (!empty($mleAffectePresentiel) && !empty($formateurPresentiel)) {
                $formateurPresentielObj = Formateur::updateOrCreate(
                    ['mle' => $mleAffectePresentiel],
                    [
                        'nom_complet' => $formateurPresentiel,
                        'type' => $this->determineTypeFormateur($mleAffectePresentiel),
                        'code_efp' => $codeEfp
                    ]
                );
                
                // Associer le formateur au secteur et module si nécessaire
                if ($secteur) {
                    $formateurPresentielObj->secteurs()->syncWithoutDetaching([$secteur->id]);
                }
                if ($module) {
                    $formateurPresentielObj->modules()->syncWithoutDetaching([$module->id]);
                }
            }

            $formateurSynObj = null;
            if (!empty($mleAffecteSyn) && !empty($formateurSyn) && $mleAffecteSyn !== $mleAffectePresentiel) {
                $formateurSynObj = Formateur::updateOrCreate(
                    ['mle' => $mleAffecteSyn],
                    [
                        'nom_complet' => $formateurSyn,
                        'type' => $this->determineTypeFormateur($mleAffecteSyn),
                        'code_efp' => $codeEfp
                    ]
                );
                
                // Associer le formateur au secteur et module si nécessaire
                if ($secteur) {
                    $formateurSynObj->secteurs()->syncWithoutDetaching([$secteur->id]);
                }
                if ($module) {
                    $formateurSynObj->modules()->syncWithoutDetaching([$module->id]);
                }
            }

            // 8. Créer/Mettre à jour l'AFFECTATION avec code_efp
            if ($groupe && $module) {
                $affectationData = [
                    'groupe_id' => $groupe->id,
                    'module_id' => $module->id
                ];

                $affectationValues = [
                    // Code EFP
                    'code_efp' => $codeEfp,
                    
                    // Formateurs
                    'mle_affecte_presentiel' => $mleAffectePresentiel,
                    'formateur_affecte_presentiel' => $formateurPresentiel,
                    'mle_affecte_syn' => $mleAffecteSyn,
                    'formateur_affecte_syn' => $formateurSyn,
                    
                    // Heures S1 DRIF
                    'mhp_s1_drif' => $this->parseDecimal($row['mhp_s1_drif'] ?? 0),
                    'mhsyn_s1_drif' => $this->parseDecimal($row['mhsyn_s1_drif'] ?? 0),
                    'mhasyn_s1_drif' => $this->parseDecimal($row['mhasyn_s1_drif'] ?? 0),
                    'mh_totale_s1_drif' => $this->parseDecimal($row['mh_totale_s1_drif'] ?? 0),
                    
                    // Heures S2 DRIF
                    'mhp_s2_drif' => $this->parseDecimal($row['mhp_s2_drif'] ?? 0),
                    'mhsyn_s2_drif' => $this->parseDecimal($row['mhsyn_s2_drif'] ?? 0),
                    'mhasyn_s2_drif' => $this->parseDecimal($row['mhasyn_s2_drif'] ?? 0),
                    'mh_totale_s2_drif' => $this->parseDecimal($row['mh_totale_s2_drif'] ?? 0),
                    
                    // Totaux DRIF
                    'mhp_totale_drif' => $this->parseDecimal($row['mhp_totale_drif'] ?? 0),
                    'mhsyn_totale_drif' => $this->parseDecimal($row['mhsyn_totale_drif'] ?? 0),
                    'mhasyn_totale_drif' => $this->parseDecimal($row['mhasyn_totale_drif'] ?? 0),
                    'mh_totale_drif' => $this->parseDecimal($row['mh_totale_drif'] ?? 0),
                    
                    // Affectées
                    'mh_affectee_presentiel' => $this->parseDecimal($row['mh_affectee_presentiel'] ?? 0),
                    'mh_affectee_sync' => $this->parseDecimal($row['mh_affectee_sync'] ?? 0),
                    'mh_affectee_globale' => $this->parseDecimal($row['mh_affectee_globale_p_syn'] ?? 0)
                ];

                $affectation = Affectation::updateOrCreate($affectationData, $affectationValues);

                // 9. Créer/Mettre à jour l'AVANCEMENT avec code_efp
                $avancementData = [
                    'affectation_id' => $affectation->id
                ];

                $avancementValues = [
                    // Code EFP
                    'code_efp' => $codeEfp,
                    
                    // Réalisées
                    'mh_realisee_presentiel' => $this->parseDecimal($row['mh_realisee_presentiel'] ?? 0),
                    'mh_realisee_sync' => $this->parseDecimal($row['mh_realisee_sync'] ?? 0),
                    'mh_realisee_globale' => $this->parseDecimal($row['mh_realisee_globale'] ?? 0),
                    
                    // Taux de réalisation
                    'taux_realisation_presentiel' => $this->parseDecimal($row['taux_realisation_presentiel'] ?? 0),
                    'taux_realisation_syn' => $this->parseDecimal($row['taux_realisation_syn'] ?? 0),
                    'taux_realisation_globale' => $this->parseDecimal($row['taux_realisation_p_syn'] ?? 0),
                    
                    // Autres informations
                    'moyenne_absence' => $this->parseDecimal($row['moy_absence'] ?? 0),
                    'nb_cc' => intval($row['nb_cc'] ?? 0),
                    'seance_efm' => trim($row['seance_efm'] ?? 'Non'),
                    'validation_efm' => trim($row['validation_efm'] ?? 'non'),
                    'classe_teams' => trim($row['classe_teams'] ?? ''),
                    'date_maj' => $this->parseDate($row['date_maj'] ?? null)
                ];

                $avancement = Avancement::updateOrCreate($avancementData, $avancementValues);
                
                if ($avancement->wasRecentlyCreated) {
                    $this->imported++;
                } else {
                    $this->updated++;
                }
            } else {
                $this->skipped++;
                Log::warning("Ligne {$lineNumber} ignorée: Groupe ou Module manquant");
            }

        } catch (\Exception $e) {
            $this->errors[] = "Ligne {$lineNumber}: " . $e->getMessage();
            Log::error("Erreur ligne {$lineNumber}: " . $e->getMessage());
            Log::error("Données de la ligne: " . json_encode($row->toArray()));
            throw $e; // Pour rollback la transaction
        }
    }

    /**
     * Parser une date depuis différents formats
     */
    protected function parseDate($dateString)
    {
        if (empty($dateString)) {
            return now();
        }

        try {
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
            
            return Carbon::parse($dateString);
            
        } catch (\Exception $e) {
            Log::warning("Impossible de parser la date: {$dateString}. Utilisation de la date actuelle.");
            return now();
        }
    }

    /**
     * Parser un nombre décimal
     */
    protected function parseDecimal($value)
    {
        if (empty($value)) {
            return 0;
        }
        
        $value = str_replace(',', '.', trim($value));
        $value = str_replace(' ', '', $value);
        
        return floatval($value);
    }

    /**
     * Générer un code à partir d'un nom
     */
    protected function generateCode($nom)
    {
        $code = strtoupper(substr($nom, 0, 3));
        $code = preg_replace('/[^A-Z]/', '', $code);
        return $code ?: 'XXX';
    }

    /**
     * Obtenir le nom complet du niveau
     */
    protected function getNiveauNom($code)
    {
        $niveaux = [
            'TS' => 'Technicien Spécialisé',
            'T' => 'Technicien',
            'S' => 'Spécialisation',
            'Q' => 'Qualification',
            'BP' => 'Brevet Professionnel',
            'FQ' => 'Formation Qualifiante'
        ];

        return $niveaux[$code] ?? $code;
    }

    /**
     * Déterminer le type de formateur (permanent ou vacataire)
     * basé sur le matricule
     */
    protected function determineTypeFormateur($mle)
    {
        // Les matricules commençant par EE, Y, H, E sont souvent des vacataires
        if (preg_match('/^(EE|Y|H|E)\d+/', $mle)) {
            return 'vacataire';
        }
        
        return 'permanent';
    }

    /**
     * Règles de validation
     */
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

    /**
     * Getters pour les statistiques
     */
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

    /**
     * Obtenir un résumé de l'importation
     */
    public function getSummary()
    {
        return [
            'imported' => $this->imported,
            'updated' => $this->updated,
            'skipped' => $this->skipped,
            'errors' => count($this->errors),
            'total' => $this->imported + $this->updated + $this->skipped,
            'error_details' => $this->errors
        ];
    }
}