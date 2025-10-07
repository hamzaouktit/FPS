@extends('layouts.app')

@section('title', 'Détails du Secteur')

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
                <li class="breadcrumb-item active">{{ $secteur->nom_secteur }}</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">{{ $secteur->nom_secteur }}</h1>
                <p class="text-muted mb-0">Détails et statistiques du secteur</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('administration.etablissement.secteurs.edit', $secteur->nom_secteur) }}" 
                   class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Modifier
                </a>
                <button type="button" 
                        class="btn btn-danger" 
                        onclick="confirmDelete()">
                    <i class="fas fa-trash me-2"></i>Supprimer
                </button>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Filières</h6>
                            <h2 class="mb-0">{{ $stats['total_filieres'] }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-layer-group"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Formations</h6>
                            <h2 class="mb-0">{{ $stats['total_formations'] }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Groupes</h6>
                            <h2 class="mb-0">{{ $stats['total_groupes'] }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations générales -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>Informations générales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Nom du secteur</dt>
                                <dd class="col-sm-8">
                                    <strong>{{ $secteur->nom_secteur }}</strong>
                                </dd>
                                
                                <dt class="col-sm-4">Nombre de filières</dt>
                                <dd class="col-sm-8">
                                    <span class="badge bg-primary">{{ $stats['total_filieres'] }}</span>
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Date de création</dt>
                                <dd class="col-sm-8">
                                    @if($secteur->created_at)
                                        {{ $secteur->created_at->format('d/m/Y à H:i') }}
                                    @else
                                        <span class="text-muted">Non disponible</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Dernière modification</dt>
                                <dd class="col-sm-8">
                                    @if($secteur->updated_at)
                                        {{ $secteur->updated_at->format('d/m/Y à H:i') }}
                                    @else
                                        <span class="text-muted">Non disponible</span>
                                    @endif
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des filières -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="fas fa-layer-group text-primary me-2"></i>Filières associées
            </h5>
        </div>
        <div class="card-body">
            @if($filieres->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Nom de la filière</th>
                                <th class="text-center">Nombre de formations</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filieres as $filiere)
                                <tr>
                                    <td>
                                        <code>{{ $filiere->code_filiere }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $filiere->nom_filiere }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $filiere->formations_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if(Route::has('administration.etablissement.filieres.show'))
                                            <a href="{{ route('administration.etablissement.filieres.show', $filiere->code_filiere) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($filieres->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Affichage de {{ $filieres->firstItem() }} à {{ $filieres->lastItem() }} sur {{ $filieres->total() }} filières
                        </div>
                        {{ $filieres->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Aucune filière associée à ce secteur.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Formulaire de suppression caché -->
    <form id="delete-form" 
          action="{{ route('administration.etablissement.secteurs.destroy', $secteur->nom_secteur) }}" 
          method="POST" 
          class="d-none">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script>
function confirmDelete() {
    const filieresCount = {{ $stats['total_filieres'] }};
    
    if (filieresCount > 0) {
        alert('Impossible de supprimer ce secteur car il contient ' + filieresCount + ' filière(s).\n\nVeuillez d\'abord supprimer ou réaffecter les filières.');
        return;
    }
    
    if (confirm('Êtes-vous sûr de vouloir supprimer ce secteur ?\n\nSecteur : {{ $secteur->nom_secteur }}\n\nCette action est irréversible.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
@endsection