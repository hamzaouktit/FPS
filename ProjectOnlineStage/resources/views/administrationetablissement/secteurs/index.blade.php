@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 d-inline-block fw-bold">Gestion des Secteurs</h1>
            <p class="text-muted">{{ $etablissement->nom_efp }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('administration.etablissement.secteurs.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un Secteur
            </a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            @if($secteurs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="45%">Nom du Secteur</th>
                                <th width="20%">Nombre de Filières</th>
                                <th width="30%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($secteurs as $secteur)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $secteur->id }}</td>
                                    <td>{{ $secteur->nom_secteur }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $secteur->filieres_count }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('administration.etablissement.secteurs.show', $secteur->id) }}" 
                                           class="btn btn-sm btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('administration.etablissement.secteurs.edit', $secteur->id) }}" 
                                           class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete({{ $secteur->id }})" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $secteur->id }}" 
                                              action="{{ route('administration.etablissement.secteurs.destroy', $secteur->id) }}" 
                                              method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $secteurs->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="alert alert-info text-center" role="alert">
                    <i class="fas fa-info-circle"></i> Aucun secteur trouvé
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce secteur ?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endsection