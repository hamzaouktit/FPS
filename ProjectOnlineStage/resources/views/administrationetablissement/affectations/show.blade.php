@extends('layouts.app')

@section('title', 'Détails de l\'Affectation')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.dashboard') }}">Tableau de Bord</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.affectations.index') }}">Affectations</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Détails</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informations Générales
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%">Groupe:</th>
                        <td><strong>{{ $affectation->groupe->code_groupe }}</strong></td>
                    </tr>
                    <tr>
                        <th>Module:</th>
                        <td>
                            <strong>{{ $affectation->module->code_module }}</strong><br>
                            <small>{{ $affectation->module->nom_module }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th>Filiere:</th>
                        <td>{{ $affectation->groupe->filiere->nom_filiere ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Secteur:</th>
                        <td>{{ $affectation->groupe->filiere->secteur->nom_secteur ?? 'N/A' }}</td>
                    </tr>
                </table>

                <div class="d-grid gap-2">
                    <a href="{{ route('administration.etablissement.affectations.edit', $affectation->id) }}" 
                       class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                </div>
            </div>
        </div>

        <!-- Formateurs assignés -->
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Formateurs Assignés
                </h6>
            </div>
            <div class="card-body">
                <h6>Présentiel:</h6>
                @if($affectation->formateurPresentiel)
                    <p class="mb-2">
                        <strong>{{ $affectation->formateurPresentiel->nom_complet }}</strong><br>
                        <small class="text-muted">MLE: {{ $affectation->formateurPresentiel->mle }}</small><br>
                        <span class="badge bg-info">{{ $affectation->formateurPresentiel->type }}</span>
                    </p>
                @else
                    <p class="text-muted mb-2">Non assigné</p>
                @endif

                <h6>Synchrone:</h6>
                @if($affectation->formateurSyn)
                    <p class="mb-0">
                        <strong>{{ $affectation->formateurSyn->nom_complet }}</strong><br>
                        <small class="text-muted">MLE: {{ $affectation->formateurSyn->mle }}</small><br>
                        <span class="badge bg-warning text-dark">{{ $affectation->formateurSyn->type }}</span>
                    </p>
                @else
                    <p class="text-muted mb-0">Non assigné</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <!-- Tableau des détails d'affectation -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>Détails d'Affectation par Groupe et Module
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Mle Affecté Présentiel Actif</th>
                                <th>Formateur Affecté Présentiel Actif</th>
                                <th>Mle Affecté Syn Actif</th>
                                <th>Formateur Affecté Syn Actif</th>
                                <th>MHP S1 DRIF</th>
                                <th>MHSYN S1 DRIF</th>
                                <th>MHASYN S1 DRIF</th>
                                <th>MH Totale S1 DRIF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $affectation->mle_affecte_presentiel ?? 'N/A' }}</td>
                                <td>{{ $affectation->formateur_affecte_presentiel ?? 'N/A' }}</td>
                                <td>{{ $affectation->mle_affecte_syn ?? 'N/A' }}</td>
                                <td>{{ $affectation->formateur_affecte_syn ?? 'N/A' }}</td>
                                <td>{{ $affectation->mhp_s1_drif }}h</td>
                                <td>{{ $affectation->mhsyn_s1_drif }}h</td>
                                <td>{{ $affectation->mhasyn_s1_drif }}h</td>
                                <td><strong>{{ $affectation->mh_totale_s1_drif }}h</strong></td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-bordered table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>MHP S2 DRIF</th>
                                <th>MHSYN S2 DRIF</th>
                                <th>MHASYN S2 DRIF</th>
                                <th>MH Totale S2 DRIF</th>
                                <th>MHP Totale DRIF</th>
                                <th>MHSYN Totale DRIF</th>
                                <th>MHASYN Totale DRIF</th>
                                <th>MH Totale DRIF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $affectation->mhp_s2_drif }}h</td>
                                <td>{{ $affectation->mhsyn_s2_drif }}h</td>
                                <td>{{ $affectation->mhasyn_s2_drif }}h</td>
                                <td><strong>{{ $affectation->mh_totale_s2_drif }}h</strong></td>
                                <td>{{ $affectation->mhp_totale_drif }}h</td>
                                <td>{{ $affectation->mhsyn_totale_drif }}h</td>
                                <td>{{ $affectation->mhasyn_totale_drif }}h</td>
                                <td><strong class="text-primary">{{ $affectation->mh_totale_drif }}h</strong></td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-bordered table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>MH Affectée Présentiel</th>
                                <th>MH Affectée Sync</th>
                                <th>MH Affectée Globale (P & SYN)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $affectation->mh_affectee_presentiel }}h</td>
                                <td>{{ $affectation->mh_affectee_sync }}h</td>
                                <td><strong class="text-success">{{ $affectation->mh_affectee_globale }}h</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tableau de réalisation -->
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>Réalisation par Groupe et Module
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>MH Réalisée Présentiel</th>
                                <th>MH Réalisée Sync</th>
                                <th>MH Réalisée Globale</th>
                                <th>Taux Réalisation Présentiel</th>
                                <th>Taux Réalisation Syn</th>
                                <th>Taux Réalisation (P & SYN)</th>
                                <th>Moy Absence</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $affectation->avancement->mh_realisee_presentiel ?? 0 }}h</td>
                                <td>{{ $affectation->avancement->mh_realisee_sync ?? 0 }}h</td>
                                <td><strong>{{ $affectation->avancement->mh_realisee_globale ?? 0 }}h</strong></td>
                                <td>
                                    <span class="badge bg-{{ ($affectation->avancement->taux_realisation_presentiel ?? 0) >= 80 ? 'success' : (($affectation->avancement->taux_realisation_presentiel ?? 0) >= 50 ? 'warning' : 'danger') }}">
                                        {{ $affectation->avancement->taux_realisation_presentiel ?? 0 }}%
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ ($affectation->avancement->taux_realisation_syn ?? 0) >= 80 ? 'success' : (($affectation->avancement->taux_realisation_syn ?? 0) >= 50 ? 'warning' : 'danger') }}">
                                        {{ $affectation->avancement->taux_realisation_syn ?? 0 }}%
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ ($affectation->avancement->taux_realisation_globale ?? 0) >= 80 ? 'success' : (($affectation->avancement->taux_realisation_globale ?? 0) >= 50 ? 'warning' : 'danger') }}">
                                        {{ $affectation->avancement->taux_realisation_globale ?? 0 }}%
                                    </span>
                                </td>
                                <td>{{ $affectation->avancement->moyenne_absence ?? 0 }}%</td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-bordered table-striped mt-3">
                        <thead class="table-dark">
                            <tr>
                                <th>NB CC</th>
                                <th>Séance EFM</th>
                                <th>Validation EFM</th>
                                <th>Classe Teams</th>
                                <th>Module PIE</th>
                                <th>EFP PIE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $affectation->avancement->nb_cc ?? 0 }}</td>
                                <td>
                                    <span class="badge bg-{{ ($affectation->avancement->seance_efm ?? 'Non') == 'Oui' ? 'success' : 'secondary' }}">
                                        {{ $affectation->avancement->seance_efm ?? 'Non' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ ($affectation->avancement->validation_efm ?? 'non') == 'oui' ? 'success' : 'secondary' }}">
                                        {{ $affectation->avancement->validation_efm ?? 'non' }}
                                    </span>
                                </td>
                                <td>{{ $affectation->avancement->classe_teams ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $affectation->module->module_pie == 'O' ? 'warning' : 'secondary' }}">
                                        {{ $affectation->module->module_pie == 'O' ? 'Oui' : 'Non' }}
                                    </span>
                                </td>
                                <td>{{ $affectation->module->efp_pie ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if(!$affectation->avancement)
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Aucune donnée d'avancement n'est disponible pour cette affectation.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection