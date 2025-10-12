@extends('layouts.app')

@section('title', 'Gestion des Secteurs')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Gestion des Secteurs</h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-industry me-2"></i>Liste des secteurs de formation
                    </p>
                </div>
                <div>
                    <a href="{{ route('administration.etablissement.secteurs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nouveau Secteur
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages de succès/erreur -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Secteurs</h6>
                            <h3 class="mb-0">{{ $secteurs->total() }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-industry fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Avec Filières</h6>
                            <h3 class="mb-0">{{ $secteurs->where('filieres_count', '>', 0)->count() }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Sans Filières</h6>
                            <h3 class="mb-0">{{ $secteurs->where('filieres_count', 0)->count() }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Total Filières</h6>
                            <h3 class="mb-0">{{ $secteurs->sum('filieres_count') }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-stream fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table des secteurs -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Liste des Secteurs</h5>
        </div>
        <div class="card-body">
            @if($secteurs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nom du Secteur</th>
                                <th>Établissement</th>
                                <th class="text-center">Filières</th>
                                <th>Date de Création</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($secteurs as $index => $secteur)
                                <tr>
                                    <td>{{ $secteurs->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $secteur->nom_secteur }}</strong>
                                    </td>
                                    <td>
                                        <i class="fas fa-building text-muted me-2"></i>
                                        {{ $secteur->etablissement->nom_efp }}
                                        <br>
                                        <small class="text-muted">
                                            <code>{{ $secteur->code_efp }}</code>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        @if($secteur->filieres_count > 0)
                                            <span class="badge bg-success">{{ $secteur->filieres_count }}</span>
                                        @else
                                            <span class="badge bg-secondary">0</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ $secteur->created_at->format('d/m/Y') }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('administration.etablissement.secteurs.show', $secteur->id) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('administration.etablissement.secteurs.edit', $secteur->id) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete({{ $secteur->id }})" 
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <form id="delete-form-{{ $secteur->id }}" 
                                              action="{{ route('administration.etablissement.secteurs.destroy', $secteur->id) }}" 
                                              method="POST" 
                                              class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $secteurs->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun secteur trouvé</h5>
                    <p class="text-muted">Commencez par créer un nouveau secteur</p>
                    <a href="{{ route('administration.etablissement.secteurs.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>Créer un Secteur
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce secteur ?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
@endsection