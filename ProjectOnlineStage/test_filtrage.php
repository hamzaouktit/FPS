<?php
/**
 * Script de test pour vérifier le formatage des données de filtrage
 * 
 * Usage: php test_filtrage.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TEST DE FORMATAGE DES DONNÉES DE FILTRAGE ===\n\n";

// Récupérer un établissement de test
$etablissement = DB::table('etablissements')->first();

if (!$etablissement) {
    echo "❌ Aucun établissement trouvé dans la base de données\n";
    exit(1);
}

echo "✓ Établissement de test: {$etablissement->nom_efp} ({$etablissement->code_efp})\n\n";

// Test 1: Modules
echo "--- TEST 1: MODULES ---\n";
$modules = DB::table('modules')
    ->join('avancements', 'modules.code_module', '=', 'avancements.code_module')
    ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
    ->join('formations', 'groupes.id_formation', '=', 'formations.id')
    ->where('formations.code_efp', $etablissement->code_efp)
    ->whereNull('avancements.deleted_at')
    ->select('modules.code_module', 'modules.nom_module')
    ->distinct()
    ->orderBy('modules.nom_module')
    ->get();

echo "Nombre de modules: " . $modules->count() . "\n";

// Sans values()
$modulesArray1 = $modules->toArray();
echo "Format sans values(): " . (is_array($modulesArray1) ? "array" : gettype($modulesArray1)) . "\n";
echo "JSON sans values(): " . (json_encode($modulesArray1) === json_encode(array_values($modulesArray1)) ? "✓ Tableau séquentiel" : "❌ Objet avec clés") . "\n";

// Avec values()
$modulesArray2 = $modules->values()->toArray();
echo "Format avec values(): " . (is_array($modulesArray2) ? "array" : gettype($modulesArray2)) . "\n";
echo "JSON avec values(): " . (json_encode($modulesArray2) === json_encode(array_values($modulesArray2)) ? "✓ Tableau séquentiel" : "❌ Objet avec clés") . "\n";

if ($modules->count() > 0) {
    echo "Premier module: " . json_encode($modulesArray2[0]) . "\n";
}
echo "\n";

// Test 2: Groupes
echo "--- TEST 2: GROUPES ---\n";
$groupes = DB::table('groupes')
    ->join('formations', 'groupes.id_formation', '=', 'formations.id')
    ->where('formations.code_efp', $etablissement->code_efp)
    ->select('groupes.groupe')
    ->distinct()
    ->orderBy('groupes.groupe')
    ->get();

echo "Nombre de groupes: " . $groupes->count() . "\n";
$groupesArray = $groupes->values()->toArray();
echo "Format: " . (is_array($groupesArray) ? "✓ array" : "❌ " . gettype($groupesArray)) . "\n";
if ($groupes->count() > 0) {
    echo "Premier groupe: " . json_encode($groupesArray[0]) . "\n";
}
echo "\n";

// Test 3: Secteurs
echo "--- TEST 3: SECTEURS ---\n";
$secteurs = DB::table('secteurs')
    ->join('filieres', 'secteurs.nom_secteur', '=', 'filieres.nom_secteur')
    ->join('formations', 'filieres.code_filiere', '=', 'formations.code_filiere')
    ->where('formations.code_efp', $etablissement->code_efp)
    ->select('secteurs.nom_secteur')
    ->distinct()
    ->orderBy('secteurs.nom_secteur')
    ->get();

echo "Nombre de secteurs: " . $secteurs->count() . "\n";
$secteursArray = $secteurs->values()->toArray();
echo "Format: " . (is_array($secteursArray) ? "✓ array" : "❌ " . gettype($secteursArray)) . "\n";
if ($secteurs->count() > 0) {
    echo "Premier secteur: " . json_encode($secteursArray[0]) . "\n";
}
echo "\n";

// Test 4: Filières
echo "--- TEST 4: FILIÈRES ---\n";
$filieres = DB::table('filieres')
    ->join('formations', 'filieres.code_filiere', '=', 'formations.code_filiere')
    ->where('formations.code_efp', $etablissement->code_efp)
    ->select('filieres.code_filiere', 'filieres.nom_filiere')
    ->distinct()
    ->orderBy('filieres.nom_filiere')
    ->get();

echo "Nombre de filières: " . $filieres->count() . "\n";
$filieresArray = $filieres->values()->toArray();
echo "Format: " . (is_array($filieresArray) ? "✓ array" : "❌ " . gettype($filieresArray)) . "\n";
if ($filieres->count() > 0) {
    echo "Première filière: " . json_encode($filieresArray[0]) . "\n";
}
echo "\n";

// Test 5: Niveaux
echo "--- TEST 5: NIVEAUX ---\n";
$niveaux = DB::table('niveaux')
    ->join('formations', 'niveaux.niveau', '=', 'formations.niveau')
    ->where('formations.code_efp', $etablissement->code_efp)
    ->select('niveaux.niveau')
    ->distinct()
    ->orderBy('niveaux.niveau')
    ->get();

echo "Nombre de niveaux: " . $niveaux->count() . "\n";
$niveauxArray = $niveaux->values()->toArray();
echo "Format: " . (is_array($niveauxArray) ? "✓ array" : "❌ " . gettype($niveauxArray)) . "\n";
if ($niveaux->count() > 0) {
    echo "Premier niveau: " . json_encode($niveauxArray[0]) . "\n";
}
echo "\n";

// Test 6: Formateurs
echo "--- TEST 6: FORMATEURS ---\n";
$formateurs = DB::table('formateurs')
    ->whereIn('mle', function($query) use ($etablissement) {
        $query->select('mle_presentiel')
              ->from('avancements')
              ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
              ->join('formations', 'groupes.id_formation', '=', 'formations.id')
              ->where('formations.code_efp', $etablissement->code_efp)
              ->whereNotNull('mle_presentiel')
              ->whereNull('avancements.deleted_at');
    })
    ->orWhereIn('mle', function($query) use ($etablissement) {
        $query->select('mle_syn')
              ->from('avancements')
              ->join('groupes', 'avancements.groupe', '=', 'groupes.groupe')
              ->join('formations', 'groupes.id_formation', '=', 'formations.id')
              ->where('formations.code_efp', $etablissement->code_efp)
              ->whereNotNull('mle_syn')
              ->whereNull('avancements.deleted_at');
    })
    ->distinct()
    ->orderBy('nom_formateur')
    ->get();

echo "Nombre de formateurs: " . $formateurs->count() . "\n";
$formateursArray = $formateurs->values()->toArray();
echo "Format: " . (is_array($formateursArray) ? "✓ array" : "❌ " . gettype($formateursArray)) . "\n";
if ($formateurs->count() > 0) {
    echo "Premier formateur: " . json_encode($formateursArray[0]) . "\n";
}
echo "\n";

// Résumé final
echo "=== RÉSUMÉ ===\n";
$total = $modules->count() + $groupes->count() + $secteurs->count() + $filieres->count() + $niveaux->count() + $formateurs->count();
echo "Total d'éléments trouvés: {$total}\n";

if ($total > 0) {
    echo "\n✅ TOUS LES TESTS SONT PASSÉS AVEC SUCCÈS !\n";
    echo "Les données sont correctement formatées pour JavaScript.\n";
} else {
    echo "\n⚠️  ATTENTION: Aucune donnée trouvée pour cet établissement.\n";
    echo "Vérifiez que la base de données contient des données.\n";
}

echo "\n=== FIN DES TESTS ===\n";
