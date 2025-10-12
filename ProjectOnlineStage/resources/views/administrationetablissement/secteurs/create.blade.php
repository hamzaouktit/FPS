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
                        <i class="fas fa-industry me-2"></i>Ajouter un secteur de formation
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

                        <!-- Nom du Secteur -->
                        <div class="mb-4">
                            <label for="nom_secteur" class="form-label">
                                Nom du Secteur <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-industry"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('nom_secteur') is-invalid @enderror" 
                                       id="nom_secteur" 
                                       name="nom_secteur" 
                                       value="{{ old('nom_secteur') }}"
                                       placeholder="Ex: Bâtiment et Travaux Publics"
                                       required>
                            </div>
                            @error('nom_secteur')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Entrez le nom du secteur de formation
                            </small>
                        </div>

                        <!-- Établissement (seulement pour directeur complexe et admin) -->
                        @if(Auth::user()->role !== 'directeur_etablissement')
                            <div class="mb-4">
                                <label for="code_efp" class="form-label">
                                    Établissement <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <select class="form-select @error('code_efp') is-invalid @enderror" 
                                            id="code_efp" 
                                            name="code_efp" 
                                            required>
                                        <option value="">Sélectionnez un établissement</option>
                                        @foreach($etablissements as $etablissement)
                                            <option value="{{ $etablissement->code_efp }}" 
                                                    {{ old('code_efp') == $etablissement->code_efp ? 'selected' : '' }}>
                                                {{ $etablissement->nom_efp }} ({{ $etablissement->code_efp }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('code_efp')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <!-- Pour directeur établissement, afficher l'établissement en lecture seule -->
                            <div class="mb-4">
                                <label class="form-label">Établissement</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ Auth::user()->etablissement->nom_efp }} ({{ Auth::user()->etablissement->code_efp }})" 
                                           readonly>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Le secteur sera créé pour votre établissement
                                </small>
                            </div>
                        @endif

                        <!-- Informations supplémentaires -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="fas fa-lightbulb me-2"></i>Informations
                            </h6>
                            <ul class="mb-0">
                                <li>Le secteur permet de regrouper plusieurs filières</li>
                                <li>Chaque secteur est associé à un établissement</li>
                                <li>Le nom du secteur doit être unique pour l'établissement</li>
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
                        un domaine d'activité commun.
                    </p>
                    
                    <h6 class="mt-3">Exemples de secteurs :</h6>
                    <ul class="text-muted mb-0">
                        <li>Bâtiment et Travaux Publics</li>
                        <li>Industrie et Technologies</li>
                        <li>Services et Commerce</li>
                        <li>Agriculture et Agroalimentaire</li>
                        <li>Tourisme et Hôtellerie</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection