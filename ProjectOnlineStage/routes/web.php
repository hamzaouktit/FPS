<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\AdministrationComplexe\DashboardComplexeController;
use App\Http\Controllers\AdministrationEtablissement\DashboardEtablissementController;

// Route d'accueil (page welcome)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');
//fallback route : 
Route::fallback(function () {
    return redirect()->route('welcome');
});
// Routes d'authentification (accessibles par tous)
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');
    Route::get('/check-auth', 'checkAuth')->name('check.auth');
});

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    
    // Routes pour l'administration du complexe
    Route::prefix('administrationcomplexe')->name('administration.complexe.')->group(function () {
        Route::get('/dashboard', [DashboardComplexeController::class, 'index'])->name('dashboard');
    });
    
    // Routes pour l'administration de l'établissement
    Route::prefix('administrationetablissement')->name('administration.etablissement.')->group(function () {
        Route::get('/dashboard', [DashboardEtablissementController::class, 'index'])->name('dashboard');
        Route::get('/import', [DashboardEtablissementController::class, 'importForm'])->name('import');        
        Route::post('/import', [DashboardEtablissementController::class, 'importExcel'])->name('import.process');
        Route::get('/filter-options', [DashboardEtablissementController::class, 'getFilteredOptions'])
        ->name('filter.options');
    });
    
}); 