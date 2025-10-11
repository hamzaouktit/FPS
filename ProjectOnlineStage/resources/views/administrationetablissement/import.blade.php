@extends('layouts.app')

@section('title', 'Importation Excel - Établissement')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><i class="fas fa-home me-1"></i><a href="{{ route('welcome') }}">Accueil</a></li>
        <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.dashboard') }}">Dashboard Établissement</a></li>
        <li class="breadcrumb-item active" aria-current="page">Importation Excel</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-0">
                    <i class="fas fa-file-excel text-success me-2"></i>
                    Importation de Données Excel
                </h1>
                <p class="text-muted mb-0">Importer les données de votre établissement depuis un fichier Excel</p>
            </div>
            <div>
                <a href="{{ route('administration.etablissement.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>
                    Retour au Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Informations Établissement -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-container me-3">
                        <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px;">
                            <i class="fas fa-university fs-5"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $etablissement->nom_efp }}</h5>
                        <p class="text-muted mb-0">
                            <strong>Code EFP :</strong> {{ $etablissement->code_efp }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Messages d'alerte -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    {{ session('warning') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->has('import_errors'))
<div class="alert alert-warning" role="alert">
    <h6><i class="fas fa-exclamation-triangle me-2"></i>Avertissements lors de l'importation :</h6>
    <div style="max-height: 300px; overflow-y: auto;">
        <ul class="mb-0">
            @foreach($errors->get('import_errors') as $errorList)
                @if(is_array($errorList))
                    @foreach($errorList as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                @else
                    <li>{{ $errorList }}</li>
                @endif
            @endforeach
        </ul>
    </div>
</div>
@endif

<!-- Instructions d'importation -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Instructions d'importation
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-file-alt text-primary me-2"></i>Format de fichier accepté :</h6>
                        <ul class="list-unstyled ms-3">
                            <li><i class="fas fa-check text-success me-2"></i>Excel (.xlsx, .xls)</li>
                            <li><i class="fas fa-check text-success me-2"></i>CSV</li>
                            <li><i class="fas fa-info text-info me-2"></i>Taille maximum : 10 MB</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-database text-success me-2"></i>Données importées :</h6>
                        <ul class="list-unstyled ms-3">
                            <li><i class="fas fa-arrow-right text-muted me-2"></i>Secteurs et filières</li>
                            <li><i class="fas fa-arrow-right text-muted me-2"></i>Formations et groupes</li>
                            <li><i class="fas fa-arrow-right text-muted me-2"></i>Modules et formateurs</li>
                            <li><i class="fas fa-arrow-right text-muted me-2"></i>Avancements pédagogiques</li>
                        </ul>
                    </div>
                </div>
                <div class="alert alert-warning mt-3" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Important :</strong> Seules les lignes contenant le code EFP "<strong>{{ $etablissement->code_efp }}</strong>" 
                    seront importées. Les autres lignes seront automatiquement ignorées.
                </div>
                <div class="alert alert-info mt-2" role="alert">
                    <i class="fas fa-sync-alt me-2"></i>
                    <strong>Mise à jour intelligente :</strong> Les enregistrements existants seront automatiquement mis à jour avec les nouvelles données.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire d'importation -->
<div class="row mb-4">
    <div class="col-md-8 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-upload text-primary me-2"></i>
                    Sélectionner le fichier Excel
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('administration.etablissement.import.process') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="excel_file" class="form-label fw-bold">
                            <i class="fas fa-file-excel me-1"></i>
                            Fichier Excel <span class="text-danger">*</span>
                        </label>
                        <input type="file" 
                               class="form-control @error('excel_file') is-invalid @enderror" 
                               id="excel_file" 
                               name="excel_file" 
                               accept=".xlsx,.xls,.csv"
                               required>
                        @error('excel_file')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                        <div class="form-text">
                            Formats acceptés : .xlsx, .xls, .csv (max. 10 MB)
                        </div>
                    </div>

                    <!-- Aperçu du fichier sélectionné -->
                    <div id="filePreview" class="mb-4" style="display: none;">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-file-excel fs-3 text-success"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1" id="fileName">Nom du fichier</h6>
                                        <small class="text-muted" id="fileSize">Taille du fichier</small>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearFile()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('administration.etablissement.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                            <i class="fas fa-upload me-1"></i>
                            <span id="submitText">Importer le fichier</span>
                            <div class="spinner-border spinner-border-sm ms-2 d-none" id="loadingSpinner"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Format Excel attendu -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-question-circle text-info me-2"></i>
                    Format Excel attendu - Structure des colonnes
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-3">Le fichier Excel doit contenir les colonnes suivantes avec les en-têtes exacts :</p>
                
                <div class="accordion" id="columnsAccordion">
                    <!-- Informations de base -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingBasic">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBasic">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                <strong>Informations de base (Obligatoires)</strong>
                            </button>
                        </h2>
                        <div id="collapseBasic" class="accordion-collapse collapse show" data-bs-parent="#columnsAccordion">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 30%;">Colonne Excel</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td><code>Date MAJ</code></td><td>Date de mise à jour des données</td></tr>
                                            <tr><td><code>Année</code></td><td>Année de formation (ex: 2024, 2025)</td></tr>
                                            <tr><td><code>Code EFP</code></td><td>Code de l'établissement (doit correspondre à {{ $etablissement->code_efp }})</td></tr>
                                            <tr><td><code>EFP</code></td><td>Nom de l'établissement</td></tr>
                                            <tr><td><code>Niveau</code></td><td>Niveau de formation (TS, T, Q, S...)</td></tr>
                                            <tr><td><code>Secteur</code></td><td>Secteur de formation</td></tr>
                                            <tr><td><code>Code Filière</code></td><td>Code de la filière</td></tr>
                                            <tr><td><code>filière</code></td><td>Nom de la filière</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formation et Groupes -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFormation">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFormation">
                                <i class="fas fa-graduation-cap text-success me-2"></i>
                                <strong>Formation et Groupes</strong>
                            </button>
                        </h2>
                        <div id="collapseFormation" class="accordion-collapse collapse" data-bs-parent="#columnsAccordion">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 30%;">Colonne Excel</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td><code>Type de formation</code></td><td>Type de formation (Résidentiel, Alternance...)</td></tr>
                                            <tr><td><code>Créneau</code></td><td>Créneau horaire</td></tr>
                                            <tr><td><code>Groupe</code></td><td>Code du groupe</td></tr>
                                            <tr><td><code>Effectif Groupe</code></td><td>Nombre de stagiaires dans le groupe</td></tr>
                                            <tr><td><code>Sous Groupe</code></td><td>Code du sous-groupe (optionnel)</td></tr>
                                            <tr><td><code>Statut Sous-Groupe</code></td><td>Statut du sous-groupe</td></tr>
                                            <tr><td><code>FusionGroupe</code></td><td>Information sur la fusion de groupes</td></tr>
                                            <tr><td><code>Code Fusion</code></td><td>Code de fusion</td></tr>
                                            <tr><td><code>Année de formation</code></td><td>Année d'étude (1, 2, 3...)</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modules et Formateurs -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingModule">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseModule">
                                <i class="fas fa-book text-info me-2"></i>
                                <strong>Modules et Formateurs</strong>
                            </button>
                        </h2>
                        <div id="collapseModule" class="accordion-collapse collapse" data-bs-parent="#columnsAccordion">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 30%;">Colonne Excel</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td><code>Mode</code></td><td>Mode de formation (Présentiel, Synchrone, Hybride...)</td></tr>
                                            <tr><td><code>Code Module</code></td><td>Code du module</td></tr>
                                            <tr><td><code>Module</code></td><td>Nom du module</td></tr>
                                            <tr><td><code>Régional</code></td><td>Module régional (O/N)</td></tr>
                                            <tr><td><code>Mle Affecté Présentiel Actif</code></td><td>Matricule du formateur présentiel</td></tr>
                                            <tr><td><code>Formateur Affecté Présentiel Actif</code></td><td>Nom du formateur présentiel</td></tr>
                                            <tr><td><code>Mle Affecté Syn Actif</code></td><td>Matricule du formateur synchrone</td></tr>
                                            <tr><td><code>Formateur Affecté Syn Actif</code></td><td>Nom du formateur synchrone</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Heures de formation -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingHeures">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseHeures">
                                <i class="fas fa-clock text-warning me-2"></i>
                                <strong>Heures de formation (DRIF)</strong>
                            </button>
                        </h2>
                        <div id="collapseHeures" class="accordion-collapse collapse" data-bs-parent="#columnsAccordion">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 30%;">Colonne Excel</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="table-info"><td colspan="2"><strong>Semestre 1</strong></td></tr>
                                            <tr><td><code>MHP S1 DRIF</code></td><td>Masse horaire présentielle semestre 1</td></tr>
                                            <tr><td><code>MHSYN S1 DRIF</code></td><td>Masse horaire synchrone semestre 1</td></tr>
                                            <tr><td><code>MHASYN S1 DRIF</code></td><td>Masse horaire asynchrone semestre 1</td></tr>
                                            <tr><td><code>MH Totale S1 DRIF</code></td><td>Masse horaire totale semestre 1</td></tr>
                                            
                                            <tr class="table-info"><td colspan="2"><strong>Semestre 2</strong></td></tr>
                                            <tr><td><code>MHP S2 DRIF</code></td><td>Masse horaire présentielle semestre 2</td></tr>
                                            <tr><td><code>MHSYN S2 DRIF</code></td><td>Masse horaire synchrone semestre 2</td></tr>
                                            <tr><td><code>MHASYN S2 DRIF</code></td><td>Masse horaire asynchrone semestre 2</td></tr>
                                            <tr><td><code>MH Totale S2 DRIF</code></td><td>Masse horaire totale semestre 2</td></tr>
                                            
                                            <tr class="table-info"><td colspan="2"><strong>Totaux annuels</strong></td></tr>
                                            <tr><td><code>MHP Totale DRIF</code></td><td>Total masse horaire présentielle</td></tr>
                                            <tr><td><code>MHSYN Totale DRIF</code></td><td>Total masse horaire synchrone</td></tr>
                                            <tr><td><code>MHASYN Totale DRIF</code></td><td>Total masse horaire asynchrone</td></tr>
                                            <tr><td><code>MH Totale  DRIF</code></td><td>Total masse horaire globale</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Heures affectées et réalisées -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingRealisation">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRealisation">
                                <i class="fas fa-chart-line text-danger me-2"></i>
                                <strong>Heures affectées et réalisées</strong>
                            </button>
                        </h2>
                        <div id="collapseRealisation" class="accordion-collapse collapse" data-bs-parent="#columnsAccordion">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 30%;">Colonne Excel</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class="table-success"><td colspan="2"><strong>Heures affectées</strong></td></tr>
                                            <tr><td><code>MH Affectée Présentiel</code></td><td>Heures affectées en présentiel</td></tr>
                                            <tr><td><code>MH Affectée Sync</code></td><td>Heures affectées en synchrone</td></tr>
                                            <tr><td><code>MH Affectée Globale (P & SYN)</code></td><td>Total heures affectées</td></tr>
                                            
                                            <tr class="table-primary"><td colspan="2"><strong>Heures réalisées</strong></td></tr>
                                            <tr><td><code>MH Réalisée Présentiel</code></td><td>Heures réalisées en présentiel</td></tr>
                                            <tr><td><code>MH Réalisée Sync</code></td><td>Heures réalisées en synchrone</td></tr>
                                            <tr><td><code>MH Réalisée Globale</code></td><td>Total heures réalisées</td></tr>
                                            
                                            <tr class="table-warning"><td colspan="2"><strong>Taux de réalisation</strong></td></tr>
                                            <tr><td><code>Taux Réalisation Présentiel</code></td><td>Taux de réalisation présentiel (%)</td></tr>
                                            <tr><td><code>Taux Réalisation Syn</code></td><td>Taux de réalisation synchrone (%)</td></tr>
                                            <tr><td><code>Taux Réalisation (P & SYN )</code></td><td>Taux de réalisation global (%)</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Évaluations et suivi -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingEvaluation">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEvaluation">
                                <i class="fas fa-tasks text-secondary me-2"></i>
                                <strong>Évaluations et suivi pédagogique</strong>
                            </button>
                        </h2>
                        <div id="collapseEvaluation" class="accordion-collapse collapse" data-bs-parent="#columnsAccordion">
                            <div class="accordion-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 30%;">Colonne Excel</th>
                                                <th>Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr><td><code>Moy Absence</code></td><td>Moyenne d'absence (%)</td></tr>
                                            <tr><td><code>NB CC</code></td><td>Nombre de contrôles continus</td></tr>
                                            <tr><td><code>Séance EFM</code></td><td>Information sur la séance EFM</td></tr>
                                            <tr><td><code>Validation EFM</code></td><td>Statut de validation de l'EFM</td></tr>
                                            <tr><td><code>Classe Teams</code></td><td>Nom de la classe Teams</td></tr>
                                            <tr><td><code>Module PIE</code></td><td>Module dans PIE</td></tr>
                                            <tr><td><code>EFP PIE</code></td><td>Code EFP dans PIE</td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-4" role="alert">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Conseils importants :</strong>
                    <ul class="mb-0 mt-2">
                        <li>Assurez-vous que la première ligne contient exactement les en-têtes mentionnés ci-dessus</li>
                        <li>Les noms des colonnes sont sensibles à la casse et aux espaces</li>
                        <li>Les colonnes peuvent être dans n'importe quel ordre</li>
                        <li>Les valeurs décimales peuvent utiliser la virgule ou le point comme séparateur</li>
                        <li>Les dates peuvent être dans différents formats (dd/mm/yyyy, yyyy-mm-dd, etc.)</li>
                    </ul>
                </div>

                <div class="alert alert-success mt-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Fonctionnalités :</strong>
                    <ul class="mb-0 mt-2">
                        <li>✓ Création automatique des secteurs, filières, niveaux, modules et formateurs</li>
                        <li>✓ Mise à jour intelligente des données existantes</li>
                        <li>✓ Filtrage automatique par code EFP</li>
                        <li>✓ Validation des données lors de l'importation</li>
                        <li>✓ Rapport détaillé des erreurs éventuelles</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('excel_file');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const importForm = document.getElementById('importForm');

    // Gestion de la sélection de fichier
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Vérifier l'extension
            const validExtensions = ['xlsx', 'xls', 'csv'];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            
            if (!validExtensions.includes(fileExtension)) {
                alert('Format de fichier non valide. Veuillez sélectionner un fichier Excel (.xlsx, .xls) ou CSV.');
                fileInput.value = '';
                return;
            }
            
            // Vérifier la taille (10 MB)
            if (file.size > 10 * 1024 * 1024) {
                alert('Le fichier est trop volumineux. La taille maximum est de 10 MB.');
                fileInput.value = '';
                return;
            }
            
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            filePreview.style.display = 'block';
            submitBtn.disabled = false;
        } else {
            filePreview.style.display = 'none';
            submitBtn.disabled = true;
        }
    });

    // Gestion de la soumission du formulaire
    importForm.addEventListener('submit', function(e) {
        if (!confirm('Êtes-vous sûr de vouloir importer ce fichier ? Les données existantes seront mises à jour.')) {
            e.preventDefault();
            return;
        }
        
        submitBtn.disabled = true;
        submitText.textContent = 'Importation en cours...';
        loadingSpinner.classList.remove('d-none');
    });

    // Fonction pour effacer le fichier
    window.clearFile = function() {
        fileInput.value = '';
        filePreview.style.display = 'none';
        submitBtn.disabled = true;
    };

    // Fonction pour formater la taille du fichier
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>

<style>
.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #000;
}
.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(0,0,0,.125);
}
</style>
@endsection