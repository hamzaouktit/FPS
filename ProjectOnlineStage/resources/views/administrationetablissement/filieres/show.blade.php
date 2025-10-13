@extends('layouts.app')

@section('title', 'Détails de la Filière')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.filieres.index') }}">
                <i class="fas fa-graduation-cap"></i> Filières
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-eye"></i> {{ $filiere->nom_filiere }}
        </li>
    </ol>
</nav>
@endsection

@section('content')
<!-- En-tête avec informations principales -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                <i class="fas fa-graduation-cap me-2"></i>
                {{ $filiere->nom_filiere }}
            </h4>
            <div>
                <a href="{{ route('administration.etablissement.filieres.edit', $filiere->code_filiere) }}" 
                   class="btn btn-warning btn-sm me-2">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
                <a href="{{ route('administration.etablissement.filieres.index') }}" 
                   class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%"><i class="fas fa-hashtag text-primary me-2"></i>Code Filière :</th>
                        <td><span class="badge bg-secondary fs-6">{{ $filiere->code_filiere }}</span></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-layer-group text-primary me-2"></i>Secteur :</th>
                        <td><strong>{{ $filiere->secteur->nom_secteur }}</strong></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%"><i class="fas fa-school text-primary me-2"></i>Établissement :</th>
                        <td>{{ $filiere->etablissement->nom_efp }}</td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-calendar text-primary me-2"></i>Date de création :</th>
                        <td>{{ $filiere->created_at->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques générales -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                <h3 class="mb-0">{{ $stats['total_formations'] }}</h3>
                <p class="mb-0">Formation(s)</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-users fa-3x mb-3"></i>
                <h3 class="mb-0">{{ $stats['total_groupes'] }}</h3>
                <p class="mb-0">Groupe(s)</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-user-graduate fa-3x mb-3"></i>
                <h3 class="mb-0">{{ $stats['effectif_total'] }}</h3>
                <p class="mb-0">Stagiaire(s)</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning shadow-sm">
            <div class="card-body text-center">
                <i class="fas fa-book fa-3x mb-3"></i>
                <h3 class="mb-0">{{ $modules->count() }}</h3>
                <p class="mb-0">Module(s)</p>
            </div>
        </div>
    </div>
</div>

<!-- Répartition par niveau et type -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-layer-group me-2"></i>
                    Répartition par Niveau
                </h5>
            </div>
            <div class="card-body">
                @if($stats['formations_par_niveau']->isEmpty())
                    <p class="text-muted text-center mb-0">Aucune formation enregistrée</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Niveau</th>
                                    <th class="text-end">Formations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['formations_par_niveau'] as $niveau => $count)
                                <tr>
                                    <td>
                                        <i class="fas fa-graduation-cap text-primary me-2"></i>
                                        {{ $niveau }}
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ $count }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Répartition par Type de Formation
                </h5>
            </div>
            <div class="card-body">
                @if($stats['formations_par_type']->isEmpty())
                    <p class="text-muted text-center mb-0">Aucune formation enregistrée</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th class="text-end">Formations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['formations_par_type'] as $type => $count)
                                <tr>
                                    <td>
                                        <i class="fas fa-tag text-success me-2"></i>
                                        {{ $type }}
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-success">{{ $count }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Liste des formations -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-chalkboard-teacher me-2"></i>
            Formations de la Filière
        </h5>
    </div>
    <div class="card-body">
        @if($filiere->formations->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Aucune formation associée à cette filière.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Année</th>
                            <th>Niveau</th>
                            <th>Type</th>
                            <th>Créneau</th>
                            <th class="text-center">Groupes</th>
                            <th class="text-center">Effectif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filiere->formations as $formation)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $formation->annee }}</span>
                            </td>
                            <td>
                                <i class="fas fa-graduation-cap text-primary me-1"></i>
                                {{ $formation->niveau->niveau ?? 'N/A' }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $formation->type_formation ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $formation->creneau ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $formation->groupes->count() }}</span>
                            </td>
                            <td class="text-center">
                                <strong>{{ $formation->groupes->sum('effectif_groupe') }}</strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Liste des niveaux -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-layer-group me-2"></i>
            Niveaux Actifs
        </h5>
    </div>
    <div class="card-body">
        @if($niveaux->isEmpty())
            <div class="alert alert-info text-center mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Aucun niveau actif pour cette filière.
            </div>
        @else
            <div class="row">
                @foreach($niveaux as $niveau)
                <div class="col-md-4 mb-3">
                    <div class="card border-info">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-graduation-cap text-info me-2"></i>
                                {{ $niveau->niveau }}
                            </h6>
                            <p class="card-text mb-0">
                                <small class="text-muted">
                                    {{ $niveau->formations()->where('filiere_id', $filiere->id)->count() }} formation(s)
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Liste des groupes -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-users me-2"></i>
            Groupes de la Filière
        </h5>
    </div>
    <div class="card-body">
        @if($filiere->groupes->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Aucun groupe associé à cette filière.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nom du Groupe</th>
                            <th>Année Formation</th>
                            <th>Effectif</th>
                            <th>Sous-groupe</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filiere->groupes as $groupe)
                        <tr>
                            <td>
                                <strong>{{ $groupe->nom_groupe }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $groupe->annee_formation ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <i class="fas fa-users text-success me-1"></i>
                                {{ $groupe->effectif_groupe ?? 0 }} stagiaires
                            </td>
                            <td>
                                @if($groupe->sous_groupe)
                                    <span class="badge bg-warning">Oui</span>
                                @else
                                    <span class="badge bg-secondary">Non</span>
                                @endif
                            </td>
                            <td>
                                @if($groupe->statut_sous_groupe)
                                    <span class="badge bg-info">{{ $groupe->statut_sous_groupe }}</span>
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>




<!-- Liste des modules (SECTION CORRIGÉE) -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
            <i class="fas fa-book me-2"></i>
            Modules Enseignés dans cette Filière
        </h5>
    </div>
    <div class="card-body">
        @if($modules->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Aucun module associé à cette filière.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Code Module</th>
                            <th>Nom du Module</th>
                            <th class="text-center">Régional</th>
                            <th class="text-center">Avancements</th>
                            <th class="text-center">Groupes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $module)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $module->code_module }}</span>
                            </td>
                            <td>
                                <i class="fas fa-book text-warning me-2"></i>
                                {{ $module->nom_module }}
                            </td>
                            <td class="text-center">
                                @php
                                    $isRegional = !empty($module->regional) && 
                                                 (strtolower($module->regional) === 'oui' || 
                                                  $module->regional === '1' || 
                                                  $module->regional === 1 || 
                                                  $module->regional === true);
                                @endphp
                                @if($isRegional)
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        <i class="fas fa-check-circle me-1"></i> Oui
                                    </span>
                                @else
                                    <span class="badge bg-secondary rounded-pill px-3 py-2">
                                        <i class="fas fa-times-circle me-1"></i> Non
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">
                                    {{ $module->avancements->count() }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">
                                    {{ $module->avancements->pluck('groupe_id')->unique()->count() }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Liste des formateurs avec détails complets (SECTION AMÉLIORÉE) -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">
            <i class="fas fa-chalkboard-teacher me-2"></i>
            Formateurs Affectés à cette Filière ({{ $formateurs->count() }})
        </h5>
    </div>
    <div class="card-body">
        @if($formateurs->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Aucun formateur affecté à cette filière.</p>
            </div>
        @else
            <div class="accordion" id="formateursAccordion">
                @foreach($formateurs as $index => $formateur)
                @php
                    // Récupérer tous les avancements du formateur pour cette filière
                    $avancementsPresentiel = $formateur->avancementsPresentiel;
                    $avancementsSynchrone = $formateur->avancementsSynchrone;
                    $totalAvancements = $avancementsPresentiel->count() + $avancementsSynchrone->count();
                    
                    // Grouper par groupe
                    $groupesData = collect();
                    
                    // Ajouter les avancements présentiel
                    foreach($avancementsPresentiel as $av) {
                        $groupeId = $av->groupe_id;
                        if (!$groupesData->has($groupeId)) {
                            $groupesData->put($groupeId, [
                                'groupe' => $av->groupe,
                                'modules_presentiel' => collect(),
                                'modules_synchrone' => collect(),
                            ]);
                        }
                        $groupesData[$groupeId]['modules_presentiel']->push($av->module);
                    }
                    
                    // Ajouter les avancements synchrone
                    foreach($avancementsSynchrone as $av) {
                        $groupeId = $av->groupe_id;
                        if (!$groupesData->has($groupeId)) {
                            $groupesData->put($groupeId, [
                                'groupe' => $av->groupe,
                                'modules_presentiel' => collect(),
                                'modules_synchrone' => collect(),
                            ]);
                        }
                        $groupesData[$groupeId]['modules_synchrone']->push($av->module);
                    }
                @endphp
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" 
                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" 
                                aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" 
                                aria-controls="collapse{{ $index }}">
                            <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                <div>
                                    <i class="fas fa-user-tie text-danger me-2"></i>
                                    <strong>{{ $formateur->nom_formateur }}</strong>
                                    <span class="badge bg-secondary ms-2">{{ $formateur->mle }}</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-primary">
                                        <i class="fas fa-users me-1"></i>{{ $groupesData->count() }} groupe(s)
                                    </span>
                                    <span class="badge bg-info">
                                        <i class="fas fa-book me-1"></i>{{ $totalAvancements }} module(s)
                                    </span>
                                    @if($avancementsPresentiel->count() > 0)
                                        <span class="badge bg-success">
                                            <i class="fas fa-user-friends me-1"></i>Présentiel
                                        </span>
                                    @endif
                                    @if($avancementsSynchrone->count() > 0)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-video me-1"></i>Synchrone
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse{{ $index }}" 
                         class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" 
                         aria-labelledby="heading{{ $index }}" 
                         data-bs-parent="#formateursAccordion">
                        <div class="accordion-body">
                            <!-- Informations générales du formateur -->
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="alert alert-light border">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong><i class="fas fa-id-card text-primary me-2"></i>Matricule:</strong><br>
                                                <span class="badge bg-secondary fs-6">{{ $formateur->mle }}</span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong><i class="fas fa-school text-info me-2"></i>Établissement:</strong><br>
                                                {{ $formateur->etablissement->nom_efp }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong><i class="fas fa-graduation-cap text-success me-2"></i>Filière:</strong><br>
                                                {{ $filiere->nom_filiere }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong><i class="fas fa-layer-group text-warning me-2"></i>Secteur:</strong><br>
                                                {{ $filiere->secteur->nom_secteur }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Détails par groupe -->
                            @if($groupesData->isEmpty())
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Aucun groupe assigné pour cette filière.
                                </div>
                            @else
                                @foreach($groupesData as $groupeId => $data)
                                <div class="card mb-3 border-primary">
                                    <div class="card-header bg-primary bg-opacity-10">
                                        <h6 class="mb-0">
                                            <i class="fas fa-users text-primary me-2"></i>
                                            <strong>Groupe: {{ $data['groupe']->nom_groupe }}</strong>
                                            <span class="badge bg-info ms-2">
                                                {{ $data['groupe']->effectif_groupe ?? 0 }} stagiaires
                                            </span>
                                            @if($data['groupe']->annee_formation)
                                                <span class="badge bg-secondary ms-1">
                                                    Année: {{ $data['groupe']->annee_formation }}
                                                </span>
                                            @endif
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- Modules en Présentiel -->
                                            @if($data['modules_presentiel']->count() > 0)
                                            <div class="col-md-6 mb-3">
                                                <h6 class="text-success">
                                                    <i class="fas fa-user-friends me-2"></i>
                                                    Modules en Présentiel ({{ $data['modules_presentiel']->count() }})
                                                </h6>
                                                <ul class="list-group list-group-flush">
                                                    @foreach($data['modules_presentiel'] as $module)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                                        <div>
                                                            <span class="badge bg-secondary me-2">{{ $module->code_module }}</span>
                                                            <small>{{ $module->nom_module }}</small>
                                                        </div>
                                                        @php
                                                            $isRegional = !empty($module->regional) && 
                                                                         ($module->regional === 'Oui' || 
                                                                          strtolower($module->regional) === 'oui' || 
                                                                          $module->regional === '1' || 
                                                                          $module->regional === 1 || 
                                                                          $module->regional === true);
                                                        @endphp
                                                        @if($isRegional)
                                                            <span class="badge bg-success rounded-pill px-3 py-1">
                                                                <i class="fas fa-check-circle me-1"></i> Régional
                                                            </span>
                                                        @endif
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            @endif

                                            <!-- Modules en Synchrone -->
                                            @if($data['modules_synchrone']->count() > 0)
                                            <div class="col-md-6 mb-3">
                                                <h6 class="text-warning">
                                                    <i class="fas fa-video me-2"></i>
                                                    Modules en Synchrone ({{ $data['modules_synchrone']->count() }})
                                                </h6>
                                                <ul class="list-group list-group-flush">
                                                    @foreach($data['modules_synchrone'] as $module)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                                        <div>
                                                            <span class="badge bg-secondary me-2">{{ $module->code_module }}</span>
                                                            <small>{{ $module->nom_module }}</small>
                                                        </div>
                                                        @php
                                                            $isRegional = !empty($module->regional) && 
                                                                         ($module->regional === 'Oui' || 
                                                                          strtolower($module->regional) === 'oui' || 
                                                                          $module->regional === '1' || 
                                                                          $module->regional === 1 || 
                                                                          $module->regional === true);
                                                        @endphp
                                                        @if($isRegional)
                                                            <span class="badge bg-success rounded-pill px-3 py-1">
                                                                <i class="fas fa-check-circle me-1"></i> Régional
                                                            </span>
                                                        @endif
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            @endif
                                        </div>

                                        <!-- Informations supplémentaires du groupe -->
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    <strong>Formation:</strong> 
                                                    {{ $data['groupe']->formation->niveau->niveau ?? 'N/A' }} - 
                                                    {{ $data['groupe']->formation->type_formation ?? 'N/A' }}
                                                    @if($data['groupe']->formation->creneau)
                                                        ({{ $data['groupe']->formation->creneau }})
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @endif

                            <!-- Statistiques du formateur -->
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <div class="card text-center bg-light">
                                        <div class="card-body py-2">
                                            <h5 class="mb-0 text-primary">{{ $groupesData->count() }}</h5>
                                            <small class="text-muted">Groupe(s)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center bg-light">
                                        <div class="card-body py-2">
                                            <h5 class="mb-0 text-success">{{ $avancementsPresentiel->count() }}</h5>
                                            <small class="text-muted">Module(s) Présentiel</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center bg-light">
                                        <div class="card-body py-2">
                                            <h5 class="mb-0 text-warning">{{ $avancementsSynchrone->count() }}</h5>
                                            <small class="text-muted">Module(s) Synchrone</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center bg-light">
                                        <div class="card-body py-2">
                                            <h5 class="mb-0 text-info">{{ $totalAvancements }}</h5>
                                            <small class="text-muted">Total Module(s)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Résumé global -->
            <div class="alert alert-info mt-4 mb-0">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h4 class="mb-0">{{ $formateurs->count() }}</h4>
                        <small>Formateur(s)</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="mb-0">
                            {{ $formateurs->sum(function($f) { 
                                return $f->avancementsPresentiel->pluck('groupe_id')
                                    ->merge($f->avancementsSynchrone->pluck('groupe_id'))
                                    ->unique()
                                    ->count(); 
                            }) }}
                        </h4>
                        <small>Groupe(s) Affecté(s)</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="mb-0">
                            {{ $formateurs->sum(function($f) { 
                                return $f->avancementsPresentiel->count() + $f->avancementsSynchrone->count(); 
                            }) }}
                        </h4>
                        <small>Total Affectation(s)</small>
                    </div>
                    <div class="col-md-3">
                        <h4 class="mb-0">
                            {{ $formateurs->sum(function($f) { 
                                return $f->avancementsPresentiel->pluck('module_id')
                                    ->merge($f->avancementsSynchrone->pluck('module_id'))
                                    ->unique()
                                    ->count(); 
                            }) }}
                        </h4>
                        <small>Module(s) Unique(s)</small>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .accordion-button:not(.collapsed) {
        background-color: #dc3545;
        color: white;
    }
    
    .accordion-button:not(.collapsed)::after {
        filter: brightness(0) invert(1);
    }
    
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,0,0,.125);
    }
    
    .list-group-item {
        border-left: 3px solid #0d6efd;
    }
    
    .card-header.bg-primary.bg-opacity-10 {
        border-left: 4px solid #0d6efd;
    }
</style>
@endpush







<!-- Actions rapides -->
<div class="text-center mt-4 mb-4">
    <a href="{{ route('administration.etablissement.filieres.edit', $filiere->code_filiere) }}" 
       class="btn btn-warning btn-lg me-2">
        <i class="fas fa-edit me-2"></i>
        Modifier cette filière
    </a>
    <a href="{{ route('administration.etablissement.filieres.index') }}" 
       class="btn btn-secondary btn-lg">
        <i class="fas fa-arrow-left me-2"></i>
        Retour à la liste
    </a>
</div>
@endsection

@push('scripts')
<script>
    // Animation pour les cartes statistiques
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.5s ease';
                
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 50);
            }, index * 50);
        });
    });
</script>
@endpush