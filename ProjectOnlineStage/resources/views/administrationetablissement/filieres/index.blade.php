@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 d-inline-block fw-bold">Gestion des Filières</h1>
            <p class="text-muted">{{ auth()->user()->etablissement->nom_efp }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('administration.etablissement.filieres.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter une Filière
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

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Rechercher..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="secteur_id" class="form-select">
                        <option value="">Tous les secteurs</option>
                        @foreach($secteurs as $secteur)
                            <option value="{{ $secteur->id }}" 
                                    @if(request('secteur_id') == $secteur->id) selected @endif>
                                {{ $secteur->nom_secteur }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($filieres->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">Code Filière</th>
                                <th width="30%">Nom Filière</th>
                                <th width="20%">Secteur</th>
                                <th width="15%">Groupes</th>
                                <th width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filieres as $filiere)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $filiere->id }}</td>
                                    <td>{{ $filiere->code_filiere }}</td>
                                    <td>{{ $filiere->nom_filiere }}</td>
                                    <td>{{ $filiere->secteur->nom_secteur }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $filiere->groupes->count() }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('administration.etablissement.filieres.show', $filiere->id) }}" 
                                           class="btn btn-sm btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('administration.etablissement.filieres.edit', $filiere->id) }}" 
                                           class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete({{ $filiere->id }})" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $filiere->id }}" 
                                              action="{{ route('administration.etablissement.filieres.destroy', $filiere->id) }}" 
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
                    {{ $filieres->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="alert alert-info text-center" role="alert">
                    <i class="fas fa-info-circle"></i> Aucune filière trouvée
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette filière ?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endsection