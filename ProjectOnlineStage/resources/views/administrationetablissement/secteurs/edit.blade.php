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
                        <i class="fas fa-industry me-2"></i>{{ $secteur->nom_secteur }}
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

                        <!-- Nom du Secteur -->
                        <div class="mb-4">
                            <label for="nom_secteur" class="form-label">
                                Nom du Secteur <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-industry"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('nom_secteur') is-invalid @enderror" 
                                       id="nom_secteur" 
                                       name="nom_secteur" 
                                       value="{{ old('nom_secteur', $secteur->nom_secteur) }}"
                                       placeholder="Ex: Bâtiment et Travaux Publics"
                                       required>
                            </div>
                            @error('nom_secteur')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Entrez le nom du secteur de formation
                            </small>
                        </div>

                        <!-- Établissement (en lecture seule) -->
                        <div class="mb-4">
                            <label class="form-label">Établissement</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-building"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       value="{{ $secteur->etablissement->nom_efp }} ({{ $secteur->code_efp }})" 
                                       readonly>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-lock me-1"></i>
                                L'établissement ne peut pas être modifié
                            </small>
                        </div>

                        <!-- Informations sur les filières associées -->
                        @if($secteur->filieres()->count() > 0)
                            <div class="alert alert-warning">
                                <h6 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Attention
                                </h6>
                                <p class="mb-0">
                                    Ce secteur contient <strong>{{ $secteur->filieres()->count() }} filière(s)</strong>. 
                                    La modification du nom affectera toutes les filières associées.
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
                                    onclick="confirmDelete()">
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
            @if($secteur->filieres()->count() > 0)
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-stream me-2"></i>Filières Associées ({{ $secteur->filieres()->count() }})
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($secteur->filieres as $filiere)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $filiere->nom_filiere }}</h6>
                                            <small class="text-muted">
                                                <code>{{ $filiere->code_filiere }}</code>
                                            </small>
                                        </div>
                                        <span class="badge bg-info">
                                            {{ $filiere->formations()->count() }} formations
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
</script>
@endpush
@endsection