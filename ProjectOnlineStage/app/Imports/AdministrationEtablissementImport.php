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
use App\Models\HistoriqueAvancement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Validators\Failure;
use Carbon\Carbon;
use Throwable;

class AdministrationEtablissementImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError, WithChunkReading, WithEvents
{
    protected $codeEfp;
    protected $importId;
    protected $errors = [];
    protected $imported = 0;
    protected $skipped = 0;
    protected $deleted = 0;

    protected $cachedFormateurs = [];
    protected $cachedSecteurs = [];
    protected $cachedNiveaux = [];
    protected $cachedFilieres = [];
    protected $cachedFormations = [];
    protected $cachedGroupes = [];
    protected $cachedModules = [];

    public function __construct($codeEfp, $importId)
    {
        $this->codeEfp = $codeEfp;
        $this->importId = $importId;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        $startTime = microtime(true);
        Log::info("🔄 Début du traitement d'un chunk de " . $rows->count() . " lignes.");

        try {
            // Nettoyer l'EFP une seule fois pour tout l'import
            $this->cleanEfpDataIfNeeded();

            $affectationsToInsert = [];
            $avancementsToInsert = [];
            $etablissementFormateurToInsert = [];
            $formateurSecteurToInsert = [];
            $formateurModuleToInsert = [];
            $filiereModuleToInsert = [];

            // Préchargement des formateurs existants de la BDD pour éviter requêtes N+1
            $this->preloadFormateurs();

            foreach ($rows as $index => $row) {
                // Sanitisation rapide
                $sanitizedRow = [];
                foreach ($row as $key => $value) {
                    $sanitizedRow[$key] = $this->sanitize($value);
                }
                $row = $sanitizedRow;
                
                $lineNumber = $index + 2; 

                $codeEfp = trim($row['code_efp'] ?? '');
                if (empty($codeEfp)) {
                    $this->errors[] = "Ligne {$lineNumber}: code_efp requis";
                    $this->skipped++;
                    continue;
                }
                
                if ($this->codeEfp && $codeEfp !== $this->codeEfp) {
                    $this->skipped++;
                    continue;
                }

                // Récupération ou création via les relations
                $secteur = $this->getOrCreateSecteur($row, $codeEfp);
                $niveau = $this->getOrCreateNiveau($row, $codeEfp);
                $filiere = $this->getOrCreateFiliere($row, $codeEfp, $secteur);
                $formation = $this->getOrCreateFormation($row, $codeEfp, $filiere, $niveau);
                $groupe = $this->getOrCreateGroupe($row, $codeEfp, $filiere, $formation);
                $module = $this->getOrCreateModule($row, $codeEfp, $filiere, $filiereModuleToInsert);

                // Formateurs
                $mleAffectePresentiel = trim($row['mle_affecte_presentiel_actif'] ?? '');
                $formateurPresentiel = trim($row['formateur_affecte_presentiel_actif'] ?? '');
                $mleAffecteSyn = trim($row['mle_affecte_syn_actif'] ?? '');
                $formateurSyn = trim($row['formateur_affecte_syn_actif'] ?? '');

                $formateurPresId = $this->processFormateur($mleAffectePresentiel, $formateurPresentiel, $codeEfp, $secteur, $module, $etablissementFormateurToInsert, $formateurSecteurToInsert, $formateurModuleToInsert);
                $formateurSynId = $this->processFormateur($mleAffecteSyn, $formateurSyn, $codeEfp, $secteur, $module, $etablissementFormateurToInsert, $formateurSecteurToInsert, $formateurModuleToInsert);

                if ($groupe && $module) {
                    $fusionGroupe = trim($row['fusiongroupe'] ?? $row['fusion_groupe'] ?? $row['fusion'] ?? '');
                    $codeFusion = trim($row['code_fusion'] ?? $row['codefusion'] ?? '');
                    
                    if (in_array($fusionGroupe, ['0', ''])) $fusionGroupe = null;
                    if (in_array($codeFusion, ['0', ''])) $codeFusion = null;

                    $mhpTotaleDrif = $this->parseDecimal($row['mhp_totale_drif'] ?? 0);
                    $mhsynTotaleDrif = $this->parseDecimal($row['mhsyn_totale_drif'] ?? 0);
                    $mhTotaleDrif = $this->calculateMhTotaleDrif($formation, $codeModule ?? null, $mhpTotaleDrif, $mhsynTotaleDrif);

                    // Ajout pour insertion par lots d'Affectation
                    $affectationData = [
                        'groupe_id' => $groupe->id,
                        'module_id' => $module->id,
                        'code_efp' => $codeEfp,
                        'fusion_groupe' => $fusionGroupe,
                        'code_fusion' => $codeFusion,
                        'mle_affecte_presentiel' => !empty($mleAffectePresentiel) ? $mleAffectePresentiel : null,
                        'formateur_affecte_presentiel' => !empty($formateurPresentiel) ? $formateurPresentiel : null,
                        'mle_affecte_syn' => !empty($mleAffecteSyn) ? $mleAffecteSyn : null,
                        'formateur_affecte_syn' => !empty($formateurSyn) ? $formateurSyn : null,
                        'mhp_s1_drif' => $this->parseDecimal($row['mhp_s1_drif'] ?? 0),
                        'mhsyn_s1_drif' => $this->parseDecimal($row['mhsyn_s1_drif'] ?? 0),
                        'mhasyn_s1_drif' => $this->parseDecimal($row['mhasyn_s1_drif'] ?? 0),
                        'mh_totale_s1_drif' => $this->parseDecimal($row['mh_totale_s1_drif'] ?? 0),
                        'mhp_s2_drif' => $this->parseDecimal($row['mhp_s2_drif'] ?? 0),
                        'mhsyn_s2_drif' => $this->parseDecimal($row['mhsyn_s2_drif'] ?? 0),
                        'mhasyn_s2_drif' => $this->parseDecimal($row['mhasyn_s2_drif'] ?? 0),
                        'mh_totale_s2_drif' => $this->parseDecimal($row['mh_totale_s2_drif'] ?? 0),
                        'mhp_totale_drif' => $mhpTotaleDrif,
                        'mhsyn_totale_drif' => $mhsynTotaleDrif,
                        'mhasyn_totale_drif' => $this->parseDecimal($row['mhasyn_totale_drif'] ?? 0),
                        'mh_totale_drif' => $mhTotaleDrif,
                        'mh_affectee_presentiel' => $this->parseDecimal($row['mh_affectee_presentiel'] ?? 0),
                        'mh_affectee_sync' => $this->parseDecimal($row['mh_affectee_sync'] ?? 0),
                        'mh_affectee_globale' => $this->parseDecimal($row['mh_affectee_globale_p_syn'] ?? 0),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    $affectationsToInsert[] = [
                        'affectation' => $affectationData,
                        'avancement' => [
                            'code_efp' => $codeEfp,
                            'mh_realisee_presentiel' => $this->parseDecimal($row['mh_realisee_presentiel'] ?? 0),
                            'mh_realisee_sync' => $this->parseDecimal($row['mh_realisee_sync'] ?? 0),
                            'mh_realisee_globale' => $this->parseDecimal($row['mh_realisee_globale'] ?? 0),
                            'taux_realisation_presentiel' => $this->parseDecimal($row['taux_realisation_presentiel'] ?? 0),
                            'taux_realisation_syn' => $this->parseDecimal($row['taux_realisation_syn'] ?? 0),
                            'taux_realisation_globale' => $this->parseDecimal($row['taux_realisation_p_syn'] ?? 0),
                            'moyenne_absence' => $this->parseDecimal($row['moy_absence'] ?? 0),
                            'nb_cc' => intval($row['nb_cc'] ?? 0),
                            'seance_efm' => trim($row['seance_efm'] ?? 'Non'),
                            'validation_efm' => trim($row['validation_efm'] ?? 'non'),
                            'classe_teams' => trim($row['classe_teams'] ?? ''),
                            'date_maj' => $this->parseDate($row['date_maj'] ?? null)->format('Y-m-d H:i:s'),
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    ];

                    $this->imported++;

                } else {
                    $this->skipped++;
                }
            }

            // ============================================
            // INSERTIONS BATCH
            // ============================================
            if (!empty($filiereModuleToInsert)) {
                DB::table('filiere_module')->insertOrIgnore(array_values(array_unique($filiereModuleToInsert, SORT_REGULAR)));
            }

            if (!empty($etablissementFormateurToInsert)) {
                DB::table('etablissement_formateur')->insertOrIgnore(array_values(array_unique($etablissementFormateurToInsert, SORT_REGULAR)));
            }

            if (!empty($formateurSecteurToInsert)) {
                DB::table('formateur_secteur')->insertOrIgnore(array_values(array_unique($formateurSecteurToInsert, SORT_REGULAR)));
            }
            if (!empty($formateurModuleToInsert)) {
                DB::table('formateur_module')->insertOrIgnore(array_values(array_unique($formateurModuleToInsert, SORT_REGULAR)));
            }

            // Insertion des affectations
            if (!empty($affectationsToInsert)) {
                $affectationChunks = array_chunk($affectationsToInsert, 200);
                
                foreach ($affectationChunks as $chunk) {
                    $affectationsData = array_column($chunk, 'affectation');
                    
                    // Utilisation de insertGetId avec foreach si on a pas le paquet de retour d'IDs
                    // ou mieux : création via modèle pour lier facilement avec avancement
                    foreach ($chunk as $item) {
                        $affectation = Affectation::create($item['affectation']);
                        $avancementData = $item['avancement'];
                        $avancementData['affectation_id'] = $affectation->id;
                        $avancementsToInsert[] = $avancementData;
                    }
                }
            }

            // Insertion des avancements
            if (!empty($avancementsToInsert)) {
                foreach (array_chunk($avancementsToInsert, 500) as $chunk) {
                    DB::table('avancements')->insert($chunk);
                }
            }

            DB::commit();

            $duration = microtime(true) - $startTime;
            Log::info("✅ Chunk traité avec succès. Temps: " . round($duration, 2) . "s");
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('❌ Erreur lors de l\'importation: ' . $e->getMessage());
            throw $e;
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function(AfterImport $event) {
                // Nettoyage des formateurs orphelins UNE SEULE FOIS pour cet import
                $this->cleanOrphanFormateurs();
            },
        ];
    }

    protected function cleanEfpDataIfNeeded()
    {
        // Cache la clef de nettoyage avec un TTL court (1 heure)
        $cacheKey = "cleaned_efp_{$this->codeEfp}_{$this->importId}";
        if (!Cache::has($cacheKey)) {
            $this->saveHistoriqueBeforeImport();
            $this->deleteExistingData();
            Cache::put($cacheKey, true, now()->addHour());
            Log::info("🗑️ Nettoyage de l'EFP {$this->codeEfp} réalisé pour l'import {$this->importId}.");
        }
    }

    protected function preloadFormateurs()
    {
        if (empty($this->cachedFormateurs)) {
            $formateurs = Formateur::all()->keyBy('mle');
            foreach ($formateurs as $formateur) {
                $this->cachedFormateurs[$formateur->mle] = $formateur;
            }
        }
    }

    protected function processFormateur($mle, $nom, $codeEfp, $secteur, $module, &$etablissementFormateur, &$formateurSecteur, &$formateurModule)
    {
        if (empty($mle) || empty($nom)) return null;

        if (!isset($this->cachedFormateurs[$mle])) {
            $formateurObj = Formateur::firstOrCreate(
                ['mle' => $mle],
                [
                    'nom_complet' => $nom,
                    'type' => $this->determineTypeFormateur($mle),
                    'masse_horaire' => 910.00
                ]
            );
            $this->cachedFormateurs[$mle] = $formateurObj;
        } else {
            $formateurObj = $this->cachedFormateurs[$mle];
        }

        if ($formateurObj) {
            $etablissementFormateur[$codeEfp . '_' . $formateurObj->id] = [
                'code_efp' => $codeEfp,
                'formateur_id' => $formateurObj->id,
            ];

            if ($secteur) {
                $formateurSecteur[$formateurObj->id . '_' . $secteur->id] = [
                    'formateur_id' => $formateurObj->id,
                    'secteur_id' => $secteur->id,
                ];
            }

            if ($module) {
                $formateurModule[$formateurObj->id . '_' . $module->id] = [
                    'formateur_id' => $formateurObj->id,
                    'module_id' => $module->id,
                ];
            }
            return $formateurObj->id;
        }

        return null;
    }


    protected function saveHistoriqueBeforeImport()
    {
        try {
            $dateCapture = Carbon::today();
            Log::info("📸 Capture historique avant suppression...");
            
            // Correction de la requête: chercher les enregistrements existants ou utiliser un Range
            $start = $dateCapture->copy()->startOfDay();
            $end = $dateCapture->copy()->endOfDay();

            $existingCount = HistoriqueAvancement::where('code_efp', $this->codeEfp)
                ->where('date_capture', '>=', $start)
                ->where('date_capture', '<=', $end)
                ->count();
            
            if ($existingCount > 0) {
                HistoriqueAvancement::where('code_efp', $this->codeEfp)
                    ->where('date_capture', '>=', $start)
                    ->where('date_capture', '<=', $end)
                    ->delete();
            }
            
            // Utiliser des chunk/batch pour économiser la mémoire et éviter N+1
            $avancements = Avancement::where('code_efp', $this->codeEfp)
                ->with([
                    'affectation.groupe',
                    'affectation.module',
                    'affectation.formateurPresentiel',
                    'affectation.formateurSyn',
                    'affectation.groupe.filiere'
                ])
                ->get(); // Peut être optimisé avec chunk pour les bases très larges
            
            $historiqueToInsert = [];
            
            foreach ($avancements as $avancement) {
                $affectation = $avancement->affectation;
                if (!$affectation) continue;

                $historiqueToInsert[] = [
                    'date_capture' => $dateCapture,
                    'affectation_id' => $affectation->id,
                    'code_efp' => $this->codeEfp,
                    'formateur_presentiel' => $affectation->formateur_affecte_presentiel ?? null,
                    'mle_presentiel' => $affectation->mle_affecte_presentiel ?? null,
                    'formateur_syn' => $affectation->formateur_affecte_syn ?? null,
                    'mle_syn' => $affectation->mle_affecte_syn ?? null,
                    'nom_module' => $affectation->module->nom_module ?? null,
                    'code_module' => $affectation->module->code_module ?? null,
                    'code_groupe' => $affectation->groupe->code_groupe ?? null,
                    'nom_filiere' => $affectation->groupe->filiere->nom_filiere ?? null,
                    'mh_affectee_presentiel' => $affectation->mh_affectee_presentiel,
                    'mh_affectee_sync' => $affectation->mh_affectee_sync,
                    'mh_affectee_globale' => $affectation->mh_affectee_globale,
                    'mh_realisee_presentiel' => $avancement->mh_realisee_presentiel,
                    'mh_realisee_sync' => $avancement->mh_realisee_sync,
                    'mh_realisee_globale' => $avancement->mh_realisee_globale,
                    'taux_realisation_presentiel' => $avancement->taux_realisation_presentiel,
                    'taux_realisation_syn' => $avancement->taux_realisation_syn,
                    'taux_realisation_globale' => $avancement->taux_realisation_globale,
                    'moyenne_absence' => $avancement->moyenne_absence,
                    'nb_cc' => $avancement->nb_cc,
                    'seance_efm' => $avancement->seance_efm,
                    'validation_efm' => $avancement->validation_efm,
                    'classe_teams' => $avancement->classe_teams
                ];
            }
            
            if (!empty($historiqueToInsert)) {
                foreach (array_chunk($historiqueToInsert, 500) as $chunk) {
                    HistoriqueAvancement::insert($chunk);
                }
            }
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la sauvegarde de l\'historique: ' . $e->getMessage());
        }
    }

    protected function deleteExistingData()
    {
        try {
            Avancement::where('code_efp', $this->codeEfp)->delete();
            Affectation::where('code_efp', $this->codeEfp)->delete();
            
            DB::table('etablissement_formateur')
                ->where('code_efp', $this->codeEfp)
                ->delete();

            $moduleIds = Module::where('code_efp', $this->codeEfp)->pluck('id')->toArray();
            if (!empty($moduleIds)) {
                DB::table('filiere_module')->whereIn('module_id', $moduleIds)->delete();
            }

            Module::where('code_efp', $this->codeEfp)->delete();
            Groupe::where('code_efp', $this->codeEfp)->delete();
            Formation::where('code_efp', $this->codeEfp)->delete();
            Filiere::where('code_efp', $this->codeEfp)->delete();
            Niveau::where('code_efp', $this->codeEfp)->delete();
            Secteur::where('code_efp', $this->codeEfp)->delete();

        } catch (\Exception $e) {
            Log::error('❌ Erreur suppression: ' . $e->getMessage());
        }
    }

    protected function cleanOrphanFormateurs()
    {
        try {
            $formateursSansEtab = DB::table('formateurs as f')
                ->leftJoin('etablissement_formateur as ef', 'f.id', '=', 'ef.formateur_id')
                ->whereNull('ef.formateur_id')
                ->pluck('f.id')
                ->toArray();

            if (!empty($formateursSansEtab)) {
                DB::table('formateur_module')->whereIn('formateur_id', $formateursSansEtab)->delete();
                DB::table('formateur_secteur')->whereIn('formateur_id', $formateursSansEtab)->delete();
                Formateur::whereIn('id', $formateursSansEtab)->delete();
                Log::info("Nettoyage: " . count($formateursSansEtab) . " formateurs orphelins supprimés.");
            }
        } catch (\Exception $e) {
            Log::error("Erreur nettoyage orphelins: " . $e->getMessage());
        }
    }

    // == Helpers pour créer les relations manquantes de manière optimisée ==

    protected function getOrCreateSecteur($row, $codeEfp) {
        $nomSecteur = trim($row['secteur'] ?? '');
        if (empty($nomSecteur)) return null;

        $cacheKey = $codeEfp . '_' . $nomSecteur;
        if (!isset($this->cachedSecteurs[$cacheKey])) {
            $this->cachedSecteurs[$cacheKey] = Secteur::firstOrCreate(
                ['nom_secteur' => $nomSecteur, 'code_efp' => $codeEfp]
            );
        }
        return $this->cachedSecteurs[$cacheKey];
    }

    protected function getOrCreateNiveau($row, $codeEfp) {
        $niveauCode = trim($row['niveau'] ?? '');
        if (empty($niveauCode)) return null;

        $cacheKey = $codeEfp . '_' . $niveauCode;
        if (!isset($this->cachedNiveaux[$cacheKey])) {
            $this->cachedNiveaux[$cacheKey] = Niveau::firstOrCreate(
                ['nom' => $this->getNiveauNom($niveauCode), 'code_efp' => $codeEfp]
            );
        }
        return $this->cachedNiveaux[$cacheKey];
    }

    protected function getOrCreateFiliere($row, $codeEfp, $secteur) {
        $codeFiliere = trim($row['code_filiere'] ?? '');
        $nomFiliere = trim($row['filiere'] ?? '');
        if (empty($codeFiliere) || empty($nomFiliere) || !$secteur) return null;

        $cacheKey = $codeEfp . '_' . $codeFiliere;
        if (!isset($this->cachedFilieres[$cacheKey])) {
            $this->cachedFilieres[$cacheKey] = Filiere::firstOrCreate(
                ['code_filiere' => $codeFiliere, 'code_efp' => $codeEfp],
                ['nom_filiere' => $nomFiliere, 'secteur_id' => $secteur->id]
            );
        }
        return $this->cachedFilieres[$cacheKey];
    }

    protected function getOrCreateFormation($row, $codeEfp, $filiere, $niveau) {
        $annee = intval($row['annee'] ?? date('Y'));
        $typeFormation = trim($row['type_de_formation'] ?? 'Diplômante');
        $mode = trim($row['mode'] ?? 'Résidentiel');
        $creneau = trim($row['creneau'] ?? 'CDJ');
        
        if (!$filiere || !$niveau) return null;

        $cacheKey = $codeEfp.'_'.$annee.'_'.$filiere->id.'_'.$niveau->id.'_'.$typeFormation.'_'.$mode.'_'.$creneau;
        if (!isset($this->cachedFormations[$cacheKey])) {
            $this->cachedFormations[$cacheKey] = Formation::firstOrCreate([
                'annee' => $annee,
                'filiere_id' => $filiere->id,
                'niveau_id' => $niveau->id,
                'type' => $typeFormation,
                'mode' => $mode,
                'creneau' => $creneau,
                'code_efp' => $codeEfp
            ]);
        }
        return $this->cachedFormations[$cacheKey];
    }

    protected function getOrCreateGroupe($row, $codeEfp, $filiere, $formation) {
        $nomGroupe = trim($row['groupe'] ?? '');
        $effectifGroupe = intval($row['effectif_groupe'] ?? 0);
        $sousGroupe = trim($row['sous_groupe'] ?? '');
        $statutSousGroupe = trim($row['statut_sous_groupe'] ?? 'Actif');
        $anneeFormation = intval($row['annee_de_formation'] ?? 1);

        if (empty($nomGroupe) || !$filiere || !$formation) return null;

        $cacheKey = $codeEfp . '_' . $nomGroupe;
        if (!isset($this->cachedGroupes[$cacheKey])) {
            $this->cachedGroupes[$cacheKey] = Groupe::firstOrCreate(
                ['code_groupe' => $nomGroupe, 'code_efp' => $codeEfp],
                [
                    'effectif_groupe' => $effectifGroupe,
                    'statut' => $statutSousGroupe,
                    'sous_groupe' => $sousGroupe,
                    'statut_sous_groupe' => $statutSousGroupe,
                    'annee_formation' => $anneeFormation,
                    'filiere_id' => $filiere->id,
                    'formation_id' => $formation->id,
                ]
            );
        }
        return $this->cachedGroupes[$cacheKey];
    }

    protected function getOrCreateModule($row, $codeEfp, $filiere, &$filiereModuleToInsert) {
        $codeModule = trim($row['code_module'] ?? '');
        $nomModule = trim($row['module'] ?? '');
        $regional = trim($row['regional'] ?? 'N');
        $modulePie = trim($row['module_pie'] ?? '');
        $efpPie = trim($row['efp_pie'] ?? '');

        if (empty($codeModule) || empty($nomModule) || !$filiere) return null;

        $cacheKey = $codeEfp . '_' . $codeModule;
        
        if (!isset($this->cachedModules[$cacheKey])) {
            $module = Module::firstOrCreate(
                ['code_module' => $codeModule, 'code_efp' => $codeEfp],
                [
                    'nom_module' => $nomModule,
                    'regional' => strtoupper($regional) === 'O' ? 'O' : 'N',
                    'module_pie' => !empty($modulePie) && strtoupper($modulePie) === 'O' ? 'O' : 'N',
                    'efp_pie' => $efpPie,
                ]
            );
            $this->cachedModules[$cacheKey] = $module;
        } else {
            $module = $this->cachedModules[$cacheKey];
        }

        if ($module) {
            $filiereModuleToInsert[] = [
                'filiere_id' => $filiere->id,
                'module_id' => $module->id,
            ];
        }

        return $module;
    }

    protected function calculateMhTotaleDrif($formation, $codeModule, $mhpTotale, $mhsynTotale)
    {
        $isAlterne = $formation && !empty($formation->mode) && strtolower(trim($formation->mode)) === 'alterné';
        $codeModuleUpper = strtoupper(trim($codeModule ?? ''));
        $isModuleMetier = !empty($codeModuleUpper) && substr($codeModuleUpper, 0, 1) === 'M';
        
        if ($isAlterne && $isModuleMetier) {
            return round(($mhpTotale / 2) + $mhsynTotale, 2);
        }
        return round($mhpTotale + $mhsynTotale, 2);
    }

    protected function parseDate($dateString)
    {
        if (empty($dateString)) return now();
        try {
            return Carbon::parse($dateString);
        } catch (\Exception $e) {
            return now();
        }
    }

    protected function parseDecimal($value)
    {
        if (empty($value)) return 0;
        $value = str_replace(',', '.', trim($value));
        $value = str_replace(' ', '', $value);
        return floatval($value);
    }

    protected function getNiveauNom($code)
    {
        $niveaux = [
            'TS' => 'Technicien Spécialisé',
            'T'  => 'Technicien',
            'S'  => 'Spécialisation',
            'Q'  => 'Qualification',
            'BP' => 'Brevet Professionnel',
            'FQ' => 'Formation Qualifiante'
        ];
        return $niveaux[$code] ?? $code;
    }

    protected function determineTypeFormateur($mle)
    {
        if (preg_match('/^(EE|Y|H|E|PB)\d+/', $mle)) {
            return 'vacataire';
        }
        return 'permanent';
    }

    protected function sanitize($value)
    {
        if (is_string($value)) {
            $value = trim($value);
            $value = strip_tags($value);
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            return $value;
        }
        return $value;
    }

    public function rules(): array
    {
        return [
            'code_efp' => ['required', 'string', 'max:255'],
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Ligne " . $failure->row() . ": " . implode(', ', $failure->errors());
            $this->skipped++;
        }
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = "Erreur inattendue: " . $e->getMessage();
        Log::error('❌ Erreur Excel: ' . $e->getMessage());
    }
}