@extends('layouts.app')

@section('title', 'Gestion des Secteurs')

@section('content')
<div class="container-fluid px-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Gestion des Secteurs</h1>
            <p class="text-muted mb-0">Liste de tous les secteurs de formation</p>
        </div>
        <a href="{{ route('administration.etablissement.secteurs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Ajouter un secteur
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

    <!-- Carte principale -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($secteurs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nom du Secteur</th>
                                <th class="text-center">Nombre de Filières</th>
                                <th class="text-center" style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($secteurs as $secteur)
                                <tr>
                                    <td>
                                        <strong>{{ $secteur->nom_secteur }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $secteur->filieres_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('administration.etablissement.secteurs.show', $secteur->nom_secteur) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('administration.etablissement.secteurs.edit', $secteur->nom_secteur) }}" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    title="Supprimer"
                                                    onclick="confirmDelete('{{ $secteur->nom_secteur }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <form id="delete-form-{{ Str::slug($secteur->nom_secteur) }}" 
                                              action="{{ route('administration.etablissement.secteurs.destroy', $secteur->nom_secteur) }}" 
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
                        Affichage de {{ $secteurs->firstItem() }} à {{ $secteurs->lastItem() }} sur {{ $secteurs->total() }} secteurs
                    </div>
                    {{ $secteurs->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucun secteur trouvé.</p>
                    <a href="{{ route('administration.etablissement.secteurs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Créer le premier secteur
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(nomSecteur) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce secteur ?\n\nSecteur : ' + nomSecteur)) {
        const slug = nomSecteur.toLowerCase()
            .replace(/\s+/g, '-')
            .replace(/[àáâãäå]/g, 'a')
            .replace(/[èéêë]/g, 'e')
            .replace(/[ìíîï]/g, 'i')
            .replace(/[òóôõö]/g, 'o')
            .replace(/[ùúûü]/g, 'u')
            .replace(/[ç]/g, 'c')
            .replace(/[^\w\-]+/g, '');
        document.getElementById('delete-form-' + slug).submit();
    }
}
</script>
@endpush
@endsection