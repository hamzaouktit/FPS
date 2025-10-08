@extends('layouts.app')

@section('title', 'Créer un Secteur')

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête -->
    <div class="mb-4">
        <h1 class="h3 mb-0">Créer un Nouveau Secteur</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.secteurs.index') }}">Secteurs</a></li>
                <li class="breadcrumb-item active">Créer</li>
            </ol>
        </nav>
    </div>

    <!-- Messages d'erreur -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulaire -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-industry me-2"></i>Informations du Secteur</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.secteurs.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="nom_secteur" class="form-label fw-bold">
                                Nom du Secteur <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nom_secteur') is-invalid @enderror" 
                                   id="nom_secteur" 
                                   name="nom_secteur" 
                                   value="{{ old('nom_secteur') }}"
                                   placeholder="Ex: Industrie, Commerce, Informatique..."
                                   required>
                            @error('nom_secteur')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Entrez le nom du secteur de formation (maximum 255 caractères)
                            </small>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Information :</strong> Le secteur sera disponible pour tous les établissements. 
                            Vous pourrez ensuite créer des filières dans ce secteur.
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('administration.etablissement.secteurs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Créer le Secteur
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection