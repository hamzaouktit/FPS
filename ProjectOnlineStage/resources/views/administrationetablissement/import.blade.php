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

@if(session('validation_errors'))
<div class="alert alert-danger" role="alert">
    <h6><i class="fas fa-exclamation-circle me-2"></i>Erreurs de validation :</h6>
    <ul class="mb-0">
        @foreach(session('validation_errors') as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if($errors->has('import_errors'))
<div class="alert alert-warning" role="alert">
    <h6><i class="fas fa-exclamation-triangle me-2"></i>Avertissements lors de l'importation :</h6>
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

<!-- Aide et format attendu -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-question-circle text-info me-2"></i>
                    Format Excel attendu
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-3">Le fichier Excel doit contenir les colonnes suivantes (les noms doivent correspondre exactement) :</p>
                
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Colonne</th>
                                <th>Description</th>
                                <th>Obligatoire</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code>Date MAJ</code></td>
                                <td>Date de mise à jour</td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td><code>Année</code></td>
                                <td>Année de formation</td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td><code>Code EFP</code></td>
                                <td>Code de l'établissement</td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td><code>EFP</code></td>
                                <td>Nom de l'établissement</td>
                                <td class="text-center"><i class="fas fa-times text-danger"></i></td>
                            </tr>
                            <tr>
                                <td><code>Niveau</code></td>
                                <td>Niveau de formation (TS, T, Q...)</td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td><code>Secteur</code></td>
                                <td>Secteur de formation</td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td><code>Code Filière</code></td>
                                <td>Code de la filière</td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td><code>filière</code></td>
                                <td>Nom de la filière</td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td><code>Groupe</code></td>
                                <td>Code du groupe</td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td><code>Code Module</code></td>
                                <td>Code du module</td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td><code>Module</code></td>
                                <td>Nom du module</td>
                                <td class="text-center"><i class="fas fa-check text-success"></i></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-center text-muted">
                                    <small>... et toutes les autres colonnes selon votre format Excel</small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="alert alert-info mt-3" role="alert">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Conseil :</strong> Assurez-vous que la première ligne de votre fichier Excel contient les en-têtes de colonnes 
                    exactement comme indiqué ci-dessus.
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
@endsection