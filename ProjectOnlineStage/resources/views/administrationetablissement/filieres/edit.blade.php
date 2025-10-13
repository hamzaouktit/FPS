@extends('layouts.app')

@section('title', 'Modifier la Filière')

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
            <li class="breadcrumb-item active">Modifier {{ $filiere->nom_filiere }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Modifier la Filière</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.filieres.update', $filiere->code_filiere) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Code Filière -->
                        <div class="mb-3">
                            <label for="code_filiere" class="form-label">
                                Code de la Filière <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('code_filiere') is-invalid @enderror" 
                                   id="code_filiere" 
                                   name="code_filiere" 
                                   value="{{ old('code_filiere', $filiere->code_filiere) }}" 
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
                                   value="{{ old('nom_filiere', $filiere->nom_filiere) }}" 
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
                                            {{ old('secteur_id', $filiere->secteur_id) == $secteur->id ? 'selected' : '' }}>
                                        {{ $secteur->nom_secteur }}
                                    </option>
                                @endforeach
                            </select>
                            @error('secteur_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Informations sur les relations -->
                        @if($filiere->formations->count() > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Attention :</strong> Cette filière est associée à 
                                {{ $filiere->formations->count() }} formation(s). 
                                La modification peut avoir un impact sur ces formations.
                            </div>
                        @endif

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
                            <div>
                                <a href="{{ route('administration.etablissement.filieres.show', $filiere->code_filiere) }}" 
                                   class="btn btn-info me-2">
                                    <i class="fas fa-eye"></i> Voir détails
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection