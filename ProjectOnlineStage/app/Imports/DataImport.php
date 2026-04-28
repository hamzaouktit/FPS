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
use App\Models\HistoriqueAvancement; // ✅ NOUVEAU
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Validators\Failure;
use Carbon\Carbon;
use Throwable;

class DataImport implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsOnError
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
            // ✅ NOUVELLE ÉTAPE 1: Sauvegarder l'historique AVANT la suppression
            $this->saveHistoriqueBeforeImport();
            
            // ÉTAPE 2: Suppression des données existantes (inchangée)
            $this->deleteExistingData();
            
            // ÉTAPE 3: Importation des nouvelles données (inchangée)
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

    /**
     * ✅ NOUVELLE MÉTHODE: Sauvegarde de l'historique avant importation
     * Cette méthode capture l'état actuel des avancements avant toute modification
     */
    protected function saveHistoriqueBeforeImport()
    {
        try {
            $dateCapture = Carbon::today();
            
            Log::info("📸 Début de la capture de l'historique pour la date: {$dateCapture->format('Y-m-d')}");
            
            // Vérifier si un historique existe déjà pour cette date
            $existingCount = HistoriqueAvancement::where('code_efp', $this->codeEfp)
                ->whereDate('date_capture', $dateCapture)
                ->count();
            
            if ($existingCount > 0) {
                Log::warning("⚠️ Un historique existe déjà pour la date {$dateCapture->format('Y-m-d')}. Suppression avant nouvelle capture.");
                HistoriqueAvancement::where('code_efp', $this->codeEfp)
                    ->whereDate('date_capture', $dateCapture)
                    ->delete();
            }
            
            // Récupérer tous les avancements actuels avec leurs relations
            $avancements = Avancement::where('code_efp', $this->codeEfp)
                ->with([
                    'affectation.groupe',
                    'affectation.module',
                    'affectation.formateurPresentiel',
                    'affectation.formateurSyn',
                    'affectation.groupe.filiere'
                ])
                ->get();
            
            $historiqueCount = 0;
            
            foreach ($avancements as $avancement) {
                $affectation = $avancement->affectation;
                
                if (!$affectation) {
                    continue;
                }
                
                // Créer l'enregistrement d'historique
                HistoriqueAvancement::create([
                    'date_capture' => $dateCapture,
                    'affectation_id' => $affectation->id,
                    'code_efp' => $this->codeEfp,
                    
                    // Informations contextuelles
                    'formateur_presentiel' => $affectation->formateur_affecte_presentiel,
                    'mle_presentiel' => $affectation->mle_affecte_presentiel,
                    'formateur_syn' => $affectation->formateur_affecte_syn,
                    'mle_syn' => $affectation->mle_affecte_syn,
                    'nom_module' => $affectation->module->nom_module ?? null,
                    'code_module' => $affectation->module->code_module ?? null,
                    'code_groupe' => $affectation->groupe->code_groupe ?? null,
                    'nom_filiere' => $affectation->groupe->filiere->nom_filiere ?? null,
                    
                    // Masses horaires affectées
                    'mh_affectee_presentiel' => $affectation->mh_affectee_presentiel,
                    'mh_affectee_sync' => $affectation->mh_affectee_sync,
                    'mh_affectee_globale' => $affectation->mh_affectee_globale,
                    
                    // Données d'avancement
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
                ]);
                
                $historiqueCount++;
            }
            
            Log::info("✅ Historique capturé avec succès: {$historiqueCount} enregistrements sauvegardés");
            
        } catch (\Exception $e) {
            Log::error('❌ Erreur lors de la sauvegarde de l\'historique: ' . $e->getMessage());
            throw new \Exception("Erreur lors de la sauvegarde de l'historique: " . $e->getMessage());
        }
    }

    // ======================================
    // TOUT LE RESTE DU CODE RESTE INCHANGÉ
    // ======================================

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
            
            // 🛡️ SANITIZATION: Sanitize all inputs in the row to prevent XSS or HTML injection
            $sanitizedRow = [];
            foreach ($row as $key => $value) {
                $sanitizedRow[$key] = $this->sanitize($value);
            }
            $row = $sanitizedRow;
            
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
                } else {
                    $module = $this->cachedModules[$cacheKey];
                }
            }

            // 7. FORMATEURS
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

            // 8. AFFECTATION
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
                
                $mhpTotaleDrif = $this->parseDecimal($row['mhp_totale_drif'] ?? 0);
                $mhsynTotaleDrif = $this->parseDecimal($row['mhsyn_totale_drif'] ?? 0);
                
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
                    'mh_totale_drif' => $mhTotaleDrif,
                    
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

    protected function calculateMhTotaleDrif($formation, $codeModule, $mhpTotale, $mhsynTotale)
    {
        $isAlterne = $formation && 
                     !empty($formation->mode) && 
                     strtolower(trim($formation->mode)) === 'alterné';
        
        $codeModuleUpper = strtoupper(trim($codeModule ?? ''));
        $isModuleMetier = !empty($codeModuleUpper) && 
                          substr($codeModuleUpper, 0, 1) === 'M';
        
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

    /**
     * Sanitize input value to prevent XSS and invalid data
     */
    protected function sanitize($value)
    {
        if (is_string($value)) {
            $value = trim($value);
            $value = strip_tags($value); // Remove HTML/PHP tags
            $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); // Convert special characters to HTML entities
            return $value;
        }
        return $value;
    }

    public function rules(): array
    {
        return [
            'code_efp' => ['required', 'string', 'max:255'],
            'secteur' => ['nullable', 'string', 'max:255'],
            'niveau' => ['nullable', 'string', 'max:100'],
            'code_filiere' => ['nullable', 'string', 'max:255'],
            'filiere' => ['nullable', 'string', 'max:500'],
            'annee' => ['nullable', 'numeric', 'min:2000', 'max:2100'],
            'type_de_formation' => ['nullable', 'string', 'max:255'],
            'mode' => ['nullable', 'string', 'max:255'],
            'creneau' => ['nullable', 'string', 'max:255'],
            'groupe' => ['nullable', 'string', 'max:255'],
            'effectif_groupe' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'sous_groupe' => ['nullable', 'string', 'max:255'],
            'statut_sous_groupe' => ['nullable', 'string', 'max:255'],
            'annee_de_formation' => ['nullable', 'numeric', 'min:1', 'max:10'],
            'code_module' => ['nullable', 'string', 'max:255'],
            'module' => ['nullable', 'string', 'max:500'],
            'regional' => ['nullable', 'string', 'max:10'],
            'module_pie' => ['nullable', 'string', 'max:10'],
            'efp_pie' => ['nullable', 'string', 'max:255'],
            'mle_affecte_presentiel_actif' => ['nullable', 'string', 'max:255'],
            'formateur_affecte_presentiel_actif' => ['nullable', 'string', 'max:255'],
            'mle_affecte_syn_actif' => ['nullable', 'string', 'max:255'],
            'formateur_affecte_syn_actif' => ['nullable', 'string', 'max:255'],
            'nb_cc' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'mh_realisee_globale' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Handle row validation failures
     */
    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->errors[] = "Ligne " . $failure->row() . ": " . implode(', ', $failure->errors());
            $this->skipped++;
        }
    }

    /**
     * Handle general import exceptions gracefully
     */
    public function onError(Throwable $e)
    {
        $this->errors[] = "Erreur inattendue: " . $e->getMessage();
        Log::error('❌ Erreur Excel: ' . $e->getMessage());
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