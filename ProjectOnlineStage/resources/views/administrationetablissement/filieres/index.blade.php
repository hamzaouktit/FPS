@extends('layouts.app')

@section('title', 'Gestion des Filières')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gestion des Filières</h2>
        <a href="{{ route('administration.etablissement.filieres.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nouvelle Filière
        </a>
    </div>

    <!-- Messages de succès/erreur -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('administration.etablissement.filieres.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Recherche</label>
                        <input type="text" 
                               name="search" 
                               class="form-control" 
                               placeholder="Code ou nom de filière..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Secteur</label>
                        <select name="secteur_id" class="form-select">
                            <option value="">Tous les secteurs</option>
                            @foreach($secteurs as $secteur)
                                <option value="{{ $secteur->id }}" 
                                        {{ request('secteur_id') == $secteur->id ? 'selected' : '' }}>
                                    {{ $secteur->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Niveau</label>
                        <select name="niveau_id" class="form-select">
                            <option value="">Tous les niveaux</option>
                            @foreach($niveaux as $niveau)
                                <option value="{{ $niveau->id }}" 
                                        {{ request('niveau_id') == $niveau->id ? 'selected' : '' }}>
                                    {{ $niveau->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i> Filtrer
                        </button>
                    </div>
                </div>
                @if(request()->hasAny(['search', 'secteur_id', 'niveau_id']))
                    <div class="mt-2">
                        <a href="{{ route('administration.etablissement.filieres.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Réinitialiser les filtres
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Liste des filières -->
    <div class="card">
        <div class="card-body">
            @if($filieres->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Nom</th>
                                <th>Secteur</th>
                                <th>Niveau</th>
                                <th>Nb Groupes</th>
                                <th>Nb Modules</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filieres as $filiere)
                            <tr>
                                <td>
                                    <strong>{{ $filiere->code }}</strong>
                                </td>
                                <td>{{ $filiere->nom }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $filiere->secteur->nom ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $filiere->niveau->nom ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $filiere->groupes->count() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $filiere->modules->count() }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('administration.etablissement.filieres.show', $filiere->id) }}" 
                                           class="btn btn-sm btn-outline-primary"
                                           title="Voir les détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('administration.etablissement.filieres.edit', $filiere->id) }}" 
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete({{ $filiere->id }})"
                                                title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>

                                    <form id="delete-form-{{ $filiere->id }}" 
                                          action="{{ route('administration.etablissement.filieres.destroy', $filiere->id) }}" 
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
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $filieres->firstItem() }} à {{ $filieres->lastItem() }} 
                        sur {{ $filieres->total() }} filières
                    </div>
                    <div>
                        {{ $filieres->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="text-muted mt-3">Aucune filière trouvée.</p>
                    <a href="{{ route('administration.etablissement.filieres.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Créer la première filière
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette filière ? Cette action est irréversible.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush

@push('styles')
<style>
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        color: #495057;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
    .btn-group {
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    }
</style>
@endpush
@endsection