<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdministrationComplexe\DashboardComplexeController;
use App\Http\Controllers\AdministrationComplexe\EtablissementController;
use App\Http\Controllers\AdministrationEtablissement\DashboardEtablissementController;
use App\Http\Controllers\AdministrationComplexe\DirecteurController;
use App\Http\Controllers\AdministrationEtablissement\SecteurController;
use App\Http\Controllers\AdministrationEtablissement\FiliereController;
use App\Http\Controllers\AdministrationEtablissement\NiveauController; // NOUVEAU

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route d'accueil (page welcome)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Fallback route
Route::fallback(function () {
    return redirect()->route('welcome');
});

/*
|--------------------------------------------------------------------------
| Routes d'authentification (accessibles par tous)
|--------------------------------------------------------------------------
*/
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/check-auth', 'checkAuth')->name('check.auth');
});

/*
|--------------------------------------------------------------------------
| Routes protégées par authentification
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Routes pour l'administration du complexe
    |--------------------------------------------------------------------------
    */
    Route::prefix('administrationcomplexe')->name('administration.complexe.')->group(function () {
        
        // Dashboard du complexe
        Route::get('/dashboard', [DashboardComplexeController::class, 'index'])->name('dashboard');
        Route::get('/filter-options', [DashboardComplexeController::class, 'getFilteredOptions'])->name('filter.options');
        
        // CRUD des établissements
        Route::resource('etablissements', EtablissementController::class)->except(['show'])->parameters([
            'etablissements' => 'code_efp'
        ]);
        
        // CRUD des directeurs
        Route::resource('directeurs', DirecteurController::class);
        
        // Route show personnalisée (avec filtres)
        Route::get('etablissements/{code_efp}', [EtablissementController::class, 'show'])
            ->name('etablissements.show');
    });
    
    /*
    |--------------------------------------------------------------------------
    | Routes pour l'administration de l'établissement
    |--------------------------------------------------------------------------
    */
    Route::prefix('administrationetablissement')->name('administration.etablissement.')->group(function () {
        
        // Dashboard de l'établissement
        Route::get('/dashboard', [DashboardEtablissementController::class, 'index'])->name('dashboard');
        Route::get('/filter-options', [DashboardEtablissementController::class, 'getFilteredOptions'])->name('filter.options');
        
        // Import Excel
        Route::get('/import', [DashboardEtablissementController::class, 'importForm'])->name('import');
        Route::post('/import', [DashboardEtablissementController::class, 'importExcel'])->name('import.process');
        
        // CRUD des secteurs
        Route::resource('secteurs', SecteurController::class)
            ->parameters(['secteurs' => 'nom_secteur']);
        
        // CRUD des filières
        Route::resource('filieres', FiliereController::class)
            ->parameters(['filieres' => 'code_filiere']);
        
        // CRUD des niveaux - NOUVEAU
        Route::resource('niveaux', NiveauController::class)
            ->parameters(['niveaux' => 'niveau']);
    });
});