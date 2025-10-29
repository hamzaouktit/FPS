@extends('layouts.app')

@section('title', 'Détails - ' . $etablissement->nom_efp)

@push('styles')
<style>
    .table-wrapper {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
    }
    
    .table-wrapper::-webkit-scrollbar {
        width: 12px;
        height: 12px;
    }
    
    .table-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .table-wrapper::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    .table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    .table-bordered th,
    .table-bordered td {
        border-color: #dee2e6 !important;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .pagination .page-link {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    /* Style pour les cellules vides (non affecté) */
    .non-affecte {
        background-color: #fff3cd;
        font-style: italic;
        color: #856404;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-3">
    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $etablissement->nom_efp }}</h1>
            <p class="text-muted mb-0">
                Code EFP: {{ $etablissement->code_efp }} | 
                Complexe: {{ $complexe->nom }}
            </p>
        </div>
        <div>
            <a href="{{ route('administration.complexe.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Tableau de bord
            </a>
            <a href="{{ route('administration.complexe.etablissements.index') }}" class="btn btn-secondary">
                <i class="bi bi-x-lg"></i> Retour à la liste
            </a>
        </div>
    </div>

    {{-- Cartes de statistiques --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Formations</p>
                            <h3 class="mb-0">{{ $statistics['nb_formations'] }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-2 rounded">
                            <i class="bi bi-book text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Groupes</p>
                            <h3 class="mb-0">{{ $statistics['nb_groupes'] }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded">
                            <i class="bi bi-people text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Apprenants</p>
                            <h3 class="mb-0">{{ number_format($statistics['nb_apprenants']) }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-2 rounded">
                            <i class="bi bi-person-badge text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-1 small">Formateurs</p>
                            <h3 class="mb-0">{{ $statistics['nb_formateurs'] }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-2 rounded">
                            <i class="bi bi-person-workspace text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques détaillées --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Heures de formation</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Heures requises</span>
                            <strong>{{ number_format($statistics['heures_requises'], 2) }}h</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">Heures affectées</span>
                            <strong>{{ number_format($statistics['heures_affectees'], 2) }}h</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted small">Heures réalisées</span>
                            <strong class="text-success">{{ number_format($statistics['heures_realisees'], 2) }}h</strong>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Différence</span>
                        <strong class="{{ $statistics['difference'] < 0 ? 'text-danger' : 'text-muted' }}">
                            {{ number_format($statistics['difference'], 2) }}h
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Taux de réalisation</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h2 class="mb-0 {{ $statistics['taux_realisation'] >= 80 ? 'text-success' : ($statistics['taux_realisation'] >= 50 ? 'text-warning' : 'text-danger') }}">
                            {{ number_format($statistics['taux_realisation'], 2) }}%
                        </h2>
                        <p class="text-muted small mb-0">Taux global</p>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar {{ $statistics['taux_realisation'] >= 80 ? 'bg-success' : ($statistics['taux_realisation'] >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                             role="progressbar" 
                             style="width: {{ min($statistics['taux_realisation'], 100) }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Taux d'affectation</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h2 class="mb-0 {{ $statistics['taux_affectation'] >= 80 ? 'text-success' : ($statistics['taux_affectation'] >= 50 ? 'text-warning' : 'text-danger') }}">
                            {{ number_format($statistics['taux_affectation'], 2) }}%
                        </h2>
                        <p class="text-muted small mb-0">Taux global</p>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar {{ $statistics['taux_affectation'] >= 80 ? 'bg-success' : ($statistics['taux_affectation'] >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                             role="progressbar" 
                             style="width: {{ min($statistics['taux_affectation'], 100) }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ CORRECTION : Section modules non affectés par filière --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">
            <i class="bi bi-exclamation-triangle text-warning"></i> Modules non affectés par filière
            @if($modulesNonAffectesParFiliere->count() > 0)
                <span class="badge bg-warning text-dark">
                    {{ $modulesNonAffectesParFiliere->sum(function($m) { return $m->count(); }) }}
                </span>
            @endif
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-wrapper" style="max-height: 400px; overflow: auto;">
            <table class="table table-sm table-hover table-bordered mb-0">
                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="width: 25%;">Filière</th>
                        <th style="width: 15%;">Code Module</th>
                        <th style="width: 40%;">Module</th>
                        <th style="width: 20%;">Raison</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($modulesNonAffectesParFiliere as $filiere => $modules)
                        @foreach($modules as $module)
                        <tr>
                            <td>
                                <strong>{{ $module->code_filiere }}</strong> - {{ $filiere }}
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $module->code_module }}</span>
                            </td>
                            <td>{{ $module->nom_module }}</td>
                            <td>
                                <span class="badge bg-{{ $module->raison == 'Aucun groupe actif' ? 'info' : 'warning' }}">
                                    {{ $module->raison }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">
                                <i class="bi bi-check-circle text-success fs-4 d-block mb-2"></i>
                                Tous les modules des filières sont affectés avec des formateurs
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($modulesNonAffectesParFiliere->count() > 0)
    <div class="card-footer bg-light border-top">
        <small class="text-muted">
            <i class="bi bi-info-circle"></i>
            <strong>Légende :</strong> 
            <span class="badge bg-warning text-dark">Affectations sans formateur</span> = L'affectation existe mais aucun formateur n'est assigné |
            <span class="badge bg-warning text-dark">Aucune affectation</span> = Le module n'est pas encore affecté |
            <span class="badge bg-info">Aucun groupe actif</span> = La filière n'a aucun groupe actif
        </small>
    </div>
    @endif
</div>

{{-- ✅ CORRECTION : Section modules non affectés par groupe --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">
            <i class="bi bi-exclamation-triangle text-warning"></i> Modules non affectés par groupe
            @if($modulesNonAffectesParGroupe->count() > 0)
                <span class="badge bg-warning text-dark">
                    {{ $modulesNonAffectesParGroupe->sum(function($m) { return $m->count(); }) }}
                </span>
            @endif
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-wrapper" style="max-height: 400px; overflow: auto;">
            <table class="table table-sm table-hover table-bordered mb-0">
                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="width: 15%;">Groupe</th>
                        <th style="width: 10%;">Effectif</th>
                        <th style="width: 20%;">Filière</th>
                        <th style="width: 15%;">Code Module</th>
                        <th style="width: 25%;">Module</th>
                        <th style="width: 15%;">Raison</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($modulesNonAffectesParGroupe as $groupe => $modules)
                        @foreach($modules as $module)
                        <tr>
                            <td>
                                <span class="badge bg-info">{{ $groupe }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $module->effectif }}</span>
                            </td>
                            <td>
                                <small><strong>{{ $module->code_filiere }}</strong></small><br>
                                <small class="text-muted">{{ $module->nom_filiere }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $module->code_module }}</span>
                            </td>
                            <td>{{ $module->nom_module }}</td>
                            <td>
                                <span class="badge bg-{{ $module->raison == 'Affectation sans formateur' ? 'warning' : 'danger' }}">
                                    {{ $module->raison }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-3 text-muted">
                                <i class="bi bi-check-circle text-success fs-4 d-block mb-2"></i>
                                Tous les modules de tous les groupes sont affectés avec des formateurs
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($modulesNonAffectesParGroupe->count() > 0)
    <div class="card-footer bg-light border-top">
        <small class="text-muted">
            <i class="bi bi-info-circle"></i>
            <strong>Légende :</strong> 
            <span class="badge bg-warning">Affectation sans formateur</span> = L'affectation existe mais aucun formateur n'est assigné |
            <span class="badge bg-danger">Aucune affectation</span> = Le module n'est pas encore affecté pour ce groupe
        </small>
    </div>
    @endif
</div>

{{-- ✅ AMÉLIORATION : Tableau des formateurs avec tri et couleurs --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom">
        <h5 class="mb-0">
            <i class="bi bi-person-badge text-primary"></i> Statistiques des formateurs
            <span class="badge bg-primary">{{ count($formateursStats['formateurs']) }}</span>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-wrapper" style="max-height: 500px; overflow: auto;">
            <table class="table table-sm table-hover table-bordered mb-0">
                <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                    <tr>
                        <th style="width: 12%;">MLE</th>
                        <th style="width: 30%;">Nom complet</th>
                        <th style="width: 12%;">Type</th>
                        <th class="text-end" style="width: 15%;">Heures requises</th>
                        <th class="text-end" style="width: 15%;">Heures affectées</th>
                        <th class="text-end" style="width: 16%;">Heures manquantes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($formateursStats['formateurs'] as $formateur)
                    <tr class="{{ $formateur['heures_manquantes'] > 50 ? 'table-danger' : ($formateur['heures_manquantes'] > 0 ? 'table-warning' : '') }}">
                        <td><code>{{ $formateur['mle'] }}</code></td>
                        <td>{{ $formateur['nom_complet'] }}</td>
                        <td>
                            <span class="badge bg-{{ $formateur['type'] == 'permanent' ? 'success' : 'warning' }}">
                                {{ ucfirst($formateur['type']) }}
                            </span>
                        </td>
                        <td class="text-end">{{ number_format($formateur['heures_requises'], 2) }}h</td>
                        <td class="text-end">{{ number_format($formateur['heures_affectees'], 2) }}h</td>
                        <td class="text-end">
                            @if($formateur['heures_manquantes'] > 0)
                                <strong class="text-danger">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    {{ number_format($formateur['heures_manquantes'], 2) }}h
                                </strong>
                            @else
                                <span class="text-success">
                                    <i class="bi bi-check-circle"></i> 0h
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light" style="position: sticky; bottom: 0;">
                    <tr>
                        <th colspan="3" class="text-end">TOTAUX:</th>
                        <th class="text-end">{{ number_format($formateursStats['totaux']['heures_requises'], 2) }}h</th>
                        <th class="text-end">{{ number_format($formateursStats['totaux']['heures_affectees'], 2) }}h</th>
                        <th class="text-end {{ $formateursStats['totaux']['heures_manquantes'] > 0 ? 'text-danger' : 'text-success' }}">
                            <strong>
                                @if($formateursStats['totaux']['heures_manquantes'] > 0)
                                    <i class="bi bi-exclamation-triangle"></i>
                                @else
                                    <i class="bi bi-check-circle"></i>
                                @endif
                                {{ number_format($formateursStats['totaux']['heures_manquantes'], 2) }}h
                            </strong>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @if($formateursStats['totaux']['heures_manquantes'] > 0)
    <div class="card-footer bg-light border-top">
        <small class="text-muted">
            <i class="bi bi-info-circle"></i>
            <strong>Légende :</strong> 
            <span class="badge bg-danger">Rouge</span> = Plus de 50h manquantes |
            <span class="badge bg-warning text-dark">Jaune</span> = Heures manquantes |
            Les formateurs sont triés par nombre d'heures manquantes (décroissant)
        </small>
    </div>
    @endif
</div>



    {{-- Graphiques --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Heures par semestre</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartSemestre" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Heures par mode de formation</h5>
                </div>
                <div class="card-body">
                    <canvas id="chartMode" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <i class="bi bi-funnel"></i> Filtres
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('administration.complexe.etablissements.show', $etablissement->code_efp) }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label small">Secteur</label>
                        <select name="secteur" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            @foreach($filterOptions['secteurs'] as $secteur)
                                <option value="{{ $secteur->nom_secteur }}" {{ $filters['secteur'] == $secteur->nom_secteur ? 'selected' : '' }}>
                                    {{ $secteur->nom_secteur }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small">Filière</label>
                        <select name="filiere" class="form-select form-select-sm">
                            <option value="">Toutes</option>
                            @foreach($filterOptions['filieres'] as $filiere)
                                <option value="{{ $filiere->code_filiere }}" {{ $filters['filiere'] == $filiere->code_filiere ? 'selected' : '' }}>
                                    {{ $filiere->nom_filiere }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small">Formateur</label>
                        <select name="formateur" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            @foreach($filterOptions['formateurs'] as $formateur)
                                <option value="{{ $formateur->mle }}" {{ $filters['formateur'] == $formateur->mle ? 'selected' : '' }}>
                                    {{ $formateur->nom_formateur }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small">Module</label>
                        <select name="module" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            @foreach($filterOptions['modules'] as $module)
                                <option value="{{ $module->code_module }}" {{ $filters['module'] == $module->code_module ? 'selected' : '' }}>
                                    {{ $module->nom_module }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small">Groupe</label>
                        <select name="groupe" class="form-select form-select-sm">
                            <option value="">Tous</option>
                            @foreach($filterOptions['groupes'] as $groupe)
                                <option value="{{ $groupe->groupe }}" {{ $filters['groupe'] == $groupe->groupe ? 'selected' : '' }}>
                                    {{ $groupe->groupe }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-search"></i> Filtrer
                        </button>
                    </div>
                </div>

                @if(array_filter($filters))
                    <div class="mt-2">
                        <a href="{{ route('administration.complexe.etablissements.show', $etablissement->code_efp) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Réinitialiser les filtres
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Tableau détaillé --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-table"></i> Détails des avancements</h5>
                <div>
                    <span class="badge bg-primary fs-6 me-2">
                        {{ $detailedData->total() }} enregistrements
                    </span>
                    <span class="badge bg-secondary fs-6">
                        Page {{ $detailedData->currentPage() }} / {{ $detailedData->lastPage() }}
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-wrapper" style="max-height: 600px; overflow: auto; position: relative;">
                <table class="table table-sm table-hover table-bordered mb-0">
                    <thead class="table-light" style="position: sticky; top: 0; z-index: 10; background-color: #f8f9fa;">
                        <tr>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Date MAJ</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Année</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Code EFP</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">EFP</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Niveau</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Secteur</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Code Filière</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Filière</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Type Formation</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Créneau</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Groupe</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Effectif</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Sous Groupe</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Statut SG</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Fusion</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Code Fusion</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Année Form</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Mode</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Code Module</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Module</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Régional</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Mle Présentiel</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Formateur Présentiel</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Mle Syn</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Formateur Syn</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MHP S1</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MHSYN S1</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MHASYN S1</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MH Total S1</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MHP S2</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MHSYN S2</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MHASYN S2</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MH Total S2</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MHP Total</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MHSYN Total</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MHASYN Total</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MH Total</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MH Aff. Prés</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MH Aff. Sync</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MH Aff. Global</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MH Réal. Prés</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MH Réal. Sync</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">MH Réal. Global</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Taux Réal. Prés</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Taux Réal. Syn</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Taux Réal. Global</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Moy Absence</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">NB CC</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Séance EFM</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Valid. EFM</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Classe Teams</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">Module PIE</th>
                            <th style="font-size: 0.75rem; padding: 0.5rem 0.4rem; white-space: nowrap;">EFP PIE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($detailedData as $row)
                        <tr>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->date_maj ? \Carbon\Carbon::parse($row->date_maj)->format('d/m/Y') : '-' }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->annee }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->code_efp }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem;">{{ $row->efp }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->niveau }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem;">{{ $row->secteur }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->code_filiere }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem;">{{ $row->filiere }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem;">{{ $row->type_formation }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->creneau }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->groupe }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->effectif_groupe }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->sous_groupe }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem;">{{ $row->statut_sous_groupe }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem;">{{ $row->fusion_groupe }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->code_fusion }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->annee_formation }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->mode }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->code_module }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem;">{{ $row->module }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->regional }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;" class="{{ !$row->mle_presentiel ? 'non-affecte' : '' }}">{{ $row->mle_presentiel ?? 'Non affecté' }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem;" class="{{ !$row->formateur_presentiel ? 'non-affecte' : '' }}">{{ $row->formateur_presentiel ?? 'Non affecté' }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;" class="{{ !$row->mle_syn ? 'non-affecte' : '' }}">{{ $row->mle_syn ?? 'Non affecté' }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem;" class="{{ !$row->formateur_syn ? 'non-affecte' : '' }}">{{ $row->formateur_syn ?? 'Non affecté' }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mhp_s1_drif }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mhsyn_s1_drif }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mhasyn_s1_drif }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mh_totale_s1_drif }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mhp_s2_drif }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mhsyn_s2_drif }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mhasyn_s2_drif }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mh_totale_s2_drif }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mhp_totale_drif }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mhsyn_totale_drif }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mhasyn_totale_drif }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mh_totale_drif }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mh_affectee_presentiel }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mh_affectee_sync }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mh_affectee_globale }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mh_realisee_presentiel }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mh_realisee_sync }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->mh_realisee_globale }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->taux_realisation_presentiel }}%</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->taux_realisation_syn }}%</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->taux_realisation_global }}%</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->moy_absence }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; text-align: right;">{{ $row->nb_cc }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->seance_efm }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem; white-space: nowrap;">{{ $row->validation_efm }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem;">{{ $row->classe_teams }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem;">{{ $row->module_pie }}</td>
                            <td style="font-size: 0.75rem; padding: 0.4rem;">{{ $row->efp_pie }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="53" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                <p class="text-muted mb-0">Aucune donnée disponible</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($detailedData->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <div class="text-muted small">
                            Affichage de <strong>{{ $detailedData->firstItem() }}</strong> à <strong>{{ $detailedData->lastItem() }}</strong> 
                            sur <strong>{{ $detailedData->total() }}</strong> enregistrements
                        </div>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="Pagination">
                            <ul class="pagination pagination-sm justify-content-md-end justify-content-center mb-0">
                                @if ($detailedData->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link"><i class="bi bi-chevron-left"></i> Précédent</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $detailedData->previousPageUrl() }}" rel="prev">
                                            <i class="bi bi-chevron-left"></i> Précédent
                                        </a>
                                    </li>
                                @endif

                                @php
                                    $start = max($detailedData->currentPage() - 2, 1);
                                    $end = min($detailedData->currentPage() + 2, $detailedData->lastPage());
                                @endphp

                                @if($start > 1)
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $detailedData->url(1) }}">1</a>
                                    </li>
                                    @if($start > 2)
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    @endif
                                @endif

                                @for ($i = $start; $i <= $end; $i++)
                                    @if ($i == $detailedData->currentPage())
                                        <li class="page-item active">
                                            <span class="page-link">{{ $i }}</span>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $detailedData->url($i) }}">{{ $i }}</a>
                                        </li>
                                    @endif
                                @endfor

                                @if($end < $detailedData->lastPage())
                                    @if($end < $detailedData->lastPage() - 1)
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    @endif
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $detailedData->url($detailedData->lastPage()) }}">{{ $detailedData->lastPage() }}</a>
                                    </li>
                                @endif

                                @if ($detailedData->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $detailedData->nextPageUrl() }}" rel="next">
                                            Suivant <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">Suivant <i class="bi bi-chevron-right"></i></span>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctxSemestre = document.getElementById('chartSemestre').getContext('2d');
    new Chart(ctxSemestre, {
        type: 'bar',
        data: {
            labels: ['Semestre 1', 'Semestre 2'],
            datasets: [
                {
                    label: 'Présentiel',
                    data: [
                        {{ $chartData['heures_par_semestre']['s1']['presentiel'] }},
                        {{ $chartData['heures_par_semestre']['s2']['presentiel'] }}
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                },
                {
                    label: 'Synchrone',
                    data: [
                        {{ $chartData['heures_par_semestre']['s1']['synchrone'] }},
                        {{ $chartData['heures_par_semestre']['s2']['synchrone'] }}
                    ],
                    backgroundColor: 'rgba(255, 206, 86, 0.7)',
                },
                {
                    label: 'Asynchrone',
                    data: [
                        {{ $chartData['heures_par_semestre']['s1']['asynchrone'] }},
                        {{ $chartData['heures_par_semestre']['s2']['asynchrone'] }}
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Heures'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    const ctxMode = document.getElementById('chartMode').getContext('2d');
    new Chart(ctxMode, {
        type: 'bar',
        data: {
            labels: ['Présentiel', 'Synchrone'],
            datasets: [
                {
                    label: 'Affectée',
                    data: [
                        {{ $chartData['taux_par_mode']['presentiel']['affectee'] }},
                        {{ $chartData['taux_par_mode']['synchrone']['affectee'] }}
                    ],
                    backgroundColor: 'rgba(255, 159, 64, 0.7)',
                },
                {
                    label: 'Réalisée',
                    data: [
                        {{ $chartData['taux_par_mode']['presentiel']['realisee'] }},
                        {{ $chartData['taux_par_mode']['synchrone']['realisee'] }}
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Heures'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
});
</script>
@endpush
@endsection