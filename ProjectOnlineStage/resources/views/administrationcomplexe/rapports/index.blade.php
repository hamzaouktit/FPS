@extends('layouts.app')

@section('title', 'Rapports Globaux (Graphiques) - ' . $complexe->nom)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><i class="fas fa-home me-1"></i><a href="{{ route('welcome') }}">Accueil</a></li>
        <li class="breadcrumb-item"><a href="{{ route('administration.complexe.dashboard') }}">Administration</a></li>
        <li class="breadcrumb-item active" aria-current="page">Rapports Globaux</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-pie text-primary me-2"></i> Rapports Visuels Globaux</h1>
        <button onclick="window.print()" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Exporter PDF
        </button>
    </div>

    <!-- Zone des Filtres -->
    <div class="card shadow mb-4 filters-section">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-1"></i> Filtres d'Analyse</h6>
        </div>
        <div class="card-body bg-light">
            <form method="GET" action="{{ route('administration.complexe.rapports.globaux') }}">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label small text-muted fw-bold">Établissement</label>
                        <select name="etablissement" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            @foreach($filterOptions['etablissements'] as $etab)
                                <option value="{{ $etab->code_efp }}" {{ request('etablissement') == $etab->code_efp ? 'selected' : '' }}>
                                    {{ $etab->nom_efp }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted fw-bold">Secteur</label>
                        <select name="secteur" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            @foreach($filterOptions['secteurs'] as $sec)
                                <option value="{{ $sec->nom_secteur }}" {{ request('secteur') == $sec->nom_secteur ? 'selected' : '' }}>
                                    {{ $sec->nom_secteur }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted fw-bold">Filière</label>
                        <select name="filiere" class="form-select form-select-sm">
                            <option value="">Toutes</option>
                            @foreach($filterOptions['filieres'] as $fil)
                                <option value="{{ $fil->code_filiere }}" {{ request('filiere') == $fil->code_filiere ? 'selected' : '' }}>
                                    {{ $fil->code_filiere }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted fw-bold">Formateur</label>
                        <select name="formateur" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            @foreach($filterOptions['formateurs'] as $form)
                                <option value="{{ $form->mle }}" {{ request('formateur') == $form->mle ? 'selected' : '' }}>
                                    {{ $form->nom_complet }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted fw-bold">Groupe</label>
                        <select name="groupe" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            @foreach($filterOptions['groupes'] as $grp)
                                <option value="{{ $grp->code_groupe }}" {{ request('groupe') == $grp->code_groupe ? 'selected' : '' }}>
                                    {{ $grp->code_groupe }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100 shadow-sm">
                            <i class="fas fa-search fa-sm"></i> Filtrer
                        </button>
                        @if(request()->anyFilled(['etablissement', 'secteur', 'filiere', 'formateur', 'groupe']))
                            <a href="{{ route('administration.complexe.rapports.globaux') }}" class="btn btn-outline-secondary btn-sm ms-2" title="Réinitialiser">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Top KPIs -->
    <div class="row">
        <!-- Taux de Réalisation Global -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Taux de Réalisation Global</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h4 mb-0 mr-3 font-weight-bold text-gray-800">{{ $statistics['taux_realisation'] }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $statistics['taux_realisation'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Heures Réalisées -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Heures Réalisées</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $statistics['heures_realisees'] }} h</div>
                            <div class="text-xs text-muted mt-1">sur {{ $statistics['heures_requises'] }} h prévues</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Taux d'Affectation -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Taux d'Affectation</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h4 mb-0 mr-3 font-weight-bold text-gray-800">{{ $statistics['taux_affectation'] }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $statistics['taux_affectation'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Groupes Encadrés -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Groupes Encadrés</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $statistics['nb_groupes'] }}</div>
                            <div class="text-xs text-muted mt-1">{{ $statistics['nb_apprenants'] }} apprenants</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row Charts -->
    <div class="row">
        <!-- Chart 1: Avancement par Établissement (Bar) -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar me-1"></i> Avancement par Établissement</h6>
                </div>
                <div class="card-body">
                    <div style="height: 350px;">
                        <canvas id="etablissementBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Heures par Semestre (Doughnut) -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie me-1"></i> Heures Prévues par Semestre</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div style="height: 250px;">
                        <canvas id="semestrePieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="me-2"><i class="fas fa-circle text-primary"></i> Semestre 1</span>
                        <span class="me-2"><i class="fas fa-circle text-success"></i> Semestre 2</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Chart 3: Heures Réalisées par Mode (Pie) -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie me-1"></i> Heures Réalisées par Mode</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="modeDoughnutChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 4: Comparaison Modes Affecté vs Réalisé (Bar) -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar me-1"></i> Comparaison Globale des Modes</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="modeBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Activer les tooltips Bootstrap
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el, { trigger: 'hover', boundary: 'window' }));

    // Configuration globale Chart.js
    Chart.defaults.font.family = "'Nunito', '-apple-system', 'system-ui', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', sans-serif";
    Chart.defaults.color = '#858796';

    // Données préparées par le backend
    const chartData = @json($chartData);
    const etabStats = @json($etablissementsStats);

    // 1. Bar Chart: Etablissements
    const ctxEtab = document.getElementById("etablissementBarChart");
    if(ctxEtab) {
        new Chart(ctxEtab, {
            type: 'bar',
            data: {
                labels: etabStats.map(e => e.nom_efp),
                datasets: [{
                    label: "Heures Réalisées",
                    backgroundColor: "#1cc88a",
                    hoverBackgroundColor: "#17a673",
                    borderColor: "#1cc88a",
                    data: etabStats.map(e => e.heures_realisees),
                }, {
                    label: "Heures Prévues",
                    backgroundColor: "#4e73df",
                    hoverBackgroundColor: "#2e59d9",
                    borderColor: "#4e73df",
                    data: etabStats.map(e => e.heures_requises),
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            title: function(ctx) {
                                // Afficher le nom complet dans le tooltip
                                return etabStats[ctx[0].dataIndex].nom_efp;
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false, drawBorder: false } },
                    y: { ticks: { beginAtZero: true } }
                }
            }
        });
    }

    // 2. Pie Chart: Semestres
    const ctxSemestre = document.getElementById("semestrePieChart");
    if(ctxSemestre) {
        const s1Total = chartData.heures_par_semestre.s1.presentiel + chartData.heures_par_semestre.s1.synchrone + chartData.heures_par_semestre.s1.asynchrone;
        const s2Total = chartData.heures_par_semestre.s2.presentiel + chartData.heures_par_semestre.s2.synchrone + chartData.heures_par_semestre.s2.asynchrone;
        new Chart(ctxSemestre, {
            type: 'doughnut',
            data: {
                labels: ["Semestre 1", "Semestre 2"],
                datasets: [{
                    data: [s1Total, s2Total],
                    backgroundColor: ['#4e73df', '#1cc88a'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });
    }

    // 3. Pie Chart: Mode
    const ctxMode = document.getElementById("modeDoughnutChart");
    if(ctxMode) {
        new Chart(ctxMode, {
            type: 'pie',
            data: {
                labels: ["Présentiel", "Synchrone"],
                datasets: [{
                    data: [chartData.heures_par_mode.presentiel, chartData.heures_par_mode.synchrone],
                    backgroundColor: ['#36b9cc', '#f6c23e'],
                    hoverBackgroundColor: ['#2c9faf', '#dda20a'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: { maintainAspectRatio: false }
        });
    }

    // 4. Bar Chart: Comparaison Modes (Affecté vs Réalisé)
    const ctxModeBar = document.getElementById("modeBarChart");
    if(ctxModeBar) {
        new Chart(ctxModeBar, {
            type: 'bar',
            data: {
                labels: ["Présentiel", "Synchrone"],
                datasets: [{
                    label: "Affecté",
                    backgroundColor: "#f6c23e",
                    data: [chartData.taux_par_mode.presentiel.affectee, chartData.taux_par_mode.synchrone.affectee],
                }, {
                    label: "Réalisé",
                    backgroundColor: "#36b9cc",
                    data: [chartData.taux_par_mode.presentiel.realisee, chartData.taux_par_mode.synchrone.realisee],
                }],
            },
            options: {
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });
    }
});
</script>

<style>
    .border-left-primary { border-left: .25rem solid #4e73df !important; }
    .border-left-success { border-left: .25rem solid #1cc88a !important; }
    .border-left-info    { border-left: .25rem solid #36b9cc !important; }
    .border-left-warning { border-left: .25rem solid #f6c23e !important; }
    .text-gray-300 { color: #dddfeb !important; }
    .text-gray-800 { color: #5a5c69 !important; }

    @media print {
        .navbar, .sidebar, .breadcrumb, .filters-section, .btn { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        .col-lg-8, .col-lg-4, .col-lg-6 { width: 100% !important; margin-bottom: 20px !important; }
        canvas { max-width: 100% !important; }
    }
</style>
@endpush
@endsection
