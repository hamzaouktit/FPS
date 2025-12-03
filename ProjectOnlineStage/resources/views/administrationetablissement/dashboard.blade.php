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
                <h3 class="mb-0">{{ $statistics['nb_formations'] }}</h3>
                <p class="text-muted mb-0 small">Formations</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fas fa-chalkboard-teacher fa-2x text-success mb-2"></i>
                <h3 class="mb-0">{{ $statistics['nb_formateurs'] }}</h3>
                <p class="text-muted mb-0 small">Formateurs</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fas fa-stream fa-2x text-info mb-2"></i>
                <h3 class="mb-0">{{ $statistics['nb_filieres'] }}</h3>
                <p class="text-muted mb-0 small">Filières</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x text-warning mb-2"></i>
                <h3 class="mb-0">{{ $statistics['nb_groupes'] }}</h3>
                <p class="text-muted mb-0 small">Groupes</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fas fa-book fa-2x text-danger mb-2"></i>
                <h3 class="mb-0">{{ $statistics['nb_modules'] }}</h3>
                <p class="text-muted mb-0 small">Modules</p>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <i class="fas fa-industry fa-2x text-secondary mb-2"></i>
                <h3 class="mb-0">{{ $statistics['nb_secteurs'] }}</h3>
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
                                @foreach($filterOptions['filieres'] as $filiere)
                                    <option value="{{ $filiere->id }}" {{ $filters['filiere'] == $filiere->id ? 'selected' : '' }}>
                                        {{ $filiere->nom_filiere }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Niveau</label>
                            <select name="niveau" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                @foreach($filterOptions['niveaux'] as $niveau)
                                    <option value="{{ $niveau->id }}" {{ $filters['niveau'] == $niveau->id ? 'selected' : '' }}>
                                        {{ $niveau->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Groupe</label>
                            <select name="groupe" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                @foreach($filterOptions['groupes'] as $groupe)
                                    <option value="{{ $groupe->id }}" {{ $filters['groupe'] == $groupe->id ? 'selected' : '' }}>
                                        {{ $groupe->code_groupe }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">Module</label>
                            <select name="module" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous</option>
                                @foreach($filterOptions['modules'] as $module)
                                    <option value="{{ $module->id }}" {{ $filters['module'] == $module->id ? 'selected' : '' }}>
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
                                        {{ Str::limit($formateur->nom_complet, 20) }}
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
                    <!-- ✅ NOUVELLE CARTE : Heures Réglementaires (Offre) -->
                    <div class="col-md-2-4">
                        <div class="stat-card info card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="text-muted mb-0">Heures Réglementaires</h6>
                                        <h3 class="mb-0 mt-2">{{ number_format($statistics['heures_reglementaires'], 2) }}</h3>
                                    </div>
                                    <div class="bg-info bg-opacity-10 p-2 rounded">
                                        <i class="fas fa-briefcase text-info fs-4"></i>
                                    </div>
                                </div>
                                <small class="text-muted">Offre formateurs (910h/an)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Heures Demandées -->
                    <div class="col-md-2-4">
                        <div class="stat-card primary card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="text-muted mb-0">Heures Demandées</h6>
                                        <h3 class="mb-0 mt-2">{{ number_format($statistics['heures_requises'], 2) }}</h3>
                                    </div>
                                    <div class="bg-primary bg-opacity-10 p-2 rounded">
                                        <i class="fas fa-tasks text-primary fs-4"></i>
                                    </div>
                                </div>
                                <small class="text-muted">Heures DRIF totales</small>
                            </div>
                        </div>
                    </div>

                    <!-- Heures Affectées -->
                    <div class="col-md-2-4">
                        <div class="stat-card success card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="text-muted mb-0">Heures Affectées</h6>
                                        <h3 class="mb-0 mt-2">{{ number_format($statistics['heures_affectees'], 2) }}</h3>
                                    </div>
                                    <div class="bg-success bg-opacity-10 p-2 rounded">
                                        <i class="fas fa-user-check text-success fs-4"></i>
                                    </div>
                                </div>
                                <small class="text-muted">{{ $statistics['taux_affectation'] }}%</small>
                            </div>
                        </div>
                    </div>

                    <!-- Heures Réalisées -->
                    <div class="col-md-2-4">
                        <div class="stat-card warning card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="text-muted mb-0">Heures Réalisées</h6>
                                        <h3 class="mb-0 mt-2">{{ number_format($statistics['heures_realisees'], 2) }}</h3>
                                    </div>
                                    <div class="bg-warning bg-opacity-10 p-2 rounded">
                                        <i class="fas fa-check-circle text-warning fs-4"></i>
                                    </div>
                                </div>
                                <small class="text-muted">{{ $statistics['taux_realisation'] }}%</small>
                            </div>
                        </div>
                    </div>

                    <!-- Différence -->
                    <div class="col-md-2-4">
                        <div class="stat-card danger card border-0 bg-light h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="text-muted mb-0">Différence</h6>
                                        <h3 class="mb-0 mt-2 text-{{ $statistics['difference'] > 0 ? 'danger' : 'success' }}">
                                            {{ number_format($statistics['difference'], 2) }}
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
                
                

                <!-- ✅ NOUVEAU : Indicateur d'adéquation Offre/Demande -->
                <div class="mt-3 p-3 bg-light rounded">
                    <div class="row text-center">
                        <div class="col-md-6">
                            <strong class="d-block mb-2">
                                <i class="fas fa-balance-scale text-primary me-2"></i>
                                Ratio Offre/Demande
                            </strong>
                            @php
                                $ratioOffreDemande = $statistics['heures_requises'] > 0 
                                    ? ($statistics['heures_reglementaires'] / $statistics['heures_requises']) * 100 
                                    : 0;
                                $ratioClass = $ratioOffreDemande >= 100 ? 'success' : ($ratioOffreDemande >= 80 ? 'warning' : 'danger');
                            @endphp
                            <h4 class="text-{{ $ratioClass }} mb-0">{{ number_format($ratioOffreDemande, 1) }}%</h4>
                            <small class="text-muted">
                                {{ number_format($statistics['heures_reglementaires'], 0) }}h disponibles / 
                                {{ number_format($statistics['heures_requises'], 0) }}h requises
                            </small>
                        </div>
                        <div class="col-md-6">
                            <strong class="d-block mb-2">
                                <i class="fas fa-chart-line text-info me-2"></i>
                                Taux d'Utilisation Formateurs
                            </strong>
                            @php
                                $tauxUtilisation = $statistics['heures_reglementaires'] > 0 
                                    ? ($statistics['heures_affectees'] / $statistics['heures_reglementaires']) * 100 
                                    : 0;
                                $utilisationClass = $tauxUtilisation >= 90 ? 'success' : ($tauxUtilisation >= 70 ? 'warning' : 'info');
                            @endphp
                            <h4 class="text-{{ $utilisationClass }} mb-0">{{ number_format($tauxUtilisation, 1) }}%</h4>
                            <small class="text-muted">
                                {{ number_format($statistics['heures_affectees'], 0) }}h affectées / 
                                {{ number_format($statistics['heures_reglementaires'], 0) }}h disponibles
                            </small>
                        </div>
                    </div>
                </div>
                <!-- Barre de progression globale -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Progression globale</span>
                        <span class="fw-bold">{{ $statistics['taux_realisation'] }}%</span>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ min(100, $statistics['taux_realisation']) }}%">
                            {{ $statistics['taux_realisation'] }}%
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
                        <h4 class="text-primary">{{ $statistics['taux_realisation_presentiel'] }}%</h4>
                        <p class="text-muted mb-2">Présentiel</p>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ $statistics['taux_realisation_presentiel'] }}%">
                                {{ $statistics['taux_realisation_presentiel'] }}%
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h4 class="text-success">{{ $statistics['taux_realisation_synchrone'] }}%</h4>
                        <p class="text-muted mb-2">Synchrone</p>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $statistics['taux_realisation_synchrone'] }}%">
                                {{ $statistics['taux_realisation_synchrone'] }}%
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h4 class="text-info">{{ $statistics['taux_realisation'] }}%</h4>
                        <p class="text-muted mb-2">Global</p>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: {{ $statistics['taux_realisation'] }}%">
                                {{ $statistics['taux_realisation'] }}%
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
                                <th>Raison</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nonAffectesParModule as $item)
                            <tr>
                                <td>{{ $item['nom_module'] }} <br><small class="text-muted">({{ $item['code_module'] }})</small></td>
                                <td>{{ $item['groupes'] }}</td>
                                <td class="text-end">{{ number_format($item['masse_horaire'], 2) }}h</td>
                                <td>{{ $item['formateur'] }}</td>
                                <td>{{ $item['raisons'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                                    Tous les modules sont affectés
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary fw-bold">
                                <th colspan="2">Total</th>
                                <th class="text-end">{{ number_format($totalNonAffectesModule, 2) }}h</th>
                                <th colspan="2"></th>
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
                                <th>Raison</th>
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
                                <td class="text-end">{{ number_format($item['masse_horaire'], 2) }}h</td>
                                <td>{{ $item['raisons'] }}</td>
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
                                <th class="text-end">{{ number_format($totalNonAffectesFiliere, 2) }}h</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Nouvelle table : Entités sans affectation -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-circle text-danger me-2"></i>
                    Entités Sans Affectation (Groupes, Modules, Formateurs créés mais non affectés)
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Groupes sans affectation -->
                    <div class="col-md-4 mb-4">
                        <h6 class="mb-3">Groupes sans affectation</h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code Groupe</th>
                                        <th>Année</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($groupesSansAffectation as $groupe)
                                    <tr>
                                        <td>{{ $groupe->code_groupe }}</td>
                                        <td>{{ $groupe->annee_formation }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">Aucun groupe sans affectation</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Modules sans affectation -->
                    <div class="col-md-4 mb-4">
                        <h6 class="mb-3">Modules sans affectation</h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code Module</th>
                                        <th>Nom Module</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($modulesSansAffectation as $module)
                                    <tr>
                                        <td>{{ $module->code_module }}</td>
                                        <td>{{ $module->nom_module }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">Aucun module sans affectation</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Formateurs sans affectation -->
                    <div class="col-md-4 mb-4">
                        <h6 class="mb-3">Formateurs sans affectation</h6>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>MLE</th>
                                        <th>Nom Complet</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($formateursSansAffectation as $formateur)
                                    <tr>
                                        <td>{{ $formateur->mle }}</td>
                                        <td>{{ $formateur->nom_complet }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">Aucun formateur sans affectation</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des formateurs - SECTION AMÉLIORÉE -->
<!-- Liste des formateurs - SECTION CORRIGÉE -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-users text-primary me-2"></i>
                    Liste des Formateurs - Charge Horaire et Affectations
                </h5>
                <small class="text-muted">
                    Suivi de la masse horaire réglementaire (910h), des heures demandées (DRIF), et des heures affectées
                </small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Formateur</th>
                                <th class="text-center">Type</th>
                                <th class="text-end">Masse Horaire Réglementaire (offres)</th>
                                <th class="text-end">Heures Demandées (DRIF)</th>
                                <th class="text-end">Heures Affectées</th>
                                <th class="text-end">Heures Disponibles</th>
                                <th class="text-center">Taux d'Occupation</th>
                                <th class="text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($formateursData as $item)
                            @php
                                $masseHoraire = $item['masse_horaire_formateur'] ?? 910;
                                $heuresDemandees = $item['heures_demandees'];
                                $heuresAffectees = $item['heures_affectees'];
                                
                                // ✅ CORRIGÉ : Disponibles = Demandées - Affectées
                                $heuresDisponibles = max(0, $heuresDemandees - $heuresAffectees);
                                
                                // ✅ CORRIGÉ : Taux = (Affectées / Demandées) * 100
                                $tauxOccupation = $heuresDemandees > 0 ? ($heuresAffectees / $heuresDemandees) * 100 : 0;
                                
                                // Déterminer le statut basé sur le taux d'occupation
                                if ($tauxOccupation > 100) {
                                    $statut = 'Surchargé';
                                    $statutClass = 'danger';
                                    $statutIcon = 'fa-exclamation-triangle';
                                } elseif ($tauxOccupation >= 90) {
                                    $statut = 'Complet';
                                    $statutClass = 'success';
                                    $statutIcon = 'fa-check-circle';
                                } elseif ($tauxOccupation >= 70) {
                                    $statut = 'En cours';
                                    $statutClass = 'warning';
                                    $statutIcon = 'fa-info-circle';
                                } else {
                                    $statut = 'Incomplet';
                                    $statutClass = 'info';
                                    $statutIcon = 'fa-clock';
                                }
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $item['nom_formateur'] }}</strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">Permanent</span>
                                </td>
                                <td class="text-end">
                                    <strong>{{ number_format($masseHoraire, 2) }}h</strong>
                                    <br><small class="text-muted">(Réglementaire)</small>
                                </td>
                                <td class="text-end">
                                    <strong class="text-primary">{{ number_format($heuresDemandees, 2) }}h</strong>
                                    <br><small class="text-muted">(Demandées)</small>
                                </td>
                                <td class="text-end">
                                    <strong class="text-success">{{ number_format($heuresAffectees, 2) }}h</strong>
                                    <br><small class="text-muted">(Affectées)</small>
                                </td>
                                <td class="text-end text-{{ $heuresDisponibles > 0 ? 'warning' : 'success' }}">
                                    <strong>{{ number_format($heuresDisponibles, 2) }}h</strong>
                                    <br><small class="text-muted">(Restantes)</small>
                                </td>
                                <td class="text-center">
                                    <div class="mb-1">
                                        <span class="badge bg-{{ $statutClass }} rounded-pill fs-6">
                                            {{ number_format($tauxOccupation, 1) }}%
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 10px; width: 120px; margin: 0 auto;">
                                        <div class="progress-bar bg-{{ $statutClass }}" 
                                             role="progressbar" 
                                             style="width: {{ min(100, $tauxOccupation) }}%"
                                             aria-valuenow="{{ $tauxOccupation }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $statutClass }}">
                                        <i class="fas {{ $statutIcon }} me-1"></i>
                                        {{ $statut }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucun formateur disponible
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="table-secondary fw-bold">
                                <th colspan="2">TOTAL</th>
                                <th class="text-end">
                                    {{ number_format($totalFormateurs['masse_horaire_totale'], 2) }}h
                                </th>
                                <th class="text-end text-primary">
                                    {{ number_format($totalFormateurs['heures_demandees'], 2) }}h
                                </th>
                                <th class="text-end text-success">
                                    {{ number_format($totalFormateurs['heures_affectees'], 2) }}h
                                </th>
                                <th class="text-end text-{{ $totalFormateurs['heures_disponibles'] > 0 ? 'warning' : 'success' }}">
                                    {{ number_format($totalFormateurs['heures_disponibles'], 2) }}h
                                </th>
                                <th class="text-center">
                                    @php
                                        $tauxOccupationTotal = $totalFormateurs['heures_demandees'] > 0 
                                            ? ($totalFormateurs['heures_affectees'] / $totalFormateurs['heures_demandees']) * 100 
                                            : 0;
                                    @endphp
                                    <span class="badge bg-primary rounded-pill fs-6">
                                        {{ number_format($tauxOccupationTotal, 1) }}%
                                    </span>
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Légende mise à jour -->
                <div class="mt-3 p-3 bg-light rounded">
                    <strong class="d-block mb-2"><i class="fas fa-info-circle text-primary me-2"></i>Légende :</strong>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <span class="badge bg-info me-2">Incomplet (&lt; 70%)</span>
                            <small class="text-muted">Moins de 70% des heures demandées sont affectées</small>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="badge bg-warning me-2">En cours (70-89%)</span>
                            <small class="text-muted">Entre 70% et 89% des heures sont affectées</small>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="badge bg-success me-2">Complet (90-100%)</span>
                            <small class="text-muted">Entre 90% et 100% des heures sont affectées</small>
                        </div>
                        <div class="col-md-6 mb-2">
                            <span class="badge bg-danger me-2">Surchargé (&gt; 100%)</span>
                            <small class="text-muted">Plus de 100% des heures demandées sont affectées</small>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row small">
                        <div class="col-md-4">
                            <strong>Masse Horaire Réglementaire :</strong> Heures annuelles théoriques (910h par défaut)
                        </div>
                        <div class="col-md-4">
                            <strong>Heures Demandées :</strong> Heures DRIF requises pour les modules assignés
                        </div>
                        <div class="col-md-4">
                            <strong>Heures Affectées :</strong> Heures effectivement affectées au formateur
                        </div>
                    </div>
                    <div class="row small mt-2">
                        <div class="col-md-4">
                            <strong>Heures Disponibles :</strong> Heures demandées - Heures affectées
                        </div>
                        <div class="col-md-4">
                            <strong>Taux d'Occupation :</strong> (Heures affectées ÷ Heures demandées) × 100
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Données détaillées -->
<!-- Données détaillées -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-table text-primary me-2"></i>
                    Données Détaillées par Groupe et Module
                </h5>
                <div>
                    <button class="btn btn-success btn-sm" onclick="exportTableToExcel('detailedTable', 'donnees_detaillees')">
                        <i class="fas fa-file-excel me-1"></i> Exporter Excel
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0" id="detailedTable">
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
                            @forelse($paginatedDetailedData as $data)
                            <tr>
                                <td><strong>{{ $data['groupe'] }}</strong></td>
                                <td>
                                    <code>{{ $data['module'] }}</code><br>
                                    <small class="text-muted">{{ Str::limit($data['module_nom'], 30) }}</small>
                                </td>
                                <td><small>{{ $data['formation'] }}</small></td>
                                <td><span class="badge bg-info text-dark">{{ $data['niveau'] }}</span></td>
                                <td><small>{{ $data['annee'] }}</small></td>
                                <td><small>{{ Str::limit($data['formateur_presentiel'], 20) }}</small></td>
                                <td><small>{{ Str::limit($data['formateur_synchrone'], 20) }}</small></td>
                                <td class="text-end">{{ $data['heures_affectees'] }}h</td>
                                <td class="text-end">{{ $data['heures_realisees'] }}h</td>
                                <td class="text-center">
                                    <span class="badge rounded-pill fw-bold {{ $data['taux_realisation'] >= 80 ? 'bg-success' : ($data['taux_realisation'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $data['taux_realisation'] }}%
                                    </span>
                                </td>
                                <td class="text-center">{{ $data['taux_realisation_presentiel'] }}%</td>
                                <td class="text-center">{{ $data['taux_realisation_synchrone'] }}%</td>
                                <td class="text-center">{{ $data['moyenne_absence'] }}%</td>
                                <td class="text-center"><span class="badge bg-secondary">{{ $data['nb_cc'] }}</span></td>
                                <td class="text-center">
                                    @if($data['efm_valide'] == 'Oui')
                                        <i class="fas fa-check-circle text-success fs-5"></i>
                                    @else
                                        <i class="fas fa-times-circle text-danger fs-5"></i>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $data['date_maj'] }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="16" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    Aucune donnée disponible
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Modernisée -->
                @if($paginatedDetailedData->hasPages())
                <div class="border-top bg-light px-4 py-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <!-- Info -->
                        <div class="text-muted small">
                            Affichage de <strong>{{ $paginatedDetailedData->firstItem() }}</strong> à 
                            <strong>{{ $paginatedDetailedData->lastItem() }}</strong> sur 
                            <strong>{{ $paginatedDetailedData->total() }}</strong> résultats
                        </div>

                        <!-- Navigation -->
                        <nav aria-label="Pagination des données détaillées">
                            <ul class="pagination pagination-sm mb-0">
                                <!-- Précédent -->
                                @if($paginatedDetailedData->onFirstPage())
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link rounded-pill px-3" aria-hidden="true">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link rounded-pill px-3" href="{{ $paginatedDetailedData->previousPageUrl() }}" rel="prev" aria-label="Précédent">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                <!-- Pages -->
                                @php
                                    $start = max(1, $paginatedDetailedData->currentPage() - 2);
                                    $end = min($paginatedDetailedData->lastPage(), $paginatedDetailedData->currentPage() + 2);
                                @endphp

                                @if($start > 1)
                                    <li class="page-item"><a class="page-link rounded-pill" href="{{ $paginatedDetailedData->url(1) }}">1</a></li>
                                    @if($start > 2)
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    @endif
                                @endif

                                @for($i = $start; $i <= $end; $i++)
                                    @if($i == $paginatedDetailedData->currentPage())
                                        <li class="page-item active" aria-current="page">
                                            <span class="page-link rounded-pill bg-primary border-primary">{{ $i }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link rounded-pill" href="{{ $paginatedDetailedData->url($i) }}">{{ $i }}</a>
                                        </li>
                                    @endif
                                @endfor

                                @if($end < $paginatedDetailedData->lastPage())
                                    @if($end < $paginatedDetailedData->lastPage() - 1)
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link rounded-pill" href="{{ $paginatedDetailedData->url($paginatedDetailedData->lastPage()) }}">
                                            {{ $paginatedDetailedData->lastPage() }}
                                        </a>
                                    </li>
                                @endif

                                <!-- Suivant -->
                                @if($paginatedDetailedData->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link rounded-pill px-3" href="{{ $paginatedDetailedData->nextPageUrl() }}" rel="next" aria-label="Suivant">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled" aria-disabled="true">
                                        <span class="page-link rounded-pill px-3" aria-hidden="true">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
                @endif
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
                <div class="h3 mb-0 fw-bold">{{ $statistics['taux_affectation'] }}%</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-gradient-info text-white border-0 shadow-sm">
            <div class="card-body">
                <div class="text-white-50 small text-uppercase mb-1">Total Contrôles Continus</div>
                <div class="h3 mb-0 fw-bold">{{ $statistics['total_cc'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-gradient-warning text-white border-0 shadow-sm">
            <div class="card-body">
                <div class="text-white-50 small text-uppercase mb-1">EFM Validés</div>
                <div class="h3 mb-0 fw-bold">{{ $statistics['total_efm'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-gradient-secondary text-white border-0 shadow-sm">
            <div class="card-body">
                <div class="text-white-50 small text-uppercase mb-1">Moyenne Absence</div>
                <div class="h3 mb-0 fw-bold">{{ $statistics['moyenne_absence'] }}%</div>
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
                        const value = context.parsed.y;
                        if (value >= 80) return 'rgba(75, 192, 192, 0.7)';
                        if (value >= 50) return 'rgba(255, 206, 86, 0.7)';
                        return 'rgba(255, 99, 132, 0.7)';
                    },
                    borderColor: function(context) {
                        const value = context.parsed.y;
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



/* === Pagination Moderne & Élégante === */
.pagination .page-link {
    color: #495057;
    background-color: #fff;
    border: 1px solid #dee2e6;
    font-weight: 500;
    padding: 0.375rem 0.75rem;
    margin: 0 2px;
    border-radius: 50px !important;
    transition: all 0.25s ease;
    min-width: 36px;
    text-align: center;
    font-size: 0.875rem;
}

.pagination .page-link:hover {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.25);
}

.pagination .page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
    font-weight: 600;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.3);
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #dee2e6;
    opacity: 0.6;
    cursor: not-allowed;
}

/* Icônes seules */
.pagination .page-link i {
    font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 768px) {
    .pagination {
        justify-content: center;
    }
    .pagination .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
        min-width: 32px;
    }
    .d-flex.flex-column .text-muted {
        text-align: center;
        font-size: 0.8rem;
    }
}

/* Grid 5 colonnes pour les cartes d'heures */
@media (min-width: 768px) {
    .col-md-2-4 {
        flex: 0 0 auto;
        width: 20%; /* 100% / 5 = 20% */
    }
}

@media (max-width: 767px) {
    .col-md-2-4 {
        width: 100%;
        margin-bottom: 1rem;
    }
}

/* Accessibilité */
.page-link:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

</style>
@endpush