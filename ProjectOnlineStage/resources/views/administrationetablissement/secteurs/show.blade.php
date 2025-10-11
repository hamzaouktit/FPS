@extends('layouts.app')

@section('title', 'Détails du Secteur')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">{{ $secteur->nom_secteur }}</h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-building me-2"></i>{{ $secteur->etablissement->nom_efp }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('administration.etablissement.secteurs.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                    <a href="{{ route('administration.etablissement.secteurs.edit', $secteur->nom_secteur) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Filières</h6>
                            <h3 class="mb-0">{{ $stats['total_filieres'] }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-stream fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Formations</h6>
                            <h3 class="mb-0">{{ $stats['total_formations'] }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-graduation-cap fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Groupes</h6>
                            <h3 class="mb-0">{{ $stats['total_groupes'] }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Stagiaires</h6>
                            <h3 class="mb-0">{{ $stats['total_stagiaires'] }}</h3>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filières -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-stream me-2"></i>Filières du Secteur</h5>
        </div>
        <div class="card-body">
            @if($secteur->filieres->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Code Filière</th>
                                <th>Nom de la Filière</th>
                                <th class="text-center">Formations</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($secteur->filieres as $filiere)
                                <tr>
                                    <td><code>{{ $filiere->code_filiere }}</code></td>
                                    <td><strong>{{ $filiere->nom_filiere }}</strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $filiere->formations_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-outline-primary" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune filière dans ce secteur</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Formations par Filière -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Formations</h5>
        </div>
        <div class="card-body">
            @if($secteur->formations->count() > 0)
                @foreach($secteur->filieres as $filiere)
                    @if($filiere->formations->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-stream me-2"></i>{{ $filiere->nom_filiere }}
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Année</th>
                                            <th>Niveau</th>
                                            <th>Type</th>
                                            <th>Créneau</th>
                                            <th class="text-center">Groupes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($filiere->formations as $formation)
                                            <tr>
                                                <td>{{ $formation->annee }}</td>
                                                <td>{{ $formation->niveau }}</td>
                                                <td>{{ $formation->type_formation }}</td>
                                                <td>{{ $formation->creneau }}</td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">{{ $formation->groupes->count() }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune formation dans ce secteur</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Groupes -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Groupes par Formation</h5>
        </div>
        <div class="card-body">
            @if($secteur->formations->count() > 0)
                @foreach($secteur->filieres as $filiere)
                    @foreach($filiere->formations as $formation)
                        @if($formation->groupes->count() > 0)
                            <div class="mb-4">
                                <h6 class="text-info mb-3">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    {{ $filiere->nom_filiere }} - {{ $formation->niveau }} ({{ $formation->annee }})
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Groupe</th>
                                                <th class="text-center">Effectif</th>
                                                <th>Sous-groupe</th>
                                                <th>Statut</th>
                                                <th class="text-center">Avancements</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($formation->groupes as $groupe)
                                                <tr>
                                                    <td><strong>{{ $groupe->groupe }}</strong></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-warning">{{ $groupe->effectif_groupe }}</span>
                                                    </td>
                                                    <td>{{ $groupe->sous_groupe ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($groupe->statut_sous_groupe)
                                                            <span class="badge bg-success">{{ $groupe->statut_sous_groupe }}</span>
                                                        @else
                                                            <span class="badge bg-secondary">Standard</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary">{{ $groupe->avancements_count ?? 0 }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            @else
                <div class="text-center py-4">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucun groupe dans ce secteur</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Informations Générales -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations Générales</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Code Établissement:</th>
                            <td><code>{{ $secteur->code_efp }}</code></td>
                        </tr>
                        <tr>
                            <th>Établissement:</th>
                            <td>{{ $secteur->etablissement->nom_efp }}</td>
                        </tr>
                        <tr>
                            <th>Date de création:</th>
                            <td>{{ $secteur->created_at->format('d/m/Y à H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th width="40%">Dernière modification:</th>
                            <td>{{ $secteur->updated_at->format('d/m/Y à H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Complexe:</th>
                            <td>{{ $secteur->etablissement->complexe->nom ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Statut:</th>
                            <td>
                                @if($secteur->filieres->count() > 0)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-warning">Sans filières</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection