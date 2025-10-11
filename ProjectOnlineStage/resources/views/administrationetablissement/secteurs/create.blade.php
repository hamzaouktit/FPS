@extends('layouts.app')

@section('title', 'Créer un Secteur')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Créer un Nouveau Secteur
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.secteurs.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="etablissement" class="form-label fw-bold">Établissement</label>
                            <input type="text" 
                                   class="form-control bg-light" 
                                   value="{{ Auth::user()->etablissement->nom_efp }}" 
                                   readonly>
                            <small class="text-muted">Code: {{ Auth::user()->etablissement->code_efp }}</small>
                        </div>

                        <div class="mb-4">
                            <label for="nom_secteur" class="form-label fw-bold">
                                Nom du Secteur <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nom_secteur') is-invalid @enderror" 
                                   id="nom_secteur" 
                                   name="nom_secteur" 
                                   value="{{ old('nom_secteur') }}" 
                                   placeholder="Ex: Informatique, BTP, Commerce, etc."
                                   required>
                            @error('nom_secteur')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Le nom du secteur doit être unique dans votre établissement.</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Information:</strong> Le secteur sera automatiquement associé à votre établissement.
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('administration.etablissement.secteurs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4 border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i>Conseils</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Choisissez un nom clair et descriptif pour le secteur</li>
                        <li>Vérifiez que le secteur n'existe pas déjà avant de le créer</li>
                        <li>Un secteur peut contenir plusieurs filières de formation</li>
                        <li>Vous pourrez ajouter des filières à ce secteur après sa création</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection