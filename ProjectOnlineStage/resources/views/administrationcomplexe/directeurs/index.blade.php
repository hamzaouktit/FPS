@extends('layouts.app')

@section('title', 'Gestion des Directeurs - Système de Pilotage')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('administration.complexe.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('administration.complexe.directeurs.index') }}">Directeurs</a></li>
        <li class="breadcrumb-item active" aria-current="page">Liste</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
    <h2 class="h3 mb-0"><i class="fas fa-users me-2"></i>Gestion des Directeurs d'Établissements</h2>
    <a href="{{ route('administration.complexe.directeurs.create') }}" class="btn btn-primary" style="background: linear-gradient(45deg, var(--ofppt-blue), var(--ofppt-dark-blue)); border: none; transition: all 0.3s ease;">
        <i class="fas fa-plus me-2"></i>Ajouter un Directeur
    </a>
</div>

<div class="table-responsive" style="border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
    <table class="table table-striped table-hover">
        <thead style="background: var(--ofppt-dark-blue); color: white;">
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Établissement Associé</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($directeurs as $directeur)
            <tr style="transition: transform 0.3s ease;">
                <td>{{ $directeur->nom }}</td>
                <td>{{ $directeur->email }}</td>
                <td>
                    <span class="badge" style="background: var(--ofppt-blue);">
                        <i class="fas fa-school me-1"></i>Directeur Établissement
                    </span>
                </td>
                <td>
                    @if($directeur->etablissement)
                        {{ $directeur->etablissement->nom_efp }}
                    @else
                        <span class="text-muted">Aucun</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('administration.complexe.directeurs.show', $directeur) }}" class="btn btn-sm btn-outline-info" style="transition: all 0.3s ease;">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('administration.complexe.directeurs.edit', $directeur) }}" class="btn btn-sm btn-outline-warning" style="transition: all 0.3s ease;">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('administration.complexe.directeurs.destroy', $directeur) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Confirmer la suppression ?')" style="transition: all 0.3s ease;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted">Aucun directeur trouvé.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $directeurs->links('pagination::bootstrap-5') }}
</div>

<style>
    .table-responsive tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .btn-outline-info:hover { background: var(--ofppt-blue); color: white; }
    .btn-outline-warning:hover { background: var(--ofppt-green); color: white; }
    .btn-outline-danger:hover { background: #dc3545; color: white; }
    .pagination .page-link { color: var(--ofppt-blue); }
    .pagination .page-item.active .page-link { background: var(--ofppt-blue); border-color: var(--ofppt-blue); }
</style>
@endsection