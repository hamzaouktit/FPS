@extends('layouts.app')

@section('title', 'Dashboard Complexe')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><i class="fas fa-home me-1"></i><a href="{{ route('welcome') }}">Accueil</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard Complexe</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-0">
                    <i class="fas fa-building text-primary me-2"></i>
                    Tableau de Bord - Complexe
                </h1>
                <p class="text-muted mb-0">Gestion et supervision du complexe de formation</p>
            </div>
            <div class="text-end">
                <span class="badge bg-success fs-6">
                    <i class="fas fa-clock me-1"></i>
                    {{ date('d/m/Y H:i') }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Informations Utilisateur -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="avatar-container me-3">
                                <div class="avatar bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px;">
                                    <i class="fas fa-user-tie fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-1">Bienvenue, {{ $user->nom }}</h4>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-building me-2"></i>
                                    <strong>Complexe :</strong> {{ $complexe->nom ?? 'Non défini' }}
                                </p>
                                <span class="badge bg-primary">
                                    <i class="fas fa-user-tie me-1"></i>
                                    Directeur de Complexe
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="d-flex flex-column align-items-md-end">
                            <div class="mb-2">
                                <i class="fas fa-calendar-alt text-muted me-1"></i>
                                <small class="text-muted">Dernière connexion</small>
                            </div>
                            <div class="text-primary fw-bold">
                                {{ date('d/m/Y à H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques rapides -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                        <i class="fas fa-school fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">15</div>
                    <div class="text-muted">Établissements</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                        <i class="fas fa-users fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">2,453</div>
                    <div class="text-muted">Apprenants</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                        <i class="fas fa-chalkboard-teacher fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">186</div>
                    <div class="text-muted">Formateurs</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-3">
                        <i class="fas fa-graduation-cap fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">42</div>
                    <div class="text-muted">Formations</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0">
                    <i class="fas fa-bolt text-warning me-2"></i>
                    Actions Rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="#" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                            <i class="fas fa-user-tie fs-3 mb-2"></i>
                            <span>Gestion des Directeurs</span>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="#" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                            <i class="fas fa-school fs-3 mb-2"></i>
                            <span>Gestion des Établissements</span>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="#" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                            <i class="fas fa-chart-line fs-3 mb-2"></i>
                            <span>Rapports Globaux</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activité récente -->
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0">
                    <i class="fas fa-history text-info me-2"></i>
                    Activité Récente
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item d-flex mb-3">
                        <div class="timeline-icon bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Nouvel établissement ajouté</div>
                            <div class="text-muted small">ISTA Casablanca - Il y a 2 heures</div>
                        </div>
                    </div>
                    <div class="timeline-item d-flex mb-3">
                        <div class="timeline-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Directeur mis à jour</div>
                            <div class="text-muted small">M. Ahmed Benali - Il y a 4 heures</div>
                        </div>
                    </div>
                    <div class="timeline-item d-flex mb-3">
                        <div class="timeline-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Rapport mensuel généré</div>
                            <div class="text-muted small">Septembre 2025 - Il y a 1 jour</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie text-success me-2"></i>
                    Aperçu du Complexe
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">Taux d'occupation</span>
                        <span class="small text-success">85%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 85%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">Formations actives</span>
                        <span class="small text-primary">92%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: 92%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">Taux de réussite</span>
                        <span class="small text-info">78%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: 78%"></div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="#" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-chart-bar me-1"></i>
                        Voir les détails
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection