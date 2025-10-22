@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="mb-1">Gestion des Formateurs</h2>
                    <p class="text-muted mb-0">{{ $etablissement->nom_efp }}</p>
                </div>
                <a href="{{ route('administration.etablissement.formateurs.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i> Nouveau Formateur
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
                            <h6 class="text-white-50 mb-2">Total Formateurs</h6>
                            <h2 class="mb-0">{{ $stats['total'] }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-users"></i>
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
                            <h6 class="text-white-50 mb-2">Permanents</h6>
                            <h2 class="mb-0">{{ $stats['permanents'] }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-user-tie"></i>
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
                            <h6 class="text-white-50 mb-2">Vacataires</h6>
                            <h2 class="mb-0">{{ $stats['vacataires'] }}</h2>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-user-clock"></i>
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

    <!-- Carte principale avec filtres et tableau -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-4 pb-3">
            <form method="GET" action="{{ route('administration.etablissement.formateurs.index') }}" id="filterForm">
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
                                   placeholder="MLE ou Nom..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="">Tous les types</option>
                            <option value="permanent" {{ request('type') == 'permanent' ? 'selected' : '' }}>
                                Permanent
                            </option>
                            <option value="vacataire" {{ request('type') == 'vacataire' ? 'selected' : '' }}>
                                Vacataire
                            </option>
                        </select>
                    </div>

                    <!-- Secteur -->
                    <div class="col-md-3">
                        <select name="secteur_id" class="form-select">
                            <option value="">Tous les secteurs</option>
                            @foreach($secteurs as $secteur)
                                <option value="{{ $secteur->id }}" 
                                    {{ request('secteur_id') == $secteur->id ? 'selected' : '' }}>
                                    {{ $secteur->nom_secteur }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Module -->
                    <div class="col-md-3">
                        <select name="module_id" class="form-select">
                            <option value="">Tous les modules</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->id }}" 
                                    {{ request('module_id') == $module->id ? 'selected' : '' }}>
                                    {{ $module->code_module }} - {{ Str::limit($module->nom_module, 30) }}
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
                            <a href="{{ route('administration.etablissement.formateurs.index') }}" 
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
                            <th class="border-0 ps-4">
                                <a href="{{ route('administration.etablissement.formateurs.index', array_merge(request()->all(), ['sort_by' => 'mle', 'sort_order' => request('sort_by') == 'mle' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                   class="text-decoration-none text-dark">
                                    MLE
                                    @if(request('sort_by') == 'mle')
                                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="border-0">
                                <a href="{{ route('administration.etablissement.formateurs.index', array_merge(request()->all(), ['sort_by' => 'nom_complet', 'sort_order' => request('sort_by') == 'nom_complet' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                   class="text-decoration-none text-dark">
                                    Nom Complet
                                    @if(request('sort_by') == 'nom_complet' || !request('sort_by'))
                                        <i class="fas fa-sort-{{ request('sort_order') == 'desc' ? 'down' : 'up' }} ms-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="border-0">
                                <a href="{{ route('administration.etablissement.formateurs.index', array_merge(request()->all(), ['sort_by' => 'type', 'sort_order' => request('sort_by') == 'type' && request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                   class="text-decoration-none text-dark">
                                    Type
                                    @if(request('sort_by') == 'type')
                                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="border-0">Secteurs</th>
                            <th class="border-0">Modules</th>
                            <th class="border-0 pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($formateurs as $formateur)
                            <tr>
                                <td class="ps-4">
                                    <strong class="text-primary">{{ $formateur->mle }}</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2">
                                            {{ strtoupper(substr($formateur->nom_complet, 0, 2)) }}
                                        </div>
                                        {{ $formateur->nom_complet }}
                                    </div>
                                </td>
                                <td>
                                    @if($formateur->type === 'permanent')
                                        <span class="badge bg-success-subtle text-success border border-success">
                                            <i class="fas fa-user-tie me-1"></i>Permanent
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning">
                                            <i class="fas fa-user-clock me-1"></i>Vacataire
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($formateur->secteurs as $secteur)
                                            <span class="badge bg-info-subtle text-info border border-info">
                                                {{ Str::limit($secteur->nom_secteur, 20) }}
                                            </span>
                                        @empty
                                            <span class="text-muted fst-italic">Aucun</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        @forelse($formateur->modules->take(3) as $module)
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary">
                                                {{ $module->code_module }}
                                            </span>
                                        @empty
                                            <span class="text-muted fst-italic">Aucun</span>
                                        @endforelse
                                        @if($formateur->modules->count() > 3)
                                            <span class="badge bg-light text-dark border">
                                                +{{ $formateur->modules->count() - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="pe-4">
                                    <div class="btn-group float-end">
                                        <a href="{{ route('administration.etablissement.formateurs.show', $formateur) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('administration.etablissement.formateurs.edit', $formateur) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('administration.etablissement.formateurs.destroy', $formateur) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ?')">
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
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">Aucun formateur trouvé</p>
                                    @if(request()->hasAny(['search', 'type', 'secteur_id', 'module_id']))
                                        <a href="{{ route('administration.etablissement.formateurs.index') }}" 
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

        @if($formateurs->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="text-muted small">
                        Affichage de <strong>{{ $formateurs->firstItem() }}</strong> à <strong>{{ $formateurs->lastItem() }}</strong> 
                        sur <strong>{{ $formateurs->total() }}</strong> formateurs
                    </div>
                    <nav aria-label="Pagination">
                        {{ $formateurs->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
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
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.75rem;
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

.bg-secondary-subtle {
    background-color: rgba(108, 117, 125, 0.1) !important;
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

.text-secondary {
    color: #6c757d !important;
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