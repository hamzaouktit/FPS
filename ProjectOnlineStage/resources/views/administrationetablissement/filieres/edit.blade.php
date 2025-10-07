@extends('layouts.app')

@section('title', 'Modifier la Filière')

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
                <li class="breadcrumb-item">
                    <a href="{{ route('administration.etablissement.filieres.show', $filiere->code_filiere) }}">
                        {{ $filiere->code_filiere }}
                    </a>
                </li>
                <li class="breadcrumb-item active">Modifier</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">
            <i class="fas fa-edit text-warning me-2"></i>Modifier la filière pour {{ $etablissement->nom_efp }}
        </h1>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations de la filière
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.filieres.update', $filiere->code_filiere) }}" 
                          method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label for="code_filiere" class="form-label">
                                    <i class="fas fa-hashtag me-1"></i>Code de la filière <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('code_filiere') is-invalid @enderror" 
                                       id="code_filiere" 
                                       name="code_filiere" 
                                       value="{{ old('code_filiere', $filiere->code_filiere) }}"
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
                                                {{ old('nom_secteur', $filiere->nom_secteur) == $secteur->nom_secteur ? 'selected' : '' }}>
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
                                   value="{{ old('nom_filiere', $filiere->nom_filiere) }}"
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
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                            <a href="{{ route('administration.etablissement.filieres.show', $filiere->code_filiere) }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Avertissement -->
            @if($filiere->formations()->count() > 0)
                <div class="card shadow-sm mt-3 border-warning">
                    <div class="card-body">
                        <h5 class="card-title text-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>Attention
                        </h5>
                        <p class="mb-0">
                            Cette filière contient <strong>{{ $filiere->formations()->where('code_efp', $etablissement->code_efp)->count() }} formation(s)</strong> dans {{ $etablissement->nom_efp }}. 
                            La modification du code mettra automatiquement à jour toutes les références dans les formations associées.
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Informations supplémentaires -->
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
                        <li>En majuscules (converti automatiquement)</li>
                        <li>Doit rester unique dans le système</li>
                        <li>Modifiable mais impacte les formations</li>
                    </ul>

                    <h6 class="text-info">
                        <i class="fas fa-check-circle me-1"></i>Nom de la filière
                    </h6>
                    <ul class="small mb-3">
                        <li>Nom complet et officiel</li>
                        <li>Descriptif et précis</li>
                        <li>Modifiable sans impact majeur</li>
                    </ul>

                    <h6 class="text-info">
                        <i class="fas fa-check-circle me-1"></i>Changement de secteur
                    </h6>
                    <ul class="small mb-0">
                        <li>Possible à tout moment</li>
                        <li>Choisissez un secteur associé à {{ $etablissement->nom_efp }}</li>
                        <li>Aucun impact sur les formations</li>
                    </ul>
                </div>
            </div>

            <!-- Statistiques actuelles -->
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-chart-bar me-1"></i>Statistiques actuelles
                    </h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Formations :</span>
                        <strong class="text-primary">{{ $filiere->formations()->where('code_efp', $etablissement->code_efp)->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Groupes :</span>
                        <strong class="text-success">
                            {{ DB::table('groupes')
                                ->join('formations', 'groupes.id_formation', '=', 'formations.id')
                                ->where('formations.code_filiere', $filiere->code_filiere)
                                ->where('formations.code_efp', $etablissement->code_efp)
                                ->count() }}
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Secteur actuel :</span>
                        <strong class="text-info">{{ $filiere->nom_secteur }}</strong>
                    </div>
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