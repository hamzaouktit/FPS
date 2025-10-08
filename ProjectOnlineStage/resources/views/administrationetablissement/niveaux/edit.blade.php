@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="mb-4">
                <h1 class="h3 mb-0">Modifier le niveau</h1>
                <p class="text-muted mb-0">{{ $etablissement->nom_efp }}</p>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.niveaux.update', $niveauModel->niveau) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="niveau" class="form-label">
                                Niveau <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('niveau') is-invalid @enderror" 
                                   id="niveau" 
                                   name="niveau" 
                                   value="{{ old('niveau', $niveauModel->niveau) }}"
                                   placeholder="Ex: Technicien, Technicien Spécialisé, Qualification"
                                   required>
                            @error('niveau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Exemples : TS (Technicien Spécialisé), T (Technicien), Q (Qualification), CAP, BTS
                            </div>
                        </div>

                        <div class="alert alert-warning" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Attention :</strong> La modification du niveau affectera toutes les formations associées.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('administration.etablissement.niveaux.index') }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection