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
use App\Http\Controllers\AdministrationEtablissement\FormationController; 
use App\Http\Controllers\AdministrationEtablissement\GroupeController;
use App\Http\Controllers\AdministrationEtablissement\FormateurController;
use App\Http\Controllers\AdministrationEtablissement\ModuleController;
use App\Http\Controllers\AdministrationEtablissement\AffectationController;
use App\Http\Controllers\AdministrationEtablissement\AvancementController; // À FAIRE
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\AdministrationEtablissement\HistoriqueController;
use App\Http\Controllers\AdministrationComplexe\HistoriqueControllerComplex;
use App\Http\Controllers\AdministrationComplexe\RapportGlobalController;
use App\Http\Controllers\AdministrationComplexe\EffectifsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/
// Route pour la page d'accueil des visiteurs
Route::get('/', [VisitorController::class, 'index'])->name('visitor.index');
// Route d'accueil (page welcome)
Route::get('/admin', function () {
    return view('welcome');
})->name('welcome');

// Fallback route
Route::fallback(function () {
    return redirect()->route('visitor.index');
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
        
        // Routes pour l'historique des avancements du complexe
        Route::prefix('historique')->name('historique.')->group(function () {
            Route::get('/', [HistoriqueControllerComplex::class, 'index'])->name('index');
            Route::get('/show/{id}', [HistoriqueControllerComplex::class, 'show'])->name('show');
            Route::get('/compare', [HistoriqueControllerComplex::class, 'compare'])->name('compare');
            Route::get('/export', [HistoriqueControllerComplex::class, 'export'])->name('export');
        });

        // Rapports Globaux
        Route::get('/rapports-globaux', [RapportGlobalController::class, 'index'])->name('rapports.globaux');
        
        // Effectifs
        Route::get('/effectifs', [EffectifsController::class, 'index'])->name('effectifs.index');
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
        // CRUD des formations
        Route::resource('formations', FormationController::class)
        ->parameters(['formations' => 'id']);
        // CRUD des groupes
        Route::resource('groupes', GroupeController::class);
        // CRUD des modules
        Route::resource('modules', ModuleController::class);
        // CRUD des formateurs
        Route::resource('formateurs', FormateurController::class);
        // CRUD des affectations - À FAIRE
        // ⚠️ IMPORTANT : Cette route DOIT être AVANT la ressource affectations
    Route::get('affectations/formateurs/{mle}/modules', [AffectationController::class, 'getFormateurModules'])
        ->name('affectations.formateurs.modules');
    
    // CRUD des affectations
    Route::resource('affectations', AffectationController::class);
        // CRUD des avancements - 
        Route::resource('avancements', AvancementController::class);
        
        // Routes pour l'historique des avancements
        Route::prefix('historique')->name('historique.')->group(function () {
            Route::get('/', [HistoriqueController::class, 'index'])->name('index');
            Route::get('/show/{id}', [HistoriqueController::class, 'show'])->name('show');
            Route::get('/compare', [HistoriqueController::class, 'compare'])->name('compare');
            Route::get('/export', [HistoriqueController::class, 'export'])->name('export');
        }); // Ces routes s'appelleront administration.etablissement.historique.*
      
    });

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
    
    // 👇 AJOUTER CES ROUTES 👇
    // Routes pour la réinitialisation du mot de passe
   // Route::get('/forgot-password', 'showForgotPasswordForm')->name('forgot.password.form');
    //Route::post('/forgot-password', 'sendResetCode')->name('forgot.password.send');
    //Route::get('/verify-code', 'showVerifyCodeForm')->name('forgot.password.code.form');
    //Route::post('/verify-code', 'verifyCode')->name('forgot.password.verify'); 
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forgot.password.form');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetCode'])->name('forgot.password.send');

Route::get('/verify-code', [ForgotPasswordController::class, 'showVerifyCodeForm'])->name('forgot.password.code.form');
Route::post('/verify-code', [ForgotPasswordController::class, 'verifyCode'])->name('forgot.password.code.verify');

Route::get('/reset-password', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('forgot.password.reset.form');
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('forgot.password.reset');
});