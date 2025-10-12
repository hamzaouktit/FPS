@extends('layouts.app')

@section('title', 'Modifier une Formation')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.formations.index') }}">
                <i class="fas fa-graduation-cap"></i> Formations
            </a>
        </li>
        <li class="breadcrumb-item active">
            <i class="fas fa-edit"></i> Modifier
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- En-tête -->
            <div class="mb-4">
                <h2 class="mb-2">
                    <i class="fas fa-edit text-warning"></i>
                    Modifier la Formation
                </h2>
                <p class="text-muted mb-0">
                    <i class="fas fa-school me-1"></i>
                    {{ Auth::user()->etablissement->nom_efp }}
                </p>
            </div>

            <!-- Formulaire -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Informations de la Formation
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('administration.etablissement.formations.update', $formation->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Année -->
                            <div class="col-md-6 mb-3">
                                <label for="annee" class="form-label">
                                    Année <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                    <input type="number" 
                                           class="form-control @error('annee') is-invalid @enderror" 
                                           id="annee" 
                                           name="annee" 
                                           value="{{ old('annee', $formation->annee) }}"
                                           min="2000"
                                           max="2100"
                                           required>
                                </div>
                                @error('annee')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Niveau -->
                            <div class="col-md-6 mb-3">
                                <label for="niveau_id" class="form-label">
                                    Niveau <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-layer-group"></i>
                                    </span>
                                    <select class="form-select @error('niveau_id') is-invalid @enderror" 
                                            id="niveau_id" 
                                            name="niveau_id" 
                                            required>
                                        <option value="">-- Sélectionnez un niveau --</option>
                                        @foreach($niveaux as $niveau)
                                            <option value="{{ $niveau->id }}" 
                                                {{ old('niveau_id', $formation->niveau_id) == $niveau->id ? 'selected' : '' }}>
                                                {{ $niveau->niveau }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('niveau_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Filière -->
                            <div class="col-md-12 mb-3">
                                <label for="filiere_id" class="form-label">
                                    Filière <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-book"></i>
                                    </span>
                                    <select class="form-select @error('filiere_id') is-invalid @enderror" 
                                            id="filiere_id" 
                                            name="filiere_id" 
                                            required>
                                        <option value="">-- Sélectionnez une filière --</option>
                                        @foreach($filieres as $filiere)
                                            <option value="{{ $filiere->id }}" 
                                                {{ old('filiere_id', $formation->filiere_id) == $filiere->id ? 'selected' : '' }}>
                                                {{ $filiere->nom_filiere }} 
                                                ({{ $filiere->code_filiere }}) 
                                                - {{ $filiere->secteur->nom_secteur ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('filiere_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Type de Formation -->
                            <div class="col-md-6 mb-3">
                                <label for="type_formation" class="form-label">
                                    Type de Formation
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-tag"></i>
                                    </span>
                                    <select class="form-select @error('type_formation') is-invalid @enderror" 
                                            id="type_formation" 
                                            name="type_formation">
                                        <option value="">-- Sélectionnez un type --</option>
                                        <option value="Initiale" 
                                            {{ old('type_formation', $formation->type_formation) == 'Initiale' ? 'selected' : '' }}>
                                            Initiale
                                        </option>
                                        <option value="Continue" 
                                            {{ old('type_formation', $formation->type_formation) == 'Continue' ? 'selected' : '' }}>
                                            Continue
                                        </option>
                                        <option value="Alternance" 
                                            {{ old('type_formation', $formation->type_formation) == 'Alternance' ? 'selected' : '' }}>
                                            Alternance
                                        </option>
                                        <option value="Apprentissage" 
                                            {{ old('type_formation', $formation->type_formation) == 'Apprentissage' ? 'selected' : '' }}>
                                            Apprentissage
                                        </option>
                                        <option value="Diplômante" 
                                            {{ old('type_formation', $formation->type_formation) == 'Diplômante' ? 'selected' : '' }}>
                                            Diplômante
                                        </option>
                                    </select>
                                </div>
                                @error('type_formation')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Créneau -->
                            <div class="col-md-6 mb-3">
                                <label for="creneau" class="form-label">
                                    Créneau
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-clock"></i>
                                    </span>
                                    <select class="form-select @error('creneau') is-invalid @enderror" 
                                            id="creneau" 
                                            name="creneau">
                                        <option value="">-- Sélectionnez un créneau --</option>
                                        <option value="Matin" 
                                            {{ old('creneau', $formation->creneau) == 'Matin' ? 'selected' : '' }}>
                                            Matin
                                        </option>
                                        <option value="Après-midi" 
                                            {{ old('creneau', $formation->creneau) == 'Après-midi' ? 'selected' : '' }}>
                                            Après-midi
                                        </option>
                                        <option value="Soir" 
                                            {{ old('creneau', $formation->creneau) == 'Soir' ? 'selected' : '' }}>
                                            Soir
                                        </option>
                                        <option value="Journée complète" 
                                            {{ old('creneau', $formation->creneau) == 'Journée complète' ? 'selected' : '' }}>
                                            Journée complète
                                        </option>
                                        <option value="CDJ" 
                                            {{ old('creneau', $formation->creneau) == 'CDJ' ? 'selected' : '' }}>
                                            CDJ
                                        </option>
                                    </select>
                                </div>
                                @error('creneau')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Note d'information -->
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Attention :</strong>
                            La modification de cette formation peut affecter les groupes associés.
                            Les champs marqués d'un <span class="text-danger">*</span> sont obligatoires.
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('administration.etablissement.formations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Retour
                            </a>
                            <button type="submit" class="btn btn-warning text-dark">
                                <i class="fas fa-save me-2"></i>
                                Enregistrer les Modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-label {
    font-weight: 500;
    color: #495057;
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: none;
}

.form-control, .form-select {
    border-left: none;
}

.form-control:focus, .form-select:focus {
    border-color: #ced4da;
    box-shadow: none;
}

.input-group:focus-within .input-group-text {
    border-color: #86b7fe;
}

.input-group:focus-within .form-control,
.input-group:focus-within .form-select {
    border-color: #86b7fe;
}
</style>
@endsection