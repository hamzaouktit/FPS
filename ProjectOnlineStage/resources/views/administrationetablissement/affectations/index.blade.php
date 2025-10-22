@extends('layouts.app')

@section('title', 'Gestion des Affectations')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.dashboard') }}">Tableau de Bord</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Affectations</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="container-fluid">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="mb-1">Gestion des Affectations</h2>
                    <p class="text-muted mb-0">{{ $etablissement->nom_efp }}</p>
                </div>
                <a href="{{ route('administration.etablissement.affectations.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Nouvelle Affectation
                </a>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">Total Affectations</h6>
                            <h2 class="mb-0">{{ $affectations->count() }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-tasks"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">MH Totale DRIF</h6>
                            <h2 class="mb-0">{{ number_format($affectations->sum('mh_totale_drif'), 0) }}h</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-2">MH Affectée</h6>
                            <h2 class="mb-0">{{ number_format($affectations->sum('mh_affectee_globale'), 0) }}h</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-check-circle"></i>
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

    <!-- Carte principale avec filtres et tableau -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 pb-3">
            <form method="GET" action="{{ route('administration.etablissement.affectations.index') }}" id="filterForm">
                <div class="row g-3">
                    <!-- Secteur -->
                    <div class="col-md-2">
                        <select name="secteur_id" class="form-select">
                            <option value="">Tous les secteurs</option>
                            @foreach($secteurs as $secteur)
                                <option value="{{ $secteur->id }}" 
                                    {{ request('secteur_id') == $secteur->id ? 'selected' : '' }}>
                                    {{ Str::limit($secteur->nom_secteur, 25) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filière -->
                    <div class="col-md-2">
                        <select name="filiere_id" class="form-select">
                            <option value="">Toutes les filières</option>
                            @foreach($filieres as $filiere)
                                <option value="{{ $filiere->id }}" 
                                    {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                    {{ $filiere->code_filiere }}
                                </option>
                            @endforeach
                        </select>
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
                            <option value="">Formateur présentiel</option>
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->mle }}" 
                                    {{ request('formateur_presentiel') == $formateur->mle ? 'selected' : '' }}>
                                    {{ Str::limit($formateur->nom_complet, 20) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Formateur Synchrone -->
                    <div class="col-md-1">
                        <select name="formateur_syn" class="form-select">
                            <option value="">F. synchrone</option>
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->mle }}" 
                                    {{ request('formateur_syn') == $formateur->mle ? 'selected' : '' }}>
                                    {{ Str::limit($formateur->nom_complet, 15) }}
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
                            <a href="{{ route('administration.etablissement.affectations.index') }}" 
                               class="btn btn-outline-secondary" 
                               title="Réinitialiser">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 ps-4">Groupe</th>
                            <th class="border-0">Module</th>
                            <th class="border-0">Filière</th>
                            <th class="border-0">Formateur Présentiel</th>
                            <th class="border-0">Formateur Synchrone</th>
                            <th class="border-0 text-center">MH DRIF</th>
                            <th class="border-0 text-center">MH Affectée</th>
                            <th class="border-0 pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($affectations as $affectation)
                            <tr>
                                <td class="ps-4">
                                    <strong class="text-primary">{{ $affectation->groupe->code_groupe }}</strong>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $affectation->module->code_module }}</strong>
                                        <br>
                                        <small class="text-muted">{{ Str::limit($affectation->module->nom_module, 35) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info">
                                        {{ Str::limit($affectation->groupe->filiere->nom_filiere ?? 'N/A', 25) }}
                                    </span>
                                </td>
                                <td>
                                    @if($affectation->formateurPresentiel)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle avatar-success me-2">
                                                {{ strtoupper(substr($affectation->formateurPresentiel->nom_complet, 0, 2)) }}
                                            </div>
                                            <span class="small">{{ Str::limit($affectation->formateurPresentiel->nom_complet, 25) }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic small">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    @if($affectation->formateurSyn)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle avatar-warning me-2">
                                                {{ strtoupper(substr($affectation->formateurSyn->nom_complet, 0, 2)) }}
                                            </div>
                                            <span class="small">{{ Str::limit($affectation->formateurSyn->nom_complet, 25) }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic small">Non assigné</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary border border-primary">
                                        {{ number_format($affectation->mh_totale_drif, 2) }}h
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success border border-success">
                                        {{ number_format($affectation->mh_affectee_globale, 2) }}h
                                    </span>
                                </td>
                                <td class="pe-4">
                                    <div class="btn-group float-end">
                                        <a href="{{ route('administration.etablissement.affectations.show', $affectation->id) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('administration.etablissement.affectations.edit', $affectation->id) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('administration.etablissement.affectations.destroy', $affectation->id) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette affectation ?')">
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
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Aucune affectation trouvée</p>
                                    @if(request()->hasAny(['secteur_id', 'filiere_id', 'groupe_id', 'module_id', 'formateur_presentiel', 'formateur_syn']))
                                        <a href="{{ route('administration.etablissement.affectations.index') }}" 
                                           class="btn btn-sm btn-outline-primary mt-2">
                                            Réinitialiser les filtres
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($affectations->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="text-muted small">
                        Affichage de <strong>{{ $affectations->firstItem() }}</strong> à <strong>{{ $affectations->lastItem() }}</strong> 
                        sur <strong>{{ $affectations->total() }}</strong> affectations
                    </div>
                    <nav aria-label="Pagination">
                        {{ $affectations->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                    </nav>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.75rem;
}

.avatar-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.avatar-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.25rem;
}

.card {
    border-radius: 0.75rem;
    overflow: hidden;
}

.badge {
    font-weight: 500;
    padding: 0.35em 0.65em;
}

.form-select, .form-control {
    border-radius: 0.5rem;
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

.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.1) !important;
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

.text-primary {
    color: #0d6efd !important;
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
</style>
@endsection