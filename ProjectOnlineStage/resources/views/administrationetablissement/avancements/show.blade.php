@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Détails de l'Avancement</h3>
                    <a href="{{ route('administration.etablissement.avancements.index') }}" class="btn btn-default float-right">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    @php
                        $affectation = $avancement->affectation;
                        $groupe = $affectation->groupe;
                        $module = $affectation->module;
                    @endphp

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Informations Groupe/Module</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Groupe</th>
                                    <td>{{ $groupe->code_groupe }}</td>
                                </tr>
                                <tr>
                                    <th>Module</th>
                                    <td>{{ $module->nom_module }}</td>
                                </tr>
                                <tr>
                                    <th>Code Module</th>
                                    <td>{{ $module->code_module }}</td>
                                </tr>
                                <tr>
                                    <th>Formateur Présentiel</th>
                                    <td>{{ $affectation->formateurPresentiel->nom_complet ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Formateur Synchrone</th>
                                    <td>{{ $affectation->formateurSyn->nom_complet ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Informations Avancement</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Date MAJ</th>
                                    <td>{{ $avancement->date_maj->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Classe Teams</th>
                                    <td>{{ $avancement->classe_teams ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Séance EFM</th>
                                    <td>{{ $avancement->seance_efm }}</td>
                                </tr>
                                <tr>
                                    <th>Validation EFM</th>
                                    <td>{{ $avancement->validation_efm }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h5>Détails des Réalisations</h5>
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>MH Réalisée Présentiel</th>
                                        <th>MH Réalisée Sync</th>
                                        <th>MH Réalisée Globale</th>
                                        <th>Taux Réalisation Présentiel</th>
                                        <th>Taux Réalisation Syn</th>
                                        <th>Taux Réalisation (P & SYN)</th>
                                        <th>Moy Absence</th>
                                        <th>NB CC</th>
                                        <th>Module PIE</th>
                                        <th>EFP PIE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $avancement->mh_realisee_presentiel }}</td>
                                        <td>{{ $avancement->mh_realisee_sync }}</td>
                                        <td>{{ $avancement->mh_realisee_globale }}</td>
                                        <td>{{ $avancement->taux_realisation_presentiel }}%</td>
                                        <td>{{ $avancement->taux_realisation_syn }}%</td>
                                        <td>{{ $avancement->taux_realisation_globale }}%</td>
                                        <td>{{ $avancement->moyenne_absence }}%</td>
                                        <td>{{ $avancement->nb_cc }}</td>
                                        <td>{{ $module->module_pie }}</td>
                                        <td>{{ $module->efp_pie ?? 'N/A' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('administration.etablissement.avancements.edit', $avancement) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <form action="{{ route('administration.etablissement.avancements.destroy', $avancement) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" 
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avancement ?')">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection