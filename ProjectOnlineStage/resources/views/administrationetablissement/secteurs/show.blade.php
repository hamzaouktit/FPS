@extends('layouts.app')

@section('title', 'Détails du Secteur')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">{{ $secteur->nom }}</h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-barcode me-2"></i>Code: <code>{{ $secteur->code }}</code>
                        <span class="mx-2">|</span>
                        <i class="fas fa-building me-2"></i>{{ $etablissement->nom_efp }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('administration.etablissement.secteurs.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                    <a href="{{ route('administration.etablissement.secteurs.edit', $secteur->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte d'information établissement -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-building fa-3x text-info"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">{{ $etablissement->nom_efp }}</h5>
                            <p class="text-muted mb-0">
                                <i class="fas fa-tag me-2"></i>Code: <code>{{ $etablissement->code_efp }}</code>
                                <span class="mx-2">|</span>
                                <i class="fas fa-map-marker-alt me-2"></i>{{ $etablissement->ville }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-4">
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
        <div class="col-md-4">
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
        <div class="col-md-4">
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
                                <th>#</th>
                                <th>Code</th>
                                <th>Nom de la Filière</th>
                                <th>Niveau</th>
                                <th class="text-center">Groupes</th>
                                <th class="text-center">Stagiaires</th>
                                <th>Date Création</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($secteur->filieres as $index => $filiere)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><code>{{ $filiere->code }}</code></td>
                                    <td><strong>{{ $filiere->nom }}</strong></td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $filiere->niveau->nom ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $filiere->groupes->count() }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning">{{ $filiere->groupes->sum('effectif') }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $filiere->created_at->format('d/m/Y') }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-secondary">
                            <tr>
                                <th colspan="4" class="text-end">TOTAL:</th>
                                <th class="text-center">{{ $stats['total_groupes'] }}</th>
                                <th class="text-center">{{ $stats['total_stagiaires'] }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucune filière dans ce secteur pour votre établissement</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Groupes par Filière -->
    @if($secteur->filieres->count() > 0 && $stats['total_groupes'] > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>Groupes par Filière</h5>
            </div>
            <div class="card-body">
                @foreach($secteur->filieres as $filiere)
                    @if($filiere->groupes->count() > 0)
                        <div class="mb-4" id="filiere-{{ $filiere->id }}">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-primary mb-0">
                                    <i class="fas fa-stream me-2"></i>{{ $filiere->nom }}
                                    <span class="badge bg-info ms-2">{{ $filiere->niveau->nom ?? 'N/A' }}</span>
                                </h6>
                                <span class="badge bg-secondary">
                                    {{ $filiere->groupes->count() }} groupe(s) - {{ $filiere->groupes->sum('effectif') }} stagiaires
                                </span>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Code Groupe</th>
                                            <th>Année Formation</th>
                                            <th class="text-center">Effectif</th>
                                            <th>Formation</th>
                                            <th>Statut</th>
                                            <th class="text-center">Modules</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($filiere->groupes as $groupe)
                                            <tr>
                                                <td><strong>{{ $groupe->code }}</strong></td>
                                                <td>
                                                    <span class="badge bg-dark">Année {{ $groupe->annee_formation }}</span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-warning">{{ $groupe->effectif }}</span>
                                                </td>
                                                <td>
                                                    @if($groupe->formation)
                                                        <small>
                                                            <i class="fas fa-graduation-cap me-1"></i>{{ $groupe->formation->type }}
                                                            <br>
                                                            <i class="fas fa-clock me-1"></i>{{ $groupe->formation->mode }}
                                                        </small>
                                                    @else
                                                        <small class="text-muted">N/A</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge {{ $groupe->statut == 'Actif' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $groupe->statut }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-primary">
                                                        {{ $groupe->affectations->count() }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Répartition des Effectifs -->
    @if($secteur->filieres->count() > 0)
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Répartition par Filière
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="filiereChart" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Effectifs par Filière
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Filière</th>
                                        <th class="text-center">Groupes</th>
                                        <th class="text-end">Effectif</th>
                                        <th class="text-end">%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalEffectif = $stats['total_stagiaires'];
                                    @endphp
                                    @foreach($secteur->filieres as $filiere)
                                        @php
                                            $effectifFiliere = $filiere->groupes->sum('effectif');
                                            $pourcentage = $totalEffectif > 0 ? round(($effectifFiliere / $totalEffectif) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td>{{ Str::limit($filiere->nom, 30) }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $filiere->groupes->count() }}</span>
                                            </td>
                                            <td class="text-end">
                                                <strong>{{ $effectifFiliere }}</strong>
                                            </td>
                                            <td class="text-end">
                                                <small class="text-muted">{{ $pourcentage }}%</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-secondary">
                                    <tr>
                                        <th>TOTAL</th>
                                        <th class="text-center">{{ $stats['total_groupes'] }}</th>
                                        <th class="text-end">{{ $stats['total_stagiaires'] }}</th>
                                        <th class="text-end">100%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

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
                            <th width="40%">Code Secteur:</th>
                            <td><code class="fs-6">{{ $secteur->code }}</code></td>
                        </tr>
                        <tr>
                            <th>Nom Complet:</th>
                            <td><strong>{{ $secteur->nom }}</strong></td>
                        </tr>
                        <tr>
                            <th>Établissement:</th>
                            <td>
                                <i class="fas fa-building me-1"></i>{{ $etablissement->nom_efp }}
                                <br>
                                <small class="text-muted">Code: {{ $etablissement->code_efp }}</small>
                            </td>
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
                            <th>Nombre de filières:</th>
                            <td><span class="badge bg-primary fs-6">{{ $stats['total_filieres'] }}</span></td>
                        </tr>
                        <tr>
                            <th>Nombre de groupes:</th>
                            <td><span class="badge bg-info fs-6">{{ $stats['total_groupes'] }}</span></td>
                        </tr>
                        <tr>
                            <th>Statut:</th>
                            <td>
                                @if($secteur->filieres->count() > 0)
                                    <span class="badge bg-success fs-6">Actif avec formations</span>
                                @else
                                    <span class="badge bg-warning fs-6">Sans filières</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($secteur->filieres->count() > 0)
    // Données pour le graphique
    const labels = {!! json_encode($secteur->filieres->pluck('nom')) !!};
    const data = {!! json_encode($secteur->filieres->map(function($filiere) {
        return $filiere->groupes->sum('effectif');
    })) !!};

    // Couleurs pour le graphique
    const backgroundColors = [
        'rgba(54, 162, 235, 0.8)',
        'rgba(255, 99, 132, 0.8)',
        'rgba(255, 206, 86, 0.8)',
        'rgba(75, 192, 192, 0.8)',
        'rgba(153, 102, 255, 0.8)',
        'rgba(255, 159, 64, 0.8)',
        'rgba(201, 203, 207, 0.8)',
        'rgba(255, 99, 71, 0.8)'
    ];

    // Créer le graphique
    const ctx = document.getElementById('filiereChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' stagiaires (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endpush
@endsection