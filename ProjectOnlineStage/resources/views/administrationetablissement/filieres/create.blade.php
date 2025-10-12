@extends('layouts.app')

@section('title', 'Nouvelle Filière')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.filieres.index') }}">
                <i class="fas fa-graduation-cap"></i> Filières
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-plus-circle"></i> Nouvelle Filière
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>
                    Ajouter une Nouvelle Filière
                </h4>
            </div>

            <div class="card-body">
                <form action="{{ route('administration.etablissement.filieres.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="code_filiere" class="form-label">
                            <i class="fas fa-hashtag me-1"></i>
                            Code de la Filière <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('code_filiere') is-invalid @enderror" 
                               id="code_filiere" 
                               name="code_filiere" 
                               value="{{ old('code_filiere') }}"
                               placeholder="Ex: TSDI, TMSIR, TS-GE"
                               required>
                        @error('code_filiere')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Code unique pour identifier la filière dans votre établissement
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="nom_filiere" class="form-label">
                            <i class="fas fa-graduation-cap me-1"></i>
                            Nom de la Filière <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nom_filiere') is-invalid @enderror" 
                               id="nom_filiere" 
                               name="nom_filiere" 
                               value="{{ old('nom_filiere') }}"
                               placeholder="Ex: Développement Informatique"
                               required>
                        @error('nom_filiere')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="secteur_id" class="form-label">
                            <i class="fas fa-layer-group me-1"></i>
                            Secteur <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('secteur_id') is-invalid @enderror" 
                                id="secteur_id" 
                                name="secteur_id"
                                required>
                            <option value="">-- Sélectionner un secteur --</option>
                            @foreach($secteurs as $secteur)
                                <option value="{{ $secteur->id }}" {{ old('secteur_id') == $secteur->id ? 'selected' : '' }}>
                                    {{ $secteur->nom_secteur }}
                                </option>
                            @endforeach
                        </select>
                        @error('secteur_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Le secteur auquel appartient cette filière
                        </small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Information :</strong> Cette filière sera automatiquement associée à votre établissement.
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('administration.etablissement.filieres.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection