@extends('layouts.app')

@section('title', 'Créer un Secteur')

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('administration.etablissement.dashboard') }}">Tableau de bord</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('administration.etablissement.secteurs.index') }}">Secteurs</a>
                </li>
                <li class="breadcrumb-item active">Créer</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">Créer un nouveau secteur pour {{ $etablissement->nom_efp }}</h1>
    </div>

    <!-- Messages d'erreur -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulaire -->
    <div class="row">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.secteurs.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="nom_secteur" class="form-label">
                                Nom du secteur <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nom_secteur') is-invalid @enderror" 
                                   id="nom_secteur" 
                                   name="nom_secteur" 
                                   value="{{ old('nom_secteur') }}"
                                   placeholder="Ex: Informatique, Électronique, Gestion..."
                                   required
                                   autofocus>
                            @error('nom_secteur')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Entrez le nom du secteur de formation pour {{ $etablissement->nom_efp }}
                            </small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Créer le secteur
                            </button>
                            <a href="{{ route('administration.etablissement.secteurs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Aide -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle text-info me-2"></i>Informations
                    </h5>
                    <ul class="mb-0">
                        <li>Un secteur regroupe plusieurs filières de formation</li>
                        <li>Le nom du secteur doit être unique</li>
                        <li>Vous pourrez associer des filières à ce secteur après sa création</li>
                        <li>Ce secteur sera disponible pour association avec des filières de {{ $etablissement->nom_efp }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection