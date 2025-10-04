@extends('layouts.app')

@section('title', 'Dashboard Complexe - Analyse et Statistiques')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><i class="fas fa-home me-1"></i><a href="{{ route('welcome') }}">Accueil</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard Complexe</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    .filter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
    }
    .table-sm th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 10;
        font-size: 0.75rem;
        padding: 0.5rem 0.25rem;
    }
    .table-sm td {
        font-size: 0.75rem;
        padding: 0.4rem 0.25rem;
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .etablissement-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    .etablissement-card:hover {
        border-left-color: #1E5F99;
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    .badge-taux {
        font-size: 0.85rem;
        padding: 5px 10px;
    }
    .progress-bar-animated {
        animation: progress-bar-stripes 1s linear infinite;
    }
    @keyframes progress-bar-stripes {
        0% { background-position: 1rem 0; }
        100% { background-position: 0 0; }
    }
    
    /* Styles de pagination personnalisés */
    .pagination {
        margin: 0;
    }
    .pagination .page-link {
        color: #1E5F99;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
        margin: 0 2px;
        border-radius: 0.375rem;
        transition: all 0.3s ease;
    }
    .pagination .page-link:hover {
        background-color: #1E5F99;
        color: white;
        border-color: #1E5F99;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(30,95,153,0.2);
    }
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #1E5F99 0%, #1a4a75 100%);
        border-color: #1E5F99;
        color: white;
        font-weight: 600;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
    .pagination-info {
        color: #6c757d;
        font-size: 0.875rem;
        padding: 0.5rem 0;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-0">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    Analyse et Statistiques - {{ $complexe->nom }}
                </h1>
                <p class="text-muted mb-0">Tableau de bord analytique du complexe de formation</p>
            </div>
            <div class="text-end">
                <span class="badge bg-success fs-6">
                    <i class="fas fa-clock me-1"></i>
                    {{ date('d/m/Y H:i') }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm filter-card">
            <div class="card-body">
                <h5 class="mb-3"><i class="fas fa-filter me-2"></i>Filtres de Recherche</h5>
                <form method="GET" action="{{ route('administration.complexe.dashboard') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label text-white">Établissement</label>
                            <select name="etablissement" id="etablissement" class="form-select">
                                <option value="">Tous les établissements</option>
                                @foreach($filterOptions['etablissements'] as $etab)
                                    <option value="{{ $etab->code_efp }}" {{ $filters['etablissement'] == $etab->code_efp ? 'selected' : '' }}>
                                        {{ $etab->nom_efp }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-white">Secteur</label>
                            <select name="secteur" id="secteur" class="form-select">
                                <option value="">Tous les secteurs</option>
                                @foreach($filterOptions['secteurs'] as $secteur)
                                    <option value="{{ $secteur->nom_secteur }}" {{ $filters['secteur'] == $secteur->nom_secteur ? 'selected' : '' }}>
                                        {{ $secteur->nom_secteur }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-white">Filière</label>
                            <select name="filiere" id="filiere" class="form-select">
                                <option value="">Toutes les filières</option>
                                @foreach($filterOptions['filieres'] as $filiere)
                                    <option value="{{ $filiere->code_filiere }}" {{ $filters['filiere'] == $filiere->code_filiere ? 'selected' : '' }}>
                                        {{ $filiere->nom_filiere }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-white">Formateur</label>
                            <select name="formateur" id="formateur" class="form-select">
                                <option value="">Tous les formateurs</option>
                                @foreach($filterOptions['formateurs'] as $formateur)
                                    <option value="{{ $formateur->mle }}" {{ $filters['formateur'] == $formateur->mle ? 'selected' : '' }}>
                                        {{ $formateur->nom_formateur }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-white">Module</label>
                            <select name="module" id="module" class="form-select">
                                <option value="">Tous les modules</option>
                                @foreach($filterOptions['modules'] as $module)
                                    <option value="{{ $module->code_module }}" {{ $filters['module'] == $module->code_module ? 'selected' : '' }}>
                                        {{ $module->nom_module }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label text-white">Groupe</label>
                            <select name="groupe" id="groupe" class="form-select">
                                <option value="">Tous les groupes</option>
                                @foreach($filterOptions['groupes'] as $groupe)
                                    <option value="{{ $groupe->groupe }}" {{ $filters['groupe'] == $groupe->groupe ? 'selected' : '' }}>
                                        {{ $groupe->groupe }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-light me-2">
                                <i class="fas fa-search me-1"></i>Filtrer
                            </button>
                            <a href="{{ route('administration.complexe.dashboard') }}" class="btn btn-outline-light">
                                <i class="fas fa-redo me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques Principales -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                        <i class="fas fa-graduation-cap fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">{{ $statistics['nb_formations'] }}</div>
                    <div class="text-muted small">Formations</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                        <i class="fas fa-users fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">{{ number_format($statistics['nb_apprenants']) }}</div>
                    <div class="text-muted small">Apprenants</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                        <i class="fas fa-chalkboard-teacher fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">{{ $statistics['nb_formateurs'] }}</div>
                    <div class="text-muted small">Formateurs</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                        <i class="fas fa-users-cog fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">{{ $statistics['nb_groupes'] }}</div>
                    <div class="text-muted small">Groupes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                        <i class="fas fa-book fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">{{ $statistics['nb_modules'] }}</div>
                    <div class="text-muted small">Modules</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle p-3">
                        <i class="fas fa-stream fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">{{ $statistics['nb_filieres'] }}</div>
                    <div class="text-muted small">Filières</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-dark bg-opacity-10 text-dark rounded-circle p-3">
                        <i class="fas fa-industry fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">{{ $statistics['nb_secteurs'] }}</div>
                    <div class="text-muted small">Secteurs</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm stat-card h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                        <i class="fas fa-school fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">{{ $etablissementsStats->count() }}</div>
                    <div class="text-muted small">Établissements</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analyse des Heures -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-clock text-primary me-2"></i>Analyse des Heures de Formation</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Heures Requises</h6>
                            <h3 class="text-primary mb-0">{{ number_format($statistics['heures_requises'], 2) }}</h3>
                        </div>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Heures Affectées</h6>
                            <h3 class="text-warning mb-0">{{ number_format($statistics['heures_affectees'], 2) }}</h3>
                            <small class="text-muted">{{ $statistics['taux_affectation'] }}%</small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Heures Réalisées</h6>
                            <h3 class="text-success mb-0">{{ number_format($statistics['heures_realisees'], 2) }}</h3>
                            <small class="text-muted">{{ $statistics['taux_realisation'] }}%</small>
                        </div>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Différence</h6>
                            <h3 class="{{ $statistics['difference'] < 0 ? 'text-danger' : 'text-info' }} mb-0">
                                {{ number_format($statistics['difference'], 2) }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des Établissements -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-building text-primary me-2"></i>Établissements du Complexe</h5>
                <span class="badge bg-primary">{{ $etablissementsStats->count() }} établissement(s)</span>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($etablissementsStats as $etab)
                    <div class="col-md-6 mb-3">
                        <div class="card etablissement-card border h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1">{{ $etab['nom_efp'] }}</h5>
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-barcode me-1"></i>{{ $etab['code_efp'] }}
                                        </p>
                                    </div>
                                    <span class="badge badge-taux 
                                        @if($etab['taux_realisation'] >= 80) bg-success 
                                        @elseif($etab['taux_realisation'] >= 50) bg-warning 
                                        @else bg-danger 
                                        @endif">
                                        {{ $etab['taux_realisation'] }}%
                                    </span>
                                </div>
                                
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="fw-bold text-primary">{{ $etab['nb_formations'] }}</div>
                                            <small class="text-muted">Formations</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="fw-bold text-success">{{ $etab['nb_apprenants'] }}</div>
                                            <small class="text-muted">Apprenants</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="fw-bold text-warning">{{ $etab['nb_formateurs'] }}</div>
                                            <small class="text-muted">Formateurs</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center p-2 bg-light rounded">
                                            <div class="fw-bold text-info">{{ $etab['nb_groupes'] }}</div>
                                            <small class="text-muted">Groupes</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between small mb-1">
                                        <span>Heures requises:</span>
                                        <strong>{{ number_format($etab['heures_requises'], 2) }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between small mb-2">
                                        <span>Heures réalisées:</span>
                                        <strong class="text-success">{{ number_format($etab['heures_realisees'], 2) }}</strong>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar 
                                            @if($etab['taux_realisation'] >= 80) bg-success 
                                            @elseif($etab['taux_realisation'] >= 50) bg-warning 
                                            @else bg-danger 
                                            @endif" 
                                            role="progressbar" 
                                            style="width: {{ min($etab['taux_realisation'], 100) }}%;" 
                                            aria-valuenow="{{ $etab['taux_realisation'] }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('administration.complexe.etablissements.show', $etab['code_efp']) }}" 
                                   class="btn btn-primary w-100">
                                    <i class="fas fa-eye me-2"></i>Voir le détail
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Aucun établissement trouvé dans ce complexe
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques -->
<div class="row mb-4">
    <div class="col-lg-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-bar text-info me-2"></i>Taux de Réalisation</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="tauxRealisationChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-bar text-warning me-2"></i>Taux d'Affectation</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="tauxAffectationChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-pie text-success me-2"></i>Répartition des Heures par Semestre</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="heuresSemestreChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-pie text-primary me-2"></i>Heures Réalisées par Mode</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="heuresModeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-chart-line text-danger me-2"></i>Taux de Réalisation par Mode de Formation</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="tauxParModeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tableau Détaillé avec Pagination Corrigée -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-table text-primary me-2"></i>Données Détaillées</h5>
                <span class="badge bg-primary">{{ $detailedData->total() }} enregistrements</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Année</th>
                                <th>EFP</th>
                                <th>Secteur</th>
                                <th>Filière</th>
                                <th>Groupe</th>
                                <th>Effectif</th>
                                <th>Module</th>
                                <th>Formateur</th>
                                <th>Mode</th>
                                <th>MH Total</th>
                                <th>MH Aff.</th>
                                <th>MH Réal.</th>
                                <th>Taux Réal.</th>
                                <th>Validation EFM</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($detailedData as $row)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $row->annee }}</span></td>
                                <td>
                                    <strong>{{ $row->efp }}</strong>
                                    <br><small class="text-muted">{{ $row->code_efp }}</small>
                                </td>
                                <td>{{ $row->secteur }}</td>
                                <td>
                                    {{ $row->filiere }}
                                    <br><small class="text-muted">{{ $row->code_filiere }}</small>
                                </td>
                                <td><span class="badge bg-info">{{ $row->groupe }}</span></td>
                                <td><strong>{{ $row->effectif_groupe }}</strong></td>
                                <td>
                                    {{ $row->module }}
                                    <br><small class="text-muted">{{ $row->code_module }}</small>
                                </td>
                                <td>
                                    @if($row->formateur_presentiel)
                                        <i class="fas fa-chalkboard-teacher text-primary me-1"></i>{{ $row->formateur_presentiel }}
                                    @endif
                                    @if($row->formateur_syn)
                                        <br><i class="fas fa-video text-info me-1"></i>{{ $row->formateur_syn }}
                                    @endif
                                </td>
                                <td>
                                    @if($row->mode == 'Présentiel')
                                        <span class="badge bg-primary">{{ $row->mode }}</span>
                                    @elseif($row->mode == 'Synchrone')
                                        <span class="badge bg-info">{{ $row->mode }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $row->mode }}</span>
                                    @endif
                                </td>
                                <td><strong>{{ number_format($row->mh_totale_drif, 2) }}h</strong></td>
                                <td class="text-warning"><strong>{{ number_format($row->mh_affectee_globale, 2) }}h</strong></td>
                                <td class="text-success"><strong>{{ number_format($row->mh_realisee_globale, 2) }}h</strong></td>
                                <td>
                                    @php
                                        $taux = $row->taux_realisation_global;
                                    @endphp
                                    <span class="badge 
                                        @if($taux >= 80) bg-success 
                                        @elseif($taux >= 50) bg-warning 
                                        @else bg-danger 
                                        @endif">
                                        {{ $taux }}%
                                    </span>
                                </td>
                                <td>
                                    @if($row->validation_efm == 'Oui' || $row->validation_efm == 'OUI')
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Validé</span>
                                    @elseif($row->validation_efm == 'Non' || $row->validation_efm == 'NON')
                                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Non validé</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="14" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">Aucune donnée disponible</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($detailedData->hasPages())
            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="pagination-info mb-2 mb-md-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Affichage de {{ $detailedData->firstItem() }} à {{ $detailedData->lastItem() }} sur {{ $detailedData->total() }} résultats
                    </div>
                    <nav aria-label="Pagination">
                        {{ $detailedData->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData);
    const statistics = @json($statistics);
    
    console.log('Chart Data:', chartData);
    console.log('Statistics:', statistics);

    // Configuration commune
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12
                    }
                }
            }
        }
    };

    // Graphique Taux de Réalisation
    const ctx1 = document.getElementById('tauxRealisationChart');
    if (ctx1) {
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: ['Réalisé', 'Restant'],
                datasets: [{
                    data: [
                        parseFloat(statistics.taux_realisation) || 0, 
                        100 - (parseFloat(statistics.taux_realisation) || 0)
                    ],
                    backgroundColor: ['#28a745', '#e9ecef'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        display: true,
                        text: (parseFloat(statistics.taux_realisation) || 0).toFixed(2) + '% Réalisé',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
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

    // Graphique Taux d'Affectation
    const ctx2 = document.getElementById('tauxAffectationChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Affecté', 'Non Affecté'],
                datasets: [{
                    data: [
                        parseFloat(statistics.taux_affectation) || 0, 
                        100 - (parseFloat(statistics.taux_affectation) || 0)
                    ],
                    backgroundColor: ['#ffc107', '#e9ecef'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        display: true,
                        text: (parseFloat(statistics.taux_affectation) || 0).toFixed(2) + '% Affecté',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
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

    // Graphique Heures par Semestre
    const ctx3 = document.getElementById('heuresSemestreChart');
    if (ctx3 && chartData.heures_par_semestre) {
        new Chart(ctx3, {
            type: 'bar',
            data: {
                labels: ['S1 Présentiel', 'S1 Synchrone', 'S1 Asynchrone', 'S2 Présentiel', 'S2 Synchrone', 'S2 Asynchrone'],
                datasets: [{
                    label: 'Heures',
                    data: [
                        parseFloat(chartData.heures_par_semestre.s1.presentiel) || 0,
                        parseFloat(chartData.heures_par_semestre.s1.synchrone) || 0,
                        parseFloat(chartData.heures_par_semestre.s1.asynchrone) || 0,
                        parseFloat(chartData.heures_par_semestre.s2.presentiel) || 0,
                        parseFloat(chartData.heures_par_semestre.s2.synchrone) || 0,
                        parseFloat(chartData.heures_par_semestre.s2.asynchrone) || 0
                    ],
                    backgroundColor: ['#007bff', '#17a2b8', '#6c757d', '#007bff', '#17a2b8', '#6c757d'],
                    borderRadius: 5
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toFixed(0) + 'h';
                            }
                        }
                    }
                },
                plugins: {
                    ...commonOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + 'h';
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique Heures Réalisées par Mode
    const ctx4 = document.getElementById('heuresModeChart');
    if (ctx4 && chartData.heures_par_mode) {
        const presentiel = parseFloat(chartData.heures_par_mode.presentiel) || 0;
        const synchrone = parseFloat(chartData.heures_par_mode.synchrone) || 0;
        const totalHeures = presentiel + synchrone;
        
        const pourcentagePresentiel = totalHeures > 0 ? ((presentiel / totalHeures) * 100).toFixed(2) : 0;
        const pourcentageSynchrone = totalHeures > 0 ? ((synchrone / totalHeures) * 100).toFixed(2) : 0;

        new Chart(ctx4, {
            type: 'pie',
            data: {
                labels: [
                    `Présentiel (${pourcentagePresentiel}%)`,
                    `Synchrone (${pourcentageSynchrone}%)`
                ],
                datasets: [{
                    data: [presentiel, synchrone],
                    backgroundColor: ['#007bff', '#17a2b8'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed.toFixed(2) + 'h';
                            }
                        }
                    }
                }
            }
        });
    }

    // Graphique Taux de Réalisation par Mode
    const ctx5 = document.getElementById('tauxParModeChart');
    if (ctx5 && chartData.taux_par_mode) {
        const affecteePres = parseFloat(chartData.taux_par_mode.presentiel.affectee) || 0;
        const realiseePres = parseFloat(chartData.taux_par_mode.presentiel.realisee) || 0;
        const affecteeSyn = parseFloat(chartData.taux_par_mode.synchrone.affectee) || 0;
        const realiseeSyn = parseFloat(chartData.taux_par_mode.synchrone.realisee) || 0;
        
        const tauxPresentiel = affecteePres > 0 ? (realiseePres / affecteePres * 100).toFixed(2) : 0;
        const tauxSynchrone = affecteeSyn > 0 ? (realiseeSyn / affecteeSyn * 100).toFixed(2) : 0;

        new Chart(ctx5, {
            type: 'bar',
            data: {
                labels: ['Présentiel', 'Synchrone'],
                datasets: [{
                    label: 'Taux de Réalisation (%)',
                    data: [tauxPresentiel, tauxSynchrone],
                    backgroundColor: ['#28a745', '#17a2b8'],
                    borderRadius: 5
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    ...commonOptions.plugins,
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + '%';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush