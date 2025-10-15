@extends('layouts.app')

@section('title', 'Créer un Secteur')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Créer un Nouveau Secteur</h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-industry me-2"></i>Ajouter un secteur pour {{ $etablissement->nom_efp }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('administration.etablissement.secteurs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages d'erreur -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreurs de validation</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulaire -->
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Informations du Secteur
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.secteurs.store') }}" method="POST">
                        @csrf

                        <!-- Code du Secteur -->
                        <div class="mb-4">
                            <label for="code" class="form-label">
                                Code du Secteur <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code') }}"
                                       placeholder="Ex: BTP"
                                       maxlength="50"
                                       style="text-transform: uppercase;"
                                       required>
                            </div>
                            @error('code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Code unique du secteur pour votre établissement (3-10 caractères recommandés)
                            </small>
                        </div>

                        <!-- Nom du Secteur -->
                        <div class="mb-4">
                            <label for="nom" class="form-label">
                                Nom du Secteur <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-industry"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('nom') is-invalid @enderror" 
                                       id="nom" 
                                       name="nom" 
                                       value="{{ old('nom') }}"
                                       placeholder="Ex: Bâtiment et Travaux Publics"
                                       required>
                            </div>
                            @error('nom')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Nom complet du secteur de formation
                            </small>
                        </div>

                        <!-- Établissement (lecture seule) -->
                        <div class="mb-4">
                            <label class="form-label">Établissement</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-building"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $etablissement->nom_efp }} ({{ $etablissement->code_efp }})" 
                                       readonly>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Le secteur sera créé pour cet établissement
                            </small>
                        </div>

                        <!-- Informations supplémentaires -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-lightbulb me-2"></i>Informations
                            </h6>
                            <ul class="mb-0">
                                <li>Le secteur permet de regrouper plusieurs filières de votre établissement</li>
                                <li>Le code du secteur doit être unique dans votre établissement</li>
                                <li>Chaque établissement gère ses propres secteurs de formation</li>
                                <li>Exemples de codes: BTP, IND, COM, AGR, TOU, INF</li>
                            </ul>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('administration.etablissement.secteurs.index') }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Créer le Secteur
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Aide -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>Aide
                    </h6>
                </div>
                <div class="card-body">
                    <h6>Qu'est-ce qu'un secteur ?</h6>
                    <p class="text-muted mb-2">
                        Un secteur est une catégorie qui regroupe plusieurs filières de formation ayant 
                        un domaine d'activité commun dans votre établissement.
                    </p>
                    
                    <h6 class="mt-3">Exemples de secteurs :</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Nom du Secteur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>BTP</code></td>
                                    <td>Bâtiment et Travaux Publics</td>
                                </tr>
                                <tr>
                                    <td><code>IND</code></td>
                                    <td>Industrie et Technologies</td>
                                </tr>
                                <tr>
                                    <td><code>INF</code></td>
                                    <td>Informatique et Nouvelles Technologies</td>
                                </tr>
                                <tr>
                                    <td><code>COM</code></td>
                                    <td>Services et Commerce</td>
                                </tr>
                                <tr>
                                    <td><code>AGR</code></td>
                                    <td>Agriculture et Agroalimentaire</td>
                                </tr>
                                <tr>
                                    <td><code>TOU</code></td>
                                    <td>Tourisme et Hôtellerie</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('code').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});
</script>
@endpush
@endsection