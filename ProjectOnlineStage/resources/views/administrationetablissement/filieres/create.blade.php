@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 d-inline-block fw-bold">Ajouter une Nouvelle Filière</h1>
            <p class="text-muted">{{ auth()->user()->etablissement->nom_efp }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('administration.etablissement.filieres.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.filieres.store') }}" method="POST" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="code_filiere" class="form-label fw-bold">Code Filière</label>
                            <input type="text" 
                                   class="form-control @error('code_filiere') is-invalid @enderror" 
                                   id="code_filiere" 
                                   name="code_filiere" 
                                   value="{{ old('code_filiere') }}"
                                   placeholder="Ex: INFO001, MECA002, etc."
                                   required>
                            @error('code_filiere')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nom_filiere" class="form-label fw-bold">Nom Filière</label>
                            <input type="text" 
                                   class="form-control @error('nom_filiere') is-invalid @enderror" 
                                   id="nom_filiere" 
                                   name="nom_filiere" 
                                   value="{{ old('nom_filiere') }}"
                                   placeholder="Ex: Informatique, Mécanique, etc."
                                   required>
                            @error('nom_filiere')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="secteur_id" class="form-label fw-bold">Secteur</label>
                            <select class="form-select @error('secteur_id') is-invalid @enderror" 
                                    id="secteur_id" 
                                    name="secteur_id" 
                                    required>
                                <option value="">-- Sélectionner un secteur --</option>
                                @foreach($secteurs as $secteur)
                                    <option value="{{ $secteur->id }}" 
                                            @if(old('secteur_id') == $secteur->id) selected @endif>
                                        {{ $secteur->nom_secteur }}
                                    </option>
                                @endforeach
                            </select>
                            @error('secteur_id')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Créer la Filière
                                </button>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('administration.etablissement.filieres.index') }}" class="btn btn-light w-100">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="card-title fw-bold mb-3">
                        <i class="fas fa-lightbulb text-warning"></i> Conseils
                    </h6>
                    <ul class="small">
                        <li>Utilisez un code unique et significatif</li>
                        <li>Le nom doit être clair et descriptif</li>
                        <li>Associez la filière à un secteur existant</li>
                        <li>Vous pourrez ajouter des modules après la création</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection