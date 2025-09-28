@extends('layouts.app')

@section('title', 'Dashboard Établissement')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><i class="fas fa-home me-1"></i><a href="{{ route('welcome') }}">Accueil</a></li>
        <li class="breadcrumb-item active" aria-current="page">Dashboard Établissement</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h2 mb-0">
                    <i class="fas fa-university text-primary me-2"></i>
                    Tableau de Bord - Établissement
                </h1>
                <p class="text-muted mb-0">Gestion et supervision de l'établissement de formation</p>
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
                                <div class="avatar bg-success text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 60px; height: 60px;">
                                    <i class="fas fa-school fs-4"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-1">Bienvenue, {{ $user->nom }}</h4>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-university me-2"></i>
                                    <strong>Établissement :</strong> {{ $etablissement->nom_efp ?? 'Non défini' }}
                                </p>
                                <span class="badge bg-success">
                                    <i class="fas fa-school me-1"></i>
                                    Directeur d'Établissement
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
                            <div class="text-success fw-bold">
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
                        <i class="fas fa-graduation-cap fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">12</div>
                    <div class="text-muted">Formations</div>
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
                    <div class="fs-5 fw-bold">342</div>
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
                    <div class="fs-5 fw-bold">28</div>
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
                        <i class="fas fa-building fs-4"></i>
                    </div>
                </div>
                <div>
                    <div class="fs-5 fw-bold">8</div>
                    <div class="text-muted">Espaces</div>
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
                    <div class="col-lg-3 col-md-6">
                        <a href="#" class="btn btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                            <i class="fas fa-building fs-3 mb-2"></i>
                            <span>Espaces Pédagogiques</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="#" class="btn btn-outline-success w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                            <i class="fas fa-graduation-cap fs-3 mb-2"></i>
                            <span>Formations</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="#" class="btn btn-outline-info w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                            <i class="fas fa-users fs-3 mb-2"></i>
                            <span>Groupes</span>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <a href="#" class="btn btn-outline-warning w-100 h-100 d-flex flex-column justify-content-center align-items-center py-3">
                            <i class="fas fa-chalkboard-teacher fs-3 mb-2"></i>
                            <span>Formateurs</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gestion des modules et métiers -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0">
                    <i class="fas fa-book text-primary me-2"></i>
                    Gestion Pédagogique
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="#" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center py-3">
                            <i class="fas fa-book fs-4 mb-2"></i>
                            <span>Modules</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="#" class="btn btn-outline-secondary w-100 d-flex flex-column align-items-center py-3">
                            <i class="fas fa-calendar-alt fs-4 mb-2"></i>
                            <span>Années</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-light border-0">
                <h5 class="mb-0">
                    <i class="fas fa-tools text-warning me-2"></i>
                    Secteurs & Métiers
                </h5>
            </div>
            <div class="card-body">
                <a href="#" class="btn btn-outline-warning w-100 d-flex flex-column align-items-center py-3">
                    <i class="fas fa-tools fs-3 mb-2"></i>
                    <span>Gestion des Métiers</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Activité récente et informations -->
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
                            <div class="fw-bold">Nouveau groupe créé</div>
                            <div class="text-muted small">TDI-205 - Technicien en développement informatique - Il y a 1 heure</div>
                        </div>
                    </div>
                    <div class="timeline-item d-flex mb-3">
                        <div class="timeline-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Nouveau formateur ajouté</div>
                            <div class="text-muted small">Mme. Sara Alaoui - Informatique - Il y a 3 heures</div>
                        </div>
                    </div>
                    <div class="timeline-item d-flex mb-3">
                        <div class="timeline-icon bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Module mis à jour</div>
                            <div class="text-muted small">Programmation orientée objet - Il y a 5 heures</div>
                        </div>
                    </div>
                    <div class="timeline-item d-flex mb-3">
                        <div class="timeline-icon bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <div class="fw-bold">Espace pédagogique réservé</div>
                            <div class="text-muted small">Laboratoire Informatique A - Il y a 1 jour</div>
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
                    Aperçu de l'Établissement
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">Occupation des espaces</span>
                        <span class="small text-success">78%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: 78%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">Formations en cours</span>
                        <span class="small text-primary">100%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: 100%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">Présence des formateurs</span>
                        <span class="small text-warning">89%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: 89%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small">Assiduité apprenants</span>
                        <span class="small text-info">82%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: 82%"></div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a href="#" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-chart-bar me-1"></i>
                        Voir les détails
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection