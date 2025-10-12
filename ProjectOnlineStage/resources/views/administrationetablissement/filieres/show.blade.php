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
                                <span class="badge bg-info">{{ $formation->type_formation }}</span>
                            </td>
                            <td>{{ $formation->creneau }}</td>
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
                                <span class="badge bg-secondary">{{ $groupe->annee_formation }}</span>
                            </td>
                            <td>
                                <i class="fas fa-users text-success me-1"></i>
                                {{ $groupe->effectif_groupe }} stagiaires
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

<!-- Liste des modules -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
            <i class="fas fa-book me-2"></i>
            Modules Enseignés
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
                            <th>Régional</th>
                            <th class="text-center">Avancements</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $module)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $module->code_module }}</span>
                            </td>
                            <td>{{ $module->nom_module }}</td>
                            <td>
                                @if($module->regional)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Oui
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times me-1"></i>Non
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">
                                    {{ $module->avancements()->whereHas('groupe.formation', function($q) use ($filiere) {
                                        $q->where('filiere_id', $filiere->id);
                                    })->count() }}
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

<!-- Liste des formateurs -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">
            <i class="fas fa-chalkboard-teacher me-2"></i>
            Formateurs Affectés
        </h5>
    </div>
    <div class="card-body">
        @if($formateurs->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Aucun formateur affecté à cette filière.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Matricule</th>
                            <th>Nom du Formateur</th>
                            <th class="text-center">Affectations</th>
                            <th class="text-center">Groupes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($formateurs as $formateur)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $formateur->mle }}</span>
                            </td>
                            <td>
                                <i class="fas fa-user text-primary me-2"></i>
                                {{ $formateur->nom_formateur }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">
                                    {{ $formateur->affectations()->whereHas('groupe.formation', function($q) use ($filiere) {
                                        $q->where('filiere_id', $filiere->id);
                                    })->count() }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">
                                    {{ $formateur->affectations()->whereHas('groupe.formation', function($q) use ($filiere) {
                                        $q->where('filiere_id', $filiere->id);
                                    })->distinct('groupe_id')->count('groupe_id') }}
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

<!-- Informations complémentaires -->
<div class="card shadow-sm">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">
            <i class="fas fa-info-circle me-2"></i>
            Informations Complémentaires
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-2">
                    <i class="fas fa-calendar-plus text-primary me-2"></i>
                    <strong>Date de création :</strong> 
                    {{ $filiere->created_at->format('d/m/Y à H:i') }}
                </p>
                <p class="mb-2">
                    <i class="fas fa-calendar-check text-success me-2"></i>
                    <strong>Dernière modification :</strong> 
                    {{ $filiere->updated_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            <div class="col-md-6">
                <p class="mb-2">
                    <i class="fas fa-hashtag text-info me-2"></i>
                    <strong>ID Filière :</strong> 
                    {{ $filiere->id }}
                </p>
                <p class="mb-2">
                    <i class="fas fa-school text-warning me-2"></i>
                    <strong>Code EFP :</strong> 
                    <span class="badge bg-secondary">{{ $filiere->code_efp }}</span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="text-center mt-4">
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