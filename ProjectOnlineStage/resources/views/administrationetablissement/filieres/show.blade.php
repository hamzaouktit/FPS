@extends('layouts.app')

@section('title', 'Détails de la filière')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ $filiere->nom }}</h2>
            <p class="text-muted mb-0">
                <i class="bi bi-tag"></i> Code: <strong>{{ $filiere->code }}</strong>
            </p>
        </div>
        <div>
            <a href="{{ route('administration.etablissement.filieres.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
            <a href="{{ route('administration.etablissement.filieres.edit', $filiere->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Modifier
            </a>
        </div>
    </div>

    <!-- Informations générales -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">
                <i class="bi bi-info-circle text-primary"></i> Informations générales
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item">
                        <strong><i class="bi bi-building"></i> Secteur:</strong> 
                        <span class="badge bg-secondary ms-2">{{ $filiere->secteur->nom ?? 'Non défini' }}</span>
                    </div>
                    <div class="info-item mt-2">
                        <strong><i class="bi bi-hash"></i> Code secteur:</strong> 
                        <span>{{ $filiere->secteur->code ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <strong><i class="bi bi-bar-chart-steps"></i> Niveau:</strong> 
                        <span class="badge bg-info ms-2">{{ $filiere->niveau->nom ?? 'Non défini' }}</span>
                    </div>
                    <div class="info-item mt-2">
                        <strong><i class="bi bi-hash"></i> Code niveau:</strong> 
                        <span>{{ $filiere->niveau->code ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <hr class="my-3">
            <div class="row text-muted small">
                <div class="col-md-6">
                    <i class="bi bi-calendar-plus"></i> Créée le: {{ $filiere->created_at->format('d/m/Y à H:i') }}
                </div>
                <div class="col-md-6">
                    <i class="bi bi-calendar-check"></i> Modifiée le: {{ $filiere->updated_at->format('d/m/Y à H:i') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card stat-primary">
                <div class="card-body text-center">
                    <div class="stat-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h3 class="stat-number mb-1">{{ $stats['total_groupes'] }}</h3>
                    <p class="stat-label mb-2">Groupes totaux</p>
                    <small class="stat-detail">
                        <span class="badge bg-success">{{ $stats['groupes_actifs'] }} actifs</span>
                        <span class="badge bg-secondary">{{ $stats['total_groupes'] - $stats['groupes_actifs'] }} inactifs</span>
                    </small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card stat-success">
                <div class="card-body text-center">
                    <div class="stat-icon">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <h3 class="stat-number mb-1">{{ $stats['effectif_total'] }}</h3>
                    <p class="stat-label mb-2">Effectif total</p>
                    <small class="stat-detail">Stagiaires inscrits</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card stat-info">
                <div class="card-body text-center">
                    <div class="stat-icon">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <h3 class="stat-number mb-1">{{ $stats['total_modules'] }}</h3>
                    <p class="stat-label mb-2">Modules</p>
                    <small class="stat-detail">
                        <span class="badge bg-info">{{ $stats['modules_regionaux'] }} régionaux</span>
                        @if($stats['modules_pie'] > 0)
                        <span class="badge bg-warning">{{ $stats['modules_pie'] }} PIE</span>
                        @endif
                    </small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm stat-card stat-warning">
                <div class="card-body text-center">
                    <div class="stat-icon">
                        <i class="bi bi-person-workspace"></i>
                    </div>
                    <h3 class="stat-number mb-1">{{ $formateurs->count() }}</h3>
                    <p class="stat-label mb-2">Formateurs</p>
                    <small class="stat-detail">Intervenants actifs</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques d'avancement -->
    @if($avancementStats && $avancementStats->total_mh_affectees > 0)
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <i class="bi bi-graph-up text-success"></i> Statistiques d'avancement global
            </h5>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="avancement-box">
                        <div class="avancement-label">MH Affectées</div>
                        <div class="avancement-value text-primary">{{ number_format($avancementStats->total_mh_affectees, 2) }}h</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="avancement-box">
                        <div class="avancement-label">MH Réalisées</div>
                        <div class="avancement-value text-success">{{ number_format($avancementStats->total_mh_realisees, 2) }}h</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="avancement-box">
                        <div class="avancement-label">Taux moyen</div>
                        <div class="avancement-value">
                            @php
                                $tauxGlobal = $avancementStats->taux_moyen;
                                $colorClass = $tauxGlobal >= 75 ? 'text-success' : ($tauxGlobal >= 50 ? 'text-warning' : 'text-danger');
                            @endphp
                            <span class="{{ $colorClass }}">{{ number_format($tauxGlobal, 2) }}%</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="avancement-box">
                        <div class="avancement-label">Moyenne absences</div>
                        <div class="avancement-value text-danger">{{ number_format($avancementStats->moyenne_absence_globale, 2) }}%</div>
                    </div>
                </div>
            </div>
            <hr class="my-3">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="avancement-box">
                        <div class="avancement-label">Total CC</div>
                        <div class="avancement-value">
                            <span class="badge bg-primary fs-5">{{ $avancementStats->total_cc }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="avancement-box">
                        <div class="avancement-label">EFM passés</div>
                        <div class="avancement-value">
                            <span class="badge bg-info fs-5">{{ $avancementStats->total_efm_passes }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="avancement-box">
                        <div class="avancement-label">EFM validés</div>
                        <div class="avancement-value">
                            <span class="badge bg-success fs-5">{{ $avancementStats->total_efm_valides }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Tabs pour les différentes sections -->
    <ul class="nav nav-tabs custom-tabs mb-3" id="filiereTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="groupes-tab" data-bs-toggle="tab" data-bs-target="#groupes" type="button">
                <i class="bi bi-people"></i> Groupes <span class="badge bg-primary ms-1">{{ $stats['total_groupes'] }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="modules-tab" data-bs-toggle="tab" data-bs-target="#modules" type="button">
                <i class="bi bi-journal-bookmark"></i> Modules <span class="badge bg-secondary ms-1">{{ $stats['total_modules'] }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="formateurs-tab" data-bs-toggle="tab" data-bs-target="#formateurs" type="button">
                <i class="bi bi-person-workspace"></i> Formateurs <span class="badge bg-secondary ms-1">{{ $formateurs->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="stats-tab" data-bs-toggle="tab" data-bs-target="#stats" type="button">
                <i class="bi bi-bar-chart"></i> Statistiques détaillées
            </button>
        </li>
        @if($progressionModules->count() > 0)
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="progression-tab" data-bs-toggle="tab" data-bs-target="#progression" type="button">
                <i class="bi bi-graph-up-arrow"></i> Progression par module
            </button>
        </li>
        @endif
    </ul>

    <div class="tab-content" id="filiereTabContent">
        <!-- TAB 1: GROUPES -->
        <div class="tab-pane fade show active" id="groupes" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-list-ul"></i> Liste des groupes
                        </h5>
                        <span class="text-muted">Total: {{ $groupes->count() }}</span>
                    </div>
                    
                    @if($groupes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover custom-table">
                                <thead>
                                    <tr>
                                        <th><i class="bi bi-hash"></i> Code</th>
                                        <th><i class="bi bi-building"></i> EFP</th>
                                        <th><i class="bi bi-people"></i> Effectif</th>
                                        <th><i class="bi bi-calendar"></i> Année</th>
                                        <th><i class="bi bi-mortarboard"></i> Type</th>
                                        <th><i class="bi bi-shuffle"></i> Mode</th>
                                        <th><i class="bi bi-clock"></i> Créneau</th>
                                        <th><i class="bi bi-check-circle"></i> Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groupes as $groupe)
                                    <tr>
                                        <td><strong class="text-primary">{{ $groupe->code }}</strong></td>
                                        <td>{{ $groupe->efp_nom }}</td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-person"></i> {{ $groupe->effectif }}
                                            </span>
                                        </td>
                                        <td>Année {{ $groupe->annee_formation }}</td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $groupe->formation->type ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $groupe->formation->mode ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>{{ $groupe->formation->creneau ?? 'N/A' }}</td>
                                        <td>
                                            @if($groupe->statut === 'Actif')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle-fill"></i> Actif
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-x-circle-fill"></i> Inactif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <p class="text-muted mt-3">Aucun groupe trouvé pour cette filière.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 2: MODULES -->
        <div class="tab-pane fade" id="modules" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-journal-bookmark"></i> Liste des modules
                        </h5>
                        <span class="text-muted">Total: {{ $modules->count() }}</span>
                    </div>
                    
                    @if($modules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover custom-table">
                                <thead>
                                    <tr>
                                        <th><i class="bi bi-hash"></i> Code</th>
                                        <th><i class="bi bi-file-text"></i> Nom</th>
                                        <th><i class="bi bi-globe"></i> Régional</th>
                                        <th><i class="bi bi-star"></i> Module PIE</th>
                                        <th><i class="bi bi-link-45deg"></i> Affectations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modules as $module)
                                    <tr>
                                        <td><strong class="text-primary">{{ $module->code }}</strong></td>
                                        <td>{{ $module->nom }}</td>
                                        <td>
                                            @if($module->regional === 'O')
                                                <span class="badge bg-info">
                                                    <i class="bi bi-check-lg"></i> Oui
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-x-lg"></i> Non
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($module->module_pie)
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-star-fill"></i> PIE
                                                </span>
                                                @if($module->efp_pie)
                                                    <br><small class="text-muted">{{ $module->efp_pie }}</small>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Non</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $module->nb_affectations }} affectation(s)
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <p class="text-muted mt-3">Aucun module trouvé pour cette filière.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 3: FORMATEURS -->
        <div class="tab-pane fade" id="formateurs" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-workspace"></i> Formateurs intervenant dans cette filière
                        </h5>
                        <span class="text-muted">Total: {{ $formateurs->count() }}</span>
                    </div>
                    
                    @if($formateurs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover custom-table">
                                <thead>
                                    <tr>
                                        <th><i class="bi bi-person-badge"></i> Matricule</th>
                                        <th><i class="bi bi-person"></i> Nom complet</th>
                                        <th><i class="bi bi-briefcase"></i> Type</th>
                                        <th><i class="bi bi-building"></i> Secteurs</th>
                                        <th><i class="bi bi-link-45deg"></i> Affectations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($formateurs as $formateur)
                                    <tr>
                                        <td><strong class="text-primary">{{ $formateur->mle }}</strong></td>
                                        <td>{{ $formateur->nom_complet }}</td>
                                        <td>
                                            @if($formateur->type === 'permanent')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-person-check-fill"></i> Permanent
                                                </span>
                                            @else
                                                <span class="badge bg-info">
                                                    <i class="bi bi-person-fill"></i> Vacataire
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @forelse($formateur->secteurs as $secteur)
                                                <span class="badge bg-secondary me-1">{{ $secteur->code }}</span>
                                            @empty
                                                <span class="text-muted">Aucun</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $formateur->nb_affectations }} intervention(s)
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <p class="text-muted mt-3">Aucun formateur affecté à cette filière.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 4: STATISTIQUES DÉTAILLÉES -->
        <div class="tab-pane fade" id="stats" role="tabpanel">
            <div class="row g-4">
                <!-- Groupes par année -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-calendar3"></i> Répartition par année de formation
                            </h5>
                            @if($groupesParAnnee->count() > 0)
                                <div class="table-responsive mt-3">
                                    <table class="table table-sm custom-table">
                                        <thead>
                                            <tr>
                                                <th>Année</th>
                                                <th>Nb groupes</th>
                                                <th>Effectif</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($groupesParAnnee as $item)
                                            <tr>
                                                <td><strong>Année {{ $item->annee_formation }}</strong></td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $item->total }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">{{ $item->effectif_total }}</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0 mt-3">Aucune donnée disponible</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Groupes par type -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-mortarboard"></i> Répartition par type de formation
                            </h5>
                            @if($groupesParType->count() > 0)
                                <div class="table-responsive mt-3">
                                    <table class="table table-sm custom-table">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Nb groupes</th>
                                                <th>Effectif</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($groupesParType as $item)
                                            <tr>
                                                <td><strong>{{ $item->type }}</strong></td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $item->total }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">{{ $item->effectif_total }}</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0 mt-3">Aucune donnée disponible</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Groupes par mode -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-shuffle"></i> Répartition par mode de formation
                            </h5>
                            @if($groupesParMode->count() > 0)
                                <div class="table-responsive mt-3">
                                    <table class="table table-sm custom-table">
                                        <thead>
                                            <tr>
                                                <th>Mode</th>
                                                <th>Nb groupes</th>
                                                <th>Effectif</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($groupesParMode as $item)
                                            <tr>
                                                <td><strong>{{ $item->mode }}</strong></td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $item->total }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">{{ $item->effectif_total }}</span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0 mt-3">Aucune donnée disponible</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Récapitulatif modules -->
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <i class="bi bi-journal-bookmark"></i> Récapitulatif modules
                            </h5>
                            <ul class="list-group list-group-flush mt-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-journal-text"></i> Total modules</span>
                                    <strong><span class="badge bg-primary">{{ $stats['total_modules'] }}</span></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-globe"></i> Modules régionaux</span>
                                    <strong><span class="badge bg-info">{{ $stats['modules_regionaux'] }}</span></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-star"></i> Modules PIE</span>
                                    <strong><span class="badge bg-warning">{{ $stats['modules_pie'] }}</span></strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-house"></i> Modules locaux</span>
                                    <strong><span class="badge bg-secondary">{{ $stats['total_modules'] - $stats['modules_regionaux'] }}</span></strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 5: PROGRESSION PAR MODULE -->
        @if($progressionModules->count() > 0)
        <div class="tab-pane fade" id="progression" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-graph-up-arrow"></i> Progression par module
                        </h5>
                        <span class="text-muted">{{ $progressionModules->count() }} module(s)</span>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover custom-table">
                            <thead>
                                <tr>
                                    <th><i class="bi bi-hash"></i> Code</th>
                                    <th><i class="bi bi-file-text"></i> Nom du module</th>
                                    <th><i class="bi bi-people"></i> Nb groupes</th>
                                    <th><i class="bi bi-clock-history"></i> MH réalisées</th>
                                    <th><i class="bi bi-percent"></i> Taux moyen</th>
                                    <th style="width: 30%;"><i class="bi bi-bar-chart"></i> Progression</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($progressionModules as $progression)
                                <tr>
                                    <td><strong class="text-primary">{{ $progression->code }}</strong></td>
                                    <td>{{ $progression->nom }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-people-fill"></i> {{ $progression->nb_groupes }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($progression->total_mh_realisees, 2) }}h</strong>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($progression->taux_moyen, 2) }}%</strong>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 25px;">
                                            @php
                                                $taux = min($progression->taux_moyen, 100);
                                                $colorClass = 'bg-danger';
                                                if($taux >= 75) {
                                                    $colorClass = 'bg-success';
                                                } elseif($taux >= 50) {
                                                    $colorClass = 'bg-warning';
                                                }
                                            @endphp
                                            <div class="progress-bar {{ $colorClass }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $taux }}%"
                                                 aria-valuenow="{{ $taux }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                <strong>{{ number_format($taux, 1) }}%</strong>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Légende -->
                    <div class="alert alert-light mt-3 mb-0">
                        <strong><i class="bi bi-info-circle"></i> Légende:</strong>
                        <span class="ms-3">
                            <span class="badge bg-danger">< 50%</span> Critique
                        </span>
                        <span class="ms-2">
                            <span class="badge bg-warning">50-75%</span> En cours
                        </span>
                        <span class="ms-2">
                            <span class="badge bg-success">≥ 75%</span> Bon
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    /* Variables personnalisées */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    /* Info items */
    .info-item {
        padding: 8px 0;
    }

    /* Cartes statistiques */
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-left: 4px solid;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
    }

    .stat-card.stat-primary {
        border-left-color: #667eea;
    }

    .stat-card.stat-success {
        border-left-color: #43e97b;
    }

    .stat-card.stat-info {
        border-left-color: #4facfe;
    }

    .stat-card.stat-warning {
        border-left-color: #fa709a;
    }

    .stat-icon {
        font-size: 2.5rem;
        margin-bottom: 10px;
        opacity: 0.3;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
    }

    .stat-label {
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #718096;
        font-weight: 600;
    }

    .stat-detail {
        color: #a0aec0;
    }

    /* Boxes d'avancement */
    .avancement-box {
        padding: 15px;
        background: #f7fafc;
        border-radius: 8px;
        text-align: center;
        border: 1px solid #e2e8f0;
    }

    .avancement-label {
        font-size: 0.85rem;
        color: #718096;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .avancement-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
    }

    /* Onglets personnalisés */
    .custom-tabs {
        border-bottom: 2px solid #e2e8f0;
    }

    .custom-tabs .nav-link {
        color: #718096;
        border: none;
        padding: 12px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
        border-bottom: 3px solid transparent;
    }

    .custom-tabs .nav-link:hover {
        color: #667eea;
        background-color: #f7fafc;
    }

    .custom-tabs .nav-link.active {
        color: #667eea;
        background-color: transparent;
        border-bottom-color: #667eea;
        font-weight: 600;
    }

    .custom-tabs .nav-link i {
        margin-right: 5px;
    }

    .custom-tabs .badge {
        font-size: 0.75rem;
    }

    /* Tableaux personnalisés */
    .custom-table {
        margin-bottom: 0;
    }

    .custom-table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        padding: 15px 12px;
        white-space: nowrap;
    }

    .custom-table thead th i {
        margin-right: 5px;
        opacity: 0.8;
    }

    .custom-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .custom-table tbody tr:hover {
        background-color: #f7fafc;
    }

    .custom-table tbody td {
        padding: 12px;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }

    /* État vide */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        opacity: 0.3;
    }

    /* Badges personnalisés */
    .badge {
        font-size: 0.85rem;
        padding: 0.4em 0.75em;
        font-weight: 500;
    }

    /* Barres de progression */
    .progress {
        background-color: #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
    }

    .progress-bar {
        font-weight: 600;
        transition: width 0.6s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Cartes */
    .card {
        border-radius: 12px;
    }

    .card-title {
        color: #2d3748;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .card-title i {
        margin-right: 8px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stat-number {
            font-size: 2rem;
        }

        .stat-icon {
            font-size: 2rem;
        }

        .avancement-value {
            font-size: 1.2rem;
        }

        .custom-tabs .nav-link {
            padding: 10px 15px;
            font-size: 0.9rem;
        }

        .table-responsive {
            font-size: 0.85rem;
        }
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .tab-pane {
        animation: fadeIn 0.4s ease;
    }

    /* Liste group personnalisée */
    .list-group-item {
        border: none;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 0;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    .list-group-item i {
        margin-right: 8px;
        opacity: 0.7;
    }

    /* Alert personnalisé */
    .alert {
        border-radius: 8px;
        border: none;
    }

    .alert-light {
        background-color: #f7fafc;
        border-left: 4px solid #667eea;
    }

    /* Ombres */
    .shadow-sm {
        box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.075) !important;
    }

    /* Scrollbar personnalisée */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #667eea;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #764ba2;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activer les tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Animation des barres de progression
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        });

        // Animation des nombres
        const statNumbers = document.querySelectorAll('.stat-number');
        statNumbers.forEach(element => {
            const finalValue = parseInt(element.textContent);
            if (!isNaN(finalValue)) {
                animateValue(element, 0, finalValue, 1000);
            }
        });

        function animateValue(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                element.textContent = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Mémoriser l'onglet actif
        const tabLinks = document.querySelectorAll('.custom-tabs .nav-link');
        const activeTab = localStorage.getItem('activeTab');
        
        if (activeTab) {
            const tab = document.querySelector(`[data-bs-target="${activeTab}"]`);
            if (tab) {
                const bsTab = new bootstrap.Tab(tab);
                bsTab.show();
            }
        }

        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const target = this.getAttribute('data-bs-target');
                localStorage.setItem('activeTab', target);
            });
        });

        // Effet hover sur les lignes de tableau
        const tableRows = document.querySelectorAll('.custom-table tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.01)';
                this.style.transition = 'transform 0.2s ease';
            });
            row.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Impression
        const printButton = document.getElementById('printButton');
        if (printButton) {
            printButton.addEventListener('click', function() {
                window.print();
            });
        }
    });

    // Fonction pour exporter en PDF (nécessite une bibliothèque comme jsPDF)
    function exportToPDF() {
        alert('Fonctionnalité d\'export PDF à venir !');
    }

    // Fonction pour exporter en Excel (nécessite une bibliothèque comme SheetJS)
    function exportToExcel() {
        alert('Fonctionnalité d\'export Excel à venir !');
    }
</script>
@endpush

@section('page-styles')
<style>
    /* Styles d'impression */
    @media print {
        .btn, .custom-tabs, .sidebar {
            display: none !important;
        }

        .card {
            break-inside: avoid;
            box-shadow: none !important;
            border: 1px solid #dee2e6 !important;
        }

        .stat-card {
            page-break-inside: avoid;
        }

        .progress {
            print-color-adjust: exact;
            -webkit-print-color-adjust: exact;
        }

        body {
            font-size: 12pt;
        }

        .table {
            font-size: 10pt;
        }
    }
</style>
@endsection

@endsection