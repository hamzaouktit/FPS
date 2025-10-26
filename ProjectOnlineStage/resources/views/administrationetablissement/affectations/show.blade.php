@extends('layouts.app')

@section('title', 'Détails de l\'Affectation')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent px-0">
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.dashboard') }}" class="text-decoration-none">
                    <i class="fas fa-home me-1"></i>Tableau de Bord
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.affectations.index') }}" class="text-decoration-none">
                    <i class="fas fa-list me-1"></i>Affectations
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Détails</li>
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
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15);
    }
    
    .card-header {
        border: none;
        padding: 1.25rem 1.5rem;
        font-weight: 600;
        background: linear-gradient(135deg, var(--bs-primary) 0%, var(--bs-primary-dark, #0056b3) 100%);
    }
    
    .card-header.bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    }
    
    .card-header.bg-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
    }
    
    .card-header.bg-success {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    }
    
    .card-header.bg-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: #000 !important;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .table-borderless th {
        color: #495057;
        font-weight: 600;
        padding: 0.75rem 0.5rem;
    }
    
    .table-borderless td {
        padding: 0.75rem 0.5rem;
        color: #212529;
    }
    
    .badge {
        padding: 0.35em 0.75em;
        font-weight: 500;
        border-radius: 6px;
    }
    
    .btn {
        border-radius: 8px;
        padding: 0.625rem 1.25rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    
    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: #000;
    }
    
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table thead th {
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
        background: #343a40;
        color: #fff;
    }
    
    .table tbody td {
        vertical-align: middle;
        padding: 1rem 0.75rem;
        border-color: #dee2e6;
    }
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .table-bordered {
        border: 1px solid #dee2e6;
    }
    
    .info-box {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        border-left: 4px solid #007bff;
    }
    
    .info-box h6 {
        font-weight: 600;
        margin-bottom: 0.5rem;
        color: #495057;
    }
    
    .info-box p {
        margin-bottom: 0;
        line-height: 1.6;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        text-align: center;
    }
    
    .stat-card h3 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stat-card p {
        margin-bottom: 0;
        opacity: 0.9;
    }
    
    .alert {
        border: none;
        border-radius: 8px;
        border-left: 4px solid;
    }
    
    .alert-warning {
        background-color: #fff3cd;
        border-left-color: #ffc107;
        color: #856404;
    }
    
    .text-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .fusion-badge {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .card-body {
            padding: 1rem;
        }
        
        .table-responsive {
            font-size: 0.875rem;
        }
    }
</style>

<div class="row g-4">
    <div class="col-lg-4">
        <!-- Informations Générales -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informations Générales
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-3">
                    <tr>
                        <th width="40%"><i class="fas fa-users text-primary me-2"></i>Groupe:</th>
                        <td><strong class="text-gradient">{{ $affectation->groupe->code_groupe }}</strong></td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-book text-info me-2"></i>Module:</th>
                        <td>
                            <strong>{{ $affectation->module->code_module }}</strong><br>
                            <small class="text-muted">{{ $affectation->module->nom_module }}</small>
                        </td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-graduation-cap text-success me-2"></i>Filière:</th>
                        <td>{{ $affectation->groupe->filiere->nom_filiere ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th><i class="fas fa-industry text-warning me-2"></i>Secteur:</th>
                        <td>{{ $affectation->groupe->filiere->secteur->nom_secteur ?? 'N/A' }}</td>
                    </tr>
                </table>

                <div class="d-grid gap-2">
                    <a href="{{ route('administration.etablissement.affectations.edit', $affectation->id) }}" 
                       class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Modifier l'Affectation
                    </a>
                    <a href="{{ route('administration.etablissement.affectations.index') }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour à la Liste
                    </a>
                </div>
            </div>
        </div>

        <!-- Fusion de Groupe -->
        @if($affectation->fusion_groupe || $affectation->code_fusion)
        <div class="card mt-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-object-group me-2"></i>Fusion de Groupe
                </h6>
            </div>
            <div class="card-body">
                @if($affectation->fusion_groupe)
                    <div class="info-box" style="border-left-color: #ffc107;">
                        <h6><i class="fas fa-layer-group me-2"></i>Groupes Fusionnés</h6>
                        <p>
                            <span class="fusion-badge">
                                <i class="fas fa-object-group me-1"></i>
                                {{ $affectation->fusion_groupe }}
                            </span>
                        </p>
                    </div>
                @endif

                @if($affectation->code_fusion)
                    <div class="info-box" style="border-left-color: #17a2b8;">
                        <h6><i class="fas fa-barcode me-2"></i>Code de Fusion</h6>
                        <p>
                            <span class="badge bg-info fs-6">{{ $affectation->code_fusion }}</span>
                        </p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Formateurs Assignés -->
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Formateurs Assignés
                </h6>
            </div>
            <div class="card-body">
                <div class="info-box">
                    <h6><i class="fas fa-user-tie me-2"></i>Présentiel</h6>
                    @if($affectation->formateurPresentiel)
                        <p>
                            <strong>{{ $affectation->formateurPresentiel->nom_complet }}</strong><br>
                            <small class="text-muted"><i class="fas fa-id-badge me-1"></i>MLE: {{ $affectation->formateurPresentiel->mle }}</small><br>
                            <span class="badge bg-info mt-1">{{ $affectation->formateurPresentiel->type }}</span>
                        </p>
                    @else
                        <p class="text-muted mb-0"><i class="fas fa-times-circle me-1"></i>Non assigné</p>
                    @endif
                </div>

                <div class="info-box" style="border-left-color: #ffc107;">
                    <h6><i class="fas fa-video me-2"></i>Synchrone</h6>
                    @if($affectation->formateurSyn)
                        <p>
                            <strong>{{ $affectation->formateurSyn->nom_complet }}</strong><br>
                            <small class="text-muted"><i class="fas fa-id-badge me-1"></i>MLE: {{ $affectation->formateurSyn->mle }}</small><br>
                            <span class="badge bg-warning text-dark mt-1">{{ $affectation->formateurSyn->type }}</span>
                        </p>
                    @else
                        <p class="text-muted mb-0"><i class="fas fa-times-circle me-1"></i>Non assigné</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Statistiques Rapides -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h3>{{ $affectation->mh_totale_drif }}h</h3>
                    <p><i class="fas fa-clock me-1"></i>MH Totale DRIF</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h3>{{ $affectation->mh_affectee_globale }}h</h3>
                    <p><i class="fas fa-calendar-check me-1"></i>MH Affectée</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <h3>{{ $affectation->avancement->taux_realisation_globale ?? 0 }}%</h3>
                    <p><i class="fas fa-chart-line me-1"></i>Taux Réalisation</p>
                </div>
            </div>
        </div>

        <!-- Détails d'Affectation -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>Détails d'Affectation par Groupe et Module
                </h5>
            </div>
            <div class="card-body">
                <h6 class="mb-3"><i class="fas fa-user-check me-2"></i>Affectations Actives</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>MLE Présentiel</th>
                                <th>Formateur Présentiel</th>
                                <th>MLE Synchrone</th>
                                <th>Formateur Synchrone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-primary">{{ $affectation->mle_affecte_presentiel ?? 'N/A' }}</span></td>
                                <td>{{ $affectation->formateur_affecte_presentiel ?? 'N/A' }}</td>
                                <td><span class="badge bg-warning text-dark">{{ $affectation->mle_affecte_syn ?? 'N/A' }}</span></td>
                                <td>{{ $affectation->formateur_affecte_syn ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Semestre 1 - DRIF</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>MHP S1</th>
                                <th>MHSYN S1</th>
                                <th>MHASYN S1</th>
                                <th>MH Totale S1</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $affectation->mhp_s1_drif }}h</td>
                                <td>{{ $affectation->mhsyn_s1_drif }}h</td>
                                <td>{{ $affectation->mhasyn_s1_drif }}h</td>
                                <td><strong class="text-primary">{{ $affectation->mh_totale_s1_drif }}h</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="mb-3"><i class="fas fa-calendar-alt me-2"></i>Semestre 2 - DRIF</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>MHP S2</th>
                                <th>MHSYN S2</th>
                                <th>MHASYN S2</th>
                                <th>MH Totale S2</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $affectation->mhp_s2_drif }}h</td>
                                <td>{{ $affectation->mhsyn_s2_drif }}h</td>
                                <td>{{ $affectation->mhasyn_s2_drif }}h</td>
                                <td><strong class="text-primary">{{ $affectation->mh_totale_s2_drif }}h</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Totaux DRIF</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>MHP Totale</th>
                                <th>MHSYN Totale</th>
                                <th>MHASYN Totale</th>
                                <th>MH Totale DRIF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $affectation->mhp_totale_drif }}h</td>
                                <td>{{ $affectation->mhsyn_totale_drif }}h</td>
                                <td>{{ $affectation->mhasyn_totale_drif }}h</td>
                                <td><strong class="text-success fs-5">{{ $affectation->mh_totale_drif }}h</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="mb-3"><i class="fas fa-tasks me-2"></i>MH Affectées</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>MH Affectée Présentiel</th>
                                <th>MH Affectée Sync</th>
                                <th>MH Affectée Globale</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $affectation->mh_affectee_presentiel }}h</td>
                                <td>{{ $affectation->mh_affectee_sync }}h</td>
                                <td><strong class="text-success fs-5">{{ $affectation->mh_affectee_globale }}h</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Réalisation -->
        <div class="card mt-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>Réalisation par Groupe et Module
                </h5>
            </div>
            <div class="card-body">
                <h6 class="mb-3"><i class="fas fa-hourglass-half me-2"></i>MH Réalisées</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>MH Réalisée Présentiel</th>
                                <th>MH Réalisée Sync</th>
                                <th>MH Réalisée Globale</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>{{ $affectation->avancement->mh_realisee_presentiel ?? 0 }}h</strong></td>
                                <td><strong>{{ $affectation->avancement->mh_realisee_sync ?? 0 }}h</strong></td>
                                <td><strong class="text-success fs-5">{{ $affectation->avancement->mh_realisee_globale ?? 0 }}h</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="mb-3"><i class="fas fa-percentage me-2"></i>Taux de Réalisation</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Taux Présentiel</th>
                                <th>Taux Synchrone</th>
                                <th>Taux Global</th>
                                <th>Moy Absence</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <span class="badge bg-{{ ($affectation->avancement->taux_realisation_presentiel ?? 0) >= 80 ? 'success' : (($affectation->avancement->taux_realisation_presentiel ?? 0) >= 50 ? 'warning' : 'danger') }} fs-6">
                                        {{ $affectation->avancement->taux_realisation_presentiel ?? 0 }}%
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ ($affectation->avancement->taux_realisation_syn ?? 0) >= 80 ? 'success' : (($affectation->avancement->taux_realisation_syn ?? 0) >= 50 ? 'warning' : 'danger') }} fs-6">
                                        {{ $affectation->avancement->taux_realisation_syn ?? 0 }}%
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ ($affectation->avancement->taux_realisation_globale ?? 0) >= 80 ? 'success' : (($affectation->avancement->taux_realisation_globale ?? 0) >= 50 ? 'warning' : 'danger') }} fs-5">
                                        {{ $affectation->avancement->taux_realisation_globale ?? 0 }}%
                                    </span>
                                </td>
                                <td><span class="badge bg-info fs-6">{{ $affectation->avancement->moyenne_absence ?? 0 }}%</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h6 class="mb-3"><i class="fas fa-clipboard-check me-2"></i>Informations Complémentaires</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>NB CC</th>
                                <th>Séance EFM</th>
                                <th>Validation EFM</th>
                                <th>Classe Teams</th>
                                <th>Module PIE</th>
                                <th>EFP PIE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-primary fs-6">{{ $affectation->avancement->nb_cc ?? 0 }}</span></td>
                                <td>
                                    <span class="badge bg-{{ ($affectation->avancement->seance_efm ?? 'Non') == 'Oui' ? 'success' : 'secondary' }} fs-6">
                                        <i class="fas fa-{{ ($affectation->avancement->seance_efm ?? 'Non') == 'Oui' ? 'check' : 'times' }} me-1"></i>
                                        {{ $affectation->avancement->seance_efm ?? 'Non' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ ($affectation->avancement->validation_efm ?? 'non') == 'oui' ? 'success' : 'secondary' }} fs-6">
                                        <i class="fas fa-{{ ($affectation->avancement->validation_efm ?? 'non') == 'oui' ? 'check-circle' : 'times-circle' }} me-1"></i>
                                        {{ ucfirst($affectation->avancement->validation_efm ?? 'non') }}
                                    </span>
                                </td>
                                <td>{{ $affectation->avancement->classe_teams ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $affectation->module->module_pie == 'O' ? 'warning' : 'secondary' }} fs-6">
                                        {{ $affectation->module->module_pie == 'O' ? 'Oui' : 'Non' }}
                                    </span>
                                </td>
                                <td>{{ $affectation->module->efp_pie ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if(!$affectation->avancement)
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention:</strong> Aucune donnée d'avancement n'est disponible pour cette affectation.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection