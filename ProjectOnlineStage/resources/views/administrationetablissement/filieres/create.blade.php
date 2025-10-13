@extends('layouts.app')

@section('title', 'Créer une Filière')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- En-tête avec fil d'Ariane -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.filieres.index') }}">Filières</a>
            </li>
            <li class="breadcrumb-item active">Nouvelle Filière</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Créer une Nouvelle Filière</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.filieres.store') }}" method="POST">
                        @csrf

                        <!-- Code Filière -->
                        <div class="mb-3">
                            <label for="code_filiere" class="form-label">
                                Code de la Filière <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('code_filiere') is-invalid @enderror" 
                                   id="code_filiere" 
                                   name="code_filiere" 
                                   value="{{ old('code_filiere') }}" 
                                   required
                                   placeholder="Ex: INFO-2024">
                            @error('code_filiere')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Code unique pour identifier la filière</div>
                        </div>

                        <!-- Nom Filière -->
                        <div class="mb-3">
                            <label for="nom_filiere" class="form-label">
                                Nom de la Filière <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nom_filiere') is-invalid @enderror" 
                                   id="nom_filiere" 
                                   name="nom_filiere" 
                                   value="{{ old('nom_filiere') }}" 
                                   required
                                   placeholder="Ex: Développement Informatique">
                            @error('nom_filiere')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Secteur -->
                        <div class="mb-4">
                            <label for="secteur_id" class="form-label">
                                Secteur <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('secteur_id') is-invalid @enderror" 
                                    id="secteur_id" 
                                    name="secteur_id" 
                                    required>
                                <option value="">-- Sélectionner un secteur --</option>
                                @foreach($secteurs as $secteur)
                                    <option value="{{ $secteur->id }}" 
                                            {{ old('secteur_id') == $secteur->id ? 'selected' : '' }}>
                                        {{ $secteur->nom_secteur }}
                                    </option>
                                @endforeach
                            </select>
                            @error('secteur_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($secteurs->count() == 0)
                                <div class="alert alert-warning mt-2">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    Aucun secteur disponible. 
                                    <a href="{{ route('administration.etablissement.secteurs.create') }}">
                                        Créer un secteur d'abord
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Informations -->
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Information :</strong> Les champs marqués d'une étoile 
                            <span class="text-danger">*</span> sont obligatoires.
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('administration.etablissement.filieres.index') }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer la Filière
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection