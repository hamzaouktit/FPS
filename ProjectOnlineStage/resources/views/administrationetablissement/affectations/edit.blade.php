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
<div class="card">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
            <i class="fas fa-edit me-2"></i>Modifier l'Affectation
        </h5>
    </div>
    
    <div class="card-body">
        <form action="{{ route('administration.etablissement.affectations.update', $affectation->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="groupe_id" class="form-label">Groupe *</label>
                        <select class="form-select @error('groupe_id') is-invalid @enderror" 
                                id="groupe_id" name="groupe_id" required>
                            <option value="">Sélectionner un groupe</option>
                            @foreach($groupes as $groupe)
                                <option value="{{ $groupe->id }}" {{ old('groupe_id', $affectation->groupe_id) == $groupe->id ? 'selected' : '' }}>
                                    {{ $groupe->code_groupe }} - {{ $groupe->filiere->nom_filiere }} ({{ $groupe->formation->type }})
                                </option>
                            @endforeach
                        </select>
                        @error('groupe_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="module_id" class="form-label">Module *</label>
                        <select class="form-select @error('module_id') is-invalid @enderror" 
                                id="module_id" name="module_id" required>
                            <option value="">Sélectionner un module</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->id }}" {{ old('module_id', $affectation->module_id) == $module->id ? 'selected' : '' }}>
                                    {{ $module->code_module }} - {{ $module->nom_module }}
                                </option>
                            @endforeach
                        </select>
                        @error('module_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mle_affecte_presentiel" class="form-label">Formateur Présentiel</label>
                        <select class="form-select @error('mle_affecte_presentiel') is-invalid @enderror" 
                                id="mle_affecte_presentiel" name="mle_affecte_presentiel">
                            <option value="">Sélectionner un formateur</option>
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->mle }}" {{ old('mle_affecte_presentiel', $affectation->mle_affecte_presentiel) == $formateur->mle ? 'selected' : '' }}>
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
                        <label for="mle_affecte_syn" class="form-label">Formateur Synchrone</label>
                        <select class="form-select @error('mle_affecte_syn') is-invalid @enderror" 
                                id="mle_affecte_syn" name="mle_affecte_syn">
                            <option value="">Sélectionner un formateur</option>
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->mle }}" {{ old('mle_affecte_syn', $affectation->mle_affecte_syn) == $formateur->mle ? 'selected' : '' }}>
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

            <hr>
            <h6 class="text-primary">
                <i class="fas fa-clock me-2"></i>Masses Horaires DRIF - Semestre 1
            </h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="mhp_s1_drif" class="form-label">MHP S1 DRIF (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mhp_s1_drif') is-invalid @enderror" 
                               id="mhp_s1_drif" name="mhp_s1_drif" value="{{ old('mhp_s1_drif', $affectation->mhp_s1_drif) }}">
                        @error('mhp_s1_drif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="mhsyn_s1_drif" class="form-label">MHSYN S1 DRIF (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mhsyn_s1_drif') is-invalid @enderror" 
                               id="mhsyn_s1_drif" name="mhsyn_s1_drif" value="{{ old('mhsyn_s1_drif', $affectation->mhsyn_s1_drif) }}">
                        @error('mhsyn_s1_drif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="mhasyn_s1_drif" class="form-label">MHASYN S1 DRIF (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mhasyn_s1_drif') is-invalid @enderror" 
                               id="mhasyn_s1_drif" name="mhasyn_s1_drif" value="{{ old('mhasyn_s1_drif', $affectation->mhasyn_s1_drif) }}">
                        @error('mhasyn_s1_drif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="mh_totale_s1_drif" class="form-label">MH Totale S1 DRIF (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mh_totale_s1_drif') is-invalid @enderror" 
                               id="mh_totale_s1_drif" name="mh_totale_s1_drif" value="{{ old('mh_totale_s1_drif', $affectation->mh_totale_s1_drif) }}">
                        @error('mh_totale_s1_drif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>
            <h6 class="text-primary">
                <i class="fas fa-clock me-2"></i>Masses Horaires DRIF - Semestre 2
            </h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="mhp_s2_drif" class="form-label">MHP S2 DRIF (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mhp_s2_drif') is-invalid @enderror" 
                               id="mhp_s2_drif" name="mhp_s2_drif" value="{{ old('mhp_s2_drif', $affectation->mhp_s2_drif) }}">
                        @error('mhp_s2_drif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="mhsyn_s2_drif" class="form-label">MHSYN S2 DRIF (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mhsyn_s2_drif') is-invalid @enderror" 
                               id="mhsyn_s2_drif" name="mhsyn_s2_drif" value="{{ old('mhsyn_s2_drif', $affectation->mhsyn_s2_drif) }}">
                        @error('mhsyn_s2_drif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="mhasyn_s2_drif" class="form-label">MHASYN S2 DRIF (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mhasyn_s2_drif') is-invalid @enderror" 
                               id="mhasyn_s2_drif" name="mhasyn_s2_drif" value="{{ old('mhasyn_s2_drif', $affectation->mhasyn_s2_drif) }}">
                        @error('mhasyn_s2_drif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="mh_totale_s2_drif" class="form-label">MH Totale S2 DRIF (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mh_totale_s2_drif') is-invalid @enderror" 
                               id="mh_totale_s2_drif" name="mh_totale_s2_drif" value="{{ old('mh_totale_s2_drif', $affectation->mh_totale_s2_drif) }}">
                        @error('mh_totale_s2_drif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>
            <h6 class="text-primary">
                <i class="fas fa-chart-bar me-2"></i>Masses Horaires Totales DRIF
            </h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="mhp_totale_drif" class="form-label">MHP Totale DRIF (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mhp_totale_drif') is-invalid @enderror" 
                               id="mhp_totale_drif" name="mhp_totale_drif" value="{{ old('mhp_totale_drif', $affectation->mhp_totale_drif) }}" readonly>
                        @error('mhp_totale_drif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="mhsyn_totale_drif" class="form-label">MHSYN Totale DRIF (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mhsyn_totale_drif') is-invalid @enderror" 
                               id="mhsyn_totale_drif" name="mhsyn_totale_drif" value="{{ old('mhsyn_totale_drif', $affectation->mhsyn_totale_drif) }}" readonly>
                        @error('mhsyn_totale_drif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="mhasyn_totale_drif" class="form-label">MHASYN Totale DRIF (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mhasyn_totale_drif') is-invalid @enderror" 
                               id="mhasyn_totale_drif" name="mhasyn_totale_drif" value="{{ old('mhasyn_totale_drif', $affectation->mhasyn_totale_drif) }}" readonly>
                        @error('mhasyn_totale_drif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="mh_totale_drif" class="form-label">MH Totale DRIF (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mh_totale_drif') is-invalid @enderror" 
                               id="mh_totale_drif" name="mh_totale_drif" value="{{ old('mh_totale_drif', $affectation->mh_totale_drif) }}" readonly>
                        @error('mh_totale_drif')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <hr>
            <h6 class="text-primary">
                <i class="fas fa-chart-pie me-2"></i>Masses Horaires Affectées
            </h6>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="mh_affectee_presentiel" class="form-label">MH Affectée Présentiel (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mh_affectee_presentiel') is-invalid @enderror" 
                               id="mh_affectee_presentiel" name="mh_affectee_presentiel" value="{{ old('mh_affectee_presentiel', $affectation->mh_affectee_presentiel) }}">
                        @error('mh_affectee_presentiel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="mh_affectee_sync" class="form-label">MH Affectée Sync (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mh_affectee_sync') is-invalid @enderror" 
                               id="mh_affectee_sync" name="mh_affectee_sync" value="{{ old('mh_affectee_sync', $affectation->mh_affectee_sync) }}">
                        @error('mh_affectee_sync')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="mh_affectee_globale" class="form-label">MH Affectée Globale (h)</label>
                        <input type="number" step="0.01" class="form-control @error('mh_affectee_globale') is-invalid @enderror" 
                               id="mh_affectee_globale" name="mh_affectee_globale" value="{{ old('mh_affectee_globale', $affectation->mh_affectee_globale) }}" readonly>
                        @error('mh_affectee_globale')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

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
    // Calcul automatique des totaux
    document.addEventListener('DOMContentLoaded', function() {
        function calculateTotals() {
            // Semestre 1
            const mhpS1 = parseFloat(document.getElementById('mhp_s1_drif').value) || 0;
            const mhsynS1 = parseFloat(document.getElementById('mhsyn_s1_drif').value) || 0;
            const mhasynS1 = parseFloat(document.getElementById('mhasyn_s1_drif').value) || 0;
            const totalS1 = mhpS1 + mhsynS1 + mhasynS1;
            document.getElementById('mh_totale_s1_drif').value = totalS1.toFixed(2);

            // Semestre 2
            const mhpS2 = parseFloat(document.getElementById('mhp_s2_drif').value) || 0;
            const mhsynS2 = parseFloat(document.getElementById('mhsyn_s2_drif').value) || 0;
            const mhasynS2 = parseFloat(document.getElementById('mhasyn_s2_drif').value) || 0;
            const totalS2 = mhpS2 + mhsynS2 + mhasynS2;
            document.getElementById('mh_totale_s2_drif').value = totalS2.toFixed(2);

            // Totaux DRIF
            const mhpTotal = mhpS1 + mhpS2;
            const mhsynTotal = mhsynS1 + mhsynS2;
            const mhasynTotal = mhasynS1 + mhasynS2;
            const mhTotal = totalS1 + totalS2;

            document.getElementById('mhp_totale_drif').value = mhpTotal.toFixed(2);
            document.getElementById('mhsyn_totale_drif').value = mhsynTotal.toFixed(2);
            document.getElementById('mhasyn_totale_drif').value = mhasynTotal.toFixed(2);
            document.getElementById('mh_totale_drif').value = mhTotal.toFixed(2);

            // Affectées
            const mhPresentiel = parseFloat(document.getElementById('mh_affectee_presentiel').value) || 0;
            const mhSync = parseFloat(document.getElementById('mh_affectee_sync').value) || 0;
            const totalAffectee = mhPresentiel + mhSync;
            document.getElementById('mh_affectee_globale').value = totalAffectee.toFixed(2);
        }

        // Écouter les changements sur les champs de masse horaire
        const mhInputs = document.querySelectorAll('input[type="number"]');
        mhInputs.forEach(input => {
            input.addEventListener('input', calculateTotals);
        });

        // Calcul initial
        calculateTotals();
    });
</script>
@endsection