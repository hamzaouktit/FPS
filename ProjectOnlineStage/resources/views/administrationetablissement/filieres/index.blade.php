@extends('layouts.app')

    @section('title', 'Liste des Filières')

    @section('breadcrumb')
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item active" aria-current="page">Filières</li>
            </ol>
        </nav>
    @endsection

    @section('content')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-stream me-2"></i>Liste des Filières</h2>
            <a href="{{ route('administration.etablissement.filieres.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Ajouter une filière
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Code Filière</th>
                        <th>Nom Filière</th>
                        <th>Secteur</th>
                        <th>Établissement</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($filieres as $filiere)
                        <tr>
                            <td>{{ $filiere->code_filiere }}</td>
                            <td>{{ $filiere->nom_filiere }}</td>
                            <td>{{ $filiere->secteur->nom_secteur ?? 'N/A' }}</td>
                            <td>{{ $filiere->etablissement->nom_efp ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('administration.etablissement.filieres.show', $filiere->code_filiere) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('administration.etablissement.filieres.edit', $filiere->code_filiere) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('administration.etablissement.filieres.destroy', $filiere->code_filiere) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Voulez-vous vraiment supprimer cette filière ?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Aucune filière trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $filieres->links() }}
    @endsection