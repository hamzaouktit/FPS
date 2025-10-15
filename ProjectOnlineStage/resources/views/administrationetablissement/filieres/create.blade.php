@extends('layouts.app')

@section('title', 'Créer une filière')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Créer une nouvelle filière</h4>
                    <p class="text-muted mb-0 small mt-1">
                        <i class="bi bi-building"></i> {{ Auth::user()->etablissement->nom_efp }}
                    </p>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('administration.etablissement.filieres.store') }}">
                        @csrf

                        <!-- Code -->
                        <div class="mb-3">
                            <label for="code" class="form-label">
                                Code de la filière <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('code') is-invalid @enderror" 
                                   id="code" 
                                   name="code" 
                                   value="{{ old('code') }}"
                                   placeholder="Ex: TSDI, TDI, etc."
                                   required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Le code doit être unique et court (ex: TSDI pour Technicien Spécialisé en Développement Informatique)
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
                                   value="{{ old('nom') }}"
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
                                            {{ old('secteur_id') == $secteur->id ? 'selected' : '' }}>
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
                                    <i class="bi bi-exclamation-triangle"></i> Aucun secteur disponible pour votre établissement
                                </small>
                            @else
                                <small class="form-text text-muted">
                                    Sélectionnez le secteur correspondant à cette filière
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
                                            {{ old('niveau_id') == $niveau->id ? 'selected' : '' }}>
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
                                    <i class="bi bi-exclamation-triangle"></i> Aucun niveau disponible pour votre établissement
                                </small>
                            @else
                                <small class="form-text text-muted">
                                    Sélectionnez le niveau de qualification (Ex: TS, T, S, Q, BP, FQ)
                                </small>
                            @endif
                        </div>

                        <hr class="my-4">

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('administration.etablissement.filieres.index') }}" 
                               class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Annuler
                            </a>
                            <button type="submit" 
                                    class="btn btn-primary"
                                    {{ ($secteurs->count() === 0 || $niveaux->count() === 0) ? 'disabled' : '' }}>
                                <i class="bi bi-check-circle"></i> Créer la filière
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Aide -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="bi bi-info-circle text-info"></i> Aide
                    </h6>
                    <ul class="mb-0 small">
                        <li>Une filière représente une formation spécifique (ex: Développement Informatique, Électricité, etc.)</li>
                        <li>Le <strong>code</strong> doit être unique et court pour faciliter l'identification</li>
                        <li>Le <strong>secteur</strong> définit le domaine d'activité (Informatique, Industriel, etc.)</li>
                        <li>Le <strong>niveau</strong> indique la qualification (TS, T, S, Q, etc.)</li>
                        <li>Tous les champs sont obligatoires</li>
                    </ul>
                </div>
            </div>

            @if($secteurs->count() === 0 || $niveaux->count() === 0)
            <div class="alert alert-warning mt-3">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Attention :</strong> 
                @if($secteurs->count() === 0 && $niveaux->count() === 0)
                    Aucun secteur ni niveau n'est disponible. Veuillez d'abord créer des secteurs et des niveaux.
                @elseif($secteurs->count() === 0)
                    Aucun secteur n'est disponible. Veuillez d'abord créer des secteurs.
                @else
                    Aucun niveau n'est disponible. Veuillez d'abord créer des niveaux.
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
</style>
@endpush
@endsection