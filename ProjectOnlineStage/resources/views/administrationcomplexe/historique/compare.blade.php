@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>
                    <i class="fas fa-exchange-alt"></i> Comparaison des Avancements (Complexe)
                </h2>
                <a href="{{ route('administration.complexe.historique.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à l'historique
                </a>
            </div>
        </div>
    </div>

    <!-- En-tête de comparaison -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <i class="fas fa-calendar-alt"></i> Période de comparaison
            @if($etablissementInfo || $formateurInfo || $moduleInfo || $groupeInfo)
                <span class="badge bg-warning text-dark ms-2">
                    <i class="fas fa-filter"></i> Filtré
                </span>
            @endif
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
            
            <!-- Affichage des filtres appliqués -->
            @if($etablissementInfo || $formateurInfo || $moduleInfo || $groupeInfo)
                <hr>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-filter"></i> Filtres appliqués:
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            @if($etablissementInfo)
                                <span class="badge bg-danger p-2">
                                    <i class="fas fa-building"></i> 
                                    Établissement: {{ $etablissementInfo->nom }} ({{ $etablissementInfo->code_efp }})
                                </span>
                            @endif
                            @if($formateurInfo)
                                <span class="badge bg-primary p-2">
                                    <i class="fas fa-user-tie"></i> 
                                    Formateur: {{ $formateurInfo->nom_complet }} ({{ $formateurInfo->mle }})
                                </span>
                            @endif
                            @if($moduleInfo)
                                <span class="badge bg-success p-2">
                                    <i class="fas fa-book"></i> 
                                    Module: {{ $moduleInfo->code_module }} - {{ $moduleInfo->nom_module }}
                                </span>
                            @endif
                            @if($groupeInfo)
                                <span class="badge bg-info p-2">
                                    <i class="fas fa-users"></i> 
                                    Groupe: {{ $groupeInfo->code_groupe }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Statistiques générales -->
    @php
        $progressionPositive = collect($comparaisons)->where('diff_taux', '>', 0)->count();
        $progressionNegative = collect($comparaisons)->where('diff_taux', '<', 0)->count();
        $progressionNulle = collect($comparaisons)->where('diff_taux', '=', 0)->count();
        $moyenneProgression = collect($comparaisons)->avg('diff_taux');
        $nouveauxModules = collect($comparaisons)->where('nouveau', true)->count();
        $modulesSupprimes = collect($comparaisons)->where('supprime', true)->count();
    @endphp

    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3>{{ $progressionPositive }}</h3>
                    <p class="mb-0">Progressions positives</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3>{{ $progressionNegative }}</h3>
                    <p class="mb-0">Progressions négatives</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h3>{{ $progressionNulle }}</h3>
                    <p class="mb-0">Sans changement</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3>{{ number_format($moyenneProgression, 2) }}%</h3>
                    <p class="mb-0">Progression moyenne</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3>{{ $nouveauxModules }}</h3>
                    <p class="mb-0">Nouveaux modules</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body text-center">
                    <h3>{{ $modulesSupprimes }}</h3>
                    <p class="mb-0">Modules arrêtés</p>
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
                                <th rowspan="2">Statut</th>
                                <th rowspan="2">Établissement</th>
                                <th rowspan="2">Formateur(s)</th>
                                <th rowspan="2">Module</th>
                                <th rowspan="2">Groupe</th>
                                <th colspan="3" class="text-center bg-secondary">MH Réalisée</th>
                                <th colspan="3" class="text-center bg-info">Taux de Réalisation</th>
                                <th colspan="3" class="text-center bg-warning">Contrôles Continus</th>
                            </tr>
                            <tr>
                                <th class="text-center">{{ $date1->format('d/m') }}</th>
                                <th class="text-center">{{ $date2->format('d/m') }}</th>
                                <th class="text-center">Évolution</th>
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
                            <tr class="{{ isset($comp['nouveau']) ? 'table-info' : (isset($comp['supprime']) ? 'table-warning' : '') }}">
                                <td class="text-center">
                                    @if(isset($comp['nouveau']))
                                        <span class="badge bg-info" title="Nouvelle affectation">
                                            <i class="fas fa-plus-circle"></i> Nouveau
                                        </span>
                                    @elseif(isset($comp['supprime']))
                                        <span class="badge bg-warning text-dark" title="Affectation arrêtée">
                                            <i class="fas fa-minus-circle"></i> Arrêté
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-check"></i> Actif
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $comp['code_efp'] }}</small>
                                </td>
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
                                
                                <!-- Contrôles Continus -->
                                <td class="text-center">{{ $comp['nb_cc_avant'] ?? 0 }}</td>
                                <td class="text-center">
                                    <strong>{{ $comp['nb_cc_apres'] ?? 0 }}</strong>
                                </td>
                                <td class="text-center">
                                    @php
                                        $diffCc = $comp['diff_cc'] ?? 0;
                                        $colorCc = $diffCc > 0 ? 'success' : ($diffCc < 0 ? 'danger' : 'secondary');
                                        $iconCc = $diffCc > 0 ? 'arrow-up' : ($diffCc < 0 ? 'arrow-down' : 'minus');
                                    @endphp
                                    <span class="badge bg-{{ $colorCc }}">
                                        <i class="fas fa-{{ $iconCc }}"></i>
                                        {{ abs($diffCc) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Légende -->
                <div class="alert alert-info mt-3">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fas fa-info-circle"></i> Légende:</strong>
                            <ul class="mb-0 mt-2">
                                <li><span class="badge bg-info"><i class="fas fa-plus-circle"></i> Nouveau</span> : Affectation créée après la date initiale</li>
                                <li><span class="badge bg-warning text-dark"><i class="fas fa-minus-circle"></i> Arrêté</span> : Affectation présente à la date initiale mais plus à la date de comparaison</li>
                                <li><span class="badge bg-secondary"><i class="fas fa-check"></i> Actif</span> : Affectation présente aux deux dates</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-chart-line"></i> Interprétation:</strong>
                            <ul class="mb-0 mt-2">
                                <li><i class="fas fa-arrow-up text-success"></i> Evolution positive du taux de réalisation</li>
                                <li><i class="fas fa-arrow-down text-danger"></i> Evolution négative ou régression</li>
                                <li><i class="fas fa-minus text-secondary"></i> Aucun changement entre les deux dates</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Actions supplémentaires -->
                @if($etablissementInfo)
                    <div class="alert alert-danger mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="fas fa-building"></i> Analyse de l'établissement: {{ $etablissementInfo->nom }}</strong>
                                <p class="mb-0 mt-2">
                                    @php
                                        $modulesActifs = collect($comparaisons)->where('nouveau', null)->where('supprime', null)->count();
                                        $moyenneTaux = collect($comparaisons)->where('nouveau', null)->where('supprime', null)->avg('taux_realisation_apres');
                                    @endphp
                                    <small>
                                        Modules actifs: <strong>{{ $modulesActifs }}</strong> | 
                                        Taux moyen de réalisation: <strong>{{ number_format($moyenneTaux, 2) }}%</strong>
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($formateurInfo)
                    <div class="alert alert-primary mt-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><i class="fas fa-user-tie"></i> Analyse du formateur: {{ $formateurInfo->nom_complet }}</strong>
                                <p class="mb-0 mt-2">
                                    @php
                                        $modulesActifs = collect($comparaisons)->where('nouveau', null)->where('supprime', null)->count();
                                        $moyenneTaux = collect($comparaisons)->where('nouveau', null)->where('supprime', null)->avg('taux_realisation_apres');
                                    @endphp
                                    <small>
                                        Modules actifs: <strong>{{ $modulesActifs }}</strong> | 
                                        Taux moyen de réalisation: <strong>{{ number_format($moyenneTaux, 2) }}%</strong>
                                    </small>
                                </p>
                            </div>
                            <div>
                                <a href="{{ route('administration.complexe.historique.index', ['formateur' => $formateurInfo->mle]) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-list"></i> Voir tout l'historique
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Aucune donnée commune trouvée entre ces deux dates pour effectuer la comparaison.
                    <hr>
                    <p class="mb-0">
                        <strong>Suggestions:</strong><br>
                        • Vérifiez que des données ont été capturées aux deux dates<br>
                        • Assurez-vous que les groupes et modules sont identiques entre les deux dates<br>
                        • Consultez l'historique pour voir les dates disponibles
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
