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
    protected $deleted = 0;
    
    protected $cachedFormateurs = [];
    protected $cachedSecteurs = [];
    protected $cachedNiveaux = [];
    protected $cachedFilieres = [];
    protected $cachedFormations = [];
    protected $cachedGroupes = [];
    protected $cachedModules = [];

    public function __construct()
    {
        if (Auth::check() && Auth::user()->etablissement) {
            $this->codeEfp = Auth::user()->etablissement->code_efp;
            $this->efpNom = Auth::user()->etablissement->nom_efp;
        }
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        
        try {
            $this->deleteExistingData();
            
            foreach ($rows as $index => $row) {
                $this->processRow($row, $index + 2);
            }
            
            DB::commit();
            
            Log::info("✅ Importation terminée: {$this->imported} importés, {$this->deleted} supprimés, {$this->skipped} ignorés");
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('❌ Erreur lors de l\'importation: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function deleteExistingData()
    {
        try {
            Log::info("🗑 Début de la suppression pour l'EFP: {$this->codeEfp}");

            $deleted = Avancement::where('code_efp', $this->codeEfp)->delete();
            $this->deleted += $deleted;
            Log::info("✓ Supprimé {$deleted} avancements");

            $deleted = Affectation::where('code_efp', $this->codeEfp)->delete();
            $this->deleted += $deleted;
            Log::info("✓ Supprimé {$deleted} affectations");

            $deleted = DB::table('etablissement_formateur')
                ->where('code_efp', $this->codeEfp)
                ->delete();
            Log::info("✓ Supprimé {$deleted} relations etablissement_formateur");

            $formateursSansEtab = DB::table('formateurs as f')
                ->leftJoin('etablissement_formateur as ef', 'f.id', '=', 'ef.formateur_id')
                ->whereNull('ef.formateur_id')
                ->pluck('f.id')
                ->toArray();

            if (!empty($formateursSansEtab)) {
                $deleted = DB::table('formateur_module')
                    ->whereIn('formateur_id', $formateursSansEtab)
                    ->delete();
                Log::info("✓ Supprimé {$deleted} relations formateur_module");
                
                $deleted = DB::table('formateur_secteur')
                    ->whereIn('formateur_id', $formateursSansEtab)
                    ->delete();
                Log::info("✓ Supprimé {$deleted} relations formateur_secteur");
                
                $deleted = Formateur::whereIn('id', $formateursSansEtab)->delete();
                Log::info("✓ Supprimé {$deleted} formateurs orphelins");
            }

            $moduleIds = Module::where('code_efp', $this->codeEfp)->pluck('id')->toArray();
            if (!empty($moduleIds)) {
                $deleted = DB::table('filiere_module')->whereIn('module_id', $moduleIds)->delete();
                Log::info("✓ Supprimé {$deleted} relations filiere_module");
            }

            $deleted = Module::where('code_efp', $this->codeEfp)->delete();
            $this->deleted += $deleted;
            Log::info("✓ Supprimé {$deleted} modules");

            $deleted = Groupe::where('code_efp', $this->codeEfp)->delete();
            $this->deleted += $deleted;
            Log::info("✓ Supprimé {$deleted} groupes");

            $deleted = Formation::where('code_efp', $this->codeEfp)->delete();
            $this->deleted += $deleted;
            Log::info("✓ Supprimé {$deleted} formations");

            $deleted = Filiere::where('code_efp', $this->codeEfp)->delete();
            $this->deleted += $deleted;
            Log::info("✓ Supprimé {$deleted} filières");

            $deleted = Niveau::where('code_efp', $this->codeEfp)->delete();
            $this->deleted += $deleted;
            Log::info("✓ Supprimé {$deleted} niveaux");

            $deleted = Secteur::where('code_efp', $this->codeEfp)->delete();
            $this->deleted += $deleted;
            Log::info("✓ Supprimé {$deleted} secteurs");

            Log::info("✅ Suppression terminée: {$this->deleted} enregistrements supprimés");

        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la suppression: ' . $e->getMessage());
            throw new \Exception("Erreur lors de la suppression des données: " . $e->getMessage());
        }
    }

    protected function processRow($row, $lineNumber)
    {
        try {
            if ($lineNumber === 2) {
                Log::info("=== TOUTES LES COLONNES DISPONIBLES (ligne 2) ===");
                foreach ($row as $key => $value) {
                    Log::info("Clé: '{$key}' => Valeur: '{$value}'");
                }
            }
            
            $codeEfp = trim($row['code_efp'] ?? '');
            
            if (empty($codeEfp)) {
                $this->errors[] = "Ligne {$lineNumber}: code_efp requis";
                $this->skipped++;
                return;
            }
            
            if ($this->codeEfp && $codeEfp !== $this->codeEfp) {
                $this->skipped++;
                return;
            }

            // 1. SECTEUR
            $nomSecteur = trim($row['secteur'] ?? '');
            $secteur = null;
            if (!empty($nomSecteur)) {
                $cacheKey = $codeEfp . '_' . $nomSecteur;
                if (!isset($this->cachedSecteurs[$cacheKey])) {
                    $this->cachedSecteurs[$cacheKey] = Secteur::create([
                        'nom_secteur' => $nomSecteur,
                        'code_efp' => $codeEfp
                    ]);
                }
                $secteur = $this->cachedSecteurs[$cacheKey];
            }

            // 2. NIVEAU
            $niveauCode = trim($row['niveau'] ?? '');
            $niveau = null;
            if (!empty($niveauCode)) {
                $cacheKey = $codeEfp . '_' . $niveauCode;
                if (!isset($this->cachedNiveaux[$cacheKey])) {
                    $this->cachedNiveaux[$cacheKey] = Niveau::create([
                        'nom' => $this->getNiveauNom($niveauCode),
                        'code_efp' => $codeEfp
                    ]);
                }
                $niveau = $this->cachedNiveaux[$cacheKey];
            }

            // 3. FILIERE
            $codeFiliere = trim($row['code_filiere'] ?? '');
            $nomFiliere = trim($row['filiere'] ?? '');
            $filiere = null;
            
            if (!empty($codeFiliere) && !empty($nomFiliere) && $secteur) {
                $cacheKey = $codeEfp . '_' . $codeFiliere;
                if (!isset($this->cachedFilieres[$cacheKey])) {
                    $this->cachedFilieres[$cacheKey] = Filiere::create([
                        'code_filiere' => $codeFiliere,
                        'nom_filiere' => $nomFiliere,
                        'secteur_id' => $secteur->id,
                        'code_efp' => $codeEfp
                    ]);
                }
                $filiere = $this->cachedFilieres[$cacheKey];
            }

            // 4. FORMATION
            $annee = intval($row['annee'] ?? date('Y'));
            $typeFormation = trim($row['type_de_formation'] ?? 'Diplômante');
            $mode = trim($row['mode'] ?? 'Résidentiel');
            $creneau = trim($row['creneau'] ?? 'CDJ');

            $formation = null;
            if ($filiere && $niveau) {
                $cacheKey = $codeEfp . '' . $annee . '' . $filiere->id . '' . $niveau->id . '' . $typeFormation . '' . $mode . '' . $creneau;
                if (!isset($this->cachedFormations[$cacheKey])) {
                    $this->cachedFormations[$cacheKey] = Formation::create([
                        'annee' => $annee,
                        'filiere_id' => $filiere->id,
                        'niveau_id' => $niveau->id,
                        'type' => $typeFormation,
                        'mode' => $mode,
                        'creneau' => $creneau,
                        'code_efp' => $codeEfp
                    ]);
                }
                $formation = $this->cachedFormations[$cacheKey];
            }

            // 5. GROUPE
            $nomGroupe = trim($row['groupe'] ?? '');
            $effectifGroupe = intval($row['effectif_groupe'] ?? 0);
            $sousGroupe = trim($row['sous_groupe'] ?? '');
            $statutSousGroupe = trim($row['statut_sous_groupe'] ?? 'Actif');
            $anneeFormation = intval($row['annee_de_formation'] ?? 1);

            $groupe = null;
            if (!empty($nomGroupe) && $filiere && $formation) {
                $cacheKey = $codeEfp . '_' . $nomGroupe;
                if (!isset($this->cachedGroupes[$cacheKey])) {
                    $this->cachedGroupes[$cacheKey] = Groupe::create([
                        'code_groupe' => $nomGroupe,
                        'effectif_groupe' => $effectifGroupe,
                        'statut' => $statutSousGroupe,
                        'sous_groupe' => $sousGroupe,
                        'statut_sous_groupe' => $statutSousGroupe,
                        'annee_formation' => $anneeFormation,
                        'filiere_id' => $filiere->id,
                        'formation_id' => $formation->id,
                        'code_efp' => $codeEfp
                    ]);
                }
                $groupe = $this->cachedGroupes[$cacheKey];
            }

            // 6. MODULE
            $codeModule = trim($row['code_module'] ?? '');
            $nomModule = trim($row['module'] ?? '');
            $regional = trim($row['regional'] ?? 'N');
            $modulePie = trim($row['module_pie'] ?? '');
            $efpPie = trim($row['efp_pie'] ?? '');

            $module = null;
            if (!empty($codeModule) && !empty($nomModule) && $filiere) {
                $cacheKey = $codeEfp . '' . $codeModule . '' . md5($nomModule) . '_' . $filiere->id;
                
                if (!isset($this->cachedModules[$cacheKey])) {
                    $module = Module::create([
                        'code_module' => $codeModule,
                        'nom_module' => $nomModule,
                        'regional' => strtoupper($regional) === 'O' ? 'O' : 'N',
                        'module_pie' => !empty($modulePie) && strtoupper($modulePie) === 'O' ? 'O' : 'N',
                        'efp_pie' => $efpPie,
                        'code_efp' => $codeEfp
                    ]);
                    
                    DB::table('filiere_module')->insert([
                        'filiere_id' => $filiere->id,
                        'module_id' => $module->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    
                    $this->cachedModules[$cacheKey] = $module;
                    
                    Log::info("✅ Nouveau module créé: ID={$module->id}, Code={$codeModule}, Nom={$nomModule}, Filière={$filiere->nom_filiere}");
                } else {
                    $module = $this->cachedModules[$cacheKey];
                    Log::info("♻ Module réutilisé: ID={$module->id}, Code={$codeModule}, Nom={$nomModule}");
                }
            }

            // 7. FORMATEURS - AVEC MASSE HORAIRE PAR DÉFAUT 910
            $mleAffectePresentiel = trim($row['mle_affecte_presentiel_actif'] ?? '');
            $formateurPresentiel = trim($row['formateur_affecte_presentiel_actif'] ?? '');
            $mleAffecteSyn = trim($row['mle_affecte_syn_actif'] ?? '');
            $formateurSyn = trim($row['formateur_affecte_syn_actif'] ?? '');

            $formateurPresObj = null;
            if (!empty($mleAffectePresentiel) && !empty($formateurPresentiel)) {
                $cacheKey = $mleAffectePresentiel;
                
                if (!isset($this->cachedFormateurs[$cacheKey])) {
                    $formateurPresObj = Formateur::where('mle', $mleAffectePresentiel)->first();
                    
                    if (!$formateurPresObj) {
                        $formateurPresObj = Formateur::create([
                            'mle' => $mleAffectePresentiel,
                            'nom_complet' => $formateurPresentiel,
                            'type' => $this->determineTypeFormateur($mleAffectePresentiel),
                            'masse_horaire' => 910.00
                        ]);
                        
                        Log::info("✅ Nouveau formateur créé: MLE={$mleAffectePresentiel}, Nom={$formateurPresentiel}, Masse horaire=910");
                    }
                    
                    $this->cachedFormateurs[$cacheKey] = $formateurPresObj;
                } else {
                    $formateurPresObj = $this->cachedFormateurs[$cacheKey];
                }
                
                if ($formateurPresObj) {
                    $exists = DB::table('etablissement_formateur')
                        ->where('code_efp', $codeEfp)
                        ->where('formateur_id', $formateurPresObj->id)
                        ->exists();
                    
                    if (!$exists) {
                        DB::table('etablissement_formateur')->insert([
                            'code_efp' => $codeEfp,
                            'formateur_id' => $formateurPresObj->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        Log::info("🔗 Formateur {$mleAffectePresentiel} attaché à l'établissement {$codeEfp}");
                    }
                }
                
                if ($secteur && $formateurPresObj) {
                    $exists = DB::table('formateur_secteur')
                        ->where('formateur_id', $formateurPresObj->id)
                        ->where('secteur_id', $secteur->id)
                        ->exists();
                    
                    if (!$exists) {
                        DB::table('formateur_secteur')->insert([
                            'formateur_id' => $formateurPresObj->id,
                            'secteur_id' => $secteur->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
                
                if ($module && $formateurPresObj) {
                    $exists = DB::table('formateur_module')
                        ->where('formateur_id', $formateurPresObj->id)
                        ->where('module_id', $module->id)
                        ->exists();
                    
                    if (!$exists) {
                        DB::table('formateur_module')->insert([
                            'formateur_id' => $formateurPresObj->id,
                            'module_id' => $module->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            $formateurSynObj = null;
            if (!empty($mleAffecteSyn) && !empty($formateurSyn) && $mleAffecteSyn !== $mleAffectePresentiel) {
                $cacheKey = $mleAffecteSyn;
                
                if (!isset($this->cachedFormateurs[$cacheKey])) {
                    $formateurSynObj = Formateur::where('mle', $mleAffecteSyn)->first();
                    
                    if (!$formateurSynObj) {
                        $formateurSynObj = Formateur::create([
                            'mle' => $mleAffecteSyn,
                            'nom_complet' => $formateurSyn,
                            'type' => $this->determineTypeFormateur($mleAffecteSyn),
                            'masse_horaire' => 910.00
                        ]);
                        
                        Log::info("✅ Nouveau formateur créé: MLE={$mleAffecteSyn}, Nom={$formateurSyn}, Masse horaire=910");
                    }
                    
                    $this->cachedFormateurs[$cacheKey] = $formateurSynObj;
                } else {
                    $formateurSynObj = $this->cachedFormateurs[$cacheKey];
                }
                
                if ($formateurSynObj) {
                    $exists = DB::table('etablissement_formateur')
                        ->where('code_efp', $codeEfp)
                        ->where('formateur_id', $formateurSynObj->id)
                        ->exists();
                    
                    if (!$exists) {
                        DB::table('etablissement_formateur')->insert([
                            'code_efp' => $codeEfp,
                            'formateur_id' => $formateurSynObj->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                        
                        Log::info("🔗 Formateur {$mleAffecteSyn} attaché à l'établissement {$codeEfp}");
                    }
                }
                
                if ($secteur && $formateurSynObj) {
                    $exists = DB::table('formateur_secteur')
                        ->where('formateur_id', $formateurSynObj->id)
                        ->where('secteur_id', $secteur->id)
                        ->exists();
                    
                    if (!$exists) {
                        DB::table('formateur_secteur')->insert([
                            'formateur_id' => $formateurSynObj->id,
                            'secteur_id' => $secteur->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
                
                if ($module && $formateurSynObj) {
                    $exists = DB::table('formateur_module')
                        ->where('formateur_id', $formateurSynObj->id)
                        ->where('module_id', $module->id)
                        ->exists();
                    
                    if (!$exists) {
                        DB::table('formateur_module')->insert([
                            'formateur_id' => $formateurSynObj->id,
                            'module_id' => $module->id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            // 8. AFFECTATION AVEC CALCULS MH TOTALE DRIF
            if ($groupe && $module) {
                $fusionGroupe = trim(
                    $row['fusiongroupe'] ?? 
                    $row['fusion_groupe'] ?? 
                    $row['fusion'] ?? 
                    ''
                );
                
                $codeFusion = trim(
                    $row['code_fusion'] ?? 
                    $row['codefusion'] ?? 
                    ''
                );
                
                if ($fusionGroupe === '0' || $fusionGroupe === '' || empty($fusionGroupe)) {
                    $fusionGroupe = null;
                }
                
                if ($codeFusion === '0' || $codeFusion === '' || empty($codeFusion)) {
                    $codeFusion = null;
                }
                
                // ✅ RÉCUPÉRATION DES VALEURS DEPUIS LE FICHIER
                $mhpTotaleDrif = $this->parseDecimal($row['mhp_totale_drif'] ?? 0);
                $mhsynTotaleDrif = $this->parseDecimal($row['mhsyn_totale_drif'] ?? 0);
                
                // ✅ CALCUL DE MH TOTALE DRIF SELON LES CONDITIONS
                $mhTotaleDrif = $this->calculateMhTotaleDrif(
                    $formation,
                    $codeModule,
                    $mhpTotaleDrif,
                    $mhsynTotaleDrif
                );
                
                $affectation = Affectation::create([
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
                    'mh_totale_drif' => $mhTotaleDrif, // ✅ VALEUR CALCULÉE
                    
                    'mh_affectee_presentiel' => $this->parseDecimal($row['mh_affectee_presentiel'] ?? 0),
                    'mh_affectee_sync' => $this->parseDecimal($row['mh_affectee_sync'] ?? 0),
                    'mh_affectee_globale' => $this->parseDecimal($row['mh_affectee_globale_p_syn'] ?? 0)
                ]);

                // 9. AVANCEMENT
                Avancement::create([
                    'affectation_id' => $affectation->id,
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
                    'date_maj' => $this->parseDate($row['date_maj'] ?? null)
                ]);
                
                $this->imported++;
                
            } else {
                $this->skipped++;
                Log::warning("⚠ Ligne {$lineNumber} ignorée: Groupe ou Module manquant");
            }

        } catch (\Exception $e) {
            $this->errors[] = "Ligne {$lineNumber}: " . $e->getMessage();
            Log::error("❌ Erreur ligne {$lineNumber}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ✅ MÉTHODE MODIFIÉE: Calcule MH Totale DRIF selon les conditions demandées
     * 
     * Règle par défaut: mh_totale_drif = mhp_totale_drif + mhsyn_totale_drif
     * Règle spéciale (Formation Alternée + Module Métier): mh_totale_drif = (mhp_totale_drif/2) + mhsyn_totale_drif
     */
    protected function calculateMhTotaleDrif($formation, $codeModule, $mhpTotale, $mhsynTotale)
    {
        // Vérifier si la formation est en mode "Alterné"
        $isAlterne = $formation && 
                     !empty($formation->mode) && 
                     strtolower(trim($formation->mode)) === 'alterné';
        
        // Vérifier si le module est de type métier (commence par "M")
        $codeModuleUpper = strtoupper(trim($codeModule ?? ''));
        $isModuleMetier = !empty($codeModuleUpper) && 
                          substr($codeModuleUpper, 0, 1) === 'M';
        
        // ✅ CONDITION SPÉCIALE: Formation alternée ET Module métier
        if ($isAlterne && $isModuleMetier) {
            $result = ($mhpTotale / 2) + $mhsynTotale;
            
            Log::info("🔄 Règle spéciale appliquée (Alterné + Métier M)", [
                'mode' => $formation->mode,
                'code_module' => $codeModuleUpper,
                'calcul' => "({$mhpTotale} / 2) + {$mhsynTotale}",
                'resultat' => $result
            ]);
            
            return round($result, 2);
        }
        
        // ✅ RÈGLE PAR DÉFAUT: Addition simple
        $result = $mhpTotale + $mhsynTotale;
        
        Log::info("📊 Règle par défaut appliquée", [
            'mode' => $formation->mode ?? 'N/A',
            'code_module' => $codeModuleUpper,
            'calcul' => "{$mhpTotale} + {$mhsynTotale}",
            'resultat' => $result
        ]);
        
        return round($result, 2);
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
            'T' => 'Technicien',
            'S' => 'Spécialisation',
            'Q' => 'Qualification',
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

    public function rules(): array
    {
        return ['code_efp' => 'required|string'];
    }

    public function getErrors() { return $this->errors; }
    public function getImported() { return $this->imported; }
    public function getSkipped() { return $this->skipped; }
    public function getDeleted() { return $this->deleted; }
    public function getUpdated() { return 0; }

    public function getSummary()
    {
        return [
            'imported' => $this->imported,
            'deleted' => $this->deleted,
            'skipped' => $this->skipped,
            'updated' => 0,
            'errors' => count($this->errors),
            'total' => $this->imported + $this->skipped,
            'error_details' => $this->errors
        ];
    }
}