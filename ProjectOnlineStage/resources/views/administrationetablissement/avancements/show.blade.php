@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec navigation -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-chart-line text-primary"></i> Détails de l'Avancement
                    </h2>
                    <p class="text-muted mb-0">
                        <i class="fas fa-calendar-alt"></i> Dernière mise à jour : 
                        <strong>{{ $avancement->date_maj->format('d/m/Y') }}</strong>
                    </p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('administration.etablissement.avancements.index') }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                    <a href="{{ route('administration.etablissement.avancements.edit', $avancement) }}" 
                       class="btn btn-warning text-white">
                        <i class="fas fa-edit me-1"></i> Modifier
                    </a>
                    <form action="{{ route('administration.etablissement.avancements.destroy', $avancement) }}" 
                          method="POST" 
                          class="d-inline"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet avancement ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @php
        $affectation = $avancement->affectation;
        $groupe = $affectation->groupe;
        $module = $affectation->module;
    @endphp

    <!-- Section Informations Groupe/Module & Avancement -->
    <div class="row mb-4">
        <!-- Informations Groupe/Module -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm modern-card">
                <div class="card-header bg-gradient-purple text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations Groupe/Module
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless detail-table">
                        <tbody>
                            <tr>
                                <th><i class="fas fa-users text-primary me-2"></i>Groupe</th>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2">
                                        {{ $groupe->code_groupe }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-book text-success me-2"></i>Module</th>
                                <td><strong>{{ $module->nom_module }}</strong></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-barcode text-info me-2"></i>Code Module</th>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info px-3 py-2">
                                        {{ $module->code_module }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-chalkboard-teacher text-purple me-2"></i>Formateur Présentiel</th>
                                <td>
                                    @if($affectation->formateurPresentiel)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-small me-2">
                                                {{ strtoupper(substr($affectation->formateurPresentiel->nom_complet, 0, 2)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $affectation->formateurPresentiel->nom_complet }}</strong>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-video text-danger me-2"></i>Formateur Synchrone</th>
                                <td>
                                    @if($affectation->formateurSyn)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-small me-2" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                                {{ strtoupper(substr($affectation->formateurSyn->nom_complet, 0, 2)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $affectation->formateurSyn->nom_complet }}</strong>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted fst-italic">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Informations Avancement -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm modern-card">
                <div class="card-header bg-gradient-blue text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Informations Avancement
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless detail-table">
                        <tbody>
                            <tr>
                                <th><i class="fas fa-calendar-check text-success me-2"></i>Date MAJ</th>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $avancement->date_maj->format('d/m/Y') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fab fa-microsoft text-primary me-2"></i>Classe Teams</th>
                                <td>
                                    @if($avancement->classe_teams)
                                        <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2">
                                            <i class="fab fa-microsoft me-1"></i>
                                            {{ $avancement->classe_teams }}
                                        </span>
                                    @else
                                        <span class="text-muted fst-italic">N/A</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-file-alt text-warning me-2"></i>Séance EFM</th>
                                <td>
                                    <span class="badge {{ $avancement->seance_efm == 'Oui' ? 'bg-success' : 'bg-secondary' }} px-3 py-2">
                                        <i class="fas {{ $avancement->seance_efm == 'Oui' ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                        {{ $avancement->seance_efm }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-check-double text-info me-2"></i>Validation EFM</th>
                                <td>
                                    <span class="badge {{ $avancement->validation_efm == 'oui' ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                                        <i class="fas {{ $avancement->validation_efm == 'oui' ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                        {{ ucfirst($avancement->validation_efm) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Détails des Réalisations (Tableau) -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm modern-card">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Détails des Réalisations
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped modern-table mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">
                                        <i class="fas fa-chalkboard-teacher me-1"></i>
                                        MH Réalisée<br>Présentiel
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-video me-1"></i>
                                        MH Réalisée<br>Sync
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-globe me-1"></i>
                                        MH Réalisée<br>Globale
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-percentage me-1"></i>
                                        Taux Réalisation<br>Présentiel
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-percentage me-1"></i>
                                        Taux Réalisation<br>Syn
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-chart-line me-1"></i>
                                        Taux Réalisation<br>(P & SYN)
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-user-slash me-1"></i>
                                        Moy<br>Absence
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-clipboard-check me-1"></i>
                                        NB<br>CC
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-project-diagram me-1"></i>
                                        Module<br>PIE
                                    </th>
                                    <th class="text-center">
                                        <i class="fas fa-building me-1"></i>
                                        EFP<br>PIE
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-info-subtle text-info border border-info fs-6 px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $avancement->mh_realisee_presentiel }}h
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning-subtle text-warning border border-warning fs-6 px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $avancement->mh_realisee_sync }}h
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success border border-success fs-6 px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $avancement->mh_realisee_globale }}h
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $tauxP = $avancement->taux_realisation_presentiel;
                                            $colorClassP = $tauxP >= 80 ? 'success' : ($tauxP >= 50 ? 'warning' : 'danger');
                                        @endphp
                                        <span class="badge bg-{{ $colorClassP }} text-white fs-6 px-3 py-2">
                                            <i class="fas fa-chart-pie me-1"></i>
                                            {{ $avancement->taux_realisation_presentiel }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $tauxS = $avancement->taux_realisation_syn;
                                            $colorClassS = $tauxS >= 80 ? 'success' : ($tauxS >= 50 ? 'warning' : 'danger');
                                        @endphp
                                        <span class="badge bg-{{ $colorClassS }} text-white fs-6 px-3 py-2">
                                            <i class="fas fa-chart-pie me-1"></i>
                                            {{ $avancement->taux_realisation_syn }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $tauxG = $avancement->taux_realisation_globale;
                                            $colorClassG = $tauxG >= 80 ? 'success' : ($tauxG >= 50 ? 'warning' : 'danger');
                                        @endphp
                                        <span class="badge bg-{{ $colorClassG }} text-white fs-6 px-3 py-2">
                                            <i class="fas fa-chart-area me-1"></i>
                                            {{ $avancement->taux_realisation_globale }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger-subtle text-danger border border-danger fs-6 px-3 py-2">
                                            <i class="fas fa-user-times me-1"></i>
                                            {{ $avancement->moyenne_absence }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary border border-primary fs-6 px-3 py-2">
                                            <i class="fas fa-list-ol me-1"></i>
                                            {{ $avancement->nb_cc }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $module->module_pie == 'O' ? 'bg-info' : 'bg-secondary' }} fs-6 px-3 py-2">
                                            {{ $module->module_pie }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($module->efp_pie)
                                            <span class="badge bg-purple-subtle text-purple border border-purple fs-6 px-3 py-2">
                                                {{ $module->efp_pie }}
                                            </span>
                                        @else
                                            <span class="text-muted fst-italic">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Gradients pour les headers */
.bg-gradient-purple {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-blue {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Cards modernes */
.modern-card {
    border-radius: 1rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    animation: fadeIn 0.5s ease-out;
}

.modern-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
}

.card-header {
    border-radius: 1rem 1rem 0 0 !important;
    padding: 1.25rem 1.5rem;
    font-weight: 600;
}

.card-body {
    padding: 1.5rem;
}

/* Tables */
.detail-table {
    margin-bottom: 0;
}

.detail-table tbody tr {
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s ease;
}

.detail-table tbody tr:hover {
    background-color: #f8f9fa;
}

.detail-table tbody tr:last-child {
    border-bottom: none;
}

.detail-table th {
    padding: 1rem;
    font-weight: 600;
    color: #495057;
    width: 45%;
    vertical-align: middle;
    font-size: 0.9rem;
}

.detail-table td {
    padding: 1rem;
    color: #212529;
    vertical-align: middle;
}

/* Tableau moderne des réalisations */
.modern-table {
    font-size: 0.9rem;
}

.modern-table thead th {
    background: linear-gradient(135deg, #343a40 0%, #495057 100%);
    color: white;
    font-weight: 600;
    text-align: center;
    vertical-align: middle;
    padding: 1.25rem 0.75rem;
    font-size: 0.85rem;
    border: none;
}

.modern-table tbody td {
    padding: 1.5rem 0.75rem;
    vertical-align: middle;
}

.modern-table tbody tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
    transition: all 0.2s ease;
}

/* Avatar */
.avatar-small {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.875rem;
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
}

/* Badges personnalisés */
.badge {
    font-weight: 500;
    font-size: 0.85rem;
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

/* Buttons */
.btn-group .btn {
    border-radius: 0.5rem !important;
    margin-left: 0.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn-group .btn:first-child {
    margin-left: 0;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

/* Animation */
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

/* Responsive */
@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        margin: 0.25rem 0;
        width: 100%;
    }
    
    .table-responsive {
        font-size: 0.75rem;
    }
    
    .detail-table th,
    .detail-table td {
        padding: 0.75rem 0.5rem;
        font-size: 0.85rem;
    }
    
    .modern-table thead th {
        font-size: 0.7rem;
        padding: 1rem 0.5rem;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.3em 0.6em;
    }
}
</style>
@endsection