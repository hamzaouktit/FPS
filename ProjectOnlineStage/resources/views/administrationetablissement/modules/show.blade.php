@extends('layouts.app')

@section('title', 'Détails du Module')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.dashboard') }}">Tableau de Bord</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.modules.index') }}">Modules</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $module->code_module }}</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informations du Module
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Code Module:</th>
                        <td><strong>{{ $module->code_module }}</strong></td>
                    </tr>
                    <tr>
                        <th>Nom Module:</th>
                        <td>{{ $module->nom_module }}</td>
                    </tr>
                    <tr>
                        <th>Régional:</th>
                        <td>
                            <span class="badge bg-{{ $module->regional == 'O' ? 'success' : 'secondary' }}">
                                {{ $module->regional == 'O' ? 'Oui' : 'Non' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Module PIE:</th>
                        <td>
                            <span class="badge bg-{{ $module->module_pie == 'O' ? 'warning' : 'secondary' }}">
                                {{ $module->module_pie == 'O' ? 'Oui' : 'Non' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>EFP PIE:</th>
                        <td>{{ $module->efp_pie ?? 'Non spécifié' }}</td>
                    </tr>
                </table>

                <div class="d-grid gap-2">
                    <a href="{{ route('administration.etablissement.modules.edit', $module->id) }}" 
                       class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                </div>
            </div>
        </div>

        <!-- Filieres associées -->
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-project-diagram me-2"></i>Filieres Associées
                </h6>
            </div>
            <div class="card-body">
                @if($module->filieres->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($module->filieres as $filiere)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $filiere->nom_filiere }}
                                <span class="badge bg-primary rounded-pill">{{ $filiere->code_filiere }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">Aucune filière associée</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Avancement par Groupe -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>Avancement par Groupe
                </h5>
            </div>
            <div class="card-body">
                @if($avancementsParGroupe->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Groupe</th>
                                    <th>Filiere</th>
                                    <th>Formateur Présentiel</th>
                                    <th>Formateur Synchrone</th>
                                    <th>Taux Réalisation</th>
                                    <th>MH Réalisée</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($avancementsParGroupe as $groupeCode => $affectations)
                                    @foreach($affectations as $affectation)
                                        <tr>
                                            <td>
                                                <strong>{{ $groupeCode }}</strong>
                                            </td>
                                            <td>
                                                {{ $affectation->groupe->filiere->nom_filiere ?? 'N/A' }}
                                            </td>
                                            <td>
                                                {{ $affectation->formateurPresentiel->nom_complet ?? 'Non assigné' }}
                                            </td>
                                            <td>
                                                {{ $affectation->formateurSyn->nom_complet ?? 'Non assigné' }}
                                            </td>
                                            <td>
                                                @if($affectation->avancement)
                                                    <span class="badge bg-{{ $affectation->avancement->taux_realisation_globale >= 80 ? 'success' : ($affectation->avancement->taux_realisation_globale >= 50 ? 'warning' : 'danger') }}">
                                                        {{ $affectation->avancement->taux_realisation_globale }}%
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">0%</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($affectation->avancement)
                                                    {{ $affectation->avancement->mh_realisee_globale }}h
                                                @else
                                                    0h
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">Aucun avancement enregistré pour ce module.</p>
                @endif
            </div>
        </div>

        <!-- Avancement par Filiere -->
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Synthèse par Filiere
                </h5>
            </div>
            <div class="card-body">
                @if($avancementsParFiliere->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Filiere</th>
                                    <th>Nombre de Groupes</th>
                                    <th>Taux Moyen</th>
                                    <th>MH Totale Réalisée</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($avancementsParFiliere as $filiereNom => $affectations)
                                    @php
                                        $totalTaux = 0;
                                        $totalMH = 0;
                                        $count = 0;
                                        
                                        foreach($affectations as $affectation) {
                                            if($affectation->avancement) {
                                                $totalTaux += $affectation->avancement->taux_realisation_globale;
                                                $totalMH += $affectation->avancement->mh_realisee_globale;
                                                $count++;
                                            }
                                        }
                                        
                                        $moyenneTaux = $count > 0 ? $totalTaux / $count : 0;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $filiereNom }}</strong></td>
                                        <td>{{ $affectations->count() }}</td>
                                        <td>
                                            <span class="badge bg-{{ $moyenneTaux >= 80 ? 'success' : ($moyenneTaux >= 50 ? 'warning' : 'danger') }}">
                                                {{ number_format($moyenneTaux, 1) }}%
                                            </span>
                                        </td>
                                        <td>{{ $totalMH }}h</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">Aucune donnée par filière disponible.</p>
                @endif
            </div>
        </div>

        <!-- Avancement par Formateur -->
        <div class="card mt-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Performance par Formateur
                </h5>
            </div>
            <div class="card-body">
                @if($avancementsParFormateur->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Formateur</th>
                                    <th>Type</th>
                                    <th>Nombre de Groupes</th>
                                    <th>Taux Moyen</th>
                                    <th>MH Réalisée</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($avancementsParFormateur as $formateurNom => $affectations)
                                    @php
                                        $totalTaux = 0;
                                        $totalMH = 0;
                                        $count = 0;
                                        
                                        foreach($affectations as $affectation) {
                                            if($affectation->avancement) {
                                                $totalTaux += $affectation->avancement->taux_realisation_globale;
                                                $totalMH += $affectation->avancement->mh_realisee_globale;
                                                $count++;
                                            }
                                        }
                                        
                                        $moyenneTaux = $count > 0 ? $totalTaux / $count : 0;
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $formateurNom }}</strong></td>
                                        <td>
                                            @php
                                                $type = '';
                                                if($affectations->first()->mle_affecte_presentiel) {
                                                    $type = 'Présentiel';
                                                } else {
                                                    $type = 'Synchrone';
                                                }
                                            @endphp
                                            <span class="badge bg-info">{{ $type }}</span>
                                        </td>
                                        <td>{{ $affectations->count() }}</td>
                                        <td>
                                            <span class="badge bg-{{ $moyenneTaux >= 80 ? 'success' : ($moyenneTaux >= 50 ? 'warning' : 'danger') }}">
                                                {{ number_format($moyenneTaux, 1) }}%
                                            </span>
                                        </td>
                                        <td>{{ $totalMH }}h</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">Aucun formateur assigné à ce module.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection