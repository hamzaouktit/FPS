@extends('layouts.app')

@section('title', 'Gestion des Filières')

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">
                <i class="fas fa-layer-group text-primary me-2"></i>Gestion des Filières
            </h1>
            <p class="text-muted mb-0">Liste de toutes les filières de formation</p>
        </div>
        <a href="{{ route('administration.etablissement.filieres.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Ajouter une filière
        </a>
    </div>

    <!-- Filtres et recherche -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('administration.etablissement.filieres.index') }}" class="row g-3">
                <div class="col-md-5">
                    <label for="search" class="form-label">
                        <i class="fas fa-search me-1"></i>Rechercher
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Code ou nom de la filière...">
                </div>
                <div class="col-md-5">
                    <label for="secteur" class="form-label">
                        <i class="fas fa-filter me-1"></i>Filtrer par secteur
                    </label>
                    <select class="form-select" id="secteur" name="secteur">
                        <option value="">Tous les secteurs</option>
                        @foreach($secteurs as $secteur)
                            <option value="{{ $secteur->nom_secteur }}" 
                                    {{ request('secteur') == $secteur->nom_secteur ? 'selected' : '' }}>
                                {{ $secteur->nom_secteur }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100 me-2">
                        <i class="fas fa-search me-1"></i>Filtrer
                    </button>
                    <a href="{{ route('administration.etablissement.filieres.index') }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($filieres->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Nom de la Filière</th>
                                <th>Secteur</th>
                                <th class="text-center">Formations</th>
                                <th class="text-center" style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filieres as $filiere)
                                <tr>
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded">{{ $filiere->code_filiere }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $filiere->nom_filiere }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-sitemap me-1"></i>{{ $filiere->nom_secteur }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $filiere->formations_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('administration.etablissement.filieres.show', $filiere->code_filiere) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('administration.etablissement.filieres.edit', $filiere->code_filiere) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Supprimer"
                                                    onclick="confirmDelete('{{ $filiere->code_filiere }}', '{{ $filiere->nom_filiere }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <form id="delete-form-{{ $filiere->code_filiere }}" 
                                              action="{{ route('administration.etablissement.filieres.destroy', $filiere->code_filiere) }}" 
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
                        Affichage de {{ $filieres->firstItem() }} à {{ $filieres->lastItem() }} sur {{ $filieres->total() }} filières
                    </div>
                    {{ $filieres->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune filière trouvée.</p>
                    <a href="{{ route('administration.etablissement.filieres.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Créer la première filière
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(code, nom) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette filière ?\n\nCode : ' + code + '\nNom : ' + nom + '\n\nCette action est irréversible.')) {
        document.getElementById('delete-form-' + code).submit();
    }
}
</script>
@endpush
@endsection