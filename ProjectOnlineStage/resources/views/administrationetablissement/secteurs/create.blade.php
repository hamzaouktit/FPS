@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 d-inline-block fw-bold">Ajouter un Nouveau Secteur</h1>
            <p class="text-muted">{{ $etablissement->nom_efp }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('administration.etablissement.secteurs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.secteurs.store') }}" method="POST" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="nom_secteur" class="form-label fw-bold">Nom du Secteur</label>
                            <input type="text" 
                                   class="form-control @error('nom_secteur') is-invalid @enderror" 
                                   id="nom_secteur" 
                                   name="nom_secteur" 
                                   value="{{ old('nom_secteur') }}"
                                   placeholder="Ex: Informatique, Mécanique, etc."
                                   required>
                            @error('nom_secteur')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Créer le Secteur
                                </button>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('administration.etablissement.secteurs.index') }}" class="btn btn-light w-100">
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
                        <li>Utilisez un nom clair et descriptif pour le secteur</li>
                        <li>Évitez les doublons de secteurs</li>
                        <li>Le nom doit être unique dans votre établissement</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection