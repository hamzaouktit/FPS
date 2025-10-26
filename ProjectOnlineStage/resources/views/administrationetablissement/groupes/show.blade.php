@extends('layouts.app')

@section('title', 'Détails du Groupe - ' . $groupe->code_groupe)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.groupes.index') }}">
                <i class="fas fa-users"></i> Groupes
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-info-circle"></i> {{ $groupe->code_groupe }}
        </li>
    </ol>
</nav>
@endsection

@section('content')
<!-- En-tête avec informations principales -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fas fa-users me-2"></i>
                Groupe : {{ $groupe->code_groupe }}
                @if($groupe->statut === 'Actif')
                    <span class="badge bg-success ms-2">Actif</span>
                @else
                    <span class="badge bg-secondary ms-2">Inactif</span>
                @endif
            </h4>
            <div>
                <a href="{{ route('administration.etablissement.groupes.edit', $groupe->id) }}" 
                   class="btn btn-warning btn-sm">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
                <a href="{{ route('administration.etablissement.groupes.index') }}" 
                   class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong><i class="fas fa-hashtag me-2 text-primary"></i>Code Groupe :</strong> 
                    {{ $groupe->code_groupe }}
                </p>
                <p><strong><i class="fas fa-users me-2 text-primary"></i>Sous-groupe :</strong> 
                    {{ $groupe->sous_groupe }}
                    <span class="badge bg-{{ $groupe->statut_sous_groupe === 'Actif' ? 'success' : 'secondary' }} ms-2">
                        {{ $groupe->statut_sous_groupe }}
                    </span>
                </p>
                <p><strong><i class="fas fa-user-graduate me-2 text-primary"></i>Effectif :</strong> 
                    <span class="badge bg-success">{{ $groupe->effectif_groupe }} stagiaires</span>
                </p>
                <p><strong><i class="fas fa-calendar me-2 text-primary"></i>Année Formation :</strong> 
                    <span class="badge bg-primary">{{ $groupe->annee_formation }}</span>
                </p>
            </div>
            <div class="col-md-6">
                <p><strong><i class="fas fa-building me-2 text-primary"></i>Établissement :</strong> 
                    {{ $groupe->etablissement->nom_efp ?? 'N/A' }}
                </p>
                <p><strong><i class="fas fa-clock me-2 text-primary"></i>Créé le :</strong> 
                    {{ $groupe->created_at->format('d/m/Y H:i') }}
                </p>
                <p><strong><i class="fas fa-clock me-2 text-primary"></i>Modifié le :</strong> 
                    {{ $groupe->updated_at->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques globales -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="fas fa-book fa-2x text-primary mb-3"></i>
                <h3 class="mb-0">{{ $stats['total_modules'] ?? 0 }}</h3>
                <p class="text-muted mb-0">Modules</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="fas fa-chalkboard-teacher fa-2x text-success mb-3"></i>
                <h3 class="mb-0">{{ $stats['total_formateurs'] ?? 0 }}</h3>
                <p class="text-muted mb-0">Formateurs</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="fas fa-clock fa-2x text-info mb-3"></i>
                <h3 class="mb-0">{{ number_format($stats['mh_totale_affectee'] ?? 0, 2) }}</h3>
                <p class="text-muted mb-0">MH Affectée</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="fas fa-check-circle fa-2x text-warning mb-3"></i>
                <h3 class="mb-0">{{ number_format($stats['mh_totale_realisee'] ?? 0, 2) }}</h3>
                <p class="text-muted mb-0">MH Réalisée</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Informations sur la formation -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Informations Formation
                </h5>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Filière</dt>
                    <dd class="col-sm-8">{{ $groupe->filiere->nom_filiere ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">Code Filière</dt>
                    <dd class="col-sm-8">{{ $groupe->filiere->code_filiere ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">Secteur</dt>
                    <dd class="col-sm-8">{{ $groupe->filiere->secteur->nom_secteur ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">Niveau</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-secondary">{{ $groupe->formation->niveau->nom ?? 'N/A' }}</span>
                    </dd>

                    <dt class="col-sm-4">Type Formation</dt>
                    <dd class="col-sm-8">{{ $groupe->formation->type ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">Mode</dt>
                    <dd class="col-sm-8">{{ $groupe->formation->mode ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">Créneau</dt>
                    <dd class="col-sm-8">{{ $groupe->formation->creneau ?? 'N/A' }}</dd>

                    <dt class="col-sm-4">Année Formation</dt>
                    <dd class="col-sm-8">{{ $groupe->formation->annee ?? 'N/A' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Affectations et modules -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-tasks me-2"></i>
                    Modules Affectés ({{ $stats['total_affectations'] ?? 0 }})
                </h5>
            </div>
            <div class="card-body">
                @if($groupe->affectations && $groupe->affectations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Module</th>
                                    <th>Code</th>
                                    <th>Formateur</th>
                                    <th>MH Affectée</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupe->affectations as $affectation)
                                <tr>
                                    <td>
                                        <strong>{{ $affectation->module->nom_module ?? 'N/A' }}</strong>
                                        @if($affectation->fusion_groupe)
                                            <br><small class="text-muted"><i class="fas fa-object-group"></i> {{ $affectation->fusion_groupe }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $affectation->module->code_module ?? 'N/A' }}</code>
                                        @if($affectation->code_fusion)
                                            <br><small class="badge bg-warning">{{ $affectation->code_fusion }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($affectation->formateurPresentiel)
                                            <small>{{ $affectation->formateurPresentiel->nom_complet }}</small>
                                        @elseif($affectation->formateurSyn)
                                            <small>{{ $affectation->formateurSyn->nom_complet }}</small>
                                        @else
                                            <span class="text-muted">Non affecté</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ number_format($affectation->mh_affectee_globale, 2) }}h</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        Aucun module affecté à ce groupe.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Détails des affectations -->
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-list-alt me-2"></i>
            Détails des Affectations et Avancements
        </h5>
    </div>
    <div class="card-body">
        @if($groupe->affectations && $groupe->affectations->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Module</th>
                            <th>Fusion</th>
                            <th>Formateur Présentiel</th>
                            <th>Formateur Synchrone</th>
                            <th>MH Affectée</th>
                            <th>MH Réalisée</th>
                            <th>Taux</th>
                            <th>Moy. Absence</th>
                            <th>NB CC</th>
                            <th>EFM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupe->affectations as $affectation)
                            <tr>
                                <td>
                                    <strong>{{ $affectation->module->nom_module ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $affectation->module->code_module ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    @if($affectation->fusion_groupe)
                                        <span class="badge bg-info">{{ $affectation->fusion_groupe }}</span>
                                        @if($affectation->code_fusion)
                                            <br><small>{{ $affectation->code_fusion }}</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($affectation->formateurPresentiel)
                                        {{ $affectation->formateurPresentiel->nom_complet }}
                                        <br>
                                        <small class="text-muted">MLE: {{ $affectation->mle_affecte_presentiel }}</small>
                                    @else
                                        <span class="text-muted">Non affecté</span>
                                    @endif
                                </td>
                                <td>
                                    @if($affectation->formateurSyn)
                                        {{ $affectation->formateurSyn->nom_complet }}
                                        <br>
                                        <small class="text-muted">MLE: {{ $affectation->mle_affecte_syn }}</small>
                                    @else
                                        <span class="text-muted">Non affecté</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ number_format($affectation->mh_affectee_globale, 2) }}h</span>
                                </td>
                                <td>
                                    @if($affectation->avancement)
                                        <span class="badge bg-{{ $affectation->avancement->mh_realisee_globale > 0 ? 'success' : 'secondary' }}">
                                            {{ number_format($affectation->avancement->mh_realisee_globale, 2) }}h
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">0h</span>
                                    @endif
                                </td>
                                <td>
                                    @if($affectation->avancement)
                                        <span class="badge bg-{{ $affectation->avancement->taux_realisation_globale >= 50 ? 'success' : 'warning' }}">
                                            {{ number_format($affectation->avancement->taux_realisation_globale, 2) }}%
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">0%</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $affectation->avancement ? number_format($affectation->avancement->moyenne_absence, 2) : 'N/A' }}
                                </td>
                                <td>
                                    {{ $affectation->avancement->nb_cc ?? '0' }}
                                </td>
                                <td>
                                    @if($affectation->avancement)
                                        <span class="badge bg-{{ $affectation->avancement->seance_efm === 'Oui' ? 'success' : 'secondary' }}">
                                            {{ $affectation->avancement->seance_efm }}
                                        </span>
                                        <br>
                                        <small class="badge bg-{{ $affectation->avancement->validation_efm === 'oui' ? 'success' : 'secondary' }}">
                                            Val: {{ $affectation->avancement->validation_efm }}
                                        </small>
                                    @else
                                        <span class="badge bg-secondary">Non</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-2"></i>
                <p class="mb-0">Aucune affectation trouvée pour ce groupe.</p>
            </div>
        @endif
    </div>
</div>
@endsection