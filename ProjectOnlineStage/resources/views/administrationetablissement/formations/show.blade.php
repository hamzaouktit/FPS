@extends('layouts.app')

@section('title', 'Détails de la Formation')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.formations.index') }}">
                <i class="fas fa-graduation-cap"></i> Formations
            </a>
        </li>
        <li class="breadcrumb-item active">
            <i class="fas fa-eye"></i> Détails
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- En-tête avec actions -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="mb-2">
                <i class="fas fa-graduation-cap text-primary"></i>
                Détails de la Formation
            </h2>
            <p class="text-muted mb-0">
                <i class="fas fa-school me-1"></i>
                {{ $formation->etablissement->nom_efp }}
            </p>
        </div>
        <div class="btn-group" role="group">
            <a href="{{ route('administration.etablissement.formations.edit', $formation->id) }}" 
               class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>
                Modifier
            </a>
            <a href="{{ route('administration.etablissement.formations.index') }}" 
               class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour
            </a>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded p-3">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Groupes</h6>
                            <h3 class="mb-0">{{ $stats['total_groupes'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded p-3">
                                <i class="fas fa-user-graduate fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Stagiaires</h6>
                            <h3 class="mb-0">{{ $stats['total_stagiaires'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 text-info rounded p-3">
                                <i class="fas fa-book fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Modules</h6>
                            <h3 class="mb-0">{{ $stats['total_modules'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded p-3">
                                <i class="fas fa-chalkboard-teacher fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Formateurs</h6>
                            <h3 class="mb-0">{{ $stats['total_formateurs'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations de la formation -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations Générales
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width: 40%;">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    Année :
                                </td>
                                <td>
                                    <strong class="badge bg-info text-white px-3 py-2">
                                        {{ $formation->annee }}
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <i class="fas fa-book me-2"></i>
                                    Filière :
                                </td>
                                <td>
                                    <strong>{{ $formation->filiere->nom_filiere }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $formation->filiere->code_filiere }}</small>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <i class="fas fa-layer-group me-2"></i>
                                    Secteur :
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $formation->filiere->secteur->nom_secteur ?? 'N/A' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <i class="fas fa-level-up-alt me-2"></i>
                                    Niveau :
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $formation->niveau->niveau }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <i class="fas fa-tag me-2"></i>
                                    Type :
                                </td>
                                <td>
                                    @if($formation->type_formation)
                                        <span class="badge bg-success">
                                            {{ $formation->type_formation }}
                                        </span>
                                    @else
                                        <span class="text-muted">Non défini</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <i class="fas fa-clock me-2"></i>
                                    Créneau :
                                </td>
                                <td>
                                    @if($formation->creneau)
                                        <span class="badge bg-warning text-dark">
                                            {{ $formation->creneau }}
                                        </span>
                                    @else
                                        <span class="text-muted">Non défini</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-school me-2"></i>
                        Établissement
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted" style="width: 40%;">
                                    <i class="fas fa-building me-2"></i>
                                    Nom :
                                </td>
                                <td>
                                    <strong>{{ $formation->etablissement->nom_efp }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <i class="fas fa-barcode me-2"></i>
                                    Code EFP :
                                </td>
                                <td>
                                    <span class="badge bg-dark">
                                        {{ $formation->etablissement->code_efp }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">
                                    <i class="fas fa-building me-2"></i>
                                    Complexe :
                                </td>
                                <td>
                                    @if($formation->etablissement->complexe)
                                        {{ $formation->etablissement->complexe->nom }}
                                    @else
                                        <span class="text-muted">Non rattaché</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des groupes -->
    @if($formation->groupes->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Groupes de Formation ({{ $formation->groupes->count() }})
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Nom du Groupe</th>
                                <th>Effectif</th>
                                <th>Année</th>
                                <th>Sous-groupe</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formation->groupes as $groupe)
                                <tr>
                                    <td class="px-4">
                                        <strong>{{ $groupe->nom_groupe }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <i class="fas fa-user-graduate me-1"></i>
                                            {{ $groupe->effectif_groupe ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-white">
                                            {{ $groupe->annee_formation }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($groupe->sous_groupe)
                                            <span class="badge bg-secondary">
                                                {{ $groupe->sous_groupe }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($groupe->statut_sous_groupe)
                                            <span class="badge bg-success">
                                                {{ $groupe->statut_sous_groupe }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Liste des modules -->
    @if($modules->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-book me-2"></i>
                    Modules Enseignés ({{ $modules->count() }})
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Code Module</th>
                                <th>Nom du Module</th>
                                <th>Régional</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modules as $module)
                                <tr>
                                    <td class="px-4">
                                        <span class="badge bg-dark">
                                            {{ $module->code_module }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $module->nom_module }}</strong>
                                    </td>
                                    <td>
                                        @if($module->regional)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>
                                                Oui
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-times me-1"></i>
                                                Non
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Liste des formateurs -->
    @if($formateurs->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-chalkboard-teacher me-2"></i>
                    Formateurs Affectés ({{ $formateurs->count() }})
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Matricule</th>
                                <th>Nom du Formateur</th>
                                <th>Établissement</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formateurs as $formateur)
                                <tr>
                                    <td class="px-4">
                                        <span class="badge bg-dark">
                                            {{ $formateur->mle }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $formateur->nom_formateur }}</strong>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $formateur->code_efp }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Message si aucune donnée -->
    @if($formation->groupes->count() == 0)
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Information :</strong> Aucun groupe n'est encore associé à cette formation.
        </div>
    @endif
</div>

<style>
.card {
    transition: all 0.3s ease;
}

.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}

.badge {
    font-weight: 500;
}
</style>
@endsection