@extends('layouts.app')

@section('title', 'Modifier un Secteur')

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête -->
    <div class="mb-4">
        <h1 class="h3 mb-0">Modifier le Secteur</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.secteurs.index') }}">Secteurs</a></li>
                <li class="breadcrumb-item active">Modifier</li>
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
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Modifier les Informations</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.secteurs.update', $secteur->nom_secteur) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="nom_secteur" class="form-label fw-bold">
                                Nom du Secteur <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nom_secteur') is-invalid @enderror" 
                                   id="nom_secteur" 
                                   name="nom_secteur" 
                                   value="{{ old('nom_secteur', $secteur->nom_secteur) }}"
                                   placeholder="Ex: Industrie, Commerce, Informatique..."
                                   required>
                            @error('nom_secteur')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Modifiez le nom du secteur (maximum 255 caractères)
                            </small>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Attention :</strong> La modification du nom du secteur affectera toutes les filières associées dans tous les établissements.
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('administration.etablissement.secteurs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Enregistrer les Modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection