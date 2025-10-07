@extends('layouts.app')

@section('title', 'Créer une Filière')

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('administration.etablissement.dashboard') }}">
                        <i class="fas fa-home me-1"></i>Tableau de bord
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('administration.etablissement.filieres.index') }}">Filières</a>
                </li>
                <li class="breadcrumb-item active">Créer</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">
            <i class="fas fa-plus-circle text-primary me-2"></i>Créer une nouvelle filière
        </h1>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations de la filière
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.filieres.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="code_filiere" class="form-label">
                                    <i class="fas fa-hashtag me-1"></i>Code de la filière <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('code_filiere') is-invalid @enderror" 
                                       id="code_filiere" 
                                       name="code_filiere" 
                                       value="{{ old('code_filiere') }}"
                                       placeholder="Ex: INFO, ELECT, GEST..."
                                       required
                                       autofocus
                                       style="text-transform: uppercase;">
                                @error('code_filiere')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Code unique en majuscules
                                </small>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="nom_secteur" class="form-label">
                                    <i class="fas fa-sitemap me-1"></i>Secteur <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('nom_secteur') is-invalid @enderror" 
                                        id="nom_secteur" 
                                        name="nom_secteur" 
                                        required>
                                    <option value="">Sélectionnez un secteur</option>
                                    @foreach($secteurs as $secteur)
                                        <option value="{{ $secteur->nom_secteur }}" 
                                                {{ old('nom_secteur') == $secteur->nom_secteur ? 'selected' : '' }}>
                                            {{ $secteur->nom_secteur }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('nom_secteur')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="nom_filiere" class="form-label">
                                <i class="fas fa-tag me-1"></i>Nom de la filière <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nom_filiere') is-invalid @enderror" 
                                   id="nom_filiere" 
                                   name="nom_filiere" 
                                   value="{{ old('nom_filiere') }}"
                                   placeholder="Ex: Technicien Spécialisé en Informatique..."
                                   required>
                            @error('nom_filiere')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>Nom complet et descriptif de la filière
                            </small>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Créer la filière
                            </button>
                            <a href="{{ route('administration.etablissement.filieres.index') }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Aide -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>Aide & Conseils
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="text-info">
                        <i class="fas fa-check-circle me-1"></i>Code de la filière
                    </h6>
                    <ul class="small mb-3">
                        <li>Utilisez un code court et explicite</li>
                        <li>En majuscules (sera converti automatiquement)</li>
                        <li>Doit être unique dans le système</li>
                    </ul>

                    <h6 class="text-info">
                        <i class="fas fa-check-circle me-1"></i>Nom de la filière
                    </h6>
                    <ul class="small mb-3">
                        <li>Nom complet et officiel</li>
                        <li>Descriptif et précis</li>
                        <li>Inclure le niveau si nécessaire</li>
                    </ul>

                    <h6 class="text-info">
                        <i class="fas fa-check-circle me-1"></i>Secteur
                    </h6>
                    <ul class="small mb-0">
                        <li>Choisissez le secteur approprié</li>
                        <li>Une filière appartient à un seul secteur</li>
                        <li>Le secteur doit exister au préalable</li>
                    </ul>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-chart-bar me-1"></i>Statistiques
                    </h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total secteurs :</span>
                        <strong class="text-primary">{{ $secteurs->count() }}</strong>
                    </div>
                    @if($secteurs->count() == 0)
                        <div class="alert alert-warning mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Aucun secteur disponible. 
                            <a href="{{ route('administration.etablissement.secteurs.create') }}" class="alert-link">
                                Créer un secteur
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Convertir le code en majuscules automatiquement
document.getElementById('code_filiere').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});
</script>
@endpush
@endsection