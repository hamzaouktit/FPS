@extends('layouts.app')

@section('title', 'Nouveau Niveau')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.niveaux.index') }}">
                <i class="fas fa-layer-group"></i> Niveaux
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-plus-circle"></i> Nouveau Niveau
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>
                    Ajouter un Nouveau Niveau
                </h4>
            </div>

            <div class="card-body">
                <form action="{{ route('administration.etablissement.niveaux.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="niveau" class="form-label">
                            <i class="fas fa-layer-group me-1"></i>
                            Niveau <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('niveau') is-invalid @enderror" 
                               id="niveau" 
                               name="niveau" 
                               value="{{ old('niveau') }}"
                               placeholder="Ex: Technicien, Technicien Spécialisé, Qualification, etc."
                               required>
                        @error('niveau')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Exemples : Technicien, Technicien Spécialisé, Qualification, Spécialisation
                        </small>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Information :</strong> Ce niveau sera automatiquement associé à votre établissement.
                    </div>

                    <!-- Exemples de niveaux communs -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                Niveaux couramment utilisés :
                            </h6>
                            <div class="d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="setNiveau('Technicien')">
                                    Technicien
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="setNiveau('Technicien Spécialisé')">
                                    Technicien Spécialisé
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="setNiveau('Qualification')">
                                    Qualification
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="setNiveau('Spécialisation')">
                                    Spécialisation
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="setNiveau('Lauréat')">
                                    Lauréat
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('administration.etablissement.niveaux.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Retour
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setNiveau(niveauText) {
        document.getElementById('niveau').value = niveauText;
        document.getElementById('niveau').focus();
    }
</script>
@endpush