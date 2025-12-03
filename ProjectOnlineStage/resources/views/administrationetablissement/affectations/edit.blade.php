@extends('layouts.app')

@section('title', 'Modifier l\'Affectation')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.dashboard') }}">Tableau de Bord</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.affectations.index') }}">Affectations</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.affectations.show', $affectation->id) }}">Détails</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Modifier</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
            <i class="fas fa-edit me-2"></i>Modifier l'Affectation
        </h5>
    </div>
    
    <div class="card-body">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Erreur de validation:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('administration.etablissement.affectations.update', $affectation->id) }}" method="POST" id="editAffectationForm">
            @csrf
            @method('PUT')
            
            <!-- Formateurs (EN PREMIER) -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i>
                        <span class="badge bg-primary">ÉTAPE 1</span> Formateurs Assignés
                    </h6>
                    <small class="text-muted">Modifiez le(s) formateur(s) pour filtrer les modules disponibles</small>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mle_affecte_presentiel" class="form-label">
                                    Formateur Présentiel 
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('mle_affecte_presentiel') is-invalid @enderror" 
                                        id="mle_affecte_presentiel" name="mle_affecte_presentiel" required>
                                    <option value="">Sélectionner un formateur</option>
                                    @foreach($formateurs as $formateur)
                                        <option value="{{ $formateur->mle }}" 
                                            {{ old('mle_affecte_presentiel', $affectation->mle_affecte_presentiel) == $formateur->mle ? 'selected' : '' }}>
                                            {{ $formateur->mle }} - {{ $formateur->nom_complet }} ({{ $formateur->type }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('mle_affecte_presentiel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mle_affecte_syn" class="form-label">
                                    Formateur Synchrone
                                    <span class="text-muted">(Optionnel)</span>
                                </label>
                                <select class="form-select @error('mle_affecte_syn') is-invalid @enderror" 
                                        id="mle_affecte_syn" name="mle_affecte_syn">
                                    <option value="">Sélectionner un formateur</option>
                                    @foreach($formateurs as $formateur)
                                        <option value="{{ $formateur->mle }}" 
                                            {{ old('mle_affecte_syn', $affectation->mle_affecte_syn) == $formateur->mle ? 'selected' : '' }}>
                                            {{ $formateur->mle }} - {{ $formateur->nom_complet }} ({{ $formateur->type }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('mle_affecte_syn')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Module (EN DEUXIÈME) -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-book me-2"></i>
                        <span class="badge bg-info">ÉTAPE 2</span> Module
                    </h6>
                    <small class="text-muted">Le module doit être enseigné par le(s) formateur(s) sélectionné(s)</small>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="module_id" class="form-label">
                            Module 
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('module_id') is-invalid @enderror" 
                                id="module_id" name="module_id" required>
                            <option value="">Sélectionner un module</option>
                            <!-- Options chargées dynamiquement -->
                        </select>
                        <div id="module-loading" class="text-center mt-2" style="display:none;">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            Chargement des modules...
                        </div>
                        <div id="module-info" class="mt-2"></div>
                        <small class="text-muted">
                            <i class="fas fa-lightbulb me-1"></i>
                            Modules enseignés par le formateur sélectionné
                        </small>
                    </div>
                </div>
            </div>

            <!-- Groupe et Fusion -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-users me-2"></i>
                        <span class="badge bg-success">ÉTAPE 3</span> Groupe et Fusion
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="groupe_id" class="form-label">
                                    Groupe <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('groupe_id') is-invalid @enderror" 
                                        id="groupe_id" name="groupe_id" required>
                                    <option value="">Sélectionner un groupe</option>
                                    @foreach($groupes as $groupe)
                                        <option value="{{ $groupe->id }}" 
                                            {{ old('groupe_id', $affectation->groupe_id) == $groupe->id ? 'selected' : '' }}>
                                            {{ $groupe->code_groupe }} - {{ $groupe->filiere->nom_filiere ?? 'N/A' }} ({{ $groupe->formation->type ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('groupe_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fusion_groupe" class="form-label">Fusion Groupe</label>
                                <input type="text" class="form-control @error('fusion_groupe') is-invalid @enderror" 
                                       id="fusion_groupe" name="fusion_groupe" 
                                       value="{{ old('fusion_groupe', $affectation->fusion_groupe) }}"
                                       placeholder="Ex: Groupe A + Groupe B">
                                @error('fusion_groupe')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code_fusion" class="form-label">Code Fusion</label>
                                <input type="text" class="form-control @error('code_fusion') is-invalid @enderror" 
                                       id="code_fusion" name="code_fusion" 
                                       value="{{ old('code_fusion', $affectation->code_fusion) }}"
                                       placeholder="Ex: GRP-FUSION-001">
                                @error('code_fusion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Masses Horaires DRIF - Semestre 1 -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Masses Horaires DRIF - Semestre 1</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="mhp_s1_drif" class="form-label">MHP S1 (h)</label>
                                <input type="number" step="0.01" class="form-control @error('mhp_s1_drif') is-invalid @enderror" 
                                       id="mhp_s1_drif" name="mhp_s1_drif" 
                                       value="{{ old('mhp_s1_drif', $affectation->mhp_s1_drif) }}"
                                       min="0" placeholder="0.00">
                                @error('mhp_s1_drif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="mhsyn_s1_drif" class="form-label">MHSYN S1 (h)</label>
                                <input type="number" step="0.01" class="form-control @error('mhsyn_s1_drif') is-invalid @enderror" 
                                       id="mhsyn_s1_drif" name="mhsyn_s1_drif" 
                                       value="{{ old('mhsyn_s1_drif', $affectation->mhsyn_s1_drif) }}"
                                       min="0" placeholder="0.00">
                                @error('mhsyn_s1_drif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="mhasyn_s1_drif" class="form-label">MHASYN S1 (h)</label>
                                <input type="number" step="0.01" class="form-control @error('mhasyn_s1_drif') is-invalid @enderror" 
                                       id="mhasyn_s1_drif" name="mhasyn_s1_drif" 
                                       value="{{ old('mhasyn_s1_drif', $affectation->mhasyn_s1_drif) }}"
                                       min="0" placeholder="0.00">
                                @error('mhasyn_s1_drif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="mh_totale_s1_drif" class="form-label">Total S1 (h)</label>
                                <input type="number" step="0.01" class="form-control bg-light" 
                                       id="mh_totale_s1_drif" name="mh_totale_s1_drif" 
                                       value="{{ old('mh_totale_s1_drif', $affectation->mh_totale_s1_drif) }}"
                                       readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Masses Horaires DRIF - Semestre 2 -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Masses Horaires DRIF - Semestre 2</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="mhp_s2_drif" class="form-label">MHP S2 (h)</label>
                                <input type="number" step="0.01" class="form-control @error('mhp_s2_drif') is-invalid @enderror" 
                                       id="mhp_s2_drif" name="mhp_s2_drif" 
                                       value="{{ old('mhp_s2_drif', $affectation->mhp_s2_drif) }}"
                                       min="0" placeholder="0.00">
                                @error('mhp_s2_drif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="mhsyn_s2_drif" class="form-label">MHSYN S2 (h)</label>
                                <input type="number" step="0.01" class="form-control @error('mhsyn_s2_drif') is-invalid @enderror" 
                                       id="mhsyn_s2_drif" name="mhsyn_s2_drif" 
                                       value="{{ old('mhsyn_s2_drif', $affectation->mhsyn_s2_drif) }}"
                                       min="0" placeholder="0.00">
                                @error('mhsyn_s2_drif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="mhasyn_s2_drif" class="form-label">MHASYN S2 (h)</label>
                                <input type="number" step="0.01" class="form-control @error('mhasyn_s2_drif') is-invalid @enderror" 
                                       id="mhasyn_s2_drif" name="mhasyn_s2_drif" 
                                       value="{{ old('mhasyn_s2_drif', $affectation->mhasyn_s2_drif) }}"
                                       min="0" placeholder="0.00">
                                @error('mhasyn_s2_drif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="mh_totale_s2_drif" class="form-label">Total S2 (h)</label>
                                <input type="number" step="0.01" class="form-control bg-light" 
                                       id="mh_totale_s2_drif" name="mh_totale_s2_drif" 
                                       value="{{ old('mh_totale_s2_drif', $affectation->mh_totale_s2_drif) }}"
                                       readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Masses Horaires Affectées -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Masses Horaires Affectées</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="mh_affectee_presentiel" class="form-label">MH Présentiel (h)</label>
                                <input type="number" step="0.01" class="form-control @error('mh_affectee_presentiel') is-invalid @enderror" 
                                       id="mh_affectee_presentiel" name="mh_affectee_presentiel" 
                                       value="{{ old('mh_affectee_presentiel', $affectation->mh_affectee_presentiel) }}"
                                       min="0" placeholder="0.00">
                                @error('mh_affectee_presentiel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="mh_affectee_sync" class="form-label">MH Synchrone (h)</label>
                                <input type="number" step="0.01" class="form-control @error('mh_affectee_sync') is-invalid @enderror" 
                                       id="mh_affectee_sync" name="mh_affectee_sync" 
                                       value="{{ old('mh_affectee_sync', $affectation->mh_affectee_sync) }}"
                                       min="0" placeholder="0.00">
                                @error('mh_affectee_sync')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="mh_affectee_globale" class="form-label">MH Globale (h)</label>
                                <input type="number" step="0.01" class="form-control bg-light" 
                                       id="mh_affectee_globale" name="mh_affectee_globale" 
                                       value="{{ old('mh_affectee_globale', $affectation->mh_affectee_globale) }}"
                                       readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('administration.etablissement.affectations.show', $affectation->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Annuler
                </a>
                <div>
                    <a href="{{ route('administration.etablissement.affectations.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-list me-2"></i>Retour à la liste
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formateurPresentielSelect = document.getElementById('mle_affecte_presentiel');
    const moduleSelect = document.getElementById('module_id');
    const moduleLoading = document.getElementById('module-loading');
    const moduleInfo = document.getElementById('module-info');
    const currentModuleId = {{ $affectation->module_id }};
    const currentMle = '{{ $affectation->mle_affecte_presentiel }}';
    
    // Variable pour stocker les modules chargés
    let loadedModules = [];
    
    // Fonction pour charger les modules
    function loadModules() {
        const mlePresentiel = formateurPresentielSelect.value;
        
        if (!mlePresentiel) {
            moduleSelect.innerHTML = '<option value="">Sélectionnez d\'abord un formateur présentiel</option>';
            moduleSelect.disabled = true;
            moduleInfo.innerHTML = '';
            return;
        }
        
        moduleLoading.style.display = 'block';
        moduleInfo.style.display = 'none';
        moduleSelect.disabled = true;
        
        // URL correcte pour la route AJAX
        const url = '{{ route("administration.etablissement.affectations.formateurs.modules", ["mle" => "__MLE__"]) }}'.replace('__MLE__', mlePresentiel);
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .then(data => {
                moduleSelect.innerHTML = '<option value="">Sélectionner un module</option>';
                loadedModules = [];
                
                if (!data.success || data.modules.length === 0) {
                    const message = data.message || 'Aucun module trouvé pour ce formateur';
                    moduleSelect.innerHTML = `<option value="">${message}</option>`;
                    moduleInfo.innerHTML = `<div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle me-1"></i> ${message}</div>`;
                } else {
                    data.modules.forEach(module => {
                        const option = document.createElement('option');
                        option.value = module.id;
                        option.textContent = module.text;
                        option.dataset.moduleCode = module.code_module;
                        option.dataset.moduleName = module.nom_module;
                        
                        // Présélectionner le module actuel
                        if (module.id == currentModuleId) {
                            option.selected = true;
                        }
                        
                        moduleSelect.appendChild(option);
                        loadedModules.push({
                            id: module.id,
                            code_module: module.code_module,
                            nom_module: module.nom_module,
                            text: module.text
                        });
                    });
                    
                    moduleSelect.disabled = false;
                    moduleInfo.innerHTML = `<div class="alert alert-success mb-0"><i class="fas fa-check-circle me-1"></i> ${data.modules.length} module(s) disponible(s) pour ce formateur</div>`;
                    
                    // Si le module actuel n'est pas dans la liste, l'ajouter
                    if (currentModuleId && !loadedModules.find(m => m.id == currentModuleId)) {
                        const currentModuleOption = document.createElement('option');
                        currentModuleOption.value = currentModuleId;
                        currentModuleOption.textContent = 'Module actuel (non assigné à ce formateur)';
                        currentModuleOption.selected = true;
                        currentModuleOption.style.color = 'red';
                        moduleSelect.appendChild(currentModuleOption);
                        
                        moduleInfo.innerHTML += `<div class="alert alert-danger mt-2 mb-0"><i class="fas fa-exclamation-triangle me-1"></i> Attention: Le module actuel n'est pas assigné à ce formateur</div>`;
                    }
                }
                
                moduleLoading.style.display = 'none';
                moduleInfo.style.display = 'block';
            })
            .catch(error => {
                console.error('Erreur:', error);
                moduleSelect.innerHTML = '<option value="">Erreur lors du chargement des modules</option>';
                moduleLoading.style.display = 'none';
                moduleInfo.innerHTML = `<div class="alert alert-danger mb-0"><i class="fas fa-times-circle me-1"></i> Erreur lors du chargement des modules: ${error.message}</div>`;
                moduleInfo.style.display = 'block';
            });
    }
    
    // Écouter les changements sur le formateur présentiel
    formateurPresentielSelect.addEventListener('change', loadModules);
    
    // Charger les modules au chargement si un formateur est déjà sélectionné
    if (currentMle) {
        loadModules();
    } else {
        moduleSelect.innerHTML = '<option value="">Sélectionnez d\'abord un formateur présentiel</option>';
        moduleSelect.disabled = true;
    }
    
    // Calcul automatique des totaux
    function calculateTotals() {
        const mhpS1 = parseFloat(document.getElementById('mhp_s1_drif').value) || 0;
        const mhsynS1 = parseFloat(document.getElementById('mhsyn_s1_drif').value) || 0;
        const mhasynS1 = parseFloat(document.getElementById('mhasyn_s1_drif').value) || 0;
        const totalS1 = mhpS1 + mhsynS1 + mhasynS1;
        document.getElementById('mh_totale_s1_drif').value = totalS1.toFixed(2);

        const mhpS2 = parseFloat(document.getElementById('mhp_s2_drif').value) || 0;
        const mhsynS2 = parseFloat(document.getElementById('mhsyn_s2_drif').value) || 0;
        const mhasynS2 = parseFloat(document.getElementById('mhasyn_s2_drif').value) || 0;
        const totalS2 = mhpS2 + mhsynS2 + mhasynS2;
        document.getElementById('mh_totale_s2_drif').value = totalS2.toFixed(2);

        const mhPresentiel = parseFloat(document.getElementById('mh_affectee_presentiel').value) || 0;
        const mhSync = parseFloat(document.getElementById('mh_affectee_sync').value) || 0;
        const totalAffectee = mhPresentiel + mhSync;
        document.getElementById('mh_affectee_globale').value = totalAffectee.toFixed(2);
    }

    // Ajouter les écouteurs d'événements pour le calcul automatique
    const mhInputs = document.querySelectorAll('input[type="number"]');
    mhInputs.forEach(input => {
        input.addEventListener('input', calculateTotals);
        input.addEventListener('change', calculateTotals);
    });

    // Calcul initial
    calculateTotals();
    
    // Validation du formulaire
    document.getElementById('editAffectationForm').addEventListener('submit', function(e) {
        const moduleId = moduleSelect.value;
        const mlePresentiel = formateurPresentielSelect.value;
        
        if (!moduleId) {
            e.preventDefault();
            alert('Veuillez sélectionner un module');
            return false;
        }
        
        // Vérifier si le module est dans la liste chargée
        if (loadedModules.length > 0 && !loadedModules.find(m => m.id == moduleId)) {
            if (!confirm('Le module sélectionné n\'est pas dans la liste des modules de ce formateur. Voulez-vous continuer?')) {
                e.preventDefault();
                return false;
            }
        }
    });
});
</script>

<style>
.card {
    border-radius: 10px;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
    font-weight: 600;
}

.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn {
    padding: 0.625rem 1.5rem;
    font-weight: 500;
    border-radius: 8px;
}

.alert {
    padding: 0.75rem 1.25rem;
    margin-bottom: 0;
}
</style>
@endsection