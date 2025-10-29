@extends('layouts.app')

@section('title', 'Gestion des Établissements')

@section('content')
<div class="container-fluid px-4 py-3">
    {{-- En-tête avec statistiques globales --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h1 class="h3 mb-1 fw-bold">Gestion des Établissements</h1>
                    <p class="text-muted mb-0">
                        <i class="bi bi-building-fill text-primary"></i> 
                        Complexe: <strong>{{ $complexe->nom }}</strong>
                    </p>
                </div>
                <div>
                    <a href="{{ route('administration.complexe.dashboard') }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>
                    <a href="{{ route('administration.complexe.etablissements.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Nouvel Établissement
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistiques globales du complexe --}}
    @php
        $totalFormations = $etablissements->sum('formations_count');
        $totalGroupes = $etablissements->sum(function($e) { return $e->stats['nb_groupes'] ?? 0; });
        $moyenneTaux = $etablissements->avg(function($e) { return $e->stats['taux_realisation'] ?? 0; });
        $totalFormateurs = $etablissements->sum(function($e) { return $e->stats['nb_formateurs'] ?? 0; });
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper bg-primary">
                                <i class="bi bi-building stat-icon"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 text-uppercase small fw-semibold">Établissements</h6>
                            <h2 class="mb-0 fw-bold stat-number">{{ $etablissements->total() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 px-4 pb-3">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Total dans le complexe
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper bg-info">
                                <i class="bi bi-book-half stat-icon"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 text-uppercase small fw-semibold">Formations</h6>
                            <h2 class="mb-0 fw-bold stat-number">{{ $totalFormations }}</h2>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 px-4 pb-3">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Toutes formations actives
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper bg-warning">
                                <i class="bi bi-people-fill stat-icon"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 text-uppercase small fw-semibold">Groupes Actifs</h6>
                            <h2 class="mb-0 fw-bold stat-number">{{ $totalGroupes }}</h2>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 px-4 pb-3">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Groupes en formation
                    </small>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 stat-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon-wrapper bg-success">
                                <i class="bi bi-graph-up-arrow stat-icon"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 text-uppercase small fw-semibold">Taux Moyen</h6>
                            <h2 class="mb-0 fw-bold stat-number">{{ number_format($moyenneTaux, 1) }}%</h2>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 px-4 pb-3">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i> Réalisation moyenne
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- Messages de succès/erreur --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Liste des établissements --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-list-ul text-primary me-2"></i> Liste des Établissements
                </h5>
                <span class="badge bg-primary rounded-pill fs-6 px-3 py-2">
                    {{ $etablissements->total() }} établissement(s)
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            @if($etablissements->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 10%;" class="ps-4">Code EFP</th>
                                <th style="width: 20%;">Établissement</th>
                                <th style="width: 15%;">Directeur</th>
                                <th style="width: 7%;" class="text-center">
                                    <i class="fas fa-book text-info"></i> Formations
                                </th>
                                <th style="width: 7%;" class="text-center">
                                    <i class="fas fa-users text-secondary"></i> Groupes
                                </th>
                                <th style="width: 9%;" class="text-center">
                                    <i class="fas fa-chalkboard-teacher text-warning"></i> Formateurs
                                </th>
                                <th style="width: 7%;" class="text-center">
                                    <i class="fas fa-clipboard-list text-dark"></i> Modules
                                </th>
                                <th style="width: 10%;" class="text-center">
                                    <i class="fas fa-chart-bar text-success"></i> Taux Réal.
                                </th>
                                <th style="width: 15%;" class="text-center pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($etablissements as $etablissement)
                            <tr class="table-row-hover">
                                <td class="ps-4">
                                    <span class="badge bg-secondary bg-gradient fs-6 px-3 py-2">
                                        {{ $etablissement->code_efp }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong class="text-dark mb-1">{{ $etablissement->nom_efp }}</strong>
                                        @if(isset($etablissement->stats['nb_secteurs']))
                                            <small class="text-muted">
                                                <i class="fas fa-layer-group"></i> 
                                                {{ $etablissement->stats['nb_secteurs'] }} secteur(s)
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($etablissement->user)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary me-2">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium text-dark">{{ $etablissement->user->nom }}</div>
                                                <small class="text-muted d-block text-truncate" style="max-width: 150px;">
                                                    {{ $etablissement->user->email }}
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">
                                            <i class="fas fa-user-times"></i> Non assigné
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info bg-gradient rounded-pill fs-6 px-3 py-2">
                                        {{ $etablissement->formations_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="badge bg-secondary bg-gradient rounded-pill fs-6 px-3 py-2 mb-1">
                                            {{ $etablissement->stats['nb_groupes'] ?? 0 }}
                                        </span>
                                        @if(isset($etablissement->stats['nb_stagiaires']) && $etablissement->stats['nb_stagiaires'] > 0)
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> {{ $etablissement->stats['nb_stagiaires'] }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if(isset($etablissement->stats['nb_formateurs']))
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="badge bg-warning bg-gradient rounded-pill text-dark fs-6 px-3 py-2 mb-1">
                                                {{ $etablissement->stats['nb_formateurs'] }}
                                            </span>
                                            @if(isset($etablissement->stats['nb_permanents']) && isset($etablissement->stats['nb_vacataires']))
                                                <small class="text-muted">
                                                    <i class="fas fa-user-check text-success"></i> {{ $etablissement->stats['nb_permanents'] }} / 
                                                    <i class="fas fa-user-clock text-info"></i> {{ $etablissement->stats['nb_vacataires'] }}
                                                </small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if(isset($etablissement->stats['nb_modules']))
                                        <span class="badge bg-dark bg-gradient rounded-pill fs-6 px-3 py-2">
                                            {{ $etablissement->stats['nb_modules'] }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @php
                                        $taux = $etablissement->stats['taux_realisation'] ?? 0;
                                        $class = $taux >= 80 ? 'success' : ($taux >= 50 ? 'warning' : 'danger');
                                        $icon = $taux >= 80 ? 'arrow-up' : ($taux >= 50 ? 'minus' : 'arrow-down');
                                    @endphp
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="badge bg-{{ $class }} bg-gradient rounded-pill fs-6 px-3 py-2 mb-1">
                                            <i class="fas fa-{{ $icon }}"></i>
                                            {{ number_format($taux, 1) }}%
                                        </span>
                                        @if(isset($etablissement->stats['mh_realisee']) && isset($etablissement->stats['mh_totale']))
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i>
                                                {{ number_format($etablissement->stats['mh_realisee'], 0) }}h / 
                                                {{ number_format($etablissement->stats['mh_totale'], 0) }}h
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="btn-group action-buttons" role="group">
                                        <a href="{{ route('administration.complexe.etablissements.show', $etablissement->code_efp) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           data-bs-toggle="tooltip"
                                           title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('administration.complexe.etablissements.edit', $etablissement->code_efp) }}" 
                                           class="btn btn-sm btn-outline-secondary" 
                                           data-bs-toggle="tooltip"
                                           title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal{{ $etablissement->code_efp }}"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    {{-- Modal de confirmation de suppression --}}
                                    <div class="modal fade" id="deleteModal{{ $etablissement->code_efp }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Confirmer la suppression
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="mb-3">Êtes-vous sûr de vouloir supprimer cet établissement ?</p>
                                                    <div class="alert alert-warning d-flex align-items-start mb-3">
                                                        <i class="fas fa-building fs-4 me-3 text-warning"></i>
                                                        <div>
                                                            <strong class="d-block">{{ $etablissement->nom_efp }}</strong>
                                                            <small class="text-muted">Code: {{ $etablissement->code_efp }}</small>
                                                        </div>
                                                    </div>
                                                    <div class="alert alert-danger d-flex align-items-start mb-0">
                                                        <i class="fas fa-exclamation-circle me-2"></i>
                                                        <small>Cette action est irréversible et supprimera toutes les données associées (formations, groupes, modules, etc.).</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fas fa-times"></i> Annuler
                                                    </button>
                                                    <form action="{{ route('administration.complexe.etablissements.destroy', $etablissement->code_efp) }}" 
                                                          method="POST" 
                                                          class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-trash"></i> Supprimer définitivement
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="empty-state-icon mb-4">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h5 class="text-muted mb-3">Aucun établissement trouvé</h5>
                    <p class="text-muted mb-4">Commencez par créer votre premier établissement</p>
                    <a href="{{ route('administration.complexe.etablissements.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle me-2"></i> Créer un établissement
                    </a>
                </div>
            @endif
        </div>

        @if($etablissements->hasPages())
            <div class="card-footer bg-white border-top py-3">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <div class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Affichage de <strong>{{ $etablissements->firstItem() }}</strong> à 
                            <strong>{{ $etablissements->lastItem() }}</strong> sur 
                            <strong>{{ $etablissements->total() }}</strong> établissement(s)
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end">
                            {{ $etablissements->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Statistiques Cards */
    .stat-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .stat-icon-box {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex !important;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .stat-icon-box svg {
        display: block !important;
    }
    
    .bg-primary-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .bg-info-gradient {
        background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);
    }
    
    .bg-warning-gradient {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .bg-success-gradient {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .stat-number {
        font-size: 2rem;
        color: #2c3e50;
    }
    
    /* Table Styles */
    .table-row-hover {
        transition: all 0.2s ease;
    }
    
    .table-row-hover:hover {
        background-color: rgba(0, 123, 255, 0.05) !important;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    /* Badges personnalisés */
    .badge {
        font-weight: 600;
        letter-spacing: 0.3px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .badge.bg-gradient {
        background-image: linear-gradient(180deg, rgba(255,255,255,.15), rgba(255,255,255,0));
    }
    
    /* Action Buttons */
    .action-buttons .btn {
        border-radius: 0;
        transition: all 0.2s ease;
        min-width: 40px;
    }
    
    .action-buttons .btn:first-child {
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px;
    }
    
    .action-buttons .btn:last-child {
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px;
    }
    
    .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        z-index: 2;
    }
    
    .action-buttons .btn-outline-primary:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
    }
    
    .action-buttons .btn-outline-secondary:hover {
        background: linear-gradient(135deg, #868e96 0%, #6c757d 100%);
        border-color: #868e96;
    }
    
    .action-buttons .btn-outline-danger:hover {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
        border-color: #ff6b6b;
    }
    
    /* Empty State */
    .empty-state-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .empty-state-icon i {
        font-size: 3rem;
        color: white;
    }
    
    /* Modal personnalisé */
    .modal-content {
        border-radius: 12px;
        overflow: hidden;
    }
    
    .modal-header.bg-danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%) !important;
    }
    
    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .stat-card {
        animation: fadeIn 0.5s ease-out;
    }
    
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    
    /* Responsive */
    @media (max-width: 768px) {
        .stat-number {
            font-size: 1.5rem;
        }
        
        .stat-icon-wrapper {
            width: 50px;
            height: 50px;
        }
        
        .stat-icon {
            font-size: 22px;
        }
    }
</style>

@push('scripts')
<script>
    // Initialiser les tooltips Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush
@endsection