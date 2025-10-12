@extends('layouts.app')

@section('title', 'Gestion des Formations')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item active">
            <i class="fas fa-graduation-cap"></i> Formations
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-2">
                <i class="fas fa-graduation-cap text-primary"></i>
                Gestion des Formations
            </h2>
            <p class="text-muted mb-0">
                <i class="fas fa-school me-1"></i>
                {{ Auth::user()->etablissement->nom_efp }}
            </p>
        </div>
        <a href="{{ route('administration.etablissement.formations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>
            Nouvelle Formation
        </a>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                                <i class="fas fa-graduation-cap fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Formations</h6>
                            <h3 class="mb-0">{{ $formations->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des formations -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Liste des Formations
            </h5>
        </div>
        <div class="card-body p-0">
            @if($formations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Année</th>
                                <th>Filière</th>
                                <th>Secteur</th>
                                <th>Niveau</th>
                                <th>Type</th>
                                <th>Créneau</th>
                                <th>Groupes</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formations as $formation)
                                <tr>
                                    <td class="px-4">
                                        <span class="badge bg-info text-white px-3 py-2">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            {{ $formation->annee }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $formation->filiere->nom_filiere }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $formation->filiere->code_filiere }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $formation->filiere->secteur->nom_secteur ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $formation->niveau->niveau }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($formation->type_formation)
                                            <span class="badge bg-success">
                                                {{ $formation->type_formation }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($formation->creneau)
                                            <span class="badge bg-warning text-dark">
                                                {{ $formation->creneau }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-dark">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $formation->groupes->count() }}
                                        </span>
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('administration.etablissement.formations.show', $formation->id) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('administration.etablissement.formations.edit', $formation->id) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('administration.etablissement.formations.destroy', $formation->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette formation ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Supprimer"
                                                        @if($formation->groupes->count() > 0) disabled @endif>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Affichage de {{ $formations->firstItem() }} à {{ $formations->lastItem() }} 
                            sur {{ $formations->total() }} formations
                        </div>
                        <div>
                            {{ $formations->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-graduation-cap fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune formation trouvée</h5>
                    <p class="text-muted">Commencez par créer une nouvelle formation.</p>
                    <a href="{{ route('administration.etablissement.formations.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus-circle me-2"></i>
                        Créer une formation
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
}
</style>
@endsection