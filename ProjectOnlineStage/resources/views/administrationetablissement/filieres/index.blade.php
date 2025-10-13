@extends('layouts.app')

@section('title', 'Gestion des Filières')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Gestion des Filières</h1>
            <p class="text-muted mb-0">{{ Auth::user()->etablissement->nom_efp }}</p>
        </div>
        <a href="{{ route('administration.etablissement.filieres.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle Filière
        </a>
    </div>

    <!-- Messages de succès/erreur -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('administration.etablissement.filieres.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Code ou nom de filière...">
                </div>
                <div class="col-md-4">
                    <label for="secteur_id" class="form-label">Secteur</label>
                    <select class="form-select" id="secteur_id" name="secteur_id">
                        <option value="">Tous les secteurs</option>
                        @foreach($secteurs as $secteur)
                            <option value="{{ $secteur->id }}" 
                                {{ request('secteur_id') == $secteur->id ? 'selected' : '' }}>
                                {{ $secteur->nom_secteur }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary me-2">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <a href="{{ route('administration.etablissement.filieres.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tableau des filières -->
    <div class="card">
        <div class="card-body">
            @if($filieres->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Nom de la Filière</th>
                                <th>Secteur</th>
                                <th>Formations</th>
                                <th>Date de création</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filieres as $filiere)
                                <tr>
                                    <td>
                                        <span class="badge bg-info">{{ $filiere->code_filiere }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $filiere->nom_filiere }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $filiere->secteur->nom_secteur }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $filiere->formations->count() }} formation(s)</span>
                                    </td>
                                    <td>{{ $filiere->created_at->format('d/m/Y') }}</td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('administration.etablissement.filieres.show', $filiere->code_filiere) }}" 
                                               class="btn btn-sm btn-info" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('administration.etablissement.filieres.edit', $filiere->code_filiere) }}" 
                                               class="btn btn-sm btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete('{{ $filiere->code_filiere }}', '{{ $filiere->nom_filiere }}')"
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
                    <div>
                        Affichage de {{ $filieres->firstItem() }} à {{ $filieres->lastItem() }} sur {{ $filieres->total() }} filières
                    </div>
                    <div>
                        {{ $filieres->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune filière trouvée</p>
                    <a href="{{ route('administration.etablissement.filieres.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Créer votre première filière
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la filière <strong id="filiereNom"></strong> ?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(codeFiliere, nom) {
    document.getElementById('filiereNom').textContent = nom;
    const form = document.getElementById('deleteForm');
    form.action = "{{ route('administration.etablissement.filieres.index') }}/" + codeFiliere;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endpush

@endsection