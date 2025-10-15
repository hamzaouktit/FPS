@extends('layouts.app')

@section('title', 'Gestion des Groupes')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Groupes</li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-users me-2"></i>Liste des Groupes</h2>
        <a href="{{ route('administration.etablissement.groupes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Ajouter un Groupe
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Code</th>
                    <th>EFP Code</th>
                    <th>EFP Nom</th>
                    <th>Effectif</th>
                    <th>Statut</th>
                    <th>Année Formation</th>
                    <th>Année</th>
                    <th>Filière</th>
                    <th>Formation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($groupes as $groupe)
                    <tr>
                        <td>{{ $groupe->code }}</td>
                        <td>{{ $groupe->efp_code }}</td>
                        <td>{{ $groupe->efp_nom }}</td>
                        <td>{{ $groupe->effectif }}</td>
                        <td>{{ $groupe->statut }}</td>
                        <td>{{ $groupe->annee_formation }}</td>
                        <td>{{ $groupe->annee }}</td>
                        <td>{{ $groupe->filiere->nom ?? 'N/A' }}</td>
                        <td>{{ $groupe->formation->type ?? 'N/A' }}</td>
                        <td>
                            <a href="{{ route('administration.etablissement.groupes.show', $groupe) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('administration.etablissement.groupes.edit', $groupe) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('administration.etablissement.groupes.destroy', $groupe) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr ?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="text-center">Aucun groupe trouvé.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $groupes->links() }}
@endsection