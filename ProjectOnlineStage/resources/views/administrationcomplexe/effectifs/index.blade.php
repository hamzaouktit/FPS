@extends('layouts.app')

@section('title', 'Effectifs par Établissement - ' . $complexe->nom)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><i class="fas fa-home me-1"></i><a href="{{ route('welcome') }}">Accueil</a></li>
        <li class="breadcrumb-item"><a href="{{ route('administration.complexe.dashboard') }}">Administration</a></li>
        <li class="breadcrumb-item active" aria-current="page">Effectifs par Établissement</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-users text-primary me-2"></i> Effectifs par Établissement</h1>
        <button onclick="window.print()" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Exporter PDF
        </button>
    </div>

    <!-- Top KPIs -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Apprenants</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($totalApprenants) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-graduate fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Groupes</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($totalGroupes) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Filières</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($totalFilieres) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-sitemap fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Secteurs</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($totalSecteurs) }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-industry fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 1: Summary table + Pie Chart -->
    <div class="row">
        <!-- Tableau résumé par établissement -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-table me-1"></i> Résumé par Établissement</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Établissement</th>
                                    <th class="text-center">Groupes</th>
                                    <th class="text-center">Filières</th>
                                    <th class="text-center">Effectif Total</th>
                                    <th class="text-center">Moy./Groupe</th>
                                    <th class="text-center">Part (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($effectifsParEtablissement as $etab)
                                    @php $part = $totalApprenants > 0 ? round($etab->effectif_total / $totalApprenants * 100, 1) : 0; @endphp
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-truncate d-inline-block" 
                                                  style="max-width: 160px; cursor:help;" 
                                                  data-bs-toggle="tooltip" 
                                                  data-bs-placement="top" 
                                                  title="{{ $etab->nom_efp }} ({{ $etab->code_efp }})">
                                                {{ $etab->nom_efp }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $etab->nombre_groupes }}</td>
                                        <td class="text-center">{{ $etab->nb_filieres }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-primary fs-6">{{ $etab->effectif_total }}</span>
                                        </td>
                                        <td class="text-center text-muted">
                                            {{ $etab->nombre_groupes > 0 ? round($etab->effectif_moyen, 1) : '—' }}
                                        </td>
                                        <td class="text-center" style="min-width: 110px;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="progress flex-grow-1 me-1" style="height: 8px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $part }}%"></div>
                                                </div>
                                                <small class="fw-bold">{{ $part }}%</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td>TOTAL</td>
                                    <td class="text-center">{{ $totalGroupes }}</td>
                                    <td class="text-center">{{ $totalFilieres }}</td>
                                    <td class="text-center"><span class="badge bg-success fs-6">{{ $totalApprenants }}</span></td>
                                    <td class="text-center text-muted">
                                        {{ $totalGroupes > 0 ? round($totalApprenants / $totalGroupes, 1) : '—' }}
                                    </td>
                                    <td class="text-center">100%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pie Chart répartition par établissement -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie me-1"></i> Répartition Visuelle</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <div style="height: 300px;">
                        <canvas id="effectifsPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Secteur table + Année Chart -->
    <div class="row">
        <!-- Tableau par Secteur -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-industry me-1"></i> Effectifs par Secteur</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Secteur</th>
                                    <th class="text-center">Filières</th>
                                    <th class="text-center">Groupes</th>
                                    <th class="text-center">Effectif</th>
                                    <th class="text-center">Part</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($effectifsParSecteur as $secteur)
                                    @php $part = $totalApprenants > 0 ? round($secteur->effectif_total / $totalApprenants * 100, 1) : 0; @endphp
                                    <tr>
                                        <td>
                                            <span class="text-truncate d-inline-block fw-bold" 
                                                  style="max-width: 150px; cursor:help;"
                                                  data-bs-toggle="tooltip" 
                                                  data-bs-placement="top" 
                                                  title="{{ $secteur->nom_secteur }}">
                                                {{ $secteur->nom_secteur }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ $secteur->nombre_filieres }}</td>
                                        <td class="text-center">{{ $secteur->nombre_groupes }}</td>
                                        <td class="text-center"><span class="badge bg-info text-dark">{{ $secteur->effectif_total }}</span></td>
                                        <td class="text-center">
                                            <div class="d-flex align-items-center justify-content-between" style="min-width: 80px;">
                                                <div class="progress flex-grow-1 me-1" style="height: 6px;">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $part }}%"></div>
                                                </div>
                                                <small>{{ $part }}%</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bar Chart Année de formation -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar me-1"></i> Effectifs par Année de Formation</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="anneeBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Top 10 Filières bar chart -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar me-1"></i> Top 10 Filières par Effectif</h6>
                </div>
                <div class="card-body">
                    <div style="height: 320px;">
                        <canvas id="filiereBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Détail complet par Établissement → Filière -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-list-alt me-1"></i> Détail Complet : Établissement → Secteur → Filière</h6>
                    <small class="text-muted">Survolez les noms pour afficher le texte complet</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0" style="font-size: 0.82rem;">
                            <thead class="table-dark">
                                <tr>
                                    <th>Établissement</th>
                                    <th>Secteur</th>
                                    <th>Filière</th>
                                    <th>Code</th>
                                    <th class="text-center">Groupes</th>
                                    <th class="text-center">Années</th>
                                    <th class="text-center">Effectif</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $lastEtab = null; @endphp
                                @foreach($detailParEtabFiliere as $ligne)
                                    @php $isNewEtab = $lastEtab !== $ligne->code_efp; $lastEtab = $ligne->code_efp; @endphp
                                    <tr class="{{ $isNewEtab ? 'table-primary' : '' }}">
                                        <td class="fw-bold">
                                            @if($isNewEtab)
                                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $ligne->nom_efp }}">
                                                    <i class="fas fa-school me-1 text-primary"></i>
                                                    <span class="text-truncate d-inline-block" style="max-width: 140px; cursor:help; vertical-align: middle;">{{ $ligne->nom_efp }}</span>
                                                </span>
                                            @else
                                                <span class="text-muted ms-3">↳</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block" 
                                                  style="max-width: 130px; cursor:help;" 
                                                  data-bs-toggle="tooltip" data-bs-placement="top" 
                                                  title="{{ $ligne->nom_secteur }}">
                                                {{ $ligne->nom_secteur }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-truncate d-inline-block" 
                                                  style="max-width: 160px; cursor:help;" 
                                                  data-bs-toggle="tooltip" data-bs-placement="top" 
                                                  title="{{ $ligne->nom_filiere }}">
                                                {{ $ligne->nom_filiere }}
                                            </span>
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $ligne->code_filiere }}</span></td>
                                        <td class="text-center">{{ $ligne->nombre_groupes }}</td>
                                        <td class="text-center">
                                            <span class="text-truncate d-inline-block" 
                                                  style="max-width: 80px; cursor:help;"
                                                  data-bs-toggle="tooltip" data-bs-placement="top" 
                                                  title="Années: {{ $ligne->annees }}">
                                                {{ $ligne->annees }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success">{{ $ligne->effectif_total }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
    tooltipTriggerList.forEach(el => {
        new bootstrap.Tooltip(el, {
            trigger: 'hover',
            boundary: 'window'
        });
    });

    Chart.defaults.font.family = "'Nunito', '-apple-system', 'system-ui', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', sans-serif";
    Chart.defaults.color = '#858796';

    const couleurs = ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#5a5c69','#f8f9fc','#2e59d9','#17a673'];

    // 1. Pie Chart: par établissement
    const dataEtab = @json($effectifsParEtablissement);
    const ctxPie = document.getElementById("effectifsPieChart");
    if(ctxPie) {
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: dataEtab.map(e => e.nom_efp),
                datasets: [{
                    data: dataEtab.map(e => e.effectif_total),
                    backgroundColor: couleurs,
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10 } },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = ((ctx.parsed / total) * 100).toFixed(1);
                                return ` ${ctx.label}: ${ctx.parsed} apprenants (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Bar Chart: Année de formation
    const dataAnnee = @json($effectifsParAnnee);
    const ctxAnnee = document.getElementById("anneeBarChart");
    if(ctxAnnee) {
        new Chart(ctxAnnee, {
            type: 'bar',
            data: {
                labels: dataAnnee.map(a => a.annee_formation || 'Non défini'),
                datasets: [{
                    label: "Apprenants",
                    backgroundColor: "#36b9cc",
                    hoverBackgroundColor: "#2c9faf",
                    data: dataAnnee.map(a => a.effectif_total),
                },{
                    label: "Groupes",
                    backgroundColor: "#f6c23e",
                    hoverBackgroundColor: "#dda20a",
                    data: dataAnnee.map(a => a.nombre_groupes),
                    yAxisID: 'y2'
                }],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Apprenants' } },
                    y2: { position: 'right', beginAtZero: true, title: { display: true, text: 'Groupes' }, grid: { drawOnChartArea: false } }
                }
            }
        });
    }

    // 3. Bar Chart: Top 10 Filières
    const dataFiliere = @json($effectifsParFiliere);
    const ctxFiliere = document.getElementById("filiereBarChart");
    if(ctxFiliere) {
        new Chart(ctxFiliere, {
            type: 'bar',
            data: {
                labels: dataFiliere.map(f => `[${f.code_filiere}] ${f.nom_filiere}`),
                datasets: [{
                    label: "Effectif",
                    backgroundColor: "#1cc88a",
                    hoverBackgroundColor: "#17a673",
                    data: dataFiliere.map(f => f.effectif_total),
                }],
            },
            options: {
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: { x: { beginAtZero: true } },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ` ${ctx.parsed.x} apprenants`;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>

<style>
    .border-left-primary { border-left: .25rem solid #4e73df !important; }
    .border-left-success { border-left: .25rem solid #1cc88a !important; }
    .border-left-info { border-left: .25rem solid #36b9cc !important; }
    .border-left-warning { border-left: .25rem solid #f6c23e !important; }
    .text-gray-300 { color: #dddfeb !important; }
    .text-gray-800 { color: #5a5c69 !important; }
    .text-truncate { overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
    
    @media print {
        .navbar, .sidebar, .breadcrumb, .btn { display: none !important; }
        .card { border: none !important; box-shadow: none !important; }
        canvas { max-width: 100% !important; }
    }
</style>
@endpush
@endsection
