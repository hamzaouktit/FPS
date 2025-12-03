@extends('layouts.app')

@section('title', 'Créer une Affectation')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.dashboard') }}">Tableau de Bord</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.affectations.index') }}">Affectations</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Nouvelle Affectation</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-plus-circle me-2"></i>Créer une Nouvelle Affectation
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

        <form action="{{ route('administration.etablissement.affectations.store') }}" method="POST" id="affectationForm">
            @csrf
            
            <!-- Formateurs (EN PREMIER) -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i>
                        <span class="badge bg-primary">ÉTAPE 1</span> Sélection des Formateurs
                    </h6>
                    <small class="text-muted">Sélectionnez d'abord le(s) formateur(s) pour filtrer les modules disponibles</small>
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
                                                {{ old('mle_affecte_presentiel') == $formateur->mle ? 'selected' : '' }}>
                                            {{ $formateur->mle }} - {{ $formateur->nom_complet }} ({{ $formateur->type }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('mle_affecte_presentiel')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-info">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Obligatoire pour filtrer les modules
                                </small>
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
                                                {{ old('mle_affecte_syn') == $formateur->mle ? 'selected' : '' }}>
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
                        <span class="badge bg-info">ÉTAPE 2</span> Sélection du Module
                    </h6>
                    <small class="text-muted">Choisissez le module parmi ceux enseignés par le(s) formateur(s)</small>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="module_id" class="form-label">
                            Module 
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('module_id') is-invalid @enderror" 
                                id="module_id" name="module_id" required disabled>
                            <option value="">Sélectionnez d'abord un formateur présentiel</option>
                        </select>
                        @error('module_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="module-loading" class="text-center mt-2" style="display:none;">
                            <div class="spinner-border spinner-border-sm text-primary me-2" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            Chargement des modules...
                        </div>
                        <small class="text-muted" id="module-info">
                            <i class="fas fa-lightbulb me-1"></i>
                            Seuls les modules enseignés par le formateur sélectionné seront affichés
                        </small>
                    </div>
                </div>
            </div>

            <!-- Groupe et Autres -->
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
                                                {{ old('groupe_id') == $groupe->id ? 'selected' : '' }}
                                                data-filiere="{{ $groupe->filiere->nom_filiere ?? '' }}"
                                                data-formation="{{ $groupe->formation->type ?? '' }}">
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

                    <!-- Fusion de Groupe -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fusion_groupe" class="form-label">Fusion Groupe</label>
                                <input type="text" class="form-control @error('fusion_groupe') is-invalid @enderror" 
                                       id="fusion_groupe" name="fusion_groupe" 
                                       value="{{ old('fusion_groupe') }}"
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
                                       value="{{ old('code_fusion') }}"
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
                                       id="mhp_s1_drif" name="mhp_s1_drif" value="{{ old('mhp_s1_drif', 0) }}"
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
                                       id="mhsyn_s1_drif" name="mhsyn_s1_drif" value="{{ old('mhsyn_s1_drif', 0) }}"
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
                                       id="mhasyn_s1_drif" name="mhasyn_s1_drif" value="{{ old('mhasyn_s1_drif', 0) }}"
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
                                       id="mh_totale_s1_drif" name="mh_totale_s1_drif" value="0.00"
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
                                       id="mhp_s2_drif" name="mhp_s2_drif" value="{{ old('mhp_s2_drif', 0) }}"
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
                                       id="mhsyn_s2_drif" name="mhsyn_s2_drif" value="{{ old('mhsyn_s2_drif', 0) }}"
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
                                       id="mhasyn_s2_drif" name="mhasyn_s2_drif" value="{{ old('mhasyn_s2_drif', 0) }}"
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
                                       id="mh_totale_s2_drif" name="mh_totale_s2_drif" value="0.00"
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
                                       value="{{ old('mh_affectee_presentiel', 0) }}"
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
                                       value="{{ old('mh_affectee_sync', 0) }}"
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
                                       id="mh_affectee_globale" name="mh_affectee_globale" value="0.00"
                                       readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('administration.etablissement.affectations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Créer l'Affectation
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formateurPresentielSelect = document.getElementById('mle_affecte_presentiel');
    const formateurSynSelect = document.getElementById('mle_affecte_syn');
    const moduleSelect = document.getElementById('module_id');
    const moduleLoading = document.getElementById('module-loading');
    const moduleInfo = document.getElementById('module-info');
    
    // ✅ URL CORRIGÉE - utilise la route nommée
    const getModulesUrl = "{{ route('administration.etablissement.affectations.formateurs.modules', ':mle') }}";
    
    function loadModules() {
        const mlePresentiel = formateurPresentielSelect.value;
        
        if (!mlePresentiel) {
            moduleSelect.disabled = true;
            moduleSelect.innerHTML = '<option value="">Sélectionnez d\'abord un formateur présentiel</option>';
            return;
        }
        
        moduleLoading.style.display = 'block';
        moduleInfo.style.display = 'none';
        moduleSelect.disabled = true;
        moduleSelect.innerHTML = '<option value="">Chargement...</option>';
        
        // ✅ Construction correcte de l'URL
        const url = getModulesUrl.replace(':mle', mlePresentiel);
        
        console.log('🔍 Chargement des modules pour MLE:', mlePresentiel);
        console.log('📡 URL:', url);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            }
        })
        .then(response => {
            console.log('📥 Réponse statut:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Données reçues:', data);
            
            moduleSelect.innerHTML = '<option value="">Sélectionner un module</option>';
            
            if (!data.success || !data.modules || data.modules.length === 0) {
                moduleSelect.innerHTML = '<option value="">Aucun module trouvé pour ce formateur</option>';
                moduleLoading.style.display = 'none';
                moduleInfo.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Ce formateur n\'a aucun module assigné';
                moduleInfo.className = 'text-warning';
                moduleInfo.style.display = 'block';
            } else {
                data.modules.forEach(module => {
                    const option = document.createElement('option');
                    option.value = module.id;
                    option.textContent = module.text;
                    moduleSelect.appendChild(option);
                });
                
                moduleSelect.disabled = false;
                moduleLoading.style.display = 'none';
                moduleInfo.innerHTML = `<i class="fas fa-check-circle me-1"></i> ${data.modules.length} module(s) disponible(s)`;
                moduleInfo.className = 'text-success';
                moduleInfo.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('❌ Erreur:', error);
            moduleSelect.innerHTML = '<option value="">Erreur lors du chargement des modules</option>';
            moduleLoading.style.display = 'none';
            moduleInfo.innerHTML = '<i class="fas fa-times-circle me-1"></i> Erreur: ' + error.message;
            moduleInfo.className = 'text-danger';
            moduleInfo.style.display = 'block';
        });
    }
    
    formateurPresentielSelect.addEventListener('change', loadModules);
    formateurSynSelect.addEventListener('change', loadModules);
    
    if (formateurPresentielSelect.value) {
        loadModules();
    }
    
    // Calcul automatique des masses horaires
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

    const mhInputs = document.querySelectorAll('input[type="number"]');
    mhInputs.forEach(input => {
        input.addEventListener('input', calculateTotals);
    });

    calculateTotals();
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

.badge {
    font-size: 0.75rem;
    padding: 0.25em 0.6em;
}

#module-loading .spinner-border {
    width: 1rem;
    height: 1rem;
}
</style>
@endsection