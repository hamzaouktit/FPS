@extends('layouts.app')

@section('title', 'Gestion des Filières')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-graduation-cap"></i> Filières
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            <i class="fas fa-graduation-cap me-2"></i>
            Liste des Filières
        </h4>
        <a href="{{ route('administration.etablissement.filieres.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus-circle me-1"></i>
            Nouvelle Filière
        </a>
    </div>

    <div class="card-body">
        @if($filieres->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Aucune filière enregistrée pour le moment.</p>
                <a href="{{ route('administration.etablissement.filieres.create') }}" class="btn btn-primary mt-3">
                    <i class="fas fa-plus-circle me-1"></i>
                    Ajouter une première filière
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-hashtag me-1"></i> Code</th>
                            <th><i class="fas fa-graduation-cap me-1"></i> Nom de la Filière</th>
                            <th><i class="fas fa-layer-group me-1"></i> Secteur</th>
                            <th><i class="fas fa-chalkboard-teacher me-1"></i> Formations</th>
                            <th><i class="fas fa-users me-1"></i> Groupes</th>
                            <th class="text-center"><i class="fas fa-cogs me-1"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filieres as $filiere)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $filiere->code_filiere }}</span>
                            </td>
                            <td>
                                <strong>{{ $filiere->nom_filiere }}</strong>
                            </td>
                            <td>
                                <i class="fas fa-layer-group text-primary me-1"></i>
                                {{ $filiere->secteur->nom_secteur ?? 'N/A' }}
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $filiere->formations()->count() }} formation(s)
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ $filiere->groupes()->count() }} groupe(s)
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('administration.etablissement.filieres.show', $filiere->code_filiere) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('administration.etablissement.filieres.edit', $filiere->code_filiere) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
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

            <div class="d-flex justify-content-center mt-4">
                {{ $filieres->links() }}
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
                <p>Êtes-vous sûr de vouloir supprimer la filière <strong id="filiereNameToDelete"></strong> ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Cette action est irréversible. Assurez-vous qu'aucune formation n'est liée à cette filière.
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
    function confirmDelete(codeFiliere, nomFiliere) {
        document.getElementById('filiereNameToDelete').textContent = nomFiliere;
        document.getElementById('deleteForm').action = 
            "{{ route('administration.etablissement.filieres.destroy', ':code') }}".replace(':code', codeFiliere);
        
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
@endpush