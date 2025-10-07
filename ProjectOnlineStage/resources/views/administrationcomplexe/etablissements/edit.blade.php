@extends('layouts.app')

@section('title', 'Modifier l\'Établissement')

@section('content')
<div class="container-fluid px-4 py-3">
    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Modifier l'Établissement</h1>
            <p class="text-muted mb-0">
                Code EFP: <span class="badge bg-secondary">{{ $etablissement->code_efp }}</span> | 
                Complexe: {{ $complexe->nom }}
            </p>
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
                        <i class="bi bi-pencil"></i> Modifier les Informations
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.complexe.etablissements.update', $etablissement->code_efp) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Code EFP (lecture seule) --}}
                        <div class="mb-3">
                            <label for="code_efp" class="form-label">Code EFP</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="code_efp" 
                                   value="{{ $etablissement->code_efp }}" 
                                   readonly
                                   disabled>
                            <div class="form-text">Le code EFP ne peut pas être modifié</div>
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
                                   value="{{ old('nom_efp', $etablissement->nom_efp) }}"
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
                                <option value="">-- Aucun directeur --</option>
                                @foreach($directeurs as $directeur)
                                    <option value="{{ $directeur->id }}" 
                                            {{ old('user_id', $etablissement->user_id) == $directeur->id ? 'selected' : '' }}>
                                        {{ $directeur->name }} ({{ $directeur->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                @if($directeurs->count() > 0)
                                    Seuls les directeurs disponibles sont affichés
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
                            <div class="form-text">Le complexe ne peut pas être modifié</div>
                        </div>

                        {{-- Statistiques --}}
                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Statistiques</h6>
                            <div class="row g-2 small">
                                <div class="col-md-4">
                                    <strong>Formations:</strong> {{ $etablissement->formations_count ?? 0 }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Directeur actuel:</strong> 
                                    {{ $etablissement->user ? $etablissement->user->name : 'Non assigné' }}
                                </div>
                            </div>
                        </div>

                        {{-- Boutons --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('administration.complexe.etablissements.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-lg"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg"></i> Enregistrer les Modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Actions supplémentaires --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="mb-3"><i class="bi bi-gear text-secondary"></i> Actions Supplémentaires</h6>
                    <div class="d-flex gap-2">
                        <a href="{{ route('administration.complexe.etablissements.show', $etablissement->code_efp) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye"></i> Voir les Détails
                        </a>
                        @if($etablissement->formations_count == 0)
                            <form action="{{ route('administration.complexe.etablissements.destroy', $etablissement->code_efp) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cet établissement ?');"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    <i class="bi bi-trash"></i> Supprimer l'Établissement
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-outline-secondary btn-sm" disabled title="Impossible de supprimer un établissement avec des formations">
                                <i class="bi bi-trash"></i> Supprimer ({{ $etablissement->formations_count }} formation(s))
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection