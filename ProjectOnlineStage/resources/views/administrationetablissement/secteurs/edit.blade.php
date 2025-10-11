@extends('layouts.app')

@section('title', 'Modifier le Secteur')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Modifier le Secteur
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.secteurs.update', $secteur->nom_secteur) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="etablissement" class="form-label fw-bold">Établissement</label>
                            <input type="text" 
                                   class="form-control bg-light" 
                                   value="{{ $secteur->etablissement->nom_efp }}" 
                                   readonly>
                            <small class="text-muted">Code: {{ $secteur->etablissement->code_efp }}</small>
                        </div>

                        <div class="mb-4">
                            <label for="nom_secteur_ancien" class="form-label fw-bold">Nom Actuel</label>
                            <input type="text" 
                                   class="form-control bg-light" 
                                   value="{{ $secteur->nom_secteur }}" 
                                   readonly>
                        </div>

                        <div class="mb-4">
                            <label for="nom_secteur" class="form-label fw-bold">
                                Nouveau Nom du Secteur <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nom_secteur') is-invalid @enderror" 
                                   id="nom_secteur" 
                                   name="nom_secteur" 
                                   value="{{ old('nom_secteur', $secteur->nom_secteur) }}" 
                                   placeholder="Ex: Informatique, BTP, Commerce, etc."
                                   required>
                            @error('nom_secteur')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Le nom du secteur doit être unique dans votre établissement.</small>
                        </div>

                        @if($secteur->filieres->count() > 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Attention:</strong> Ce secteur contient <strong>{{ $secteur->filieres->count() }}</strong> filière(s). 
                                La modification du nom mettra à jour toutes les filières associées.
                            </div>
                        @endif

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Information:</strong> La modification du nom du secteur sera répercutée sur toutes les filières associées.
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('administration.etablissement.secteurs.show', $secteur->nom_secteur) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Mettre à Jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations Supplémentaires</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Nombre de filières:</strong> 
                                <span class="badge bg-info">{{ $secteur->filieres->count() }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><strong>Créé le:</strong> 
                                {{ $secteur->created_at ? $secteur->created_at->format('d/m/Y à H:i') : 'N/A' }}
                            </p>
                        </div>
                    </div>
                    @if($secteur->updated_at && $secteur->created_at && $secteur->updated_at != $secteur->created_at)
                        <p class="mb-0 text-muted">
                            <small>Dernière modification: {{ $secteur->updated_at->format('d/m/Y à H:i') }}</small>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection