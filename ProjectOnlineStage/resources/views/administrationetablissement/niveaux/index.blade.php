@extends('layouts.app')

@section('title', 'Gestion des Niveaux')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-layer-group"></i> Niveaux
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            <i class="fas fa-layer-group me-2"></i>
            Liste des Niveaux de mon Établissement
        </h4>
        <a href="{{ route('administration.etablissement.niveaux.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus-circle me-1"></i>
            Nouveau Niveau
        </a>
    </div>

    <div class="card-body">
        <!-- Filtres -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Filtrer les niveaux
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('administration.etablissement.niveaux.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Recherche</label>
                            <input type="text" name="search" class="form-control form-control-sm" 
                                   placeholder="Nom du niveau..." value="{{ request('search') }}">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Formations (min)</label>
                            <select name="formations_min" class="form-select form-select-sm">
                                <option value="">-- Toutes --</option>
                                <option value="0" {{ request('formations_min') == '0' ? 'selected' : '' }}>0+</option>
                                <option value="1" {{ request('formations_min') == '1' ? 'selected' : '' }}>1+</option>
                                <option value="2" {{ request('formations_min') == '2' ? 'selected' : '' }}>2+</option>
                                <option value="5" {{ request('formations_min') == '5' ? 'selected' : '' }}>5+</option>
                                <option value="10" {{ request('formations_min') == '10' ? 'selected' : '' }}>10+</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Groupes (min)</label>
                            <select name="groupes_min" class="form-select form-select-sm">
                                <option value="">-- Tous --</option>
                                <option value="0" {{ request('groupes_min') == '0' ? 'selected' : '' }}>0+</option>
                                <option value="1" {{ request('groupes_min') == '1' ? 'selected' : '' }}>1+</option>
                                <option value="2" {{ request('groupes_min') == '2' ? 'selected' : '' }}>2+</option>
                                <option value="5" {{ request('groupes_min') == '5' ? 'selected' : '' }}>5+</option>
                                <option value="10" {{ request('groupes_min') == '10' ? 'selected' : '' }}>10+</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Tri par</label>
                            <select name="sort_by" class="form-select form-select-sm">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>
                                    Date de création
                                </option>
                                <option value="nom" {{ request('sort_by') == 'nom' ? 'selected' : '' }}>
                                    Nom (A-Z)
                                </option>
                                <option value="id" {{ request('sort_by') == 'id' ? 'selected' : '' }}>
                                    ID
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label">Ordre</label>
                            <select name="sort_order" class="form-select form-select-sm">
                                <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>
                                    <i class="fas fa-arrow-up"></i> Croissant
                                </option>
                                <option value="desc" {{ request('sort_order') == 'desc' || !request('sort_order') ? 'selected' : '' }}>
                                    <i class="fas fa-arrow-down"></i> Décroissant
                                </option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search me-1"></i>Filtrer
                                </button>
                                <a href="{{ route('administration.etablissement.niveaux.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-redo me-1"></i>Réinitialiser
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistiques des filtres -->
        @if(request()->filled('search') || request()->filled('formations_min') || request()->filled('groupes_min'))
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Résultats filtrés :</strong> {{ $stats['total_niveaux'] }} niveau(x) trouvé(s) 
            | {{ $stats['total_formations'] }} formations | {{ $stats['total_groupes'] }} groupes 
            | {{ $stats['effectif_total'] }} stagiaires
        </div>
        @endif

        @if($niveaux->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Aucun niveau enregistré pour votre établissement.</p>
                <a href="{{ route('administration.etablissement.niveaux.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus-circle me-1"></i>
                    Ajouter un premier niveau
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-hashtag me-1"></i> ID</th>
                            <th><i class="fas fa-layer-group me-1"></i> Niveau</th>
                            <th><i class="fas fa-graduation-cap me-1"></i> Formations</th>
                            <th><i class="fas fa-users me-1"></i> Groupes</th>
                            <th><i class="fas fa-user-graduate me-1"></i> Effectif</th>
                            <th><i class="fas fa-calendar me-1"></i> Date de création</th>
                            <th class="text-center"><i class="fas fa-cogs me-1"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($niveaux as $niveau)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">#{{ $niveau->id }}</span>
                            </td>
                            <td>
                                <strong>
                                    <i class="fas fa-graduation-cap text-primary me-2"></i>
                                    {{ $niveau->nom }}
                                </strong>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $niveau->formations_count }} formation(s)
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ $niveau->groupes_count }} groupe(s)
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $niveau->effectif_total }} stagiaire(s)
                                </span>
                            </td>
                            <td>
                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                {{ $niveau->created_at->format('d/m/Y') }}
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('administration.etablissement.niveaux.show', $niveau->id) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('administration.etablissement.niveaux.edit', $niveau->id) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete('{{ $niveau->id }}', '{{ $niveau->nom }}')"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $niveaux->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmation de suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le niveau <strong id="niveauNameToDelete"></strong> ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Cette action est irréversible. Assurez-vous qu'aucune donnée n'est liée à ce niveau.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Annuler
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(niveauId, niveauName) {
        document.getElementById('niveauNameToDelete').textContent = niveauName;
        document.getElementById('deleteForm').action = 
            "{{ route('administration.etablissement.niveaux.destroy', ':niveau') }}".replace(':niveau', niveauId);
        
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
@endpush