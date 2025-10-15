@extends('layouts.app')

@section('title', 'Modifier le Niveau')

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
            <i class="fas fa-edit"></i> Modifier
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Modifier le Niveau : {{ $niveauData->nom }}
                </h4>
            </div>

            <div class="card-body">
                <form action="{{ route('administration.etablissement.niveaux.update', $niveauData->code) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="niveau" class="form-label">
                            <i class="fas fa-layer-group me-1"></i>
                            Niveau <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('niveau') is-invalid @enderror" 
                               id="niveau" 
                               name="niveau" 
                               value="{{ old('niveau', $niveauData->nom) }}"
                               required>
                        @error('niveau')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Code actuel : <strong>{{ $niveauData->code }}</strong> (sera mis à jour automatiquement)
                        </small>
                    </div>

                    @if($niveauData->formations_count > 0 || $niveauData->groupes_count > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Ce niveau contient des données dans votre établissement. 
                        Les modifications peuvent impacter :
                        <ul class="mb-0 mt-2">
                            <li>{{ $niveauData->formations_count }} filière(s)</li>
                            <li>{{ $niveauData->groupes_count }} groupe(s)</li>
                        </ul>
                    </div>
                    @endif

                    <!-- Statistiques du niveau dans votre établissement -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-chart-bar text-primary me-2"></i>
                                Statistiques dans votre établissement :
                            </h6>
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="p-2">
                                        <i class="fas fa-graduation-cap fa-2x text-primary mb-2"></i>
                                        <h5>{{ $niveauData->formations_count }}</h5>
                                        <small class="text-muted">Filières</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2">
                                        <i class="fas fa-users fa-2x text-success mb-2"></i>
                                        <h5>{{ $niveauData->groupes_count }}</h5>
                                        <small class="text-muted">Groupes</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2">
                                        <i class="fas fa-user-graduate fa-2x text-info mb-2"></i>
                                        <h5>{{ $niveauData->effectif_total ?? 0 }}</h5>
                                        <small class="text-muted">Stagiaires</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('administration.etablissement.niveaux.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Retour
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection