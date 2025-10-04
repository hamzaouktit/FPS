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

<!-- Tableau Détaillé -->
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
                                <th>Date MAJ</th>
                                <th>Année</th>
                                <th>Code EFP</th>
                                <th>EFP</th>
                                <th>Niveau</th>
                                <th>Secteur</th>
                                <th>Code Filière</th>
                                <th>Filière</th>
                                <th>Type Formation</th>
                                <th>Créneau</th>
                                <th>Groupe</th>
                                <th>Effectif</th>
                                <th>Sous Groupe</th>
                                <th>Statut SG</th>
                                <th>Fusion</th>
                                <th>Code Fusion</th>
                                <th>Année Form</th>
                                <th>Mode</th>
                                <th>Code Module</th>
                                <th>Module</th>
                                <th>Régional</th>
                                <th>Mle Présentiel</th>
                                <th>Formateur Présentiel</th>
                                <th>Mle Syn</th>
                                <th>Formateur Syn</th>
                                <th>MHP S1</th>
                                <th>MHSYN S1</th>
                                <th>MHASYN S1</th>
                                <th>MH Total S1</th>
                                <th>MHP S2</th>
                                <th>MHSYN S2</th>
                                <th>MHASYN S2</th>
                                <th>MH Total S2</th>
                                <th>MHP Total</th>
                                <th>MHSYN Total</th>
                                <th>MHASYN Total</th>
                                <th>MH Total</th>
                                <th>MH Aff. Prés</th>
                                <th>MH Aff. Sync</th>
                                <th>MH Aff. Global</th>
                                <th>MH Réal. Prés</th>
                                <th>MH Réal. Sync</th>
                                <th>MH Réal. Global</th>
                                <th>Taux Réal. Prés</th>
                                <th>Taux Réal. Syn</th>
                                <th>Taux Réal. Global</th>
                                <th>Moy Absence</th>
                                <th>NB CC</th>
                                <th>Séance EFM</th>
                                <th>Valid. EFM</th>
                                <th>Classe Teams</th>
                                <th>Module PIE</th>
                                <th>EFP PIE</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($detailedData as $row)
                            <tr>
                                <td>{{ $row->date_maj ? \Carbon\Carbon::parse($row->date_maj)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $row->annee }}</td>
                                <td>{{ $row->code_efp }}</td>
                                <td>{{ $row->efp }}</td>
                                <td>{{ $row->niveau }}</td>
                                <td>{{ $row->secteur }}</td>
                                <td>{{ $row->code_filiere }}</td>
                                <td>{{ $row->filiere }}</td>
                                <td>{{ $row->type_formation }}</td>
                                <td>{{ $row->creneau }}</td>
                                <td>{{ $row->groupe }}</td>
                                <td>{{ $row->effectif_groupe }}</td>
                                <td>{{ $row->sous_groupe }}</td>
                                <td>{{ $row->statut_sous_groupe }}</td>
                                <td>{{ $row->fusion_groupe }}</td>
                                <td>{{ $row->code_fusion }}</td>
                                <td>{{ $row->annee_formation }}</td>
                                <td>{{ $row->mode }}</td>
                                <td>{{ $row->code_module }}</td>
                                <td>{{ $row->module }}</td>
                                <td>{{ $row->regional }}</td>
                                <td>{{ $row->mle_presentiel }}</td>
                                <td>{{ $row->formateur_presentiel }}</td>
                                <td>{{ $row->mle_syn }}</td>
                                <td>{{ $row->formateur_syn }}</td>
                                <td>{{ $row->mhp_s1_drif }}</td>
                                <td>{{ $row->mhsyn_s1_drif }}</td>
                                <td>{{ $row->mhasyn_s1_drif }}</td>
                                <td>{{ $row->mh_totale_s1_drif }}</td>
                                <td>{{ $row->mhp_s2_drif }}</td>
                                <td>{{ $row->mhsyn_s2_drif }}</td>
                                <td>{{ $row->mhasyn_s2_drif }}</td>
                                <td>{{ $row->mh_totale_s2_drif }}</td>
                                <td>{{ $row->mhp_totale_drif }}</td>
                                <td>{{ $row->mhsyn_totale_drif }}</td>
                                <td>{{ $row->mhasyn_totale_drif }}</td>
                                <td>{{ $row->mh_totale_drif }}</td>
                                <td>{{ $row->mh_affectee_presentiel }}</td>
                                <td>{{ $row->mh_affectee_sync }}</td>
                                <td>{{ $row->mh_affectee_globale }}</td>
                                <td>{{ $row->mh_realisee_presentiel }}</td>
                                <td>{{ $row->mh_realisee_sync }}</td>
                                <td>{{ $row->mh_realisee_globale }}</td>
                                <td>{{ $row->taux_realisation_presentiel }}%</td>
                                <td>{{ $row->taux_realisation_syn }}%</td>
                                <td>{{ $row->taux_realisation_global }}%</td>
                                <td>{{ $row->moy_absence }}</td>
                                <td>{{ $row->nb_cc }}</td>
                                <td>{{ $row->seance_efm }}</td>
                                <td>{{ $row->validation_efm }}</td>
                                <td>{{ $row->classe_teams }}</td>
                                <td>{{ $row->module_pie }}</td>
                                <td>{{ $row->efp_pie }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="53" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Aucune donnée disponible</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($detailedData->hasPages())
            <div class="card-footer bg-light">
                {{ $detailedData->links() }}
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
    // Données des graphiques
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
                position: 'bottom'
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
                    borderWidth: 0
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        display: true,
                        text: (parseFloat(statistics.taux_realisation) || 0).toFixed(2) + '% Réalisé'
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
                    borderWidth: 0
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        display: true,
                        text: (parseFloat(statistics.taux_affectation) || 0).toFixed(2) + '% Affecté'
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
                    backgroundColor: ['#007bff', '#17a2b8', '#6c757d', '#007bff', '#17a2b8', '#6c757d']
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true
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
                    borderWidth: 0
                }]
            },
            options: commonOptions
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
                    backgroundColor: ['#28a745', '#17a2b8']
                }]
            },
            options: {
                ...commonOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }
});
</script>
@endpush