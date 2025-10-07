@extends('layouts.app')

@section('title', 'Gestion des Établissements')

@section('content')
<div class="container-fluid px-4 py-3">
    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Gestion des Établissements</h1>
            <p class="text-muted mb-0">Complexe: {{ $complexe->nom }}</p>
        </div>
        <div>
            <a href="{{ route('administration.complexe.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
            <a href="{{ route('administration.complexe.etablissements.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nouvel Établissement
            </a>
        </div>
    </div>

    {{-- Messages de succès/erreur --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Liste des établissements --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-building"></i> Liste des Établissements
                </h5>
                <span class="badge bg-primary fs-6">
                    {{ $etablissements->total() }} établissement(s)
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($etablissements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 15%;">Code EFP</th>
                                <th style="width: 30%;">Nom de l'Établissement</th>
                                <th style="width: 20%;">Directeur</th>
                                <th style="width: 10%;" class="text-center">Formations</th>
                                <th style="width: 10%;" class="text-center">Groupes</th>
                                <th style="width: 10%;" class="text-center">Taux Réal.</th>
                                <th style="width: 5%;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($etablissements as $etablissement)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">{{ $etablissement->code_efp }}</span>
                                </td>
                                <td>
                                    <strong>{{ $etablissement->nom_efp }}</strong>
                                </td>
                                <td>
                                    @if($etablissement->user)
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle text-primary me-2"></i>
                                            <span>{{ $etablissement->user->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">Non assigné</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $etablissement->formations_count }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $etablissement->stats['nb_groupes'] ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $taux = $etablissement->stats['taux_realisation'] ?? 0;
                                        $class = $taux >= 80 ? 'success' : ($taux >= 50 ? 'warning' : 'danger');
                                    @endphp
                                    <span class="badge bg-{{ $class }}">{{ number_format($taux, 1) }}%</span>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('administration.complexe.etablissements.show', $etablissement->code_efp) }}">
                                                    <i class="bi bi-eye me-2"></i>Voir détails
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('administration.complexe.etablissements.edit', $etablissement->code_efp) }}">
                                                    <i class="bi bi-pencil me-2"></i>Modifier
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('administration.complexe.etablissements.destroy', $etablissement->code_efp) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet établissement ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i>Supprimer
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                    <p class="text-muted mb-3">Aucun établissement trouvé</p>
                    <a href="{{ route('administration.complexe.etablissements.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Créer un établissement
                    </a>
                </div>
            @endif
        </div>

        @if($etablissements->hasPages())
            <div class="card-footer bg-white border-top">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <div class="text-muted small">
                            Affichage de <strong>{{ $etablissements->firstItem() }}</strong> à <strong>{{ $etablissements->lastItem() }}</strong> 
                            sur <strong>{{ $etablissements->total() }}</strong> établissement(s)
                        </div>
                    </div>
                    <div class="col-md-6">
                        {{ $etablissements->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection