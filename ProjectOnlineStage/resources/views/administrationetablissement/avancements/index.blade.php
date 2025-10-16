@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Liste des Avancements - {{ $etablissement->nom_efp }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('administration.etablissement.avancements.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvel Avancement
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($avancements->isEmpty())
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Aucun avancement trouvé pour votre établissement.
                            <br>
                            <a href="{{ route('administration.etablissement.avancements.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus"></i> Créer le premier avancement
                            </a>
                        </div>
                    @else
                        @foreach($avancements as $groupeModule => $avancementsGroupe)
                            @php
                                $firstAvancement = $avancementsGroupe->first();
                                $groupe = $firstAvancement->affectation->groupe;
                                $module = $firstAvancement->affectation->module;
                            @endphp
                            
                            <div class="card mb-4">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h4 class="card-title mb-0">
                                        <i class="fas fa-users text-primary"></i> 
                                        <strong>Groupe: {{ $groupe->code_groupe }}</strong> | 
                                        <i class="fas fa-book text-success"></i>
                                        <strong>Module: {{ $module->nom_module }}</strong>
                                    </h4>
                                    <span class="badge badge-primary">
                                        {{ $avancementsGroupe->count() }} avancement(s)
                                    </span>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover mb-0">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th width="15%">Formateur Présentiel</th>
                                                    <th width="15%">Formateur Synchrone</th>
                                                    <th width="8%">MH Présentiel</th>
                                                    <th width="8%">MH Sync</th>
                                                    <th width="8%">MH Globale</th>
                                                    <th width="8%">Taux Présentiel</th>
                                                    <th width="8%">Taux Syn</th>
                                                    <th width="8%">Taux Global</th>
                                                    <th width="10%">Date MAJ</th>
                                                    <th width="12%">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($avancementsGroupe as $avancement)
                                                    <tr>
                                                        <td>
                                                            <small class="text-muted">MLE:</small> 
                                                            {{ $avancement->affectation->formateurPresentiel->mle ?? 'N/A' }}<br>
                                                            <strong>{{ $avancement->affectation->formateurPresentiel->nom_complet ?? 'N/A' }}</strong>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">MLE:</small> 
                                                            {{ $avancement->affectation->formateurSyn->mle ?? 'N/A' }}<br>
                                                            <strong>{{ $avancement->affectation->formateurSyn->nom_complet ?? 'N/A' }}</strong>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-info">{{ $avancement->mh_realisee_presentiel }}h</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-warning">{{ $avancement->mh_realisee_sync }}h</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-success">{{ $avancement->mh_realisee_globale }}h</span>
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                $tauxPresentiel = $avancement->taux_realisation_presentiel;
                                                                $badgeClass = $tauxPresentiel >= 80 ? 'badge-success' : ($tauxPresentiel >= 50 ? 'badge-warning' : 'badge-danger');
                                                            @endphp
                                                            <span class="badge {{ $badgeClass }}">{{ $tauxPresentiel }}%</span>
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                $tauxSyn = $avancement->taux_realisation_syn;
                                                                $badgeClass = $tauxSyn >= 80 ? 'badge-success' : ($tauxSyn >= 50 ? 'badge-warning' : 'badge-danger');
                                                            @endphp
                                                            <span class="badge {{ $badgeClass }}">{{ $tauxSyn }}%</span>
                                                        </td>
                                                        <td class="text-center">
                                                            @php
                                                                $tauxGlobal = $avancement->taux_realisation_globale;
                                                                $badgeClass = $tauxGlobal >= 80 ? 'badge-success' : ($tauxGlobal >= 50 ? 'badge-warning' : 'badge-danger');
                                                            @endphp
                                                            <span class="badge {{ $badgeClass }}">{{ $tauxGlobal }}%</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <small>{{ $avancement->date_maj->format('d/m/Y') }}</small>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <a href="{{ route('administration.etablissement.avancements.show', $avancement) }}" 
                                                                   class="btn btn-info" title="Voir détails">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('administration.etablissement.avancements.edit', $avancement) }}" 
                                                                   class="btn btn-warning" title="Modifier">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <form action="{{ route('administration.etablissement.avancements.destroy', $avancement) }}" 
                                                                      method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger" 
                                                                            title="Supprimer" 
                                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avancement ?')">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer bg-light">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle"></i> 
                                                Code Module: <strong>{{ $module->code_module }}</strong> | 
                                                Régional: <strong>{{ $module->regional == 'O' ? 'Oui' : 'Non' }}</strong>
                                            </small>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <small class="text-muted">
                                                Effectif groupe: <strong>{{ $groupe->effectif_groupe }}</strong> | 
                                                Statut: 
                                                <span class="badge {{ $groupe->statut == 'Actif' ? 'badge-success' : 'badge-secondary' }}">
                                                    {{ $groupe->statut }}
                                                </span>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-3">
                            <div class="alert alert-secondary">
                                <i class="fas fa-chart-bar"></i>
                                <strong>Résumé:</strong> 
                                {{ $avancements->count() }} groupe(s)-module(s) trouvé(s) | 
                                Total avancements: {{ $avancements->flatten()->count() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .table th {
        background-color: #343a40;
        color: white;
        font-weight: 600;
        text-align: center;
        vertical-align: middle;
    }
    .table td {
        vertical-align: middle;
    }
    .card-header h4 {
        font-size: 1.1rem;
    }
    .badge {
        font-size: 0.75rem;
    }
    .btn-group .btn {
        border-radius: 0.25rem;
        margin: 0 1px;
    }
</style>
@endsection