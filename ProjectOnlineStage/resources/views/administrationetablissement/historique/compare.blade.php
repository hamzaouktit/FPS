@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>
                    <i class="fas fa-exchange-alt"></i> Comparaison des Avancements
                </h2>
                <a href="{{ route('administration.etablissement.historique.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à l'historique
                </a>
            </div>
        </div>
    </div>

    <!-- En-tête de comparaison -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <i class="fas fa-calendar-alt"></i> Période de comparaison
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-5">
                    <h4>{{ $date1->format('d/m/Y') }}</h4>
                    <p class="text-muted">Date initiale</p>
                </div>
                <div class="col-md-2">
                    <i class="fas fa-arrow-right fa-3x text-primary"></i>
                </div>
                <div class="col-md-5">
                    <h4>{{ $date2->format('d/m/Y') }}</h4>
                    <p class="text-muted">Date de comparaison</p>
                </div>
            </div>
            <div class="text-center mt-3">
                <span class="badge bg-secondary">
                    Écart: {{ $date1->diffInDays($date2) }} jour(s)
                </span>
            </div>
        </div>
    </div>

    <!-- Statistiques générales -->
    @php
        $progressionPositive = collect($comparaisons)->where('diff_taux', '>', 0)->count();
        $progressionNegative = collect($comparaisons)->where('diff_taux', '<', 0)->count();
        $progressionNulle = collect($comparaisons)->where('diff_taux', '=', 0)->count();
        $moyenneProgression = collect($comparaisons)->avg('diff_taux');
    @endphp

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>{{ $progressionPositive }}</h3>
                    <p>Progressions positives</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3>{{ $progressionNegative }}</h3>
                    <p>Progressions négatives</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h3>{{ $progressionNulle }}</h3>
                    <p>Sans changement</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3>{{ number_format($moyenneProgression, 2) }}%</h3>
                    <p>Progression moyenne</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau de comparaison -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-table"></i> Détails des comparaisons
            <span class="badge bg-light text-dark">{{ count($comparaisons) }} enregistrement(s)</span>
        </div>
        <div class="card-body">
            @if(count($comparaisons) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th rowspan="2">Formateur(s)</th>
                                <th rowspan="2">Module</th>
                                <th rowspan="2">Groupe</th>
                                <th colspan="3" class="text-center bg-secondary">MH Réalisée</th>
                                <th colspan="3" class="text-center bg-info">Taux de Réalisation</th>
                            </tr>
                            <tr>
                                <th class="text-center">{{ $date1->format('d/m') }}</th>
                                <th class="text-center">{{ $date2->format('d/m') }}</th>
                                <th class="text-center">Évolution</th>
                                <th class="text-center">{{ $date1->format('d/m') }}</th>
                                <th class="text-center">{{ $date2->format('d/m') }}</th>
                                <th class="text-center">Évolution</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comparaisons as $comp)
                            <tr>
                                <td>
                                    @if($comp['formateur_presentiel'])
                                        <div><strong>P:</strong> {{ $comp['formateur_presentiel'] }}</div>
                                    @endif
                                    @if($comp['formateur_syn'])
                                        <div><strong>S:</strong> {{ $comp['formateur_syn'] }}</div>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted d-block">{{ $comp['code_module'] }}</small>
                                    {{ Str::limit($comp['module'], 40) }}
                                </td>
                                <td>{{ $comp['groupe'] }}</td>
                                
                                <!-- MH Réalisée -->
                                <td class="text-end">{{ number_format($comp['mh_realisee_globale_avant'], 2) }}</td>
                                <td class="text-end">
                                    <strong>{{ number_format($comp['mh_realisee_globale_apres'], 2) }}</strong>
                                </td>
                                <td class="text-end">
                                    @php
                                        $diffMh = $comp['diff_mh'];
                                        $colorMh = $diffMh > 0 ? 'success' : ($diffMh < 0 ? 'danger' : 'secondary');
                                        $iconMh = $diffMh > 0 ? 'arrow-up' : ($diffMh < 0 ? 'arrow-down' : 'minus');
                                    @endphp
                                    <span class="badge bg-{{ $colorMh }}">
                                        <i class="fas fa-{{ $iconMh }}"></i>
                                        {{ number_format(abs($diffMh), 2) }}
                                    </span>
                                </td>
                                
                                <!-- Taux de réalisation -->
                                <td class="text-center">{{ number_format($comp['taux_realisation_avant'], 2) }}%</td>
                                <td class="text-center">
                                    <strong>{{ number_format($comp['taux_realisation_apres'], 2) }}%</strong>
                                </td>
                                <td class="text-center">
                                    @php
                                        $diffTaux = $comp['diff_taux'];
                                        $colorTaux = $diffTaux > 0 ? 'success' : ($diffTaux < 0 ? 'danger' : 'secondary');
                                        $iconTaux = $diffTaux > 0 ? 'arrow-up' : ($diffTaux < 0 ? 'arrow-down' : 'minus');
                                    @endphp
                                    <span class="badge bg-{{ $colorTaux }}">
                                        <i class="fas fa-{{ $iconTaux }}"></i>
                                        {{ number_format(abs($diffTaux), 2) }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Aucune donnée commune trouvée entre ces deux dates pour effectuer la comparaison.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection