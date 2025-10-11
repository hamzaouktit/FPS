@extends('layouts.app')

@section('title', 'Gestion des Secteurs')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">Gestion des Secteurs</h2>
                    <p class="text-muted mb-0">{{ Auth::user()->etablissement->nom_efp ?? 'Tous les établissements' }}</p>
                </div>
                <a href="{{ route('administration.etablissement.secteurs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nouveau Secteur
                </a>
            </div>
        </div>
    </div>

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

    <div class="card shadow-sm">
        <div class="card-body">
            @if($secteurs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nom du Secteur</th>
                                <th>Établissement</th>
                                <th class="text-center">Nombre de Filières</th>
                                <th class="text-center">Date de Création</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($secteurs as $secteur)
                                <tr>
                                    <td>
                                        <strong>{{ $secteur->nom_secteur }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $secteur->etablissement->nom_efp ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $secteur->filieres->count() }}</span>
                                    </td>
                                    <td class="text-center">
                                        <small class="text-muted">{{ $secteur->created_at ? $secteur->created_at->format('d/m/Y') : 'N/A' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('administration.etablissement.secteurs.show', $secteur->nom_secteur) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('administration.etablissement.secteurs.edit', $secteur->nom_secteur) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('administration.etablissement.secteurs.destroy', $secteur->nom_secteur) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce secteur ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger" 
                                                        title="Supprimer"
                                                        {{ $secteur->filieres->count() > 0 ? 'disabled' : '' }}>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $secteurs->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun secteur trouvé</h5>
                    <p class="text-muted">Commencez par créer votre premier secteur.</p>
                    <a href="{{ route('administration.etablissement.secteurs.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>Créer un Secteur
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection