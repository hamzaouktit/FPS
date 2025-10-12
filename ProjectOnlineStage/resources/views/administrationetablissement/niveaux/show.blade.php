@extends('layouts.app')

@section('title', 'Détails du Niveau')

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
            <i class="fas fa-eye"></i> {{ $niveauData->niveau }}
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
                <i class="fas fa-layer-group me-2"></i>
                {{ $niveauData->niveau }}
            </h4>
            <div>
                <a href="{{ route('administration.etablissement.niveaux.edit', $niveauData->niveau) }}" 
                   class="btn btn-warning btn-sm me-2">
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
                <table class="table table-borderless">
                    <tr>
                        <th width="40%"><i class="fas fa-hashtag text-primary me-2"></i>ID Niveau :</th>
                        <td><span class="badge bg-secondary fs-6">{{ $niveauData->id }}</span></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-school text-primary me-2"></i>Établissement :</th>
                        <td><strong>{{ $niveauData->etablissement->nom_efp }}</strong></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="40%"><i class="fas fa-calendar-plus text-primary me-2"></i>Date de création :</th>
                        <td>{{ $niveauData->created_at->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-calendar-check text-primary me-2"></i>Dernière modification :</th>
                        <td>{{ $niveauData->updated_at->format('d/m/Y') }}</td>
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
                <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                <h3 class="mb-0">{{ $filieres->count() }}</h3>
                <p class="mb-0">Filière(s)</p>
            </div>
        </div>
    </div>
</div>

<!-- Répartition par filière et type -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Répartition par Filière
                </h5>
            </div>
            <div class="card-body">
                @if($stats['formations_par_filiere']->isEmpty())
                    <p class="text-muted text-center mb-0">Aucune formation enregistrée</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Filière</th>
                                    <th class="text-end">Formations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['formations_par_filiere'] as $filiere => $count)
                                <tr>
                                    <td>
                                        <i class="fas fa-graduation-cap text-primary me-2"></i>
                                        {{ $filiere ?? 'Non définie' }}
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

<!-- Répartition par année -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">
            <i class="fas fa-calendar-alt me-2"></i>
            Répartition par Année de Formation
        </h5>
    </div>
    <div class="card-body">
        @if($stats['formations_par_annee']->isEmpty())
            <div class="alert alert-info text-center mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Aucune formation enregistrée pour ce niveau.
            </div>
        @else
            <div class="row">
                @foreach($stats['formations_par_annee'] as $annee => $count)
                <div class="col-md-3 mb-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <h4 class="card-title text-info mb-2">
                                <i class="fas fa-calendar me-2"></i>{{ $annee }}
                            </h4>
                            <p class="card-text mb-0">
                                <span class="badge bg-info fs-6">{{ $count }} formation(s)</span>
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Liste des secteurs -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0">
            <i class="fas fa-layer-group me-2"></i>
            Secteurs Concernés
        </h5>
    </div>
    <div class="card-body">
        @if($secteurs->isEmpty())
            <div class="alert alert-info text-center mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Aucun secteur associé à ce niveau.
            </div>
        @else
            <div class="row">
                @foreach($secteurs as $secteur)
                <div class="col-md-4 mb-3">
                    <div class="card border-secondary">
                        <div class="card-body">
                            <h6 class="card-title">
                                <i class="fas fa-layer-group text-secondary me-2"></i>
                                {{ $secteur->nom_secteur }}
                            </h6>
                            <p class="card-text mb-0">
                                <small class="text-muted">
                                    {{ $secteur->filieres()->whereHas('formations', function($q) use ($niveauData) {
                                        $q->where('niveau_id', $niveauData->id);
                                    })->count() }} filière(s)
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

<!-- Liste des filières -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-graduation-cap me-2"></i>
            Filières du Niveau
        </h5>
    </div>
    <div class="card-body">
        @if($filieres->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Aucune filière associée à ce niveau.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Nom de la Filière</th>
                            <th>Secteur</th>
                            <th class="text-center">Formations</th>
                            <th class="text-center">Groupes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($filieres as $filiere)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $filiere->code_filiere }}</span>
                            </td>
                            <td>
                                <strong>{{ $filiere->nom_filiere }}</strong>
                            </td>
                            <td>
                                <i class="fas fa-layer-group text-primary me-1"></i>
                                {{ $filiere->secteur->nom_secteur ?? 'N/A' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">
                                    {{ $filiere->formations()->where('niveau_id', $niveauData->id)->count() }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">
                                    {{ $filiere->groupes()->whereHas('formation', function($q) use ($niveauData) {
                                        $q->where('niveau_id', $niveauData->id);
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

<!-- Liste des formations -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">
            <i class="fas fa-chalkboard-teacher me-2"></i>
            Formations du Niveau
        </h5>
    </div>
    <div class="card-body">
        @if($niveauData->formations->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Aucune formation enregistrée pour ce niveau.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Année</th>
                            <th>Filière</th>
                            <th>Secteur</th>
                            <th>Type</th>
                            <th>Créneau</th>
                            <th class="text-center">Groupes</th>
                            <th class="text-center">Effectif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($niveauData->formations as $formation)
                        <tr>
                            <td>
                                <span class="badge bg-secondary">{{ $formation->annee }}</span>
                            </td>
                            <td>
                                <i class="fas fa-graduation-cap text-primary me-1"></i>
                                {{ $formation->filiere->nom_filiere ?? 'N/A' }}
                            </td>
                            <td>
                                {{ $formation->filiere->secteur->nom_secteur ?? 'N/A' }}
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

<!-- Liste des groupes -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
            <i class="fas fa-users me-2"></i>
            Groupes du Niveau
        </h5>
    </div>
    <div class="card-body">
        @if($niveauData->groupes->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Aucun groupe associé à ce niveau.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nom du Groupe</th>
                            <th>Filière</th>
                            <th>Année Formation</th>
                            <th class="text-center">Effectif</th>
                            <th>Sous-groupe</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($niveauData->groupes as $groupe)
                        <tr>
                            <td>
                                <strong>{{ $groupe->nom_groupe }}</strong>
                            </td>
                            <td>
                                <i class="fas fa-graduation-cap text-primary me-1"></i>
                                {{ $groupe->formation->filiere->nom_filiere ?? 'N/A' }}
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $groupe->annee_formation }}</span>
                            </td>
                            <td class="text-center">
                                <i class="fas fa-users text-success me-1"></i>
                                <strong>{{ $groupe->effectif_groupe }}</strong>
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
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">
            <i class="fas fa-book me-2"></i>
            Modules Enseignés
        </h5>
    </div>
    <div class="card-body">
        @if($modules->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Aucun module associé à ce niveau.</p>
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
                                <span class="badge bg-info">{{ $module->avancements_count }}</span>
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
    <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <h5 class="mb-0">
            <i class="fas fa-chalkboard-teacher me-2"></i>
            Formateurs Affectés
        </h5>
    </div>
    <div class="card-body">
        @if($formateurs->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p class="mb-0">Aucun formateur affecté à ce niveau.</p>
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
                                    {{ $formateur->affectations()->whereHas('groupe.formation', function($q) use ($niveauData) {
                                        $q->where('niveau_id', $niveauData->id);
                                    })->count() }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">
                                    {{ $formateur->affectations()->whereHas('groupe.formation', function($q) use ($niveauData) {
                                        $q->where('niveau_id', $niveauData->id);
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
    <div class="card-header bg-dark text-white">
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
                    {{ $niveauData->created_at->format('d/m/Y à H:i') }}
                </p>
                <p class="mb-2">
                    <i class="fas fa-calendar-check text-success me-2"></i>
                    <strong>Dernière modification :</strong> 
                    {{ $niveauData->updated_at->format('d/m/Y à H:i') }}
                </p>
            </div>
            <div class="col-md-6">
                <p class="mb-2">
                    <i class="fas fa-hashtag text-info me-2"></i>
                    <strong>ID Niveau :</strong> 
                    {{ $niveauData->id }}
                </p>
                <p class="mb-2">
                    <i class="fas fa-school text-warning me-2"></i>
                    <strong>Code EFP :</strong> 
                    <span class="badge bg-secondary">{{ $niveauData->code_efp }}</span>
                </p>
            </div>
        </div>

        <!-- Résumé global -->
        <div class="card bg-light mt-4">
            <div class="card-body">
                <h6 class="card-title">
                    <i class="fas fa-chart-line text-success me-2"></i>
                    Résumé Global
                </h6>
                <p class="mb-1">
                    Le niveau <strong>{{ $niveauData->niveau }}</strong> compte actuellement 
                    <strong class="text-primary">{{ $stats['total_formations'] }}</strong> formation(s) réparties sur 
                    <strong class="text-success">{{ $stats['total_groupes'] }}</strong> groupe(s), 
                    avec un effectif total de <strong class="text-info">{{ $stats['effectif_total'] }}</strong> stagiaire(s).
                </p>
                @if($filieres->count() > 0)
                <p class="mb-0">
                    Ces formations couvrent <strong class="text-warning">{{ $filieres->count() }}</strong> filière(s) 
                    dans <strong class="text-secondary">{{ $secteurs->count() }}</strong> secteur(s) différent(s).
                </p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="text-center mt-4">
    <a href="{{ route('administration.etablissement.niveaux.edit', $niveauData->niveau) }}" 
       class="btn btn-warning btn-lg me-2">
        <i class="fas fa-edit me-2"></i>
        Modifier ce niveau
    </a>
    <a href="{{ route('administration.etablissement.niveaux.index') }}" 
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
            }, index * 30);
        });
    });
</script>
@endpush