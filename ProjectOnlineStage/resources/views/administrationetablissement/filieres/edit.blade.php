@extends('layouts.app')

@section('title', 'Modifier la filière')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Modifier la filière : {{ $filiere->nom }}</h4>
                    <p class="text-muted mb-0 small mt-1">
                        <i class="bi bi-building"></i> {{ Auth::user()->etablissement->nom_efp }}
                    </p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('administration.etablissement.filieres.update', $filiere->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Code -->
                        <div class="mb-3">
                            <label for="code" class="form-label">
                                Code de la filière <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('code') is-invalid @enderror" 
                                   id="code" 
                                   name="code" 
                                   value="{{ old('code', $filiere->code) }}"
                                   placeholder="Ex: TSDI, TDI, etc."
                                   required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Le code doit être unique et court
                            </small>
                        </div>

                        <!-- Nom -->
                        <div class="mb-3">
                            <label for="nom" class="form-label">
                                Nom de la filière <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" 
                                   name="nom" 
                                   value="{{ old('nom', $filiere->nom) }}"
                                   placeholder="Ex: Technicien Spécialisé en Développement Informatique"
                                   required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Secteur -->
                        <div class="mb-3">
                            <label for="secteur_id" class="form-label">
                                Secteur <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('secteur_id') is-invalid @enderror" 
                                    id="secteur_id" 
                                    name="secteur_id" 
                                    required>
                                <option value="">-- Sélectionnez un secteur --</option>
                                @forelse($secteurs as $secteur)
                                    <option value="{{ $secteur->id }}" 
                                            {{ old('secteur_id', $filiere->secteur_id) == $secteur->id ? 'selected' : '' }}>
                                        {{ $secteur->nom }} ({{ $secteur->code }})
                                    </option>
                                @empty
                                    <option value="" disabled>Aucun secteur disponible</option>
                                @endforelse
                            </select>
                            @error('secteur_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($secteurs->count() === 0)
                                <small class="text-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Aucun secteur disponible
                                </small>
                            @else
                                <small class="form-text text-muted">
                                    Sélectionnez le secteur correspondant
                                </small>
                            @endif
                        </div>

                        <!-- Niveau -->
                        <div class="mb-3">
                            <label for="niveau_id" class="form-label">
                                Niveau <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('niveau_id') is-invalid @enderror" 
                                    id="niveau_id" 
                                    name="niveau_id" 
                                    required>
                                <option value="">-- Sélectionnez un niveau --</option>
                                @forelse($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}" 
                                            {{ old('niveau_id', $filiere->niveau_id) == $niveau->id ? 'selected' : '' }}>
                                        {{ $niveau->nom }} ({{ $niveau->code }})
                                    </option>
                                @empty
                                    <option value="" disabled>Aucun niveau disponible</option>
                                @endforelse
                            </select>
                            @error('niveau_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($niveaux->count() === 0)
                                <small class="text-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Aucun niveau disponible
                                </small>
                            @else
                                <small class="form-text text-muted">
                                    Sélectionnez le niveau de qualification
                                </small>
                            @endif
                        </div>

                        <hr class="my-4">

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('administration.etablissement.filieres.index') }}" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Annuler
                            </a>
                            
                            <div>
                                <a href="{{ route('administration.etablissement.filieres.show', $filiere->id) }}" 
                                   class="btn btn-outline-info me-2">
                                    <i class="bi bi-eye"></i> Voir les détails
                                </a>
                                <button type="submit" 
                                        class="btn btn-primary"
                                        {{ ($secteurs->count() === 0 || $niveaux->count() === 0) ? 'disabled' : '' }}>
                                    <i class="bi bi-check-circle"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informations supplémentaires -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-info-circle text-info"></i> Informations
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Nombre de groupes :</strong> {{ $filiere->groupes->count() }}</p>
                            <p class="mb-1"><strong>Nombre de modules :</strong> {{ $filiere->modules->count() }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Créée le :</strong> {{ $filiere->created_at->format('d/m/Y H:i') }}</p>
                            <p class="mb-1"><strong>Modifiée le :</strong> {{ $filiere->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Avertissement -->
            @if($filiere->groupes->count() > 0)
            <div class="alert alert-warning mt-3">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Attention :</strong> Cette filière contient {{ $filiere->groupes->count() }} groupe(s). 
                Toute modification peut impacter les données existantes.
            </div>
            @endif

            @if($secteurs->count() === 0 || $niveaux->count() === 0)
            <div class="alert alert-danger mt-3">
                <i class="bi bi-x-circle"></i>
                <strong>Erreur :</strong> 
                @if($secteurs->count() === 0 && $niveaux->count() === 0)
                    Aucun secteur ni niveau n'est disponible. Modification impossible.
                @elseif($secteurs->count() === 0)
                    Aucun secteur n'est disponible. Modification impossible.
                @else
                    Aucun niveau n'est disponible. Modification impossible.
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-label {
        font-weight: 500;
        color: #495057;
    }
    .text-danger {
        font-weight: bold;
    }
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
    }
    .alert {
        border-left: 4px solid;
    }
</style>
@endpush
@endsection