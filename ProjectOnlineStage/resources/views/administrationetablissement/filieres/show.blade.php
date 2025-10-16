@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 d-inline-block fw-bold">{{ $filiere->nom_filiere }}</h1>
            <p class="text-muted">{{ auth()->user()->etablissement->nom_efp }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('administration.etablissement.filieres.edit', $filiere->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <a href="{{ route('administration.etablissement.filieres.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted mb-2">Groupes</h6>
                    <h2 class="text-primary fw-bold">{{ $stats['total_groupes'] }}</h2>
                    <small class="text-success">{{ $stats['groupes_actifs'] }} actifs</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted mb-2">Effectif Total</h6>
                    <h2 class="text-info fw-bold">{{ $stats['effectif_total'] }}</h2>
                    <small>Stagiaires</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted mb-2">Modules</h6>
                    <h2 class="text-success fw-bold">{{ $stats['total_modules'] }}</h2>
                    <small>{{ $stats['modules_regionaux'] }} régionaux</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h6 class="card-title text-muted mb-2">Secteur</h6>
                    <p class="fw-bold">{{ $filiere->secteur->nom_secteur }}</p>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">
                <i class="fas fa-info-circle"></i> Informations
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="groupes-tab" data-bs-toggle="tab" data-bs-target="#groupes" type="button">
                <i class="fas fa-users"></i> Groupes ({{ $groupes->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="modules-tab" data-bs-toggle="tab" data-bs-target="#modules" type="button">
                <i class="fas fa-book"></i> Modules ({{ $modules->count() }})
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="formateurs-tab" data-bs-toggle="tab" data-bs-target="#formateurs" type="button">
                <i class="fas fa-chalkboard-user"></i> Formateurs
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="progression-tab" data-bs-toggle="tab" data-bs-target="#progression" type="button">
                <i class="fas fa-chart-line"></i> Progression
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- TAB INFO -->
        <div class="tab-pane fade show active" id="info" role="tabpanel">
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 fw-bold">Détails de la Filière</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless small">
                                <tr>
                                    <td class="fw-bold text-muted">ID:</td>
                                    <td class="text-end">{{ $filiere->id }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Code:</td>
                                    <td class="text-end"><span class="badge bg-secondary">{{ $filiere->code_filiere }}</span></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Nom:</td>
                                    <td class="text-end">{{ $filiere->nom_filiere }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Secteur:</td>
                                    <td class="text-end">{{ $filiere->secteur->nom_secteur }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Code EFP:</td>
                                    <td class="text-end">{{ $filiere->code_efp }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Créé le:</td>
                                    <td class="text-end">{{ $filiere->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Modifié le:</td>
                                    <td class="text-end">{{ $filiere->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 fw-bold">Statistiques par Année</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Année</th>
                                            <th>Groupes</th>
                                            <th>Effectif</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupesParAnnee as $groupe)
                                            <tr>
                                                <td class="fw-bold">{{ $groupe->annee_formation }}</td>
                                                <td><span class="badge bg-primary">{{ $groupe->total }}</span></td>
                                                <td>{{ $groupe->effectif_total }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 fw-bold">Répartition par Type de Formation</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Type</th>
                                            <th>Groupes</th>
                                            <th>Effectif</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupesParType as $groupe)
                                            <tr>
                                                <td class="fw-bold">{{ $groupe->type }}</td>
                                                <td><span class="badge bg-info">{{ $groupe->total }}</span></td>
                                                <td>{{ $groupe->effectif_total }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 fw-bold">Répartition par Mode de Formation</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Mode</th>
                                            <th>Groupes</th>
                                            <th>Effectif</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupesParMode as $groupe)
                                            <tr>
                                                <td class="fw-bold">{{ $groupe->mode }}</td>
                                                <td><span class="badge bg-success">{{ $groupe->total }}</span></td>
                                                <td>{{ $groupe->effectif_total }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB GROUPES -->
        <div class="tab-pane fade" id="groupes" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-bold">Liste des Groupes</h5>
                </div>
                <div class="card-body">
                    @if($groupes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code Groupe</th>
                                        <th>Année Formation</th>
                                        <th>Effectif</th>
                                        <th>Statut</th>
                                        <th>Type Formation</th>
                                        <th>Mode</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groupes as $groupe)
                                        <tr>
                                            <td class="fw-bold">{{ $groupe->code_groupe }}</td>
                                            <td>{{ $groupe->annee_formation }}</td>
                                            <td><span class="badge bg-secondary">{{ $groupe->effectif_groupe }}</span></td>
                                            <td>
                                                @if($groupe->statut === 'Actif')
                                                    <span class="badge bg-success">{{ $groupe->statut }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $groupe->statut }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $groupe->formation->type }}</td>
                                            <td>{{ $groupe->formation->mode }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Aucun groupe associé
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB MODULES -->
        <div class="tab-pane fade" id="modules" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-bold">Modules de la Filière</h5>
                </div>
                <div class="card-body">
                    @if($modules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code Module</th>
                                        <th>Nom Module</th>
                                        <th>Type</th>
                                        <th>Affectations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($modules as $module)
                                        <tr>
                                            <td class="fw-bold">{{ $module->code_module }}</td>
                                            <td>{{ $module->nom_module }}</td>
                                            <td>
                                                @if($module->regional === 'O')
                                                    <span class="badge bg-warning">Régional</span>
                                                @endif
                                                @if($module->module_pie === 'O')
                                                    <span class="badge bg-info">PIE</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-secondary">{{ $module->nb_affectations }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Aucun module associé
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB FORMATEURS -->
        <div class="tab-pane fade" id="formateurs" role="tabpanel">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0 fw-bold">Formateurs Intervenant</h5>
                </div>
                <div class="card-body">
                    @if($formateurs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Matricule</th>
                                        <th>Nom Complet</th>
                                        <th>Type</th>
                                        <th>Affectations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($formateurs as $formateur)
                                        <tr>
                                            <td class="fw-bold">{{ $formateur->mle }}</td>
                                            <td>{{ $formateur->nom_complet }}</td>
                                            <td>
                                                @if($formateur->type === 'permanent')
                                                    <span class="badge bg-success">Permanent</span>
                                                @else
                                                    <span class="badge bg-warning">Vacataire</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-secondary">{{ $formateur->nb_affectations }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle"></i> Aucun formateur associé
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB PROGRESSION -->
        <div class="tab-pane fade" id="progression" role="tabpanel">
            <div class="row">
                @if($avancementStats)
                    <div class="col-md-12 mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card shadow-sm">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-muted mb-2">Taux de Réalisation Moyen</h6>
                                        <h2 class="text-primary fw-bold">
                                            {{ $avancementStats->taux_moyen ? number_format($avancementStats->taux_moyen, 2) : '0' }}%
                                        </h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card shadow-sm">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-muted mb-2">MH Réalisées</h6>
                                        <h2 class="text-info fw-bold">{{ $avancementStats->total_mh_realisees ?? '0' }}</h2>
                                        <small>/ {{ $avancementStats->total_mh_affectees ?? '0' }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card shadow-sm">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-muted mb-2">EFM Validés</h6>
                                        <h2 class="text-success fw-bold">{{ $avancementStats->total_efm_valides ?? '0' }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card shadow-sm">
                                    <div class="card-body text-center">
                                        <h6 class="card-title text-muted mb-2">Absence Moyenne</h6>
                                        <h2 class="text-warning fw-bold">{{ $avancementStats->moyenne_absence_globale ? number_format($avancementStats->moyenne_absence_globale, 2) : '0' }}%</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 fw-bold">Progression par Module</h5>
                        </div>
                        <div class="card-body">
                            @if($progressionModules->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Code Module</th>
                                                <th>Nom Module</th>
                                                <th>Groupes</th>
                                                <th>Taux Moyen</th>
                                                <th>MH Réalisées</th>
                                                <th>MH Affectées</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($progressionModules as $module)
                                                <tr>
                                                    <td class="fw-bold">{{ $module->code_module }}</td>
                                                    <td>{{ $module->nom_module }}</td>
                                                    <td><span class="badge bg-secondary">{{ $module->nb_groupes }}</span></td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar" role="progressbar" 
                                                                 style="width: {{ $module->taux_moyen ?? 0 }}%">
                                                                {{ $module->taux_moyen ? number_format($module->taux_moyen, 1) : '0' }}%
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $module->total_mh_realisees ?? '0' }}</td>
                                                    <td>{{ $module->total_mh_affectees ?? '0' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle"></i> Aucune donnée de progression disponible
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $filiere->id }})">
            <i class="fas fa-trash"></i> Supprimer cette Filière
        </button>
        <form id="delete-form-{{ $filiere->id }}" 
              action="{{ route('administration.etablissement.filieres.destroy', $filiere->id) }}" 
              method="POST" style="display:none;">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
    function confirmDelete(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette filière ? Cette action est irréversible.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endsection