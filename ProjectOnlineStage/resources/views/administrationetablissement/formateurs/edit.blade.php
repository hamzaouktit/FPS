@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Modifier le Formateur</h2>
                    <p class="text-muted mb-0">{{ $formateur->nom_complet }} ({{ $formateur->mle }})</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('administration.etablissement.formateurs.show', $formateur) }}" 
                       class="btn btn-info">
                        <i class="fas fa-eye me-2"></i> Voir Détails
                    </a>
                    <a href="{{ route('administration.etablissement.formateurs.index') }}" 
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations actuelles -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3"></i>
                <div>
                    <strong>Information:</strong> Vous modifiez les informations du formateur 
                    <strong>{{ $formateur->nom_complet }}</strong>. 
                    Les modifications seront appliquées immédiatement.
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form action="{{ route('administration.etablissement.formateurs.update', $formateur) }}" 
                          method="POST" 
                          id="formateurForm">
                        @csrf
                        @method('PUT')

                        <!-- Section 1: Informations de base -->
                        <div class="form-section mb-4">
                            <h5 class="form-section-title">
                                <i class="fas fa-user-circle me-2 text-primary"></i>
                                Informations de base
                            </h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="mle" class="form-label required">
                                            <i class="fas fa-id-card me-2 text-muted"></i>MLE
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('mle') is-invalid @enderror" 
                                               id="mle" 
                                               name="mle" 
                                               value="{{ old('mle', $formateur->mle) }}" 
                                               placeholder="Ex: MLE12345"
                                               required>
                                        @error('mle')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                        <small class="form-text text-muted">Identifiant unique du formateur</small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="nom_complet" class="form-label required">
                                            <i class="fas fa-user me-2 text-muted"></i>Nom Complet
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('nom_complet') is-invalid @enderror" 
                                               id="nom_complet" 
                                               name="nom_complet" 
                                               value="{{ old('nom_complet', $formateur->nom_complet) }}"
                                               placeholder="Ex: Ahmed ALAMI"
                                               required>
                                        @error('nom_complet')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="type" class="form-label required">
                                            <i class="fas fa-briefcase me-2 text-muted"></i>Type
                                        </label>
                                        <select class="form-select @error('type') is-invalid @enderror" 
                                                id="type" 
                                                name="type" 
                                                required>
                                            <option value="">Sélectionnez le type</option>
                                            <option value="permanent" 
                                                {{ old('type', $formateur->type) == 'permanent' ? 'selected' : '' }}>
                                                Permanent
                                            </option>
                                            <option value="vacataire" 
                                                {{ old('type', $formateur->type) == 'vacataire' ? 'selected' : '' }}>
                                                Vacataire
                                            </option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label for="masse_horaire" class="form-label">
                                            <i class="fas fa-hourglass-half me-2 text-muted"></i>Masse Horaire Annuelle
                                        </label>
                                        <div class="input-group">
                                            <input type="number" 
                                                   class="form-control @error('masse_horaire') is-invalid @enderror" 
                                                   id="masse_horaire" 
                                                   name="masse_horaire" 
                                                   value="{{ old('masse_horaire', $formateur->masse_horaire) }}"
                                                   min="0"
                                                   step="0.01"
                                                   placeholder="910.00">
                                            <span class="input-group-text">heures</span>
                                            @error('masse_horaire')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <small class="form-text text-muted">Masse horaire annuelle du formateur</small>
                                    </div>
                                </div>

                                <div class="col-md-8">
                                    <div class="form-group mb-3">
                                        <label for="description" class="form-label">
                                            <i class="fas fa-info-circle me-2 text-muted"></i>Description
                                        </label>
                                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                                  id="description" 
                                                  name="description" 
                                                  rows="3" 
                                                  placeholder="Entrez une brève description du formateur...">{{ old('description', $formateur->description) }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                        <small class="form-text text-muted">Une brève description du formateur (optionnel)</small>
                                    </div>
                                </div>
                            </div>

                                <div class="col-md-8">
                                    <div class="info-box">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-building fa-2x me-3 text-primary"></i>
                                            <div>
                                                <strong>Établissement(s):</strong>
                                                @foreach($formateur->etablissements as $etab)
                                                    <span class="badge bg-primary ms-2">{{ $etab->nom_efp }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Section 2: Compétences -->
                        <div class="form-section mb-4">
                            <h5 class="form-section-title">
                                <i class="fas fa-graduation-cap me-2 text-success"></i>
                                Compétences et Spécialisations
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="secteurs" class="form-label">
                                            <i class="fas fa-industry me-2 text-muted"></i>Secteurs
                                            <span class="badge bg-info-subtle text-info ms-2" id="secteurCount">
                                                {{ count(old('secteurs', $formateur->secteurs->pluck('id')->toArray())) }} sélectionné(s)
                                            </span>
                                        </label>
                                        <select multiple 
                                                class="form-select @error('secteurs') is-invalid @enderror" 
                                                id="secteurs" 
                                                name="secteurs[]" 
                                                size="8">
                                            @forelse($secteurs as $secteur)
                                                <option value="{{ $secteur->id }}" 
                                                    {{ in_array($secteur->id, old('secteurs', $formateur->secteurs->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                    {{ $secteur->nom_secteur }}
                                                </option>
                                            @empty
                                                <option disabled>Aucun secteur disponible</option>
                                            @endforelse
                                        </select>
                                        @error('secteurs')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>Maintenez <kbd>Ctrl</kbd> (Windows) ou <kbd>Cmd</kbd> (Mac) pour sélectionner plusieurs secteurs
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="modules" class="form-label">
                                            <i class="fas fa-book me-2 text-muted"></i>Modules (Compétences)
                                            <span class="badge bg-secondary-subtle text-secondary ms-2" id="moduleCount">
                                                {{ count(old('modules', $formateur->modules->pluck('id')->toArray())) }} sélectionné(s)
                                            </span>
                                        </label>
                                        <select multiple 
                                                class="form-select @error('modules') is-invalid @enderror" 
                                                id="modules" 
                                                name="modules[]" 
                                                size="8">
                                            @forelse($modules as $module)
                                                <option value="{{ $module->id }}" 
                                                    {{ in_array($module->id, old('modules', $formateur->modules->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                    {{ $module->code_module }} - {{ $module->nom_module }}
                                                </option>
                                            @empty
                                                <option disabled>Aucun module disponible</option>
                                            @endforelse
                                        </select>
                                        @error('modules')
                                            <div class="invalid-feedback">
                                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle me-1"></i>Maintenez <kbd>Ctrl</kbd> (Windows) ou <kbd>Cmd</kbd> (Mac) pour sélectionner plusieurs modules
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Boutons d'action -->
                        <div class="form-actions d-flex justify-content-between align-items-center">
                            <div>
                                <span class="text-muted small">
                                    <i class="fas fa-asterisk text-danger me-1" style="font-size: 0.5rem;"></i>
                                    Les champs marqués d'un astérisque sont obligatoires
                                </span>
                            </div>
                            <div class="btn-group">
                                <button type="button" 
                                        class="btn btn-outline-secondary" 
                                        onclick="window.location.reload()">
                                    <i class="fas fa-undo me-2"></i> Annuler les modifications
                                </button>
                                <a href="{{ route('administration.etablissement.formateurs.index') }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i> Retour à la liste
                                </a>
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="fas fa-save me-2"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Card */
.card {
    border-radius: 0.75rem;
    overflow: hidden;
}

/* Alert */
.alert {
    border-radius: 0.75rem;
    padding: 1rem 1.5rem;
}

/* Form Section */
.form-section {
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 0.75rem;
}

.form-section-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #dee2e6;
}

/* Info Box */
.info-box {
    padding: 1rem;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border: 1px solid rgba(102, 126, 234, 0.3);
    border-radius: 0.75rem;
    margin-top: 1.5rem;
}

/* Form Labels */
.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-label.required::after {
    content: " *";
    color: #dc3545;
}

/* Form Controls */
.form-control, .form-select {
    border-radius: 0.5rem;
    border: 1px solid #dee2e6;
    padding: 0.625rem 0.875rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-select[multiple] {
    min-height: 200px;
    background-image: none;
}

.form-select[multiple] option {
    padding: 0.5rem;
    border-radius: 0.25rem;
    margin-bottom: 0.25rem;
    transition: background-color 0.15s ease;
}

.form-select[multiple] option:hover {
    background-color: #e9ecef;
}

.form-select[multiple] option:checked {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

/* Input Group */
.input-group-text {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0 0.5rem 0.5rem 0;
    font-weight: 500;
    color: #6c757d;
}

/* Invalid Feedback */
.invalid-feedback {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.875rem;
}

/* Small Text */
.form-text {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.875rem;
}

/* KBD Tag */
kbd {
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
    color: #fff;
    background-color: #212529;
    border-radius: 0.25rem;
}

/* Badges */
.badge {
    font-weight: 500;
    padding: 0.35rem 0.65rem;
}

.bg-info-subtle {
    background-color: rgba(13, 202, 240, 0.1) !important;
}

.bg-secondary-subtle {
    background-color: rgba(108, 117, 125, 0.1) !important;
}

.text-info {
    color: #0dcaf0 !important;
}

.text-secondary {
    color: #6c757d !important;
}

/* Buttons */
.btn-group .btn {
    border-radius: 0.5rem !important;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.5rem;
}

.btn {
    padding: 0.625rem 1.25rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-success {
    background: linear-gradient(135deg, #198754 0%, #0f5132 100%);
    border: none;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
}

.btn-info {
    background: linear-gradient(135deg, #0dcaf0 0%, #0891b2 100%);
    border: none;
    color: white;
}

.btn-info:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(13, 202, 240, 0.3);
    color: white;
}

/* HR */
hr {
    border-top: 2px solid #e9ecef;
    opacity: 1;
}

/* Shadow */
.shadow-sm {
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075) !important;
}

/* Responsive */
@media (max-width: 768px) {
    .form-section {
        padding: 1rem;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-actions > div {
        width: 100%;
    }
    
    .btn-group {
        width: 100%;
        flex-direction: column;
    }
    
    .btn-group .btn {
        width: 100%;
        margin-right: 0 !important;
        margin-bottom: 0.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Compteur pour les secteurs sélectionnés
    const secteursSelect = document.getElementById('secteurs');
    const secteurCount = document.getElementById('secteurCount');
    
    if (secteursSelect && secteurCount) {
        secteursSelect.addEventListener('change', function() {
            const count = Array.from(this.selectedOptions).length;
            secteurCount.textContent = count + ' sélectionné(s)';
        });
    }
    
    // Compteur pour les modules sélectionnés
    const modulesSelect = document.getElementById('modules');
    const moduleCount = document.getElementById('moduleCount');
    
    if (modulesSelect && moduleCount) {
        modulesSelect.addEventListener('change', function() {
            const count = Array.from(this.selectedOptions).length;
            moduleCount.textContent = count + ' sélectionné(s)';
        });
    }
    
    // Validation et confirmation avant soumission
    const form = document.getElementById('formateurForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            const mle = document.getElementById('mle').value.trim();
            const nomComplet = document.getElementById('nom_complet').value.trim();
            const type = document.getElementById('type').value;
            
            if (!mle || !nomComplet || !type) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires (MLE, Nom Complet, Type)');
                return false;
            }
            
            // Désactiver le bouton pour éviter les doubles soumissions
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Enregistrement...';
        });
    }
});
</script>
@endsection