@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="mb-1">Gestion des Avancements</h2>
                    <p class="text-muted mb-0">{{ $etablissement->nom_efp }}</p>
                </div>
                <a href="{{ route('administration.etablissement.avancements.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Nouvel Avancement
                </a>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Total Avancements</h6>
                            <h2 class="mb-0">{{ $stats['total'] }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Taux Moyen</h6>
                            <h2 class="mb-0">{{ $stats['taux_moyen'] }}%</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-percentage"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">MH Total Réalisée</h6>
                            <h2 class="mb-0">{{ $stats['mh_total'] }}h</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">En Retard</h6>
                            <h2 class="mb-0">{{ $stats['en_retard'] }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Carte principale avec filtres -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 pb-3">
            <form method="GET" action="{{ route('administration.etablissement.avancements.index') }}" id="filterForm">
                <div class="row g-3">
                    <!-- Recherche -->
                    <div class="col-md-3">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control border-start-0 ps-0" 
                                   placeholder="Groupe ou Module..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Groupe -->
                    <div class="col-md-2">
                        <select name="groupe_id" class="form-select">
                            <option value="">Tous les groupes</option>
                            @foreach($groupes as $groupe)
                                <option value="{{ $groupe->id }}" 
                                    {{ request('groupe_id') == $groupe->id ? 'selected' : '' }}>
                                    {{ $groupe->code_groupe }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Module -->
                    <div class="col-md-2">
                        <select name="module_id" class="form-select">
                            <option value="">Tous les modules</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->id }}" 
                                    {{ request('module_id') == $module->id ? 'selected' : '' }}>
                                    {{ $module->code_module }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Formateur Présentiel -->
                    <div class="col-md-2">
                        <select name="formateur_presentiel" class="form-select">
                            <option value="">Formateur Présentiel</option>
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->mle }}" 
                                    {{ request('formateur_presentiel') == $formateur->mle ? 'selected' : '' }}>
                                    {{ Str::limit($formateur->nom_complet, 20) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Formateur Synchrone -->
                    <div class="col-md-2">
                        <select name="formateur_syn" class="form-select">
                            <option value="">Formateur Synchrone</option>
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->mle }}" 
                                    {{ request('formateur_syn') == $formateur->mle ? 'selected' : '' }}>
                                    {{ Str::limit($formateur->nom_complet, 20) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="col-md-1">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary" title="Filtrer">
                                <i class="fas fa-filter"></i>
                            </button>
                            <a href="{{ route('administration.etablissement.avancements.index') }}" 
                               class="btn btn-outline-secondary" 
                               title="Réinitialiser">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Ligne 2 : Filtres avancés -->
                <div class="row g-3 mt-2">
                    <!-- Taux Min -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Taux Min</span>
                            <input type="number" 
                                   name="taux_min" 
                                   class="form-control" 
                                   min="0" 
                                   max="100"
                                   placeholder="0"
                                   value="{{ request('taux_min') }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <!-- Taux Max -->
                    <div class="col-md-2">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Taux Max</span>
                            <input type="number" 
                                   name="taux_max" 
                                   class="form-control" 
                                   min="0" 
                                   max="100"
                                   placeholder="100"
                                   value="{{ request('taux_max') }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>

                    <!-- Date Début -->
                    <div class="col-md-2">
                        <input type="date" 
                               name="date_debut" 
                               class="form-control form-control-sm" 
                               value="{{ request('date_debut') }}">
                    </div>

                    <!-- Date Fin -->
                    <div class="col-md-2">
                        <input type="date" 
                               name="date_fin" 
                               class="form-control form-control-sm" 
                               value="{{ request('date_fin') }}">
                    </div>

                    <!-- Tri -->
                    <div class="col-md-3">
                        <select name="sort_by" class="form-select form-select-sm">
                            <option value="date_maj" {{ request('sort_by') == 'date_maj' ? 'selected' : '' }}>
                                Trier par Date MAJ
                            </option>
                            <option value="taux_realisation_globale" {{ request('sort_by') == 'taux_realisation_globale' ? 'selected' : '' }}>
                                Trier par Taux Global
                            </option>
                            <option value="mh_realisee_globale" {{ request('sort_by') == 'mh_realisee_globale' ? 'selected' : '' }}>
                                Trier par MH Réalisée
                            </option>
                        </select>
                    </div>

                    <!-- Ordre -->
                    <div class="col-md-1">
                        <select name="sort_order" class="form-select form-select-sm">
                            <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>↑</option>
                            <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>↓</option>
                        </select>
                    </div>
                </div>

                <!-- Ligne 3 : Sélecteur per page -->
                <div class="row g-3 mt-1">
                    <div class="col-md-12 d-flex align-items-center">
                        <label class="text-muted small me-2 mb-0">Afficher :</label>
                        <select name="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                            <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                        <span class="text-muted small ms-2">groupes-modules par page</span>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if($avancements->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Aucun avancement trouvé</p>
                    @if(request()->hasAny(['search', 'groupe_id', 'module_id', 'formateur_presentiel', 'formateur_syn', 'taux_min', 'taux_max', 'date_debut', 'date_fin']))
                        <a href="{{ route('administration.etablissement.avancements.index') }}" 
                           class="btn btn-sm btn-outline-primary mt-2">
                            Réinitialiser les filtres
                        </a>
                    @else
                        <a href="{{ route('administration.etablissement.avancements.create') }}" 
                           class="btn btn-primary mt-2">
                            <i class="fas fa-plus"></i> Créer le premier avancement
                        </a>
                    @endif
                </div>
            @else
                @foreach($avancements as $groupeModule => $avancementsGroupe)
                    @php
                        $firstAvancement = $avancementsGroupe->first();
                        $groupe = $firstAvancement->affectation->groupe;
                        $module = $firstAvancement->affectation->module;
                    @endphp
                    
                    <div class="avancement-group-card mb-3 mx-3">
                        <div class="group-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <span class="badge bg-primary-subtle text-primary border border-primary me-2">
                                        <i class="fas fa-users"></i> {{ $groupe->code_groupe }}
                                    </span>
                                    <span class="badge bg-success-subtle text-success border border-success">
                                        <i class="fas fa-book"></i> {{ $module->nom_module }}
                                    </span>
                                </h5>
                                <span class="badge bg-secondary">
                                    {{ $avancementsGroupe->count() }} avancement(s)
                                </span>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="15%">Formateur Présentiel</th>
                                        <th width="15%">Formateur Synchrone</th>
                                        <th width="8%" class="text-center">MH Présentiel</th>
                                        <th width="8%" class="text-center">MH Sync</th>
                                        <th width="8%" class="text-center">MH Globale</th>
                                        <th width="8%" class="text-center">Taux Prés.</th>
                                        <th width="8%" class="text-center">Taux Syn</th>
                                        <th width="8%" class="text-center">Taux Global</th>
                                        <th width="10%" class="text-center">Date MAJ</th>
                                        <th width="12%" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($avancementsGroupe as $avancement)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2">
                                                        {{ $avancement->affectation->formateurPresentiel ? strtoupper(substr($avancement->affectation->formateurPresentiel->nom_complet, 0, 2)) : 'NA' }}
                                                    </div>
                                                    <div>
                                                        <small class="text-muted d-block">{{ $avancement->affectation->formateurPresentiel->mle ?? 'N/A' }}</small>
                                                        <strong class="small">{{ Str::limit($avancement->affectation->formateurPresentiel->nom_complet ?? 'N/A', 20) }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                                        {{ $avancement->affectation->formateurSyn ? strtoupper(substr($avancement->affectation->formateurSyn->nom_complet, 0, 2)) : 'NA' }}
                                                    </div>
                                                    <div>
                                                        <small class="text-muted d-block">{{ $avancement->affectation->formateurSyn->mle ?? 'N/A' }}</small>
                                                        <strong class="small">{{ Str::limit($avancement->affectation->formateurSyn->nom_complet ?? 'N/A', 20) }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info-subtle text-info border border-info">
                                                    {{ $avancement->mh_realisee_presentiel }}h
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning-subtle text-warning border border-warning">
                                                    {{ $avancement->mh_realisee_sync }}h
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success-subtle text-success border border-success">
                                                    {{ $avancement->mh_realisee_globale }}h
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $tauxPresentiel = $avancement->taux_realisation_presentiel;
                                                    if ($tauxPresentiel >= 80) {
                                                        $badgeClass = 'bg-success text-white';
                                                    } elseif ($tauxPresentiel >= 50) {
                                                        $badgeClass = 'bg-warning text-dark';
                                                    } else {
                                                        $badgeClass = 'bg-danger text-white';
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $tauxPresentiel }}%</span>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $tauxSyn = $avancement->taux_realisation_syn;
                                                    if ($tauxSyn >= 80) {
                                                        $badgeClass = 'bg-success text-white';
                                                    } elseif ($tauxSyn >= 50) {
                                                        $badgeClass = 'bg-warning text-dark';
                                                    } else {
                                                        $badgeClass = 'bg-danger text-white';
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $tauxSyn }}%</span>
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $tauxGlobal = $avancement->taux_realisation_globale;
                                                    if ($tauxGlobal >= 80) {
                                                        $badgeClass = 'bg-success text-white';
                                                    } elseif ($tauxGlobal >= 50) {
                                                        $badgeClass = 'bg-warning text-dark';
                                                    } else {
                                                        $badgeClass = 'bg-danger text-white';
                                                    }
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ $tauxGlobal }}%</span>
                                            </td>
                                            <td class="text-center">
                                                <small class="text-muted">{{ $avancement->date_maj->format('d/m/Y') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group float-end">
                                                    <a href="{{ route('administration.etablissement.avancements.show', $avancement) }}" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('administration.etablissement.avancements.edit', $avancement) }}" 
                                                       class="btn btn-sm btn-outline-warning" 
                                                       title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('administration.etablissement.avancements.destroy', $avancement) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avancement ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Supprimer">
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
                        
                        <div class="group-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Code Module: <strong>{{ $module->code_module }}</strong> | 
                                        Régional: <strong>{{ $module->regional == 'O' ? 'Oui' : 'Non' }}</strong>
                                        @if($module->module_pie == 'O')
                                            | <span class="badge bg-purple-subtle text-purple border border-purple">PIE</span>
                                        @endif
                                    </small>
                                </div>
                                <div class="col-md-6 text-end">
                                    <small class="text-muted">
                                        Effectif: <strong>{{ $groupe->effectif_groupe }}</strong> | 
                                        Statut: 
                                        <span class="badge {{ $groupe->statut == 'Actif' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $groupe->statut }}
                                        </span>
                                        | Année: <strong>{{ $groupe->annee_formation }}</strong>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        @if(!$avancements->isEmpty())
            <div class="card-footer bg-white border-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="alert alert-secondary mb-0">
                            <i class="fas fa-chart-bar"></i>
                            <strong>Résumé:</strong> 
                            {{ $stats['total'] }} avancement(s) total | 
                            Taux moyen global: <strong>{{ $stats['taux_moyen'] }}%</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        @if($avancements->hasPages())
                            <div class="d-flex justify-content-end align-items-center">
                                <div class="text-muted small me-3">
                                    Affichage de <strong>{{ $avancements->firstItem() }}</strong> à 
                                    <strong>{{ $avancements->lastItem() }}</strong> 
                                    sur <strong>{{ $avancements->total() }}</strong> groupe(s)-module(s)
                                </div>
                                <nav aria-label="Pagination">
                                    {{ $avancements->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                                </nav>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
/* Cartes de statistiques */
.card {
    border-radius: 0.75rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Avatar Circle */
.avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.75rem;
    flex-shrink: 0;
}

/* Groupe Card Styling */
.avancement-group-card {
    border: 1px solid #e9ecef;
    border-radius: 0.75rem;
    overflow: hidden;
    background: white;
    transition: box-shadow 0.2s ease;
}

.avancement-group-card:hover {
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
}

.group-header {
    background: linear-gradient(to right, #f8f9fa, #ffffff);
    padding: 1rem 1.25rem;
    border-bottom: 2px solid #e9ecef;
}

.group-footer {
    background: #f8f9fa;
    padding: 0.75rem 1.25rem;
    border-top: 1px solid #e9ecef;
}

/* Table Styles */
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #495057;
    vertical-align: middle;
}

.table td {
    vertical-align: middle;
    font-size: 0.875rem;
}

/* Badge Styles */
.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
    font-size: 0.75rem;
}

.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.1) !important;
}

.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.bg-warning-subtle {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.bg-info-subtle {
    background-color: rgba(13, 202, 240, 0.1) !important;
}

.bg-danger-subtle {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.bg-purple-subtle {
    background-color: rgba(102, 16, 242, 0.1) !important;
}

.text-primary {
    color: #0d6efd !important;
}

.text-success {
    color: #198754 !important;
}

.text-warning {
    color: #ffc107 !important;
}

.text-info {
    color: #0dcaf0 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.text-purple {
    color: #6610f2 !important;
}

.border-primary {
    border-color: #0d6efd !important;
}

.border-success {
    border-color: #198754 !important;
}

.border-warning {
    border-color: #ffc107 !important;
}

.border-info {
    border-color: #0dcaf0 !important;
}

.border-danger {
    border-color: #dc3545 !important;
}

.border-purple {
    border-color: #6610f2 !important;
}

/* Form Styles */
.form-select, .form-control {
    border-radius: 0.5rem;
    border: 1px solid #dee2e6;
    font-size: 0.875rem;
}

.form-select:focus, .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.input-group-text {
    border-radius: 0.5rem;
    font-size: 0.875rem;
}

/* Button Group */
.btn-group .btn {
    border-radius: 0.375rem !important;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.25rem;
}

/* Button Styles */
.btn {
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5568d3 0%, #63408b 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Alert Styles */
.alert {
    border-radius: 0.5rem;
    border: none;
}

.alert-success {
    background-color: rgba(25, 135, 84, 0.1);
    color: #198754;
    border-left: 4px solid #198754;
}

.alert-danger {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border-left: 4px solid #dc3545;
}

.alert-secondary {
    background-color: #f8f9fa;
    color: #495057;
    border-left: 4px solid #6c757d;
}

/* Styles de pagination personnalisés */
.pagination {
    margin-bottom: 0;
    gap: 0.25rem;
}

.pagination .page-item {
    margin: 0;
}

.pagination .page-link {
    border-radius: 0.5rem !important;
    border: 1px solid #dee2e6;
    color: #495057;
    padding: 0.5rem 0.75rem;
    font-weight: 500;
    transition: all 0.2s ease;
    margin: 0 2px;
}

.pagination .page-link:hover {
    background-color: #f8f9fa;
    border-color: #667eea;
    color: #667eea;
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
    box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
}

.pagination .page-item.disabled .page-link {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #6c757d;
}

.pagination .page-link:focus {
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Style pour les icônes de navigation */
.pagination .page-link svg {
    width: 1rem;
    height: 1rem;
    vertical-align: middle;
}

/* Responsive Design */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.75rem;
    }
    
    .avatar-circle {
        width: 30px;
        height: 30px;
        font-size: 0.65rem;
    }
    
    .btn-sm {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }
}

/* Animation pour les cartes */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.avancement-group-card {
    animation: fadeInUp 0.3s ease-out;
}

/* Style pour les icônes */
.fas, .far {
    transition: transform 0.2s ease;
}

.btn:hover .fas,
.btn:hover .far {
    transform: scale(1.1);
}
</style>
@endsection