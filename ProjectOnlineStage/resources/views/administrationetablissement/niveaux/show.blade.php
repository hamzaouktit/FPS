@extends('layouts.app')

@section('title', 'Détails du Niveau - ' . $niveauData->nom)

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.niveaux.index') }}">
                <i class="fas fa-layer-group"></i> Niveaux
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-info-circle"></i> {{ $niveauData->nom }}
        </li>
    </ol>
</nav>
@endsection

@section('content')

<!-- En-tête avec informations principales -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fas fa-layer-group me-2"></i>
                {{ $niveauData->nom }}
                <span class="badge bg-light text-dark ms-2">ID: {{ $niveauData->id }}</span>
            </h4>
            <div>
                <a href="{{ route('administration.etablissement.niveaux.edit', $niveauData->id) }}" 
                   class="btn btn-warning btn-sm">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
                <a href="{{ route('administration.etablissement.niveaux.index') }}" 
                   class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong><i class="fas fa-building me-2 text-primary"></i>Établissement :</strong> 
                    {{ $niveauData->etablissement->nom_efp ?? 'N/A' }}
                </p>
                <p><strong><i class="fas fa-barcode me-2 text-primary"></i>Code EFP :</strong> 
                    {{ $niveauData->code_efp ?? 'N/A' }}
                </p>
            </div>
            <div class="col-md-6">
                <p><strong><i class="fas fa-calendar me-2 text-primary"></i>Date de création :</strong> 
                    {{ $niveauData->created_at ? $niveauData->created_at->format('d/m/Y H:i') : 'N/A' }}
                </p>
                <p><strong><i class="fas fa-clock me-2 text-primary"></i>Dernière modification :</strong> 
                    {{ $niveauData->updated_at ? $niveauData->updated_at->format('d/m/Y H:i') : 'N/A' }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques globales -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="fas fa-graduation-cap fa-3x text-primary mb-3"></i>
                <h3 class="mb-0">{{ $stats['total_formations'] ?? 0 }}</h3>
                <p class="text-muted mb-0">Formations</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="fas fa-users fa-3x text-success mb-3"></i>
                <h3 class="mb-0">{{ $stats['total_groupes'] ?? 0 }}</h3>
                <p class="text-muted mb-0">Groupes</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="fas fa-user-graduate fa-3x text-info mb-3"></i>
                <h3 class="mb-0">{{ $stats['effectif_total'] ?? 0 }}</h3>
                <p class="text-muted mb-0">Stagiaires</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <i class="fas fa-chalkboard-teacher fa-3x text-warning mb-3"></i>
                <h3 class="mb-0">{{ $formateurs->count() ?? 0 }}</h3>
                <p class="text-muted mb-0">Formateurs</p>
            </div>
        </div>
    </div>
</div>

<!-- Onglets de navigation -->
<ul class="nav nav-tabs mb-3" id="niveauTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="formations-tab" data-bs-toggle="tab" 
                data-bs-target="#formations" type="button" role="tab">
            <i class="fas fa-graduation-cap me-1"></i> Formations ({{ $stats['total_formations'] ?? 0 }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="groupes-tab" data-bs-toggle="tab" 
                data-bs-target="#groupes" type="button" role="tab">
            <i class="fas fa-users me-1"></i> Groupes ({{ $stats['total_groupes'] ?? 0 }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="secteurs-tab" data-bs-toggle="tab" 
                data-bs-target="#secteurs" type="button" role="tab">
            <i class="fas fa-industry me-1"></i> Secteurs ({{ $secteurs->count() ?? 0 }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="modules-tab" data-bs-toggle="tab" 
                data-bs-target="#modules" type="button" role="tab">
            <i class="fas fa-book me-1"></i> Modules ({{ $modules->count() ?? 0 }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="formateurs-tab" data-bs-toggle="tab" 
                data-bs-target="#formateurs-content" type="button" role="tab">
            <i class="fas fa-chalkboard-teacher me-1"></i> Formateurs ({{ $formateurs->count() ?? 0 }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="stats-tab" data-bs-toggle="tab" 
                data-bs-target="#stats-content" type="button" role="tab">
            <i class="fas fa-chart-bar me-1"></i> Statistiques
        </button>
    </li>
</ul>

<!-- Contenu des onglets -->
<div class="tab-content" id="niveauTabsContent">
    <!-- Onglet Formations -->
    <div class="tab-pane fade show active" id="formations" role="tabpanel">
        <!-- Filtres Formations -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Filtrer les formations
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('administration.etablissement.niveaux.show', $niveauData->id) }}" id="formationsFilterForm">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Année</label>
                            <select name="annee" class="form-select form-select-sm">
                                <option value="">Toutes</option>
                                @foreach($filterData['annees'] as $annee)
                                    <option value="{{ $annee }}" {{ request('annee') == $annee ? 'selected' : '' }}>
                                        {{ $annee }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                @foreach($filterData['types'] as $type)
                                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Mode</label>
                            <select name="mode" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                @foreach($filterData['modes'] as $mode)
                                    <option value="{{ $mode }}" {{ request('mode') == $mode ? 'selected' : '' }}>
                                        {{ $mode }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Créneau</label>
                            <select name="creneau" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                @foreach($filterData['creneaux'] as $creneau)
                                    <option value="{{ $creneau }}" {{ request('creneau') == $creneau ? 'selected' : '' }}>
                                        {{ $creneau }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search me-1"></i>Filtrer
                                </button>
                                <a href="{{ route('administration.etablissement.niveaux.show', $niveauData->id) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-redo me-1"></i>Réinitialiser
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau Formations -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Formations du Niveau {{ $niveauData->nom }}
                </h5>
            </div>
            <div class="card-body">
                @if($formations && $formations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-calendar me-1"></i>Année</th>
                                    <th><i class="fas fa-stream me-1"></i>Filière</th>
                                    <th><i class="fas fa-industry me-1"></i>Secteur</th>
                                    <th><i class="fas fa-certificate me-1"></i>Type</th>
                                    <th><i class="fas fa-clock me-1"></i>Mode</th>
                                    <th><i class="fas fa-sun me-1"></i>Créneau</th>
                                    <th><i class="fas fa-users me-1"></i>Groupes</th>
                                    <th><i class="fas fa-user-graduate me-1"></i>Effectif Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($formations as $formation)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $formation->annee ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $formation->filiere->nom_filiere ?? 'N/A' }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $formation->filiere->code_filiere ?? 'N/A' }}
                                        </small>
                                    </td>
                                    <td>{{ $formation->filiere->secteur->nom_secteur ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $formation->type }}
                                        </span>
                                    </td>
                                    <td>{{ $formation->mode }}</td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $formation->creneau }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $formation->groupes->count() }} groupe(s)
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $formation->groupes->sum('effectif_groupe') }} stagiaire(s)
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <p class="mb-0">Aucune formation pour ce niveau.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Onglet Groupes -->
    <div class="tab-pane fade" id="groupes" role="tabpanel">
        <!-- Filtres Groupes -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Filtrer les groupes
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('administration.etablissement.niveaux.show', $niveauData->id) }}" id="groupesFilterForm">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label">Recherche</label>
                            <input type="text" name="groupe_search" class="form-control form-control-sm" 
                                   placeholder="Code..." value="{{ request('groupe_search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Statut</label>
                            <select name="groupe_statut" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                @foreach($filterData['statuts'] as $statut)
                                    <option value="{{ $statut }}" {{ request('groupe_statut') == $statut ? 'selected' : '' }}>
                                        {{ $statut }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Année</label>
                            <select name="groupe_annee" class="form-select form-select-sm">
                                <option value="">Toutes</option>
                                @foreach($filterData['annees'] as $annee)
                                    <option value="{{ $annee }}" {{ request('groupe_annee') == $annee ? 'selected' : '' }}>
                                        {{ $annee }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Filière</label>
                            <select name="groupe_filiere" class="form-select form-select-sm">
                                <option value="">Toutes</option>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" {{ request('groupe_filiere') == $filiere->id ? 'selected' : '' }}>
                                        {{ $filiere->nom_filiere }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search me-1"></i>Filtrer
                                </button>
                                <a href="{{ route('administration.etablissement.niveaux.show', $niveauData->id) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-redo me-1"></i>Réinitialiser
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau Groupes -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>
                    Groupes du Niveau {{ $niveauData->nom }}
                </h5>
            </div>
            <div class="card-body">
                @if($niveauData->groupes && $niveauData->groupes->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i>Code</th>
                                    <th><i class="fas fa-stream me-1"></i>Filière</th>
                                    <th><i class="fas fa-certificate me-1"></i>Type Formation</th>
                                    <th><i class="fas fa-users me-1"></i>Effectif</th>
                                    <th><i class="fas fa-calendar me-1"></i>Année</th>
                                    <th><i class="fas fa-toggle-on me-1"></i>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($niveauData->groupes as $groupe)
                                <tr>
                                    <td>
                                        <strong>{{ $groupe->code_groupe }}</strong>
                                    </td>
                                    <td>
                                        {{ $groupe->filiere->nom_filiere ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">
                                            {{ $groupe->filiere->secteur->nom_secteur ?? 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $groupe->formation->type ?? 'N/A' }}
                                        </span>
                                        <br>
                                        <small>{{ $groupe->formation->mode ?? 'N/A' }} - {{ $groupe->formation->creneau ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $groupe->effectif_groupe }} stagiaire(s)
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $groupe->annee_formation }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($groupe->statut === 'Actif')
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <p class="mb-0">Aucun groupe pour ce niveau.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Onglet Secteurs -->
    <div class="tab-pane fade" id="secteurs" role="tabpanel">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-industry me-2"></i>
                    Secteurs concernés
                </h5>
            </div>
            <div class="card-body">
                @if($secteurs && $secteurs->count() > 0)
                    <div class="row">
                        @foreach($secteurs as $secteur)
                        <div class="col-md-4 mb-3">
                            <div class="card border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-industry fa-2x text-info mb-2"></i>
                                    <h6 class="card-title">{{ $secteur->nom_secteur }}</h6>
                                    <p class="card-text">
                                        <small class="text-muted">Code: {{ $secteur->code ?? 'N/A' }}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <p class="mb-0">Aucun secteur pour ce niveau.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Onglet Modules -->
    <div class="tab-pane fade" id="modules" role="tabpanel">
        <!-- Filtres Modules -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Filtrer les modules
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('administration.etablissement.niveaux.show', $niveauData->id) }}" id="modulesFilterForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Recherche</label>
                            <input type="text" name="module_search" class="form-control form-control-sm" 
                                   placeholder="Code ou nom..." value="{{ request('module_search') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search me-1"></i>Filtrer
                                </button>
                                <a href="{{ route('administration.etablissement.niveaux.show', $niveauData->id) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-redo me-1"></i>Réinitialiser
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau Modules -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-book me-2"></i>
                    Modules du Niveau {{ $niveauData->nom }}
                </h5>
            </div>
            <div class="card-body">
                @if($modules && $modules->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-hashtag me-1"></i>Code</th>
                                    <th><i class="fas fa-book me-1"></i>Nom du Module</th>
                                    <th><i class="fas fa-map-marker-alt me-1"></i>Régional</th>
                                    <th><i class="fas fa-tasks me-1"></i>Affectations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($modules as $module)
                                <tr>
                                    <td><code>{{ $module->code_module }}</code></td>
                                    <td>{{ $module->nom_module }}</td>
                                    <td>
                                        @if($module->regional === 'O')
                                            <span class="badge bg-success">Oui</span>
                                        @else
                                            <span class="badge bg-secondary">Non</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $module->affectations_count ?? 0 }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <p class="mb-0">Aucun module pour ce niveau.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Onglet Formateurs -->
    <div class="tab-pane fade" id="formateurs-content" role="tabpanel">
        <!-- Filtres Formateurs -->
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Filtrer les formateurs
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('administration.etablissement.niveaux.show', $niveauData->id) }}" id="formateursFilterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Recherche</label>
                            <input type="text" name="formateur_search" class="form-control form-control-sm" 
                                   placeholder="Nom..." value="{{ request('formateur_search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Type</label>
                            <select name="formateur_type" class="form-select form-select-sm">
                                <option value="">Tous</option>
                                <option value="permanent" {{ request('formateur_type') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                                <option value="vacataire" {{ request('formateur_type') == 'vacataire' ? 'selected' : '' }}>Vacataire</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search me-1"></i>Filtrer
                                </button>
                                <a href="{{ route('administration.etablissement.niveaux.show', $niveauData->id) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-redo me-1"></i>Réinitialiser
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau Formateurs -->
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fas fa-chalkboard-teacher me-2"></i>
                    Formateurs intervenant dans ce niveau
                </h5>
            </div>
            <div class="card-body">
                @if($formateurs && $formateurs->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-id-card me-1"></i>Matricule</th>
                                    <th><i class="fas fa-user me-1"></i>Nom Complet</th>
                                    <th><i class="fas fa-briefcase me-1"></i>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($formateurs as $formateur)
                                <tr>
                                    <td><code>{{ $formateur->mle }}</code></td>
                                    <td>{{ $formateur->nom_complet }}</td>
                                    <td>
                                        @if($formateur->type === 'permanent')
                                            <span class="badge bg-success">Permanent</span>
                                        @else
                                            <span class="badge bg-warning">Vacataire</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <p class="mb-0">Aucun formateur affecté à ce niveau.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Onglet Statistiques -->
    <div class="tab-pane fade" id="stats-content" role="tabpanel">
        <div class="row">
            <!-- Formations par Type -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Formations par Type
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(isset($stats['formations_par_type']) && count($stats['formations_par_type']) > 0)
                            <ul class="list-group list-group-flush">
                                @foreach($stats['formations_par_type'] as $type => $count)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $type }}
                                    <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted text-center mb-0">Aucune donnée disponible</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Formations par Année -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Formations par Année
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(isset($stats['formations_par_annee']) && count($stats['formations_par_annee']) > 0)
                            <div class="row">
                                @foreach($stats['formations_par_annee'] as $annee => $count)
                                <div class="col-md-6 mb-2">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h5 class="mb-0">{{ $count }}</h5>
                                            <small class="text-muted">Année {{ $annee }}</small>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center mb-0">Aucune donnée disponible</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialiser les tooltips Bootstrap si nécessaire
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush