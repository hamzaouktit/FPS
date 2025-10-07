@extends('layouts.app')

@section('title', 'Créer un Établissement')

@section('content')
<div class="container-fluid px-4 py-3">
    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Créer un Établissement</h1>
            <p class="text-muted mb-0">Complexe: {{ $complexe->nom }}</p>
        </div>
        <a href="{{ route('administration.complexe.etablissements.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Retour à la liste
        </a>
    </div>

    {{-- Formulaire --}}
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="bi bi-building"></i> Informations de l'Établissement
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.complexe.etablissements.store') }}" method="POST">
                        @csrf

                        {{-- Code EFP --}}
                        <div class="mb-3">
                            <label for="code_efp" class="form-label">
                                Code EFP <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('code_efp') is-invalid @enderror" 
                                   id="code_efp" 
                                   name="code_efp" 
                                   value="{{ old('code_efp') }}"
                                   placeholder="Ex: EFP001"
                                   required>
                            @error('code_efp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Code unique identifiant l'établissement</div>
                        </div>

                        {{-- Nom EFP --}}
                        <div class="mb-3">
                            <label for="nom_efp" class="form-label">
                                Nom de l'Établissement <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nom_efp') is-invalid @enderror" 
                                   id="nom_efp" 
                                   name="nom_efp" 
                                   value="{{ old('nom_efp') }}"
                                   placeholder="Ex: Institut de Formation Professionnelle"
                                   required>
                            @error('nom_efp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Directeur --}}
                        <div class="mb-4">
                            <label for="user_id" class="form-label">
                                Directeur de l'Établissement
                            </label>
                            <select class="form-select @error('user_id') is-invalid @enderror" 
                                    id="user_id" 
                                    name="user_id">
                                <option value="">-- Sélectionner un directeur (optionnel) --</option>
                                @foreach($directeurs as $directeur)
                                    <option value="{{ $directeur->id }}" {{ old('user_id') == $directeur->id ? 'selected' : '' }}>
                                        {{ $directeur->name }} ({{ $directeur->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                @if($directeurs->count() > 0)
                                    Seuls les directeurs sans établissement sont affichés
                                @else
                                    <span class="text-warning">
                                        <i class="bi bi-exclamation-triangle"></i> Aucun directeur disponible
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Complexe (lecture seule) --}}
                        <div class="mb-4">
                            <label class="form-label">Complexe</label>
                            <input type="text" 
                                   class="form-control" 
                                   value="{{ $complexe->nom }}" 
                                   readonly
                                   disabled>
                            <div class="form-text">L'établissement sera créé dans ce complexe</div>
                        </div>

                        {{-- Boutons --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('administration.complexe.etablissements.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Créer l'Établissement
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Informations supplémentaires --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-3"><i class="bi bi-info-circle text-info"></i> Informations</h6>
                    <ul class="mb-0 small text-muted">
                        <li>Le code EFP doit être unique dans le système</li>
                        <li>Vous pouvez créer l'établissement sans directeur et l'assigner plus tard</li>
                        <li>Après la création, vous pourrez ajouter des formations à cet établissement</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection