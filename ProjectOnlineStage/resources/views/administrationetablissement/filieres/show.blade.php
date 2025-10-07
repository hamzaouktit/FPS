@extends('layouts.app')

@section('title', 'Détails de la Filière')

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
                <li class="breadcrumb-item active">{{ $filiere->code_filiere }}</li>
            </ol>
        </nav>
        
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-1">
                    <i class="fas fa-layer-group text-primary me-2"></i>{{ $filiere->nom_filiere }}
                </h1>
                <p class="text-muted mb-0">
                    <code class="bg-light px-2 py-1 rounded">{{ $filiere->code_filiere }}</code>
                    <span class="mx-2">•</span>
                    <span class="badge bg-primary">{{ $filiere->nom_secteur }}</span>
                </p>
            </div>
            <div class="btn-group">
                <a href="{{ route('administration.etablissement.filieres.edit', $filiere->code_filiere) }}" 
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
        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
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

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white">
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

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Stagiaires</h6>
                            <h2 class="mb-0">{{ $stats['effectif_total'] }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 h-100" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Établissements</h6>
                            <h2 class="mb-0">{{ $stats['etablissements'] }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-building"></i>
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
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>Informations générales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-5">
                                    <i class="fas fa-hashtag text-muted me-2"></i>Code filière
                                </dt>
                                <dd class="col-sm-7">
                                    <code class="bg-light px-2 py-1 rounded">{{ $filiere->code_filiere }}</code>
                                </dd>
                                
                                <dt class="col-sm-5">
                                    <i class="fas fa-tag text-muted me-2"></i>Nom filière
                                </dt>
                                <dd class="col-sm-7">
                                    <strong>{{ $filiere->nom_filiere }}</strong>
                                </dd>
                                
                                <dt class="col-sm-5">
                                    <i class="fas fa-sitemap text-muted me-2"></i>Secteur
                                </dt>
                                <dd class="col-sm-7">
                                    <a href="{{ route('administration.etablissement.secteurs.show', $filiere->nom_secteur) }}" 
                                       class="badge bg-primary text-decoration-none">
                                        {{ $filiere->nom_secteur }}
                                    </a>
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-5">
                                    <i class="fas fa-graduation-cap text-muted me-2"></i>Formations
                                </dt>
                                <dd class="col-sm-7">
                                    <span class="badge bg-info">{{ $stats['total_formations'] }}</span>
                                </dd>
                                
                                <dt class="col-sm-5">
                                    <i class="fas fa-calendar-plus text-muted me-2"></i>Date de création
                                </dt>
                                <dd class="col-sm-7">
                                    {{ $filiere->created_at ? $filiere->created_at->format('d/m/Y à H:i') : 'Non disponible' }}
                                </dd>
                                
                                <dt class="col-sm-5">
                                    <i class="fas fa-calendar-check text-muted me-2"></i>Dernière modification
                                </dt>
                                <dd class="col-sm-7">
                                    {{ $filiere->updated_at ? $filiere->updated_at->format('d/m/Y à H:i') : 'Non disponible' }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des formations -->
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-graduation-cap text-primary me-2"></i>Formations associées
            </h5>
        </div>
        <div class="card-body">
            @if($formations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Année</th>
                                <th>Établissement</th>
                                <th>Niveau</th>
                                <th>Type</th>
                                <th>Créneau</th>
                                <th class="text-center">Groupes</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formations as $formation)
                                <tr>
                                    <td>
                                        <span class="badge bg-dark">{{ $formation->annee }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $formation->etablissement->nom_efp ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $formation->code_efp }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $formation->niveau }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $formation->type_formation ?? 'Standard' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">{{ $formation->creneau ?? 'Normal' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $formation->groupes_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if(Route::has('administration.etablissement.formations.show'))
                                            <a href="{{ route('administration.etablissement.formations.show', $formation->id) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($formations->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted">
                            Affichage de {{ $formations->firstItem() }} à {{ $formations->lastItem() }} sur {{ $formations->total() }} formations
                        </div>
                        {{ $formations->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Aucune formation associée à cette filière.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Formulaire de suppression caché -->
    <form id="delete-form" 
          action="{{ route('administration.etablissement.filieres.destroy', $filiere->code_filiere) }}" 
          method="POST" 
          class="d-none">
        @csrf
        @method('DELETE')
    </form>
</div>

@push('scripts')
<script>
function confirmDelete() {
    const formationsCount = {{ $stats['total_formations'] }};
    
    if (formationsCount > 0) {
        alert('Impossible de supprimer cette filière car elle contient ' + formationsCount + ' formation(s).\n\nVeuillez d\'abord supprimer ou réaffecter les formations.');
        return;
    }
    
    if (confirm('Êtes-vous sûr de vouloir supprimer cette filière ?\n\nCode : {{ $filiere->code_filiere }}\nNom : {{ $filiere->nom_filiere }}\n\nCette action est irréversible.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
@endsection