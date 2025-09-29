@extends('layouts.app')

@section('title', 'Dashboard Établissement')

@section('styles')
<style>
    .filter-card { transition: all 0.3s ease; }
    .filter-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important; }
    .chart-container { position: relative; height: 300px; }
    .stat-card { border-left: 4px solid; }
    .stat-card.primary { border-left-color: #0d6efd; }
    .stat-card.success { border-left-color: #198754; }
    .stat-card.warning { border-left-color: #ffc107; }
    .stat-card.danger { border-left-color: #dc3545; }
    .table-responsive { max-height: 600px; overflow-y: auto; }
    .table thead th { position: sticky; top: 0; background-color: #f8f9fa; z-index: 10; }
    .badge-taux {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
</style>
@endsection

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><i class="fas fa-home me-1"></i><a href="{{ route('welcome') }}">Accueil</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard Établissement</li>
    </ol>
</nav>
@endsection

@section('content')
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-0">
                    <i class="fas fa-university text-primary me-2"></i>
                    Tableau de Bord - Établissement
                </h1>
                <p class="text-muted mb-0">Gestion et supervision de l'établissement de formation</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('administration.etablissement.import') }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i>
                    Importer Excel
                </a>
                <span class="badge bg-success fs-6">
                    <i class="fas fa-clock me-1"></i>
                    {{ date('d/m/Y H:i') }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Messages d'alerte -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('warning'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    {{ session('warning') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Informations Utilisateur -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="avatar-container me-3">
                                <div class="avatar bg-success text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px;">
                                    <i class="fas fa-school fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-1">Bienvenue, {{ $user->nom }}</h4>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-university me-2"></i>
                                    <strong>Établissement :</strong> {{ $etablissement->nom_efp ?? 'Non défini' }}
                                </p>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-code me-2"></i>
                                    <strong>Code EFP :</strong> {{ $etablissement->code_efp ?? 'Non défini' }}
                                </p>
                                <span class="badge bg-success">
                                    <i class="fas fa-school me-1"></i>
                                    Directeur d'Établissement
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="d-flex flex-column align-items-md-end">
                            <div class="mb-2">
                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                <small class="text-muted">Dernière connexion</small>
                            </div>
                            <div class="text-success fw-bold">
                                {{ date('d/m/Y à H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques rapides -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                        <i class="fas fa-graduation-cap fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">{{ $stats['formations'] ?? 0 }}</div>
                    <div class="text-muted">Formations</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                        <i class="fas fa-users fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">{{ $stats['apprenants'] ?? 0 }}</div>
                    <div class="text-muted">Apprenants</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                        <i class="fas fa-chalkboard-teacher fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">{{ $stats['formateurs'] ?? 0 }}</div>
                    <div class="text-muted">Formateurs</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                        <i class="fas fa-layer-group fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">{{ $stats['groupes'] ?? 0 }}</div>
                    <div class="text-muted">Groupes</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm filter-card">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0">
                    <i class="fas fa-filter text-primary me-2"></i>
                    Filtres d'Analyse
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('administration.etablissement.dashboard') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-chalkboard-teacher me-1"></i> Formateur</label>
                            <select name="formateur" class="form-select" id="formateur-select">
                                <option value="">Tous les formateurs</option>
                                @foreach($filterOptions['formateurs'] as $formateur)
                                    <option value="{{ $formateur->mle }}" {{ $filters['formateur'] == $formateur->mle ? 'selected' : '' }}>
                                        {{ $formateur->nom_formateur }} ({{ $formateur->mle }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-book me-1"></i> Module</label>
                            <select name="module" id="module-select" class="form-select">
                                <option value="">Tous les modules</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-users me-1"></i> Groupe</label>
                            <select name="groupe" id="groupe-select" class="form-select">
                                <option value="">Tous les groupes</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-industry me-1"></i> Secteur</label>
                            <select name="secteur" id="secteur-select" class="form-select">
                                <option value="">Tous les secteurs</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-stream me-1"></i> Filière</label>
                            <select name="filiere" id="filiere-select" class="form-select">
                                <option value="">Toutes les filières</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><i class="fas fa-layer-group me-1"></i> Niveau</label>
                            <select name="niveau" id="niveau-select" class="form-select">
                                <option value="">Tous les niveaux</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>
                                Appliquer les filtres
                            </button>
                            <a href="{{ route('administration.etablissement.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques des Heures -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0">
                    <i class="fas fa-clock text-info me-2"></i>
                    Analyse des Heures de Formation
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-3">
                        <div class="stat-card primary card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="text-muted mb-0">Heures Requises</h6>
                                        <h3 class="mb-0 mt-2">{{ number_format($heuresData['heures_requises'], 0) }}</h3>
                                    </div>
                                    <div class="bg-primary bg-opacity-10 p-2 rounded">
                                        <i class="fas fa-tasks text-primary fs-4"></i>
                                    </div>
                                </div>
                                <small class="text-muted">Heures DRIF totales</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card success card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="text-muted mb-0">Heures Affectées</h6>
                                        <h3 class="mb-0 mt-2">{{ number_format($heuresData['heures_affectees'], 0) }}</h3>
                                    </div>
                                    <div class="bg-success bg-opacity-10 p-2 rounded">
                                        <i class="fas fa-user-check text-success fs-4"></i>
                                    </div>
                                </div>
                                <small class="text-{{ $heuresData['difference_affectees'] < 0 ? 'success' : 'danger' }}">
                                    {{ $heuresData['difference_affectees'] >= 0 ? '-' : '+' }}{{ number_format(abs($heuresData['difference_affectees']), 0) }} h
                                    ({{ $heuresData['taux_affectation'] }}%)
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card warning card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="text-muted mb-0">Heures Réalisées</h6>
                                        <h3 class="mb-0 mt-2">{{ number_format($heuresData['heures_realisees'], 0) }}</h3>
                                    </div>
                                    <div class="bg-warning bg-opacity-10 p-2 rounded">
                                        <i class="fas fa-check-circle text-warning fs-4"></i>
                                    </div>
                                </div>
                                <small class="text-{{ $heuresData['difference_realisees'] < 0 ? 'success' : 'danger' }}">
                                    {{ $heuresData['difference_realisees'] >= 0 ? '-' : '+' }}{{ number_format(abs($heuresData['difference_realisees']), 0) }} h
                                    ({{ $heuresData['taux_realisation'] }}%)
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card danger card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="text-muted mb-0">Différence</h6>
                                        <h3 class="mb-0 mt-2 text-{{ $heuresData['difference_realisees'] > 0 ? 'danger' : 'success' }}">
                                            {{ number_format(abs($heuresData['difference_realisees']), 0) }}
                                        </h3>
                                    </div>
                                    <div class="bg-danger bg-opacity-10 p-2 rounded">
                                        <i class="fas fa-exclamation-triangle text-danger fs-4"></i>
                                    </div>
                                </div>
                                <small class="text-muted">Heures restantes</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Barre de progression -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Progression globale</span>
                        <span class="fw-bold">{{ $heuresData['taux_realisation'] }}%</span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ min(100, $heuresData['taux_realisation']) }}%"
                             aria-valuenow="{{ $heuresData['taux_realisation'] }}" 
                             aria-valuemin="0" aria-valuemax="100">
                            {{ $heuresData['taux_realisation'] }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tableau détaillé des données -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-table text-primary me-2"></i>
                    Données Détaillées par Groupe et Module
                </h5>
                <span class="badge bg-primary">{{ count($tableauDetaille) }} enregistrements</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Groupe</th>
                                <th>Module</th>
                                <th>Secteur</th>
                                <th>Filière</th>
                                <th>Niveau</th>
                                <th>Formateur Présentiel</th>
                                <th>Formateur Synchrone</th>
                                <th class="text-center">H. Requises</th>
                                <th class="text-center">H. Affectées</th>
                                <th class="text-center">H. Réalisées</th>
                                <th class="text-center">Taux (%)</th>
                                <th class="text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tableauDetaille as $index => $ligne)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td><strong>{{ $ligne->groupe }}</strong></td>
                                <td>
                                    <div class="text-truncate" style="max-width: 250px;" title="{{ $ligne->nom_module }}">
                                        {{ $ligne->nom_module }}
                                    </div>
                                    <small class="text-muted">{{ $ligne->code_module }}</small>
                                </td>
                                <td>{{ $ligne->nom_secteur }}</td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $ligne->nom_filiere }}">
                                        {{ $ligne->nom_filiere }}
                                    </div>
                                </td>
                                <td><span class="badge bg-secondary">{{ $ligne->niveau }}</span></td>
                                <td>
                                    @if($ligne->formateur_presentiel)
                                        <small>{{ $ligne->formateur_presentiel }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($ligne->formateur_synchrone)
                                        <small>{{ $ligne->formateur_synchrone }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center"><strong>{{ number_format($ligne->mh_totale_drif, 1) }}</strong></td>
                                <td class="text-center">{{ number_format($ligne->mh_affectee_globale, 1) }}</td>
                                <td class="text-center text-primary"><strong>{{ number_format($ligne->mh_realisee_globale, 1) }}</strong></td>
                                <td class="text-center">
                                    <span class="badge badge-taux bg-{{ $ligne->taux_realisation_global >= 75 ? 'success' : ($ligne->taux_realisation_global >= 50 ? 'warning' : 'danger') }}">
                                        {{ number_format($ligne->taux_realisation_global, 1) }}%
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($ligne->taux_realisation_global >= 100)
                                        <i class="fas fa-check-circle text-success fs-5" title="Complété"></i>
                                    @elseif($ligne->taux_realisation_global >= 75)
                                        <i class="fas fa-hourglass-half text-warning fs-5" title="En bonne voie"></i>
                                    @elseif($ligne->taux_realisation_global > 0)
                                        <i class="fas fa-exclamation-triangle text-warning fs-5" title="En retard"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger fs-5" title="Non démarré"></i>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="13" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucune donnée disponible pour les filtres sélectionnés</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if(count($tableauDetaille) > 0)
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="8" class="text-end">TOTAUX :</td>
                                <td class="text-center">{{ number_format($tableauDetaille->sum('mh_totale_drif'), 1) }}</td>
                                <td class="text-center">{{ number_format($tableauDetaille->sum('mh_affectee_globale'), 1) }}</td>
                                <td class="text-center text-primary">{{ number_format($tableauDetaille->sum('mh_realisee_globale'), 1) }}</td>
                                <td class="text-center">
                                    @php
                                        $tauxMoyen = $tableauDetaille->avg('taux_realisation_global');
                                    @endphp
                                    <span class="badge badge-taux bg-{{ $tauxMoyen >= 75 ? 'success' : ($tauxMoyen >= 50 ? 'warning' : 'danger') }}">
                                        {{ number_format($tauxMoyen, 1) }}%
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques -->
<div class="row mb-4">
    <!-- Taux de réalisation global -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie text-success me-2"></i>
                    Taux de Réalisation Global
                </h5>
            </div>
            <div class="card-body d-flex flex-column justify-content-center align-items-center">
                <div class="chart-container" style="position: relative; height: 250px; width: 100%;">
                    <canvas id="tauxGlobalChart"></canvas>
                </div>
                <div class="text-center mt-3">
                    <h2 class="mb-0 text-{{ $chartData['taux_global'] >= 75 ? 'success' : ($chartData['taux_global'] >= 50 ? 'warning' : 'danger') }}">
                        {{ $chartData['taux_global'] }}%
                    </h2>
                    <p class="text-muted mb-0">Taux moyen de réalisation</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Taux d'affectation -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie text-info me-2"></i>
                    Taux d'Affectation
                </h5>
            </div>
            <div class="card-body d-flex flex-column align-items-center">
                <!-- Liste des non affectés -->
                <div class="text-start w-100 mb-3">
                    <h6>Modules non affectés :</h6>
                    @if($nonAssigned['modules']->isEmpty())
                        <p class="text-muted">Aucun</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($nonAssigned['modules'] as $module)
                                <li class="list-group-item px-0">{{ $module->nom_module }} ({{ $module->code_module }})</li>
                            @endforeach
                        </ul>
                    @endif

                    <h6 class="mt-3">Groupes non affectés :</h6>
                    @if($nonAssigned['groupes']->isEmpty())
                        <p class="text-muted">Aucun</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($nonAssigned['groupes'] as $groupe)
                                <li class="list-group-item px-0">{{ $groupe->groupe }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <h6 class="mt-3">Formateurs non affectés :</h6>
                    @if($nonAssigned['formateurs']->isEmpty())
                        <p class="text-muted">Aucun</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach($nonAssigned['formateurs'] as $formateur)
                                <li class="list-group-item px-0">{{ $formateur->nom_formateur }} ({{ $formateur->mle }})</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="chart-container" style="position: relative; height: 250px; width: 100%;">
                    <canvas id="tauxAffectationChart"></canvas>
                </div>
                <div class="text-center mt-3">
                    <h2 class="mb-0 text-{{ $heuresData['taux_affectation'] >= 75 ? 'success' : ($heuresData['taux_affectation'] >= 50 ? 'warning' : 'danger') }}">
                        {{ $heuresData['taux_affectation'] }}%
                    </h2>
                    <p class="text-muted mb-0">Taux moyen d'affectation</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Top modules -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0">
                    <i class="fas fa-trophy text-warning me-2"></i>
                    Top 10 Modules - Meilleurs Taux de Réalisation
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="height: 400px;">
                    <canvas id="topModulesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éléments du formulaire
    const formateurSelect = document.getElementById('formateur-select');
    const moduleSelect = document.getElementById('module-select');
    const groupeSelect = document.getElementById('groupe-select');
    const secteurSelect = document.getElementById('secteur-select');
    const filiereSelect = document.getElementById('filiere-select');
    const niveauSelect = document.getElementById('niveau-select');

    // Valeurs initiales (pour conserver les filtres après soumission)
    const initialFilters = {
        formateur: '{{ $filters["formateur"] ?? "" }}',
        module: '{{ $filters["module"] ?? "" }}',
        groupe: '{{ $filters["groupe"] ?? "" }}',
        secteur: '{{ $filters["secteur"] ?? "" }}',
        filiere: '{{ $filters["filiere"] ?? "" }}',
        niveau: '{{ $filters["niveau"] ?? "" }}'
    };

    // Fonction pour charger les options dynamiquement
    function loadFilterOptions() {
        const params = new URLSearchParams({
            formateur: formateurSelect.value,
            module: moduleSelect.value,
            groupe: groupeSelect.value,
            secteur: secteurSelect.value,
            filiere: filiereSelect.value,
            get_modules: '1',
            get_groupes: '1',
            get_secteurs: '1',
            get_filieres: '1',
            get_niveaux: '1'
        });

        console.log('Params envoyés :', params.toString()); // Débogage

        fetch('{{ route("administration.etablissement.filter.options") }}?' + params.toString())
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Données reçues:', data); // Débogage

                // Modules
                if (data.modules) {
                    updateSelect(moduleSelect, data.modules, 'code_module', 'nom_module', initialFilters.module);
                }

                // Groupes
                if (data.groupes) {
                    updateSelect(groupeSelect, data.groupes, 'groupe', 'groupe', initialFilters.groupe);
                }

                // Secteurs
                if (data.secteurs) {
                    updateSelect(secteurSelect, data.secteurs, 'nom_secteur', 'nom_secteur', initialFilters.secteur);
                }

                // Filières
                if (data.filieres) {
                    updateSelect(filiereSelect, data.filieres, 'code_filiere', 'nom_filiere', initialFilters.filiere);
                }

                // Niveaux
                if (data.niveaux) {
                    updateSelect(niveauSelect, data.niveaux, 'niveau', 'niveau', initialFilters.niveau);
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement des options:', error);
            });
    }

    // Fonction pour mettre à jour un select
    function updateSelect(selectElement, data, valueKey, textKey, selectedValue = '') {
        const currentValue = selectElement.value || selectedValue;
        const defaultOptionText = selectElement.options[0] ? selectElement.options[0].text : 'Tous';

        selectElement.innerHTML = `<option value="">${defaultOptionText}</option>`;

        if (data && data.length > 0) {
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item[valueKey];
                option.textContent = item[textKey];
                if (item[valueKey] == currentValue) {
                    option.selected = true;
                }
                selectElement.appendChild(option);
            });
        } else {
            console.log(`Aucune donnée pour ${textKey}, select reste avec l'option par défaut.`);
        }
    }

    // Événements de changement avec reset des dépendants
    formateurSelect.addEventListener('change', function() {
        moduleSelect.innerHTML = '<option value="">Tous les modules</option>';
        groupeSelect.innerHTML = '<option value="">Tous les groupes</option>';
        secteurSelect.innerHTML = '<option value="">Tous les secteurs</option>';
        filiereSelect.innerHTML = '<option value="">Toutes les filières</option>';
        niveauSelect.innerHTML = '<option value="">Tous les niveaux</option>';
        loadFilterOptions();
    });

    moduleSelect.addEventListener('change', function() {
        groupeSelect.innerHTML = '<option value="">Tous les groupes</option>';
        loadFilterOptions();
    });

    groupeSelect.addEventListener('change', function() {
        loadFilterOptions();
    });

    secteurSelect.addEventListener('change', function() {
        filiereSelect.innerHTML = '<option value="">Toutes les filières</option>';
        niveauSelect.innerHTML = '<option value="">Tous les niveaux</option>';
        loadFilterOptions();
    });

    filiereSelect.addEventListener('change', function() {
        niveauSelect.innerHTML = '<option value="">Tous les niveaux</option>';
        loadFilterOptions();
    });

    // Chargement initial
    loadFilterOptions();

    // Configuration commune pour les graphiques
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#666';

    // 1. Taux global (Doughnut)
    const tauxGlobalCtx = document.getElementById('tauxGlobalChart');
    if (tauxGlobalCtx) {
        const tauxGlobal = parseFloat('{{ $chartData["taux_global"] }}') || 0;
        new Chart(tauxGlobalCtx, {
            type: 'doughnut',
            data: {
                labels: ['Réalisé', 'Restant'],
                datasets: [{
                    data: [tauxGlobal, Math.max(0, 100 - tauxGlobal)],
                    backgroundColor: [
                        tauxGlobal >= 75 ? '#198754' : (tauxGlobal >= 50 ? '#ffc107' : '#dc3545'),
                        '#e9ecef'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed.toFixed(2) + '%';
                            }
                        }
                    }
                },
                cutout: '75%'
            }
        });
    }

    // 2. Taux d'affectation (Doughnut)
    const tauxAffectationCtx = document.getElementById('tauxAffectationChart');
    if (tauxAffectationCtx) {
        const tauxAffectation = parseFloat('{{ $heuresData["taux_affectation"] }}') || 0;
        new Chart(tauxAffectationCtx, {
            type: 'doughnut',
            data: {
                labels: ['Affecté', 'Restant'],
                datasets: [{
                    data: [tauxAffectation, Math.max(0, 100 - tauxAffectation)],
                    backgroundColor: [
                        tauxAffectation >= 75 ? '#198754' : (tauxAffectation >= 50 ? '#ffc107' : '#dc3545'),
                        '#e9ecef'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed.toFixed(2) + '%';
                            }
                        }
                    }
                },
                cutout: '75%'
            }
        });
    }

    // 3. Top modules (Bar)
    const topModulesCtx = document.getElementById('topModulesChart');
    if (topModulesCtx) {
        const modules = @json($chartData['top_modules']);
        if (!modules || modules.length === 0) {
            topModulesCtx.parentElement.innerHTML = '<div class="text-center py-5"><i class="fas fa-chart-bar fa-3x text-muted mb-3"></i><p class="text-muted">Aucune donnée disponible pour afficher le graphique</p></div>';
        } else {
            const labels = modules.map(m => {
                const nom = m.nom_module || 'Module inconnu';
                return nom.length > 35 ? nom.substring(0, 35) + '...' : nom;
            });
            const data = modules.map(m => parseFloat(m.taux_moyen) || 0);

            new Chart(topModulesCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Taux de réalisation (%)',
                        data: data,
                        backgroundColor: function(context) {
                            const value = context.parsed.y;
                            return value >= 75 ? '#198754' : (value >= 50 ? '#ffc107' : '#dc3545');
                        },
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    return modules[index].nom_module;
                                },
                                label: function(context) {
                                    return 'Taux: ' + context.parsed.y.toFixed(2) + '%';
                                },
                                afterLabel: function(context) {
                                    const index = context.dataIndex;
                                    return 'Code: ' + modules[index].code_module;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: { callback: value => value + '%' },
                            grid: { color: '#f0f0f0' }
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 45,
                                minRotation: 45,
                                font: { size: 10 }
                            },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    }
});
</script>
@endsection