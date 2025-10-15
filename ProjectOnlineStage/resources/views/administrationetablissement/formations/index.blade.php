@extends('layouts.app')

@section('title', 'Gestion des Formations')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800">Gestion des Formations</h1>
            <p class="text-muted mb-0">{{ $etablissement->nom_efp }} ({{ $etablissement->code_efp }})</p>
        </div>
        <a href="{{ route('administration.etablissement.formations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouvelle Formation
        </a>
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
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Formations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Diplômantes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['diplomante'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-certificate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Qualifiantes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['qualifiante'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-award fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">PP (Passerelles)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pp'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres et Recherche</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('administration.etablissement.formations.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">Tous les types</option>
                            <option value="Diplômante" {{ request('type') == 'Diplômante' ? 'selected' : '' }}>Diplômante</option>
                            <option value="Qualifiante" {{ request('type') == 'Qualifiante' ? 'selected' : '' }}>Qualifiante</option>
                            <option value="PP" {{ request('type') == 'PP' ? 'selected' : '' }}>PP</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="mode" class="form-label">Mode</label>
                        <select name="mode" id="mode" class="form-select">
                            <option value="">Tous les modes</option>
                            <option value="Résidentiel" {{ request('mode') == 'Résidentiel' ? 'selected' : '' }}>Résidentiel</option>
                            <option value="Alterné" {{ request('mode') == 'Alterné' ? 'selected' : '' }}>Alterné</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="creneau" class="form-label">Créneau</label>
                        <select name="creneau" id="creneau" class="form-select">
                            <option value="">Tous les créneaux</option>
                            <option value="CDJ" {{ request('creneau') == 'CDJ' ? 'selected' : '' }}>CDJ (Cours de Jour)</option>
                            <option value="CDS" {{ request('creneau') == 'CDS' ? 'selected' : '' }}>CDS (Cours du Soir)</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               placeholder="Rechercher..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                    <a href="{{ route('administration.etablissement.formations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des formations -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Formations ({{ $formations->total() }})</h6>
        </div>
        <div class="card-body">
            @if($formations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Mode</th>
                                <th>Créneau</th>
                                <th>Groupes Associés</th>
                                <th>Date de création</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formations as $formation)
                            <tr>
                                <td>{{ $formation->id }}</td>
                                <td>
                                    @if($formation->type == 'Diplômante')
                                        <span class="badge bg-success">{{ $formation->type }}</span>
                                    @elseif($formation->type == 'Qualifiante')
                                        <span class="badge bg-info">{{ $formation->type }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ $formation->type }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $formation->mode == 'Résidentiel' ? 'primary' : 'secondary' }}">
                                        {{ $formation->mode }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $formation->creneau == 'CDJ' ? 'light text-dark' : 'dark' }}">
                                        {{ $formation->creneau }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $formation->groupes->count() }} groupe(s)</span>
                                </td>
                                <td>{{ $formation->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('administration.etablissement.formations.show', $formation->id) }}" 
                                           class="btn btn-sm btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('administration.etablissement.formations.edit', $formation->id) }}" 
                                           class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger delete-btn" 
                                                data-id="{{ $formation->id }}"
                                                data-type="{{ $formation->type }}"
                                                data-mode="{{ $formation->mode }}"
                                                data-creneau="{{ $formation->creneau }}"
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

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $formations->firstItem() }} à {{ $formations->lastItem() }} sur {{ $formations->total() }} résultats
                    </div>
                    {{ $formations->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune formation trouvée.</p>
                    <a href="{{ route('administration.etablissement.formations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Créer la première formation
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la suppression
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const formationId = this.dataset.id;
            const type = this.dataset.type;
            const mode = this.dataset.mode;
            const creneau = this.dataset.creneau;
            
            Swal.fire({
                title: 'Confirmer la suppression ?',
                html: `Voulez-vous vraiment supprimer la formation :<br><strong>${type} - ${mode} - ${creneau}</strong> ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Afficher un loader
                    Swal.fire({
                        title: 'Suppression en cours...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Envoyer la requête de suppression
                    fetch(`{{ route('administration.etablissement.formations.index') }}/${formationId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Supprimé !',
                                text: data.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Erreur !',
                                text: data.message,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Erreur !',
                            text: 'Une erreur est survenue lors de la suppression.',
                            icon: 'error'
                        });
                    });
                }
            });
        });
    });
});
</script>
@endpush
@endsection