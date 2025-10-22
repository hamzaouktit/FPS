@extends('layouts.app')

@section('title', 'Gestion des Modules')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.dashboard') }}">Tableau de Bord</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Modules</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-cubes me-2"></i>Liste des Modules - {{ $etablissement->nom_efp }}
        </h5>
        <a href="{{ route('administration.etablissement.modules.create') }}" class="btn btn-light">
            <i class="fas fa-plus me-2"></i>Nouveau Module
        </a>
    </div>
    
    <div class="card-body">
        {{-- Formulaire de filtrage --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Filtres de recherche
                    <button type="button" class="btn btn-sm btn-outline-secondary float-end" 
                            data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </h6>
            </div>
            <div class="collapse show" id="filterCollapse">
                <div class="card-body">
                    <form method="GET" action="{{ route('administration.etablissement.modules.index') }}" id="filterForm">
                        <div class="row g-3">
                            {{-- Code Module --}}
                            <div class="col-md-3">
                                <label class="form-label">Code Module</label>
                                <input type="text" 
                                       name="code_module" 
                                       class="form-control" 
                                       placeholder="Ex: M01"
                                       value="{{ request('code_module') }}">
                            </div>

                            {{-- Nom Module --}}
                            <div class="col-md-3">
                                <label class="form-label">Nom Module</label>
                                <input type="text" 
                                       name="nom_module" 
                                       class="form-control" 
                                       placeholder="Rechercher..."
                                       value="{{ request('nom_module') }}">
                            </div>

                            {{-- Régional --}}
                            <div class="col-md-2">
                                <label class="form-label">Régional</label>
                                <select name="regional" class="form-select">
                                    <option value="">Tous</option>
                                    <option value="O" {{ request('regional') == 'O' ? 'selected' : '' }}>Oui</option>
                                    <option value="N" {{ request('regional') == 'N' ? 'selected' : '' }}>Non</option>
                                </select>
                            </div>

                            {{-- Module PIE --}}
                            <div class="col-md-2">
                                <label class="form-label">Module PIE</label>
                                <select name="module_pie" class="form-select">
                                    <option value="">Tous</option>
                                    <option value="O" {{ request('module_pie') == 'O' ? 'selected' : '' }}>Oui</option>
                                    <option value="N" {{ request('module_pie') == 'N' ? 'selected' : '' }}>Non</option>
                                </select>
                            </div>

                            {{-- Filière --}}
                            <div class="col-md-2">
                                <label class="form-label">Filière</label>
                                <select name="filiere_id" class="form-select">
                                    <option value="">Toutes</option>
                                    @foreach($filieres as $filiere)
                                        <option value="{{ $filiere->id }}" 
                                                {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                            {{ $filiere->code_filiere }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Boutons d'action --}}
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Rechercher
                                </button>
                                <a href="{{ route('administration.etablissement.modules.index') }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-redo me-2"></i>Réinitialiser
                                </a>
                                
                                {{-- Nombre par page --}}
                                <div class="float-end">
                                    <select name="per_page" class="form-select form-select-sm d-inline-block w-auto" 
                                            onchange="document.getElementById('filterForm').submit()">
                                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 par page</option>
                                        <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 par page</option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 par page</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 par page</option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 par page</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Résultats --}}
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $modules->total() }}</strong> module(s) trouvé(s)
            </div>
            <div>
                Affichage de {{ $modules->firstItem() ?? 0 }} à {{ $modules->lastItem() ?? 0 }} sur {{ $modules->total() }}
            </div>
        </div>

        @if($modules->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>
                                <a href="{{ route('administration.etablissement.modules.index', array_merge(request()->all(), ['sort_by' => 'code_module', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                   class="text-white text-decoration-none">
                                    Code Module
                                    @if(request('sort_by') == 'code_module')
                                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('administration.etablissement.modules.index', array_merge(request()->all(), ['sort_by' => 'nom_module', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc'])) }}" 
                                   class="text-white text-decoration-none">
                                    Nom Module
                                    @if(request('sort_by') == 'nom_module')
                                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Régional</th>
                            <th>Module PIE</th>
                            <th>Filières</th>
                            <th>Formateurs</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $module)
                            <tr>
                                <td><strong>{{ $module->code_module }}</strong></td>
                                <td>{{ $module->nom_module }}</td>
                                <td>
                                    <span class="badge bg-{{ $module->regional == 'O' ? 'success' : 'secondary' }}">
                                        {{ $module->regional == 'O' ? 'Oui' : 'Non' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $module->module_pie == 'O' ? 'warning' : 'secondary' }}">
                                        {{ $module->module_pie == 'O' ? 'Oui' : 'Non' }}
                                    </span>
                                </td>
                                <td>
                                    @if($module->filieres->count() > 0)
                                        <span class="badge bg-info" 
                                              title="{{ $module->filieres->pluck('code_filiere')->implode(', ') }}">
                                            {{ $module->filieres->count() }} filière(s)
                                        </span>
                                    @else
                                        <span class="text-muted">Aucune</span>
                                    @endif
                                </td>
                                <td>
                                    @if($module->formateurs->count() > 0)
                                        <span class="badge bg-primary">
                                            {{ $module->formateurs->count() }} formateur(s)
                                        </span>
                                    @else
                                        <span class="text-muted">Aucun</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('administration.etablissement.modules.show', $module->id) }}" 
                                           class="btn btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('administration.etablissement.modules.edit', $module->id) }}" 
                                           class="btn btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('administration.etablissement.modules.destroy', $module->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce module ?')"
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

            {{-- Pagination --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $modules->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                @if(request()->hasAny(['code_module', 'nom_module', 'regional', 'module_pie', 'filiere_id']))
                    Aucun module ne correspond à vos critères de recherche.
                    <a href="{{ route('administration.etablissement.modules.index') }}" class="alert-link">
                        Réinitialiser les filtres
                    </a>
                @else
                    Aucun module n'a été créé pour votre établissement.
                    <a href="{{ route('administration.etablissement.modules.create') }}" class="alert-link">
                        Créer le premier module
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .table th a {
        display: block;
    }
    .table th a:hover {
        opacity: 0.8;
    }

    /* Pagination personnalisée Bootstrap 5 */
    .pagination {
        margin-bottom: 0;
    }

    .page-link {
        color: #0d6efd;
        border-radius: 0.375rem;
        margin: 0 2px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .page-link:hover {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }

    .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.3);
    }

    .page-item.disabled .page-link {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #adb5bd;
    }

    .page-item:first-child .page-link {
        border-radius: 0.375rem;
    }

    .page-item:last-child .page-link {
        border-radius: 0.375rem;
    }
</style>
@endpush
@endsection