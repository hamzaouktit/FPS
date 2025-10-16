@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails du Formateur - {{ $formateur->nom_complet }}</h5>
                    <div>
                        <a href="{{ route('administration.etablissement.formateurs.edit', $formateur) }}" 
                           class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('administration.etablissement.formateurs.index') }}" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Informations générales -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Informations Personnelles</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">MLE</th>
                                    <td>{{ $formateur->mle }}</td>
                                </tr>
                                <tr>
                                    <th>Nom Complet</th>
                                    <td>{{ $formateur->nom_complet }}</td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td>
                                        <span class="badge badge-{{ $formateur->type === 'permanent' ? 'success' : 'warning' }}">
                                            {{ $formateur->type }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Établissement</th>
                                    <td>{{ $formateur->etablissement->nom_efp }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6>Compétences</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Secteurs</th>
                                    <td>
                                        @foreach($formateur->secteurs as $secteur)
                                            <span class="badge badge-info mb-1">{{ $secteur->nom_secteur }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <th>Modules</th>
                                    <td>
                                        @foreach($formateur->modules as $module)
                                            <span class="badge badge-secondary mb-1">
                                                {{ $module->code_module }} - {{ $module->nom_module }}
                                            </span>
                                        @endforeach
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Détails des heures par module -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6>Détail des Heures par Module et Groupe</h6>
                            
                            @if(count($heuresParModule) > 0)
                                @foreach($heuresParModule as $moduleId => $moduleData)
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">{{ $moduleData['module_nom'] }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Groupe</th>
                                                            <th>Heures Requises</th>
                                                            <th>Heures Affectées</th>
                                                            <th>Heures Manquantes</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($moduleData['groupes'] as $groupeNom => $heures)
                                                            <tr>
                                                                <td>{{ $groupeNom }}</td>
                                                                <td>{{ number_format($heures['heures_requises'], 2) }}h</td>
                                                                <td class="{{ $heures['heures_affectees'] < $heures['heures_requises'] ? 'text-warning' : 'text-success' }}">
                                                                    {{ number_format($heures['heures_affectees'], 2) }}h
                                                                </td>
                                                                <td class="{{ $heures['heures_manquantes'] > 0 ? 'text-danger' : 'text-success' }}">
                                                                    {{ number_format($heures['heures_manquantes'], 2) }}h
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="bg-light font-weight-bold">
                                                        <tr>
                                                            <td>Total</td>
                                                            <td>{{ number_format($moduleData['total_requis'], 2) }}h</td>
                                                            <td class="{{ $moduleData['total_affecte'] < $moduleData['total_requis'] ? 'text-warning' : 'text-success' }}">
                                                                {{ number_format($moduleData['total_affecte'], 2) }}h
                                                            </td>
                                                            <td class="{{ $moduleData['total_manquant'] > 0 ? 'text-danger' : 'text-success' }}">
                                                                {{ number_format($moduleData['total_manquant'], 2) }}h
                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    Aucune affectation trouvée pour ce formateur.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection