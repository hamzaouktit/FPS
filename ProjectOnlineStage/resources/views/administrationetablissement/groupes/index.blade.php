@extends('layouts.app')

@section('title', 'Gestion des Groupes')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-users"></i> Groupes
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            <i class="fas fa-users me-2"></i>
            Liste des Groupes de mon Établissement
        </h4>
        <a href="{{ route('administration.etablissement.groupes.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus-circle me-1"></i>
            Nouveau Groupe
        </a>
    </div>

    <div class="card-body">
        @if($groupes->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Aucun groupe enregistré pour votre établissement.</p>
                <a href="{{ route('administration.etablissement.groupes.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus-circle me-1"></i>
                    Ajouter un premier groupe
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-hashtag me-1"></i> Code</th>
                            <th><i class="fas fa-stream me-1"></i> Filière</th>
                            <th><i class="fas fa-layer-group me-1"></i> Niveau</th>
                            <th><i class="fas fa-graduation-cap me-1"></i> Formation</th>
                            <th><i class="fas fa-user-graduate me-1"></i> Effectif</th>
                            <th><i class="fas fa-calendar me-1"></i> Année</th>
                            <th><i class="fas fa-toggle-on me-1"></i> Statut</th>
                            <th class="text-center"><i class="fas fa-cogs me-1"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupes as $groupe)
                        <tr>
                            <td>
                                <strong>{{ $groupe->code_groupe }}</strong>
                                @if($groupe->sous_groupe)
                                    <br><small class="text-muted">Sous-groupe: {{ $groupe->sous_groupe }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $groupe->filiere->nom_filiere ?? 'N/A' }}
                                <br>
                                <small class="text-muted">{{ $groupe->filiere->secteur->nom_secteur ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $groupe->formation->niveau->nom ?? 'N/A' }}</span>
                            </td>
                            <td>
                                {{ $groupe->formation->type ?? 'N/A' }}
                                <br>
                                <small class="text-muted">{{ $groupe->formation->mode ?? 'N/A' }} - {{ $groupe->formation->creneau ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $groupe->effectif_groupe }} stagiaires</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $groupe->annee_formation }}</span>
                            </td>
                            <td>
                                @if($groupe->statut === 'Actif')
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('administration.etablissement.groupes.show', $groupe->id) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('administration.etablissement.groupes.edit', $groupe->id) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete('{{ $groupe->id }}', '{{ $groupe->code_groupe }}')"
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
                {{ $groupes->links() }}
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
                <p>Êtes-vous sûr de vouloir supprimer le groupe <strong id="groupeNameToDelete"></strong> ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Cette action est irréversible. Assurez-vous qu'aucune affectation n'est liée à ce groupe.
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
    function confirmDelete(groupeId, groupeName) {
        document.getElementById('groupeNameToDelete').textContent = groupeName;
        document.getElementById('deleteForm').action = 
            "{{ route('administration.etablissement.groupes.destroy', ':groupe') }}".replace(':groupe', groupeId);
        
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
@endpush