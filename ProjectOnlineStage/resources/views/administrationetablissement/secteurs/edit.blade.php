@extends('layouts.app')

@section('title', 'Modifier le Secteur')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Modifier le Secteur</h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-industry me-2"></i>{{ $secteur->nom }} - {{ $etablissement->nom_efp }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('administration.etablissement.secteurs.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                    <a href="{{ route('administration.etablissement.secteurs.show', $secteur->id) }}" class="btn btn-info">
                        <i class="fas fa-eye me-2"></i>Voir Détails
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages d'erreur -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreurs de validation</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulaire -->
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Modifier les Informations
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.secteurs.update', $secteur->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Code du Secteur -->
                        <div class="mb-4">
                            <label for="code" class="form-label">
                                Code du Secteur <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('code') is-invalid @enderror" 
                                       id="code" 
                                       name="code" 
                                       value="{{ old('code', $secteur->code) }}"
                                       placeholder="Ex: BTP"
                                       maxlength="50"
                                       style="text-transform: uppercase;"
                                       required>
                            </div>
                            @error('code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Code unique du secteur dans votre établissement
                            </small>
                        </div>

                        <!-- Nom du Secteur -->
                        <div class="mb-4">
                            <label for="nom" class="form-label">
                                Nom du Secteur <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-industry"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('nom') is-invalid @enderror" 
                                       id="nom" 
                                       name="nom" 
                                       value="{{ old('nom', $secteur->nom) }}"
                                       placeholder="Ex: Bâtiment et Travaux Publics"
                                       required>
                            </div>
                            @error('nom')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Nom complet du secteur de formation
                            </small>
                        </div>

                        <!-- Établissement (lecture seule) -->
                        <div class="mb-4">
                            <label class="form-label">Établissement</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-building"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $etablissement->nom_efp }} ({{ $etablissement->code_efp }})" 
                                       readonly>
                            </div>
                        </div>

                        <!-- Informations sur les filières associées -->
                        @php
                            $filieresCount = $secteur->filieres()->where('code_efp', $etablissement->code_efp)->count();
                        @endphp
                        
                        @if($filieresCount > 0)
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Attention
                                </h6>
                                <p class="mb-0">
                                    Ce secteur contient <strong>{{ $filieresCount }} filière(s)</strong> de votre établissement. 
                                    La modification affectera toutes les filières associées.
                                </p>
                            </div>
                        @endif

                        <!-- Informations de modification -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="far fa-calendar-plus me-1"></i>
                                            <strong>Créé le:</strong> {{ $secteur->created_at->format('d/m/Y à H:i') }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">
                                            <i class="far fa-calendar-check me-1"></i>
                                            <strong>Modifié le:</strong> {{ $secteur->updated_at->format('d/m/Y à H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between">
                            <button type="button" 
                                    class="btn btn-danger" 
                                    onclick="confirmDelete()"
                                    @if($filieresCount > 0) disabled title="Impossible de supprimer: le secteur contient des filières" @endif>
                                <i class="fas fa-trash me-2"></i>Supprimer
                            </button>
                            <div class="d-flex gap-2">
                                <a href="{{ route('administration.etablissement.secteurs.index') }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-save me-2"></i>Enregistrer
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Formulaire de suppression -->
                    <form id="delete-form" 
                          action="{{ route('administration.etablissement.secteurs.destroy', $secteur->id) }}" 
                          method="POST" 
                          class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>

            <!-- Filières associées -->
            @if($filieresCount > 0)
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-stream me-2"></i>Filières Associées de votre Établissement ({{ $filieresCount }})
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($secteur->filieres()->where('code_efp', $etablissement->code_efp)->get() as $filiere)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $filiere->nom }}</h6>
                                            <small class="text-muted">
                                                <code>{{ $filiere->code }}</code>
                                                | Niveau: {{ $filiere->niveau->nom ?? 'N/A' }}
                                            </small>
                                        </div>
                                        <span class="badge bg-info">
                                            {{ $filiere->groupes()->where('code_efp', $etablissement->code_efp)->count() }} groupes
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce secteur ?\n\nAttention: Cette action est irréversible.')) {
        document.getElementById('delete-form').submit();
    }
}

document.getElementById('code').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});
</script>
@endpush
@endsection