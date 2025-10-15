@extends('layouts.app')

@section('title', 'Détails du Groupe')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.groupes.index') }}">Groupes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Détails</li>
        </ol>
    </nav>
@endsection

@section('content')
    <h2><i class="fas fa-users me-2"></i>Détails du Groupe: {{ $groupe->code }}</h2>

    <div class="card mb-4">
        <a href="{{ route('administration.etablissement.groupes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
        <div class="card-header bg-primary text-white">Informations Générales</div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Code</dt><dd class="col-sm-9">{{ $groupe->code }}</dd>
                <dt class="col-sm-3">EFP Code</dt><dd class="col-sm-9">{{ $groupe->efp_code }}</dd>
                <dt class="col-sm-3">EFP Nom</dt><dd class="col-sm-9">{{ $groupe->efp_nom }}</dd>
                <dt class="col-sm-3">Effectif</dt><dd class="col-sm-9">{{ $groupe->effectif }}</dd>
                <dt class="col-sm-3">Statut</dt><dd class="col-sm-9">{{ $groupe->statut }}</dd>
                <dt class="col-sm-3">Fusion Groupe</dt><dd class="col-sm-9">{{ $groupe->fusion_groupe ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Code Fusion</dt><dd class="col-sm-9">{{ $groupe->code_fusion ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Année Formation</dt><dd class="col-sm-9">{{ $groupe->annee_formation }}</dd>
                <dt class="col-sm-3">Année</dt><dd class="col-sm-9">{{ $groupe->annee }}</dd>
            </dl>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Filière, Niveau et Formation</div>
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">Filière</dt><dd class="col-sm-9">{{ $groupe->filiere->nom ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Secteur</dt><dd class="col-sm-9">{{ $groupe->secteur->nom ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Niveau</dt><dd class="col-sm-9">{{ $groupe->niveau->nom ?? 'N/A' }}</dd>
                <dt class="col-sm-3">Formation</dt><dd class="col-sm-9">{{ $groupe->formation->type ?? 'N/A' }} - {{ $groupe->formation->mode ?? '' }}</dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">Formateurs, Modules et Avancements</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th>Formateur Présentiel</th>
                            <th>Formateur Synchrone</th>
                            <th>MH Affectée Globale</th>
                            <th>MH Réalisée Globale</th>
                            <th>Taux Réalisation Globale</th>
                            <th>Moyenne Absence</th>
                            <th>NB CC</th>
                            <th>Séance EFM</th>
                            <th>Validation EFM</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($groupe->affectations as $affectation)
                            <tr>
                                <td>{{ $affectation->module->nom ?? 'N/A' }}</td>
                                <td>{{ $affectation->formateur_affecte_presentiel ?? 'N/A' }}</td>
                                <td>{{ $affectation->formateur_affecte_syn ?? 'N/A' }}</td>
                                <td>{{ $affectation->mh_affectee_globale }}</td>
                                <td>{{ $affectation->avancement->mh_realisee_globale ?? 'N/A' }}</td>
                                <td>{{ $affectation->avancement->taux_realisation_globale ?? 'N/A' }}%</td>
                                <td>{{ $affectation->avancement->moyenne_absence ?? 'N/A' }}</td>
                                <td>{{ $affectation->avancement->nb_cc ?? 'N/A' }}</td>
                                <td>{{ $affectation->avancement->seance_efm ?? 'N/A' }}</td>
                                <td>{{ $affectation->avancement->validation_efm ?? 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center">Aucune affectation trouvée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <a href="{{ route('administration.etablissement.groupes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
    </a>
@endsection