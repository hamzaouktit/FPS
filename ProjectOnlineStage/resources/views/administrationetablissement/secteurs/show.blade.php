@extends('layouts.app')

@section('title', 'Détails du Secteur')
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 d-inline-block fw-bold">{{ $secteur->nom_secteur }}</h1>
            <p class="text-muted">{{ $etablissement->nom_efp }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('administration.etablissement.secteurs.edit', $secteur->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('administration.etablissement.secteurs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted mb-2">Filières</h6>
                    <h2 class="text-primary fw-bold">{{ $stats['total_filieres'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted mb-2">Groupes</h6>
                    <h2 class="text-info fw-bold">{{ $stats['total_groupes'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted mb-2">Stagiaires</h6>
                    <h2 class="text-success fw-bold">{{ $stats['total_stagiaires'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted mb-2">ID</h6>
                    <h5 class="fw-bold">{{ $secteur->id }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-list"></i> Filières du Secteur
                    </h5>
                </div>
                <div class="card-body">
                    @if($secteur->filieres->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th width="10%">#</th>
                                        <th width="30%">Code Filière</th>
                                        <th width="35%">Nom Filière</th>
                                        <th width="25%">Groupes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($secteur->filieres as $filiere)
                                        <tr>
                                            <td class="fw-bold text-primary">{{ $filiere->id }}</td>
                                            <td>{{ $filiere->code_filiere }}</td>
                                            <td>{{ $filiere->nom_filiere }}</td>
                                            <td>
                                                <span class="badge bg-success">{{ $filiere->groupes->count() }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center" role="alert">
                            <i class="fas fa-info-circle"></i> Aucune filière associée
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle"></i> Détails
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless small">
                        <tr>
                            <td class="fw-bold text-muted">ID Secteur:</td>
                            <td class="text-end">{{ $secteur->id }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Nom:</td>
                            <td class="text-end">{{ $secteur->nom_secteur }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Code EFP:</td>
                            <td class="text-end">
                                <span class="badge bg-secondary">{{ $secteur->code_efp }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Établissement:</td>
                            <td class="text-end">{{ $etablissement->nom_efp }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Créé le:</td>
                            <td class="text-end">{{ $secteur->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-muted">Modifié le:</td>
                            <td class="text-end">{{ $secteur->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <button type="button" class="btn btn-danger w-100" onclick="confirmDelete({{ $secteur->id }})">
                        <i class="fas fa-trash"></i> Supprimer ce Secteur
                    </button>
                    <form id="delete-form-{{ $secteur->id }}" 
                          action="{{ route('administration.etablissement.secteurs.destroy', $secteur->id) }}" 
                          method="POST" style="display:none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce secteur ? Cette action est irréversible.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endsection