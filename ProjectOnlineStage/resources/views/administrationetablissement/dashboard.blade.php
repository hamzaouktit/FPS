@extends('layouts.app')

@section('title', 'Dashboard Établissement')

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
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    Dashboard - {{ $etablissement->nom_efp }}
                </h1>
                <p class="text-muted mb-0">Analyse des heures de formation et suivi pédagogique</p>
            </div>
            <div>
                <a href="{{ route('administration.etablissement.import') }}" class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i>
                    Importer Excel
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Informations Établissement -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="avatar-container me-3">
                        <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px;">
                            <i class="fas fa-university fs-5"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $etablissement->nom_efp }}</h5>
                        <p class="text-muted mb-0">
                            <strong>Code EFP :</strong> {{ $etablissement->code_efp }}
                            @if($etablissement->complexe)
                                | <strong>Complexe :</strong> {{ $etablissement->complexe->nom }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Messages -->
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

<!-- Statistiques Générales -->
<div class="row mb-4">
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fas fa-graduation-cap fa-2x text-primary mb-2"></i>
                <h3 class="mb-0">{{ $stats['total_formations'] }}</h3>
                <p class="text-muted mb-0 small">Formations</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fas fa-chalkboard-teacher fa-2x text-success mb-2"></i>
                <h3 class="mb-0">{{ $stats['total_formateurs'] }}</h3>
                <p class="text-muted mb-0 small">Formateurs</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fas fa-stream fa-2x text-info mb-2"></i>
                <h3 class="mb-0">{{ $stats['total_filieres'] }}</h3>
                <p class="text-muted mb-0 small">Filières</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x text-warning mb-2"></i>
                <h3 class="mb-0">{{ $stats['total_groupes'] }}</h3>
                <p class="text-muted mb-0 small">Groupes</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fas fa-book fa-2x text-danger mb-2"></i>
                <h3 class="mb-0">{{ $stats['total_modules'] }}</h3>
                <p class="text-muted mb-0 small">Modules</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fas fa-industry fa-2x text-secondary mb-2"></i>
                <h3 class="mb-0">{{ $stats['total_secteurs'] }}</h3>
                <p class="text-muted mb-0 small">Secteurs</p>
            </div>
        </div>
    </div>
</div>

<!-- Filtres d'analyse -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-filter text-primary me-2"></i>
                    Filtres d'Analyse
                </h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('administration.etablissement.dashboard') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Année</label>
                            <select name="annee" class="form-select" onchange="this.form.submit()">
                                <option value="">Toutes</option>
                                @foreach($filterOptions['annees'] as $annee)
                                    <option value="{{ $annee }}" {{ $filters['annee'] == $annee ? 'selected' : '' }}>
                                        {{ $annee }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Filière</label>
                            <select name="filiere" class="form-select" onchange="this.form.submit()">
                                <option value="">Toutes</option>
                                @foreach($filterOptions['filieres'] as $nomFiliere => $codeFiliere)
                                    <option value="{{ $codeFiliere }}" {{ $filters['filiere'] == $codeFiliere ? 'selected' : '' }}>
                                        {{ $nomFiliere }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Niveau</label>
                            <select name="niveau" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                @foreach($filterOptions['niveaux'] as $niveau)
                                    <option value="{{ $niveau }}" {{ $filters['niveau'] == $niveau ? 'selected' : '' }}>
                                        {{ $niveau }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Groupe</label>
                            <select name="groupe" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                @foreach($filterOptions['groupes'] as $groupe)
                                    <option value="{{ $groupe->groupe }}" {{ $filters['groupe'] == $groupe->groupe ? 'selected' : '' }}>
                                        {{ $groupe->groupe }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Module</label>
                            <select name="module" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                @foreach($filterOptions['modules'] as $module)
                                    <option value="{{ $module->code_module }}" {{ $filters['module'] == $module->code_module ? 'selected' : '' }}>
                                        {{ Str::limit($module->nom_module, 20) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Formateur</label>
                            <select name="formateur" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                @foreach($filterOptions['formateurs'] as $formateur)
                                    <option value="{{ $formateur->mle }}" {{ $filters['formateur'] == $formateur->mle ? 'selected' : '' }}>
                                        {{ Str::limit($formateur->nom_formateur, 20) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <a href="{{ route('administration.etablissement.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i> Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Analyse des Heures de Formation -->
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
                
                <div class="mt-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Progression globale</span>
                        <span class="fw-bold">{{ $heuresData['taux_realisation'] }}%</span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ min(100, $heuresData['taux_realisation']) }}%">
                            {{ $heuresData['taux_realisation'] }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques des Taux -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-line text-success me-2"></i>Taux de Réalisation</h6>
            </div>
            <div class="card-body">
                <canvas id="tauxRealisationChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Taux d'Affectation</h6>
            </div>
            <div class="card-body">
                <canvas id="tauxAffectationChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-line text-warning me-2"></i>Moyenne d'Absence</h6>
            </div>
            <div class="card-body">
                <canvas id="moyenneAbsenceChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques des heures -->
<div class="row mb-4">
    <div class="col-xl-8 col-lg-7 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar text-primary me-2"></i>
                    Analyse des Heures de Formation
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="heuresChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-5 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie text-primary me-2"></i>
                    Répartition par Mode
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="modeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Taux de réalisation par mode -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-percentage text-primary me-2"></i>
                    Taux de Réalisation par Mode de Formation
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <h4 class="text-primary">{{ $stats['taux_realisation_presentiel'] }}%</h4>
                        <p class="text-muted mb-2">Présentiel</p>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ $stats['taux_realisation_presentiel'] }}%">
                                {{ $stats['taux_realisation_presentiel'] }}%
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h4 class="text-success">{{ $stats['taux_realisation_synchrone'] }}%</h4>
                        <p class="text-muted mb-2">Synchrone</p>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $stats['taux_realisation_synchrone'] }}%">
                                {{ $stats['taux_realisation_synchrone'] }}%
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h4 class="text-info">{{ $stats['taux_realisation_global'] }}%</h4>
                        <p class="text-muted mb-2">Global</p>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: {{ $stats['taux_realisation_global'] }}%">
                                {{ $stats['taux_realisation_global'] }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top 10 Modules -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-trophy text-warning me-2"></i>
                    Top 10 Modules - Meilleurs Taux de Réalisation
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 80px;">Rang</th>
                                <th>Code Module</th>
                                <th>Nom Module</th>
                                <th class="text-center">Taux Moyen</th>
                                <th class="text-end">Heures Réalisées</th>
                                <th class="text-end">Heures Affectées</th>
                                <th class="text-center">Nb Groupes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topModules as $index => $module)
                            <tr>
                                <td class="text-center fw-bold">
                                    @if($index === 0)
                                        <i class="fas fa-medal text-warning fs-5"></i> {{ $index + 1 }}
                                    @elseif($index === 1)
                                        <i class="fas fa-medal fs-5" style="color: #C0C0C0;"></i> {{ $index + 1 }}
                                    @elseif($index === 2)
                                        <i class="fas fa-medal fs-5" style="color: #CD7F32;"></i> {{ $index + 1 }}
                                    @else
                                        {{ $index + 1 }}
                                    @endif
                                </td>
                                <td><code>{{ $module['code_module'] }}</code></td>
                                <td>{{ $module['nom_module'] }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success fs-6">{{ $module['taux_moyen'] }}%</span>
                                </td>
                                <td class="text-end">{{ number_format($module['heures_realisees'], 2) }}h</td>
                                <td class="text-end">{{ number_format($module['heures_affectees'], 2) }}h</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $module['nb_groupes'] }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucune donnée disponible
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Formateurs -->
<div class="row mb-4">
    <div class="col-lg-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-chalkboard-teacher text-primary me-2"></i>
                    Formateurs Présentiel - Top Performances
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Formateur</th>
                                <th class="text-center">Taux</th>
                                <th class="text-end">Heures</th>
                                <th class="text-center">Modules</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($formateurStats['presentiel']->take(10) as $formateur)
                            <tr>
                                <td>{{ $formateur['nom'] }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $formateur['taux_realisation'] >= 80 ? 'bg-success' : ($formateur['taux_realisation'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $formateur['taux_realisation'] }}%
                                    </span>
                                </td>
                                <td class="text-end">{{ number_format($formateur['heures_realisees'], 0) }}h</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $formateur['nb_modules'] }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Aucune donnée</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-video text-success me-2"></i>
                    Formateurs Synchrone - Top Performances
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Formateur</th>
                                <th class="text-center">Taux</th>
                                <th class="text-end">Heures</th>
                                <th class="text-center">Modules</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($formateurStats['synchrone']->take(10) as $formateur)
                            <tr>
                                <td>{{ $formateur['nom'] }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $formateur['taux_realisation'] >= 80 ? 'bg-success' : ($formateur['taux_realisation'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $formateur['taux_realisation'] }}%
                                    </span>
                                </td>
                                <td class="text-end">{{ number_format($formateur['heures_realisees'], 0) }}h</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $formateur['nb_modules'] }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">Aucune donnée</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Taux de réalisation par filière -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-graduation-cap text-primary me-2"></i>
                    Taux de Réalisation par Filière
                </h5>
            </div>
            <div class="card-body">
                <div class="chart-container" style="position: relative; height: 400px;">
                    <canvas id="filiereChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modules non affectés par module -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Modules Non Affectés - Par Module
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Module</th>
                                <th>Groupes</th>
                                <th class="text-end">Masse Horaire</th>
                                <th>Formateur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nonAffectesParModule as $item)
                            <tr>
                                <td>{{ $item['nom_module'] }} <br><small class="text-muted">({{ $item['code_module'] }})</small></td>
                                <td>{{ $item['groupes'] }}</td>
                                <td class="text-end">{{ number_format($item['masse_horaire'], 0) }}h</td>
                                <td>{{ $item['formateur'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                                    Tous les modules sont affectés
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary fw-bold">
                                <th colspan="2">Total</th>
                                <th class="text-end">{{ number_format($totalNonAffectesModule, 0) }}h</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modules non affectés par filière -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Modules Non Affectés - Par Filière
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Filière</th>
                                <th>Modules Non Affectés</th>
                                <th class="text-end">Masse Horaire</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nonAffectesParFiliere as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item['nom_filiere'] }}</strong><br>
                                    <small class="text-muted">{{ $item['code_filiere'] }}</small>
                                </td>
                                <td>{{ $item['modules'] }}</td>
                                <td class="text-end">{{ number_format($item['masse_horaire'], 0) }}h</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                                    Tous les modules sont affectés
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary fw-bold">
                                <th colspan="2">Total</th>
                                <th class="text-end">{{ number_format($totalNonAffectesFiliere, 0) }}h</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des formateurs -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-users text-primary me-2"></i>
                    Liste des Formateurs - Heures Requises / Affectées / Manquantes
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Formateur</th>
                                <th class="text-end">Heures Requises</th>
                                <th class="text-end">Heures Affectées</th>
                                <th class="text-end">Heures Manquantes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($formateursData as $item)
                            <tr>
                                <td>{{ $item['nom_formateur'] }}</td>
                                <td class="text-end">{{ number_format($item['heures_requises'], 0) }}h</td>
                                <td class="text-end">{{ number_format($item['heures_affectees'], 0) }}h</td>
                                <td class="text-end text-{{ $item['heures_manquantes'] > 0 ? 'danger' : 'success' }}">
                                    {{ number_format($item['heures_manquantes'], 0) }}h
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucun formateur disponible
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary fw-bold">
                                <th>TOTAL</th>
                                <th class="text-end">{{ number_format($totalFormateurs['heures_requises'], 0) }}h</th>
                                <th class="text-end">{{ number_format($totalFormateurs['heures_affectees'], 0) }}h</th>
                                <th class="text-end text-{{ $totalFormateurs['heures_manquantes'] > 0 ? 'danger' : 'success' }}">
                                    {{ number_format($totalFormateurs['heures_manquantes'], 0) }}h
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Données détaillées -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-table text-primary me-2"></i>
                    Données Détaillées par Groupe et Module
                </h5>
                <button class="btn btn-success" onclick="exportTableToExcel('detailedTable', 'donnees_detaillees')">
                    <i class="fas fa-file-excel me-1"></i> Exporter Excel
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle" id="detailedTable">
                        <thead class="table-dark">
                            <tr>
                                <th>Groupe</th>
                                <th>Module</th>
                                <th>Formation</th>
                                <th>Niveau</th>
                                <th>Année</th>
                                <th>Formateur Présentiel</th>
                                <th>Formateur Synchrone</th>
                                <th class="text-end">H. Affectées</th>
                                <th class="text-end">H. Réalisées</th>
                                <th class="text-center">Taux Global</th>
                                <th class="text-center">Taux Présentiel</th>
                                <th class="text-center">Taux Synchrone</th>
                                <th class="text-center">Moy. Absence</th>
                                <th class="text-center">Nb CC</th>
                                <th class="text-center">EFM</th>
                                <th>Dernière MAJ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($detailedData as $data)
                            <tr>
                                <td><strong>{{ $data['groupe'] }}</strong></td>
                                <td>
                                    <code>{{ $data['module'] }}</code><br>
                                    <small class="text-muted">{{ Str::limit($data['module_nom'], 30) }}</small>
                                </td>
                                <td><small>{{ $data['formation'] }}</small></td>
                                <td><span class="badge bg-info">{{ $data['niveau'] }}</span></td>
                                <td><small>{{ $data['annee'] }}</small></td>
                                <td><small>{{ Str::limit($data['formateur_presentiel'], 20) }}</small></td>
                                <td><small>{{ Str::limit($data['formateur_synchrone'], 20) }}</small></td>
                                <td class="text-end">{{ $data['heures_affectees'] }}h</td>
                                <td class="text-end">{{ $data['heures_realisees'] }}h</td>
                                <td class="text-center">
                                    <span class="badge {{ $data['taux_realisation'] >= 80 ? 'bg-success' : ($data['taux_realisation'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $data['taux_realisation'] }}%
                                    </span>
                                </td>
                                <td class="text-center">{{ $data['taux_realisation_presentiel'] }}%</td>
                                <td class="text-center">{{ $data['taux_realisation_synchrone'] }}%</td>
                                <td class="text-center">{{ $data['moyenne_absence'] }}%</td>
                                <td class="text-center"><span class="badge bg-secondary">{{ $data['nb_cc'] }}</span></td>
                                <td class="text-center">
                                    @if($data['efm_valide'] == 'Oui')
                                        <i class="fas fa-check-circle text-success"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger"></i>
                                    @endif
                                </td>
                                <td><small>{{ $data['date_maj'] }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="16" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucune donnée disponible
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Indicateurs supplémentaires -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-gradient-success text-white border-0 shadow-sm">
            <div class="card-body">
                <div class="text-white-50 small text-uppercase mb-1">Taux d'Affectation</div>
                <div class="h3 mb-0 fw-bold">{{ $stats['taux_affectation'] }}%</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-gradient-info text-white border-0 shadow-sm">
            <div class="card-body">
                <div class="text-white-50 small text-uppercase mb-1">Total Contrôles Continus</div>
                <div class="h3 mb-0 fw-bold">{{ $stats['total_cc'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-gradient-warning text-white border-0 shadow-sm">
            <div class="card-body">
                <div class="text-white-50 small text-uppercase mb-1">EFM Validés</div>
                <div class="h3 mb-0 fw-bold">{{ $stats['total_efm'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-gradient-secondary text-white border-0 shadow-sm">
            <div class="card-body">
                <div class="text-white-50 small text-uppercase mb-1">Moyenne Absence</div>
                <div class="h3 mb-0 fw-bold">{{ $stats['moyenne_absence'] }}%</div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.font.family = "'Segoe UI', 'Roboto', 'Arial', sans-serif";
    Chart.defaults.font.size = 12;

    // Taux de Réalisation
    const tauxRealisationCtx = document.getElementById('tauxRealisationChart');
    if (tauxRealisationCtx) {
        new Chart(tauxRealisationCtx, {
            type: 'doughnut',
            data: {
                labels: ['Réalisé', 'Non réalisé'],
                datasets: [{
                    data: [{{ $tauxChartData['taux_realisation'] }}, {{ 100 - $tauxChartData['taux_realisation'] }}],
                    backgroundColor: ['rgba(75, 192, 192, 0.8)', 'rgba(200, 200, 200, 0.3)'],
                    borderWidth: 2
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
                }
            }
        });
    }

    // Taux d'Affectation
    const tauxAffectationCtx = document.getElementById('tauxAffectationChart');
    if (tauxAffectationCtx) {
        new Chart(tauxAffectationCtx, {
            type: 'doughnut',
            data: {
                labels: ['Affecté', 'Non affecté'],
                datasets: [{
                    data: [{{ $tauxChartData['taux_affectation'] }}, {{ 100 - $tauxChartData['taux_affectation'] }}],
                    backgroundColor: ['rgba(54, 162, 235, 0.8)', 'rgba(200, 200, 200, 0.3)'],
                    borderWidth: 2
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
                }
            }
        });
    }

    // Moyenne d'Absence
    const moyenneAbsenceCtx = document.getElementById('moyenneAbsenceChart');
    if (moyenneAbsenceCtx) {
        new Chart(moyenneAbsenceCtx, {
            type: 'doughnut',
            data: {
                labels: ['Absent', 'Présent'],
                datasets: [{
                    data: [{{ $tauxChartData['moyenne_absence'] }}, {{ 100 - $tauxChartData['moyenne_absence'] }}],
                    backgroundColor: ['rgba(255, 206, 86, 0.8)', 'rgba(75, 192, 192, 0.3)'],
                    borderWidth: 2
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
                }
            }
        });
    }

    // Graphique des heures
    const heuresCtx = document.getElementById('heuresChart');
    if (heuresCtx) {
        new Chart(heuresCtx, {
            type: 'bar',
            data: {
                labels: ['Semestre 1', 'Semestre 2', 'Total Annuel'],
                datasets: [
                    {
                        label: 'Présentiel',
                        data: [{{ $heuresAnalysis['presentiel']['s1'] }}, {{ $heuresAnalysis['presentiel']['s2'] }}, {{ $heuresAnalysis['presentiel']['total'] }}],
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Synchrone',
                        data: [{{ $heuresAnalysis['synchrone']['s1'] }}, {{ $heuresAnalysis['synchrone']['s2'] }}, {{ $heuresAnalysis['synchrone']['total'] }}],
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2
                    },
                    {
                        label: 'Asynchrone',
                        data: [{{ $heuresAnalysis['asynchrone']['s1'] }}, {{ $heuresAnalysis['asynchrone']['s2'] }}, {{ $heuresAnalysis['asynchrone']['total'] }}],
                        backgroundColor: 'rgba(255, 206, 86, 0.7)',
                        borderColor: 'rgba(255, 206, 86, 1)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { padding: 15, usePointStyle: true } },
                    title: { display: true, text: 'Répartition des Heures par Semestre', font: { size: 16, weight: 'bold' }, padding: 20 },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' heures';
                            }
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: function(value) { return value + 'h'; } }, title: { display: true, text: 'Heures', font: { weight: 'bold' } } },
                    x: { title: { display: true, text: 'Périodes', font: { weight: 'bold' } } }
                }
            }
        });
    }

    // Graphique mode
    const modeCtx = document.getElementById('modeChart');
    if (modeCtx) {
        new Chart(modeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Présentiel', 'Synchrone'],
                datasets: [{
                    data: [{{ $chartData['repartition_mode']['Présentiel'] }}, {{ $chartData['repartition_mode']['Synchrone'] }}],
                    backgroundColor: ['rgba(54, 162, 235, 0.8)', 'rgba(75, 192, 192, 0.8)'],
                    borderColor: ['rgba(54, 162, 235, 1)', 'rgba(75, 192, 192, 1)'],
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, font: { size: 13 } } },
                    title: { display: true, text: 'Heures Réalisées par Mode', font: { size: 16, weight: 'bold' }, padding: 20 },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed.toFixed(2) + 'h (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique filière
    const filiereCtx = document.getElementById('filiereChart');
    if (filiereCtx) {
        new Chart(filiereCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['taux_par_filiere']->keys()) !!},
                datasets: [{
                    label: 'Taux de Réalisation (%)',
                    data: {!! json_encode($chartData['taux_par_filiere']->values()) !!},
                    backgroundColor: function(context) {
                        const value = context.parsed.x;
                        if (value >= 80) return 'rgba(75, 192, 192, 0.7)';
                        if (value >= 50) return 'rgba(255, 206, 86, 0.7)';
                        return 'rgba(255, 99, 132, 0.7)';
                    },
                    borderColor: function(context) {
                        const value = context.parsed.x;
                        if (value >= 80) return 'rgba(75, 192, 192, 1)';
                        if (value >= 50) return 'rgba(255, 206, 86, 1)';
                        return 'rgba(255, 99, 132, 1)';
                    },
                    borderWidth: 2
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Taux: ' + context.parsed.x.toFixed(2) + '%';
                            }
                        }
                    }
                },
                scales: {
                    x: { beginAtZero: true, max: 100, ticks: { callback: function(value) { return value + '%'; } }, title: { display: true, text: 'Taux de Réalisation (%)', font: { weight: 'bold' } } },
                    y: { title: { display: true, text: 'Filières', font: { weight: 'bold' } } }
                }
            }
        });
    }

    // Export Excel
    window.exportTableToExcel = function(tableID, filename = '') {
        const table = document.getElementById(tableID);
        if (!table) { alert('Tableau introuvable!'); return; }
        const tableClone = table.cloneNode(true);
        const badges = tableClone.querySelectorAll('.badge, .fa, .fas, .far');
        badges.forEach(badge => { if (badge.textContent.trim()) { badge.outerHTML = badge.textContent; } else { badge.remove(); } });
        const tableHTML = tableClone.outerHTML;
        const dataType = 'application/vnd.ms-excel';
        filename = filename ? filename + '_' + new Date().toISOString().slice(0,10) + '.xls' : 'export_table.xls';
        if (navigator.msSaveOrOpenBlob) {
            const blob = new Blob(['\ufeff', tableHTML], { type: dataType });
            navigator.msSaveOrOpenBlob(blob, filename);
        } else {
            const downloadLink = document.createElement("a");
            downloadLink.href = 'data:' + dataType + ';charset=utf-8,' + encodeURIComponent(tableHTML);
            downloadLink.download = filename;
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    };
});
</script>

<style>
.chart-container { position: relative; width: 100%; }
.table-hover tbody tr:hover { background-color: rgba(0, 123, 255, 0.05); }
.bg-gradient-success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
.bg-gradient-info { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); }
.bg-gradient-warning { background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); }
.bg-gradient-secondary { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); }
.text-xs { font-size: 0.7rem; }
.badge { transition: all 0.2s ease; }
.badge:hover { transform: scale(1.1); }
.table thead th { font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; }
.table tbody td { vertical-align: middle; }
.progress { border-radius: 0.5rem; box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1); }
.progress-bar { transition: width 0.6s ease; }
@media (max-width: 768px) {
    .h2 { font-size: 1.5rem; }
    .table { font-size: 0.85rem; }
    .chart-container { height: 300px !important; }
}
@media print {
    .btn, .card-header, nav { display: none !important; }
    .card { border: 1px solid #dee2e6 !important; page-break-inside: avoid; }
}
</style>
@endpush