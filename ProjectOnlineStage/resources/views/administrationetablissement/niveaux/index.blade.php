@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Gestion des Niveaux</h1>
            <p class="text-muted mb-0">{{ $etablissement->nom_efp }}</p>
        </div>
        <a href="{{ route('administration.etablissement.niveaux.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Ajouter un niveau
        </a>
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
            @if($niveaux->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Niveau</th>
                                <th>Nombre de formations</th>
                                <th>Date de création</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($niveaux as $niveau)
                                <tr>
                                    <td>
                                        <strong>{{ $niveau->niveau }}</strong>
                                    </td>
                                    <td>
                                        @if($niveau->formations_count > 0)
                                            <span class="badge bg-info">
                                                {{ $niveau->formations_count }} formation(s)
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Aucune formation
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $niveau->created_at ? $niveau->created_at->format('d/m/Y') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('administration.etablissement.niveaux.show', $niveau->niveau) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('administration.etablissement.niveaux.edit', $niveau->niveau) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('administration.etablissement.niveaux.destroy', $niveau->niveau) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce niveau ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Supprimer">
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
            @else
                <div class="text-center py-5">
                    <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-3">Aucun niveau enregistré pour cet établissement.</p>
                    <a href="{{ route('administration.etablissement.niveaux.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Créer le premier niveau
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection