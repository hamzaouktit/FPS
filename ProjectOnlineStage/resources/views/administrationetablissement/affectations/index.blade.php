@extends('layouts.app')

@section('title', 'Gestion des Affectations')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.dashboard') }}" class="text-decoration-none">
                    <i class="fas fa-home me-1"></i>Tableau de Bord
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Affectations</li>
        </ol>
    </nav>
@endsection

@section('content')
<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    
    .card-header {
        border: none;
        padding: 1.25rem 1.5rem;
        font-weight: 600;
        background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-dark, #0056b3) 100%);
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        transition: transform 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }
    
    .stats-card h3 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stats-card p {
        margin-bottom: 0;
        opacity: 0.95;
        font-size: 0.9rem;
    }
    
    .filter-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table thead th {
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
        background: #343a40;
        color: #fff;
        white-space: nowrap;
    }
    
    .table tbody td {
        vertical-align: middle;
        padding: 0.875rem 0.75rem;
        border-color: #dee2e6;
        font-size: 0.875rem;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .badge {
        padding: 0.35em 0.65em;
        font-weight: 500;
        border-radius: 6px;
        font-size: 0.75rem;
    }
    
    .btn {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: nowrap;
    }
    
    .form-select, .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .alert {
        border: none;
        border-radius: 8px;
        border-left: 4px solid;
    }

    /* === PAGINATION STYLE BOOTSTRAP 5 - THÈME OFPPT === */
    
    /* Card footer pagination */
    .card-footer {
        background: white !important;
        border-top: 2px solid #e9ecef !important;
        padding: 1.25rem 1.5rem !important;
    }

    .card-footer .text-muted {
        color: #6c757d !important;
        font-size: 0.875rem;
    }

    .card-footer .text-muted strong {
        color: var(--ofppt-green, #2E8B57);
        font-weight: 600;
    }

    /* Pagination Bootstrap 5 */
    .pagination {
        margin-bottom: 0 !important;
        gap: 0.25rem;
        display: flex;
        flex-wrap: wrap;
    }

    .pagination .page-item {
        margin: 0;
    }

    .pagination .page-link {
        border-radius: 0.5rem !important;
        border: 1px solid #dee2e6 !important;
        color: var(--ofppt-blue, #1E5F99) !important;
        background-color: white !important;
        padding: 0.5rem 0.75rem !important;
        font-weight: 500 !important;
        transition: all 0.2s ease !important;
        margin: 0 2px;
        position: relative;
        overflow: hidden;
    }

    /* Effet de survol */
    .pagination .page-link:hover {
        background-color: #f8f9fa !important;
        border-color: var(--ofppt-green, #2E8B57) !important;
        color: var(--ofppt-green, #2E8B57) !important;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(46, 139, 87, 0.15);
    }

    /* Page active */
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, var(--ofppt-green, #2E8B57) 0%, #228B54 100%) !important;
        border-color: var(--ofppt-green, #2E8B57) !important;
        color: white !important;
        box-shadow: 0 3px 8px rgba(46, 139, 87, 0.35);
        font-weight: 600 !important;
        transform: scale(1.05);
    }

    .pagination .page-item.active .page-link:hover {
        background: linear-gradient(135deg, #228B54 0%, var(--ofppt-green, #2E8B57) 100%) !important;
        transform: scale(1.05) translateY(-2px);
    }

    /* Page désactivée */
    .pagination .page-item.disabled .page-link {
        background-color: #f8f9fa !important;
        border-color: #dee2e6 !important;
        color: #6c757d !important;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .pagination .page-item.disabled .page-link:hover {
        transform: none;
        box-shadow: none;
    }

    /* Focus */
    .pagination .page-link:focus {
        box-shadow: 0 0 0 0.2rem rgba(30, 95, 153, 0.25) !important;
        border-color: var(--ofppt-blue, #1E5F99) !important;
        outline: none;
        z-index: 2;
    }

    /* Icônes de navigation SVG */
    .pagination .page-link svg {
        width: 1rem;
        height: 1rem;
        vertical-align: middle;
        fill: currentColor;
    }

    /* Premier et dernier bouton (Previous/Next) */
    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        font-weight: 600 !important;
        padding: 0.5rem 1rem !important;
    }

    .pagination .page-item:first-child .page-link:hover,
    .pagination .page-item:last-child .page-link:hover {
        background: linear-gradient(135deg, var(--ofppt-blue, #1E5F99) 0%, var(--ofppt-dark-blue, #1a4a75) 100%) !important;
        color: white !important;
        border-color: var(--ofppt-blue, #1E5F99) !important;
    }

    /* Animation d'entrée */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card-footer {
        animation: fadeInUp 0.4s ease-out;
    }

    /* Effet pulsation subtile sur page active */
    @keyframes subtlePulse {
        0%, 100% {
            box-shadow: 0 3px 8px rgba(46, 139, 87, 0.35);
        }
        50% {
            box-shadow: 0 4px 12px rgba(46, 139, 87, 0.45);
        }
    }

    .pagination .page-item.active .page-link {
        animation: subtlePulse 2.5s ease-in-out infinite;
    }

    /* === RESPONSIVE OPTIMISÉ === */
    @media (max-width: 992px) {
        .card-footer .d-flex {
            flex-direction: column;
            gap: 1rem !important;
            text-align: center;
        }

        .card-footer .text-muted {
            width: 100%;
        }

        .pagination {
            justify-content: center;
        }
    }

    @media (max-width: 768px) {
        .stats-card {
            margin-bottom: 1rem;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .table-responsive {
            font-size: 0.8rem;
        }
        
        .filter-section {
            padding: 1rem;
        }

        .card-footer {
            padding: 1rem !important;
        }

        .pagination .page-link {
            padding: 0.4rem 0.6rem !important;
            font-size: 0.875rem !important;
            margin: 0 1px;
        }
        
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            padding: 0.4rem 0.75rem !important;
        }
        
        .pagination {
            gap: 0.15rem;
        }

        .card-footer .text-muted {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 576px) {
        /* Masquer certains numéros de page sur très petits écrans */
        .pagination .page-item:not(.active):not(:first-child):not(:last-child):not(.disabled) {
            display: none;
        }
        
        /* Afficher la page active et celles adjacentes */
        .pagination .page-item.active,
        .pagination .page-item.active + .page-item:not(:last-child):not(.disabled),
        .pagination .page-item:has(+ .page-item.active):not(:first-child):not(.disabled) {
            display: flex !important;
        }

        .card-footer .text-muted {
            font-size: 0.75rem;
        }

        .pagination .page-link {
            padding: 0.35rem 0.5rem !important;
            font-size: 0.8rem !important;
        }

        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            padding: 0.35rem 0.65rem !important;
        }

        .card-footer {
            padding: 0.75rem !important;
        }
    }

    /* Préférence pour animations réduites */
    @media (prefers-reduced-motion: reduce) {
        .pagination .page-link,
        .card-footer,
        .pagination .page-item.active .page-link {
            animation: none !important;
            transition: none !important;
        }
        
        .pagination .page-link:hover {
            transform: none !important;
        }
    }

    /* Mode sombre (optionnel) */
    @media (prefers-color-scheme: dark) {
        .card-footer {
            background: #1a1a1a !important;
            border-top-color: #333 !important;
        }

        .card-footer .text-muted {
            color: #adb5bd !important;
        }

        .pagination .page-link {
            background-color: #2d2d2d !important;
            border-color: #444 !important;
            color: #0dcaf0 !important;
        }

        .pagination .page-link:hover {
            background-color: #3d3d3d !important;
            border-color: var(--ofppt-green, #2E8B57) !important;
        }
    }
</style>

<!-- Statistiques -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h3>{{ $stats['total'] }}</h3>
            <p><i class="fas fa-list me-1"></i>Total Affectations</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <h3>{{ number_format($stats['mh_totale_drif'], 0) }}h</h3>
            <p><i class="fas fa-clock me-1"></i>MH Totale DRIF</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <h3>{{ number_format($stats['mh_affectee'], 0) }}h</h3>
            <p><i class="fas fa-calendar-check me-1"></i>MH Affectée</p>
        </div>
    </div>
</div>

<!-- Section Filtres -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>Filtres de Recherche
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('administration.etablissement.affectations.index') }}" class="filter-section">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-industry me-1"></i>Secteur</label>
                    <select name="secteur_id" class="form-select">
                        <option value="">Tous les secteurs</option>
                        @foreach($secteurs as $secteur)
                            <option value="{{ $secteur->id }}" {{ request('secteur_id') == $secteur->id ? 'selected' : '' }}>
                                {{ $secteur->nom_secteur }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-graduation-cap me-1"></i>Filière</label>
                    <select name="filiere_id" class="form-select">
                        <option value="">Toutes les filières</option>
                        @foreach($filieres as $filiere)
                            <option value="{{ $filiere->id }}" {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                {{ $filiere->nom_filiere }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-users me-1"></i>Groupe</label>
                    <select name="groupe_id" class="form-select">
                        <option value="">Tous les groupes</option>
                        @foreach($groupes as $groupe)
                            <option value="{{ $groupe->id }}" {{ request('groupe_id') == $groupe->id ? 'selected' : '' }}>
                                {{ $groupe->code_groupe }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label"><i class="fas fa-book me-1"></i>Module</label>
                    <select name="module_id" class="form-select">
                        <option value="">Tous les modules</option>
                        @foreach($modules as $module)
                            <option value="{{ $module->id }}" {{ request('module_id') == $module->id ? 'selected' : '' }}>
                                {{ $module->code_module }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-chalkboard-teacher me-1"></i>Formateur Présentiel</label>
                    <select name="formateur_presentiel" class="form-select">
                        <option value="">Tous les formateurs</option>
                        @foreach($formateurs as $formateur)
                            <option value="{{ $formateur->mle }}" {{ request('formateur_presentiel') == $formateur->mle ? 'selected' : '' }}>
                                {{ $formateur->nom_complet }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-video me-1"></i>Formateur Synchrone</label>
                    <select name="formateur_syn" class="form-select">
                        <option value="">Tous les formateurs</option>
                        @foreach($formateurs as $formateur)
                            <option value="{{ $formateur->mle }}" {{ request('formateur_syn') == $formateur->mle ? 'selected' : '' }}>
                                {{ $formateur->nom_complet }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-object-group me-1"></i>Fusion Groupe</label>
                    <input type="text" name="fusion_groupe" class="form-control" 
                           placeholder="Ex: Groupe A + B" 
                           value="{{ request('fusion_groupe') }}">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Rechercher
                    </button>
                    <a href="{{ route('administration.etablissement.affectations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-2"></i>Réinitialiser
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Liste des Affectations -->
<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>Liste des Affectations
        </h5>
        <a href="{{ route('administration.etablissement.affectations.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus me-2"></i>Nouvelle Affectation
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Groupe</th>
                        <th>Module</th>
                        <th>Fusion</th>
                        <th>Formateur P.</th>
                        <th>Formateur S.</th>
                        <th>MH Demandée</th>
                        <th>MH Affectée</th>
                        <th>Taux Réal.</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($affectations as $affectation)
                        <tr>
                            <td><strong>{{ $loop->iteration + ($affectations->currentPage() - 1) * $affectations->perPage() }}</strong></td>
                            <td>
                                <strong class="text-primary">{{ $affectation->groupe->code_groupe }}</strong><br>
                                <small class="text-muted">{{ $affectation->groupe->filiere->nom_filiere ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <strong>{{ $affectation->module->code_module }}</strong><br>
                                <small class="text-muted">{{ Str::limit($affectation->module->nom_module, 30) }}</small>
                            </td>
                            <td>
                                @if($affectation->fusion_groupe)
                                    <span class="badge bg-info">
                                        <i class="fas fa-object-group me-1"></i>{{ $affectation->fusion_groupe }}
                                    </span>
                                    @if($affectation->code_fusion)
                                        <br><small class="text-muted">Code: {{ $affectation->code_fusion }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($affectation->formateurPresentiel)
                                    <span class="badge bg-primary">{{ $affectation->formateurPresentiel->mle }}</span><br>
                                    <small>{{ Str::limit($affectation->formateurPresentiel->nom_complet, 20) }}</small>
                                @else
                                    <span class="text-muted">Non assigné</span>
                                @endif
                            </td>
                            <td>
                                @if($affectation->formateurSyn)
                                    <span class="badge bg-warning text-dark">{{ $affectation->formateurSyn->mle }}</span><br>
                                    <small>{{ Str::limit($affectation->formateurSyn->nom_complet, 20) }}</small>
                                @else
                                    <span class="text-muted">Non assigné</span>
                                @endif
                            </td>
                            <td><strong class="text-success">{{ $affectation->mh_totale_drif }}h</strong></td>
                            <td><strong class="text-info">{{ $affectation->mh_affectee_globale }}h</strong></td>
                            <td>
                                @php
                                    $taux = $affectation->avancement->taux_realisation_globale ?? 0;
                                    $badgeClass = $taux >= 80 ? 'success' : ($taux >= 50 ? 'warning' : 'danger');
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $taux }}%</span>
                            </td>
                            <td>
                                <div class="action-buttons justify-content-center">
                                    <a href="{{ route('administration.etablissement.affectations.show', $affectation->id) }}" 
                                       class="btn btn-sm btn-info" title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('administration.etablissement.affectations.edit', $affectation->id) }}" 
                                       class="btn btn-sm btn-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('administration.etablissement.affectations.destroy', $affectation->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette affectation ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">Aucune affectation trouvée</p>
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
@endsection