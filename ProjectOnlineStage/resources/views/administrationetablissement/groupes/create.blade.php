@extends('layouts.app')

@section('title', 'Nouveau Groupe')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.groupes.index') }}">
                <i class="fas fa-users"></i> Groupes
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-plus-circle"></i> Nouveau Groupe
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>
                    Ajouter un Nouveau Groupe pour mon Établissement
                </h4>
            </div>

            <div class="card-body">
                <form action="{{ route('administration.etablissement.groupes.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="code_groupe" class="form-label">
                                <i class="fas fa-hashtag me-1"></i>
                                Code du Groupe <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('code_groupe') is-invalid @enderror" 
                                   id="code_groupe" 
                                   name="code_groupe" 
                                   value="{{ old('code_groupe') }}"
                                   placeholder="Ex: G1, G2, TP1, etc."
                                   required>
                            @error('code_groupe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="sous_groupe" class="form-label">
                                <i class="fas fa-users me-1"></i>
                                Sous-groupe <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('sous_groupe') is-invalid @enderror" 
                                   id="sous_groupe" 
                                   name="sous_groupe" 
                                   value="{{ old('sous_groupe') }}"
                                   placeholder="Ex: SG1, Sous-groupe A, etc."
                                   required>
                            @error('sous_groupe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="effectif_groupe" class="form-label">
                                <i class="fas fa-user-graduate me-1"></i>
                                Effectif <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('effectif_groupe') is-invalid @enderror" 
                                   id="effectif_groupe" 
                                   name="effectif_groupe" 
                                   value="{{ old('effectif_groupe') }}"
                                   min="0"
                                   required>
                            @error('effectif_groupe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="annee_formation" class="form-label">
                                <i class="fas fa-calendar me-1"></i>
                                Année de Formation <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('annee_formation') is-invalid @enderror" 
                                   id="annee_formation" 
                                   name="annee_formation" 
                                   value="{{ old('annee_formation', date('Y')) }}"
                                   required>
                            @error('annee_formation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="statut" class="form-label">
                                <i class="fas fa-toggle-on me-1"></i>
                                Statut <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('statut') is-invalid @enderror" 
                                    id="statut" 
                                    name="statut" 
                                    required>
                                <option value="Actif" {{ old('statut') == 'Actif' ? 'selected' : '' }}>Actif</option>
                                <option value="Inactif" {{ old('statut') == 'Inactif' ? 'selected' : '' }}>Inactif</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="statut_sous_groupe" class="form-label">
                                <i class="fas fa-toggle-on me-1"></i>
                                Statut Sous-groupe <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('statut_sous_groupe') is-invalid @enderror" 
                                    id="statut_sous_groupe" 
                                    name="statut_sous_groupe" 
                                    required>
                                <option value="Actif" {{ old('statut_sous_groupe') == 'Actif' ? 'selected' : '' }}>Actif</option>
                                <option value="Inactif" {{ old('statut_sous_groupe') == 'Inactif' ? 'selected' : '' }}>Inactif</option>
                            </select>
                            @error('statut_sous_groupe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fusion_groupe" class="form-label">
                                <i class="fas fa-object-group me-1"></i>
                                Fusion de Groupe
                            </label>
                            <input type="text" 
                                   class="form-control @error('fusion_groupe') is-invalid @enderror" 
                                   id="fusion_groupe" 
                                   name="fusion_groupe" 
                                   value="{{ old('fusion_groupe') }}"
                                   placeholder="Ex: Fusion avec G2">
                            @error('fusion_groupe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="code_fusion" class="form-label">
                                <i class="fas fa-code me-1"></i>
                                Code Fusion
                            </label>
                            <input type="text" 
                                   class="form-control @error('code_fusion') is-invalid @enderror" 
                                   id="code_fusion" 
                                   name="code_fusion" 
                                   value="{{ old('code_fusion') }}"
                                   placeholder="Ex: FUS-G1-G2">
                            @error('code_fusion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="filiere_id" class="form-label">
                                <i class="fas fa-stream me-1"></i>
                                Filière <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('filiere_id') is-invalid @enderror" 
                                    id="filiere_id" 
                                    name="filiere_id" 
                                    required>
                                <option value="">Sélectionnez une filière</option>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" {{ old('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                        {{ $filiere->nom_filiere }} ({{ $filiere->code_filiere }})
                                    </option>
                                @endforeach
                            </select>
                            @error('filiere_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="formation_id" class="form-label">
                                <i class="fas fa-graduation-cap me-1"></i>
                                Formation <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('formation_id') is-invalid @enderror" 
                                    id="formation_id" 
                                    name="formation_id" 
                                    required>
                                <option value="">Sélectionnez une formation</option>
                                @foreach($formations as $formation)
                                    <option value="{{ $formation->id }}" {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                                        {{ $formation->type }} -{{ $formation->filiere->nom_filiere }} - {{ $formation->mode }} ({{ $formation->creneau }})
                                    </option>
                                @endforeach
                            </select>
                            @error('formation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Information :</strong> Ce groupe sera spécifique à votre établissement.
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('administration.etablissement.groupes.index') }}" class="btn btn-secondary">
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