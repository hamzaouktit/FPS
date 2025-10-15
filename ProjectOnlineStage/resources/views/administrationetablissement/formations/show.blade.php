@extends('layouts.app')

@section('title', 'Détails Formation #' . $formation->id)

@section('content')
<div class="container-fluid px-4">

    <!-- Header avec Navigation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('administration.etablissement.dashboard') }}" class="text-decoration-none">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('administration.etablissement.formations.index') }}" class="text-decoration-none">
                                Formations
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            Formation #{{ $formation->id }}
                        </li>
                    </ol>
                </nav>
                <div class="btn-group">
                    <a href="{{ route('administration.etablissement.formations.edit', $formation->id) }}" 
                       class="btn btn-warning btn-lg rounded-pill px-4 me-2 shadow-sm">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                    <a href="{{ route('administration.etablissement.formations.index') }}" 
                       class="btn btn-secondary btn-lg rounded-pill px-4 shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Header Principal de la Formation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="formation-header bg-gradient-primary rounded-4 p-5 shadow-lg position-relative overflow-hidden">
                <div class="position-absolute top-0 end-0 w-50 h-100 opacity-10">
                    <i class="fas fa-graduation-cap fa-10x text-white"></i>
                </div>
                <div class="row align-items-center position-relative">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="formation-avatar bg-white rounded-3 p-3 me-4 shadow">
                                <i class="fas fa-graduation-cap fa-3x text-primary"></i>
                            </div>
                            <div class="text-white">
                                <h1 class="display-4 fw-bold mb-2">Formation #{{ $formation->id }}</h1>
                                <div class="d-flex flex-wrap gap-3 align-items-center">
                                    <span class="badge bg-white text-primary fs-6 px-4 py-2 rounded-pill shadow-sm">
                                        <i class="fas fa-tag me-2"></i>{{ $formation->type }}
                                    </span>
                                    <span class="badge bg-light text-dark fs-6 px-4 py-2 rounded-pill shadow-sm">
                                        <i class="fas fa-{{ $formation->mode == 'Résidentiel' ? 'school' : 'exchange-alt' }} me-2"></i>{{ $formation->mode }}
                                    </span>
                                    <span class="badge bg-light text-info fs-6 px-4 py-2 rounded-pill shadow-sm">
                                        <i class="fas fa-clock me-2"></i>{{ $formation->creneau }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="text-white">
                            <div class="mb-2">
                                <i class="fas fa-calendar-plus me-2"></i>
                                <strong>Créée le:</strong> {{ $formation->created_at->format('d/m/Y') }}
                            </div>
                            <div>
                                <i class="fas fa-calendar-check me-2"></i>
                                <strong>Modifiée le:</strong> {{ $formation->updated_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de Statistiques Principales -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card bg-primary text-white shadow-hover border-0 rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h2 class="card-title display-5 fw-bold mb-1">{{ $stats['total_groupes'] }}</h2>
                            <p class="card-text mb-0 opacity-75">Groupes Total</p>
                            <small class="opacity-75">
                                <i class="fas fa-info-circle me-1"></i>
                                Tous les groupes associés
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-white bg-opacity-20 rounded-circle p-3">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card bg-success text-white shadow-hover border-0 rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h2 class="card-title display-5 fw-bold mb-1">{{ $stats['effectif_total'] }}</h2>
                            <p class="card-text mb-0 opacity-75">Effectif Total</p>
                            <small class="opacity-75">
                                <i class="fas fa-user-graduate me-1"></i>
                                Nombre total d'apprenants
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-white bg-opacity-20 rounded-circle p-3">
                                <i class="fas fa-user-graduate fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card bg-info text-white shadow-hover border-0 rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h2 class="card-title display-5 fw-bold mb-1">{{ $stats['groupes_actifs'] }}</h2>
                            <p class="card-text mb-0 opacity-75">Groupes Actifs</p>
                            <small class="opacity-75">
                                <i class="fas fa-check-circle me-1"></i>
                                Groupes en activité
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-white bg-opacity-20 rounded-circle p-3">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card bg-warning text-white shadow-hover border-0 rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h2 class="card-title display-5 fw-bold mb-1">{{ $stats['groupes_inactifs'] }}</h2>
                            <p class="card-text mb-0 opacity-75">Groupes Inactifs</p>
                            <small class="opacity-75">
                                <i class="fas fa-pause-circle me-1"></i>
                                Groupes non actifs
                            </small>
                        </div>
                        <div class="flex-shrink-0">
                            <div class="stat-icon bg-white bg-opacity-20 rounded-circle p-3">
                                <i class="fas fa-pause-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations Détaillées de la Formation -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 h-100 rounded-3">
                <div class="card-header bg-white border-0 py-4">
                    <h4 class="mb-0 text-primary">
                        <i class="fas fa-info-circle me-2"></i>Informations de la Formation
                    </h4>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="info-icon bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-fingerprint text-primary"></i>
                                </div>
                                <div>
                                    <span class="fw-semibold text-dark">ID Formation</span>
                                </div>
                            </div>
                            <span class="badge bg-primary rounded-pill fs-6 px-3 py-2">#{{ $formation->id }}</span>
                        </div>

                        <div class="info-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="info-icon bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-tag text-success"></i>
                                </div>
                                <div>
                                    <span class="fw-semibold text-dark">Type de Formation</span>
                                </div>
                            </div>
                            <span class="badge bg-success rounded-pill fs-6 px-3 py-2">{{ $formation->type }}</span>
                        </div>

                        <div class="info-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="info-icon bg-dark bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-{{ $formation->mode == 'Résidentiel' ? 'school' : 'exchange-alt' }} text-dark"></i>
                                </div>
                                <div>
                                    <span class="fw-semibold text-dark">Mode</span>
                                </div>
                            </div>
                            <span class="badge bg-dark rounded-pill fs-6 px-3 py-2">{{ $formation->mode }}</span>
                        </div>

                        <div class="info-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="info-icon bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-clock text-info"></i>
                                </div>
                                <div>
                                    <span class="fw-semibold text-dark">Créneau</span>
                                </div>
                            </div>
                            <span class="badge bg-info rounded-pill fs-6 px-3 py-2">{{ $formation->creneau }}</span>
                        </div>

                        <div class="info-item d-flex justify-content-between align-items-center py-3 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="info-icon bg-secondary bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-building text-secondary"></i>
                                </div>
                                <div>
                                    <span class="fw-semibold text-dark">Établissement</span>
                                </div>
                            </div>
                            <span class="text-end">
                                <div class="fw-semibold">{{ $etablissement->nom_efp }}</div>
                                <small class="text-muted">{{ $etablissement->code_efp }}</small>
                            </span>
                        </div>

                        <div class="info-item d-flex justify-content-between align-items-center py-3">
                            <div class="d-flex align-items-center">
                                <div class="info-icon bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="fas fa-calendar text-warning"></i>
                                </div>
                                <div>
                                    <span class="fw-semibold text-dark">Date de création</span>
                                </div>
                            </div>
                            <span class="text-muted text-end">
                                <div>{{ $formation->created_at->format('d/m/Y') }}</div>
                                <small>{{ $formation->created_at->format('H:i') }}</small>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques Avancées -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 h-100 rounded-3">
                <div class="card-header bg-white border-0 py-4">
                    <h4 class="mb-0 text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques Détaillées
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Répartition Statut Groupes -->
                        <div class="col-md-6 mb-4">
                            <h6 class="fw-semibold text-muted mb-3">
                                <i class="fas fa-chart-pie me-2"></i>Répartition par Statut
                            </h6>
                            <div class="progress-stats">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Groupes Actifs</span>
                                    <span class="fw-semibold">{{ $stats['groupes_actifs'] }} ({{ $stats['total_groupes'] > 0 ? round(($stats['groupes_actifs'] / $stats['total_groupes']) * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="progress mb-3" style="height: 12px;">
                                    <div class="progress-bar bg-success" 
                                         style="width: {{ $stats['total_groupes'] > 0 ? ($stats['groupes_actifs'] / $stats['total_groupes']) * 100 : 0 }}%"
                                         role="progressbar">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Groupes Inactifs</span>
                                    <span class="fw-semibold">{{ $stats['groupes_inactifs'] }} ({{ $stats['total_groupes'] > 0 ? round(($stats['groupes_inactifs'] / $stats['total_groupes']) * 100, 1) : 0 }}%)</span>
                                </div>
                                <div class="progress mb-3" style="height: 12px;">
                                    <div class="progress-bar bg-warning" 
                                         style="width: {{ $stats['total_groupes'] > 0 ? ($stats['groupes_inactifs'] / $stats['total_groupes']) * 100 : 0 }}%"
                                         role="progressbar">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Moyennes et Calculs -->
                        <div class="col-md-6 mb-4">
                            <h6 class="fw-semibold text-muted mb-3">
                                <i class="fas fa-calculator me-2"></i>Calculs Moyennes
                            </h6>
                            <div class="average-stats">
                                <div class="avg-item bg-light rounded-3 p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Moyenne Effectif/Groupe</span>
                                        <span class="fw-bold text-primary fs-5">
                                            {{ $stats['total_groupes'] > 0 ? round($stats['effectif_total'] / $stats['total_groupes'], 1) : 0 }}
                                        </span>
                                    </div>
                                    <small class="text-muted">Apprenants par groupe en moyenne</small>
                                </div>
                                <div class="avg-item bg-light rounded-3 p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Taux d'Activité</span>
                                        <span class="fw-bold text-success fs-5">
                                            {{ $stats['total_groupes'] > 0 ? round(($stats['groupes_actifs'] / $stats['total_groupes']) * 100, 1) : 0 }}%
                                        </span>
                                    </div>
                                    <small class="text-muted">Pourcentage de groupes actifs</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Indicateurs de Performance -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h6 class="fw-semibold text-muted mb-3">
                                <i class="fas fa-tachometer-alt me-2"></i>Indicateurs de Performance
                            </h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="performance-indicator bg-primary bg-opacity-10 rounded-3 p-3 text-center">
                                        <div class="indicator-value text-primary fw-bold fs-4 mb-1">
                                            {{ $stats['total_groupes'] }}
                                        </div>
                                        <div class="indicator-label text-muted small">
                                            Groupes Total
                                        </div>
                                        <div class="indicator-progress mt-2">
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-primary" style="width: 100%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="performance-indicator bg-success bg-opacity-10 rounded-3 p-3 text-center">
                                        <div class="indicator-value text-success fw-bold fs-4 mb-1">
                                            {{ $stats['effectif_total'] }}
                                        </div>
                                        <div class="indicator-label text-muted small">
                                            Apprenants Total
                                        </div>
                                        <div class="indicator-progress mt-2">
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-success" style="width: 100%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="performance-indicator bg-info bg-opacity-10 rounded-3 p-3 text-center">
                                        <div class="indicator-value text-info fw-bold fs-4 mb-1">
                                            {{ $stats['total_groupes'] > 0 ? round($stats['effectif_total'] / $stats['total_groupes'], 1) : 0 }}
                                        </div>
                                        <div class="indicator-label text-muted small">
                                            Densité Moyenne
                                        </div>
                                        <div class="indicator-progress mt-2">
                                            <div class="progress" style="height: 4px;">
                                                <div class="progress-bar bg-info" style="width: 100%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste Détailée des Groupes -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-0 py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 text-primary">
                            <i class="fas fa-users me-2"></i>
                            Groupes Associés à cette Formation
                            <span class="badge bg-primary ms-2 fs-6">{{ $groupes->count() }}</span>
                        </h4>
                        <div class="export-actions">
                            <button class="btn btn-outline-success btn-sm rounded-pill px-3 me-2">
                                <i class="fas fa-file-excel me-1"></i>Excel
                            </button>
                            <button class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                <i class="fas fa-file-pdf me-1"></i>PDF
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($groupes->isEmpty())
                        <div class="text-center py-5">
                            <div class="empty-state">
                                <i class="fas fa-users-slash fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted mb-3">Aucun groupe associé</h4>
                                <p class="text-muted mb-4">Cette formation n'a pas encore de groupes assignés</p>
                                <a href="#" class="btn btn-primary rounded-pill px-4">
                                    <i class="fas fa-plus me-2"></i>Créer un Groupe
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4 py-3 border-0">Code Groupe</th>
                                        <th class="py-3 border-0">Filière & Secteur</th>
                                        <th class="py-3 border-0">Niveau</th>
                                        <th class="py-3 border-0">Effectif</th>
                                        <th class="py-3 border-0">Statut</th>
                                        <th class="py-3 border-0">Année</th>
                                        <th class="py-3 border-0">Année Formation</th>
                                        <th class="text-end pe-4 py-3 border-0">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groupes as $groupe)
                                        <tr class="groupe-row">
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="groupe-avatar bg-primary bg-opacity-10 rounded-2 p-2 me-3">
                                                        <i class="fas fa-users text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-primary">{{ $groupe->code }}</div>
                                                        <small class="text-muted">ID: {{ $groupe->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-semibold">{{ $groupe->filiere->nom ?? 'N/A' }}</div>
                                                    <small class="text-muted">
                                                        <i class="fas fa-tag me-1"></i>
                                                        {{ $groupe->filiere->secteur->nom ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                                    {{ $groupe->filiere->niveau->nom ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="effectif-display">
                                                        <span class="fw-bold fs-5 text-{{ $groupe->effectif > 0 ? 'success' : 'secondary' }}">
                                                            {{ $groupe->effectif }}
                                                        </span>
                                                        <small class="text-muted d-block">apprenants</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $groupe->statut == 'Actif' ? 'success' : 'secondary' }} rounded-pill px-3 py-2">
                                                    <i class="fas fa-{{ $groupe->statut == 'Actif' ? 'check' : 'pause' }}-circle me-1"></i>
                                                    {{ $groupe->statut }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-dark rounded-pill px-3 py-2">
                                                    {{ $groupe->annee }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-info rounded-pill px-3 py-2">
                                                    {{ $groupe->annee_formation }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-outline-primary btn-sm rounded-pill px-3 me-2">
                                                        <i class="fas fa-eye"></i>
                                                        <span class="d-none d-md-inline"><a href="{{ route('administration.etablissement.groupes.show', $groupe) }}" class="">voir<i class=""></i></a>
</span>
                                                    </button>
                                                    <button class="btn btn-outline-warning btn-sm rounded-pill px-3">
                                                        <i class="fas fa-edit"></i>
                                                        <span class="d-none d-md-inline"><a href="{{ route('administration.etablissement.groupes.edit', $groupe) }}" class=""><i class="">Modifier</i></a></span>
                                                    </button>
                                                </div>
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
</div>

<style>
.formation-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.formation-avatar {
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-card {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    overflow: hidden;
    position: relative;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.stat-card:hover::before {
    left: 100%;
}

.stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 12px 35px rgba(0,0,0,0.2) !important;
}

.stat-icon {
    transition: all 0.3s ease;
}

.stat-card:hover .stat-icon {
    transform: scale(1.1) rotate(5deg);
}

.shadow-hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.info-item {
    transition: all 0.3s ease;
    border-radius: 10px;
    margin: 2px 0;
}

.info-item:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.groupe-row {
    transition: all 0.3s ease;
}

.groupe-row:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    transform: scale(1.005);
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.progress {
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    border-radius: 10px;
    transition: width 1.5s ease-in-out;
}

.performance-indicator {
    transition: all 0.3s ease;
}

.performance-indicator:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.breadcrumb {
    background: transparent;
    padding: 0;
}

.breadcrumb-item.active {
    color: #6c757d;
}

.table th {
    font-weight: 600;
    color: #495057;
    font-size: 0.85em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e9ecef;
}

.groupe-avatar {
    transition: all 0.3s ease;
}

.groupe-row:hover .groupe-avatar {
    transform: scale(1.1);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.groupe-row:hover .groupe-avatar i {
    color: white !important;
}

.empty-state {
    padding: 3rem 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des cartes de statistiques
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px) scale(0.95)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0) scale(1)';
        }, index * 200);
    });

    // Animation des barres de progression
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.transition = 'width 1.5s ease-in-out';
            bar.style.width = width;
        }, 500);
    });

    // Animation des lignes de groupes
    const groupeRows = document.querySelectorAll('.groupe-row');
    groupeRows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-30px)';
        
        setTimeout(() => {
            row.style.transition = 'all 0.5s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, index * 100 + 800);
    });

    // Effet de scintillement sur le header
    const header = document.querySelector('.formation-header');
    setInterval(() => {
        header.style.background = `linear-gradient(135deg, #667eea 0%, #764ba2 ${Math.random() * 100}%)`;
    }, 5000);

    // Tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection