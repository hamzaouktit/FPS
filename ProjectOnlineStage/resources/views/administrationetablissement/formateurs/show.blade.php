@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec navigation -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Détails du Formateur</h2>
                    <p class="text-muted mb-0">{{ $formateur->nom_complet }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('administration.etablissement.formateurs.edit', $formateur) }}" 
                       class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i> Modifier
                    </a>
                    <a href="{{ route('administration.etablissement.formateurs.index') }}" 
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations générales -->
    <div class="row mb-4">
        <!-- Informations Personnelles -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-user-circle me-2 text-primary"></i>
                        Informations Personnelles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-group mb-3">
                        <label class="info-label">MLE</label>
                        <div class="info-value">
                            <strong class="text-primary">{{ $formateur->mle }}</strong>
                        </div>
                    </div>
                    
                    <div class="info-group mb-3">
                        <label class="info-label">Nom Complet</label>
                        <div class="info-value">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle me-2">
                                    {{ strtoupper(substr($formateur->nom_complet, 0, 2)) }}
                                </div>
                                {{ $formateur->nom_complet }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-group mb-3">
                        <label class="info-label">Type</label>
                        <div class="info-value">
                            @if($formateur->type === 'permanent')
                                <span class="badge bg-success-subtle text-success border border-success px-3 py-2">
                                    <i class="fas fa-user-tie me-1"></i>Permanent
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2">
                                    <i class="fas fa-user-clock me-1"></i>Vacataire
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="info-group">
                        <label class="info-label">Établissement</label>
                        <div class="info-value">
                            <i class="fas fa-building me-2 text-muted"></i>
                            {{ $formateur->etablissement->nom_efp }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Compétences -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-graduation-cap me-2 text-success"></i>
                        Compétences
                    </h5>
                </div>
                <div class="card-body">
                    <div class="info-group mb-4">
                        <label class="info-label">Secteurs</label>
                        <div class="info-value">
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($formateur->secteurs as $secteur)
                                    <span class="badge bg-info-subtle text-info border border-info px-3 py-2">
                                        <i class="fas fa-industry me-1"></i>
                                        {{ $secteur->nom_secteur }}
                                    </span>
                                @empty
                                    <span class="text-muted fst-italic">Aucun secteur assigné</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    
                    <div class="info-group">
                        <label class="info-label">Modules</label>
                        <div class="info-value">
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($formateur->modules as $module)
                                    <div class="module-badge">
                                        <span class="module-code">{{ $module->code_module }}</span>
                                        <span class="module-name">{{ Str::limit($module->nom_module, 30) }}</span>
                                    </div>
                                @empty
                                    <span class="text-muted fst-italic">Aucun module assigné</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails des heures par module -->
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-clock me-2 text-warning"></i>
                        Détail des Heures par Module et Groupe
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($heuresParModule) > 0)
                        @foreach($heuresParModule as $moduleId => $moduleData)
                            <div class="module-card mb-4">
                                <div class="module-card-header">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-book me-2"></i>
                                        <h6 class="mb-0">{{ $moduleData['module_nom'] }}</h6>
                                    </div>
                                </div>
                                <div class="module-card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead>
                                                <tr>
                                                    <th class="border-0 ps-3">
                                                        <i class="fas fa-users me-2 text-muted"></i>Groupe
                                                    </th>
                                                    <th class="border-0 text-center">
                                                        <i class="fas fa-tasks me-2 text-muted"></i>Heures Requises
                                                    </th>
                                                    <th class="border-0 text-center">
                                                        <i class="fas fa-check-circle me-2 text-muted"></i>Heures Affectées
                                                    </th>
                                                    <th class="border-0 text-center">
                                                        <i class="fas fa-exclamation-circle me-2 text-muted"></i>Heures Manquantes
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($moduleData['groupes'] as $groupeNom => $heures)
                                                    <tr>
                                                        <td class="ps-3">
                                                            <strong>{{ $groupeNom }}</strong>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-light text-dark border">
                                                                {{ number_format($heures['heures_requises'], 2) }}h
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            @if($heures['heures_affectees'] < $heures['heures_requises'])
                                                                <span class="badge bg-warning-subtle text-warning border border-warning">
                                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                                    {{ number_format($heures['heures_affectees'], 2) }}h
                                                                </span>
                                                            @else
                                                                <span class="badge bg-success-subtle text-success border border-success">
                                                                    <i class="fas fa-check me-1"></i>
                                                                    {{ number_format($heures['heures_affectees'], 2) }}h
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            @if($heures['heures_manquantes'] > 0)
                                                                <span class="badge bg-danger-subtle text-danger border border-danger">
                                                                    <i class="fas fa-times me-1"></i>
                                                                    {{ number_format($heures['heures_manquantes'], 2) }}h
                                                                </span>
                                                            @else
                                                                <span class="badge bg-success-subtle text-success border border-success">
                                                                    <i class="fas fa-check-double me-1"></i>
                                                                    0.00h
                                                                </span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-light">
                                                <tr class="fw-bold">
                                                    <td class="ps-3">
                                                        <i class="fas fa-calculator me-2"></i>Total
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-dark text-white">
                                                            {{ number_format($moduleData['total_requis'], 2) }}h
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($moduleData['total_affecte'] < $moduleData['total_requis'])
                                                            <span class="badge bg-warning-subtle text-warning border border-warning">
                                                                {{ number_format($moduleData['total_affecte'], 2) }}h
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success-subtle text-success border border-success">
                                                                {{ number_format($moduleData['total_affecte'], 2) }}h
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($moduleData['total_manquant'] > 0)
                                                            <span class="badge bg-danger-subtle text-danger border border-danger">
                                                                {{ number_format($moduleData['total_manquant'], 2) }}h
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success-subtle text-success border border-success">
                                                                0.00h
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p class="mb-0">Aucune affectation trouvée pour ce formateur.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Avatar Circle */
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.85rem;
}

/* Card Styles */
.card {
    border-radius: 0.75rem;
    overflow: hidden;
    transition: transform 0.2s ease;
}

.card-header {
    border-bottom: 1px solid #f0f0f0;
}

/* Info Group */
.info-group {
    padding: 0.75rem 0;
}

.info-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #6c757d;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
    display: block;
}

.info-value {
    font-size: 1rem;
    color: #212529;
    font-weight: 500;
}

/* Module Badge */
.module-badge {
    display: inline-flex;
    flex-direction: column;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border: 1px solid rgba(102, 126, 234, 0.3);
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    margin-bottom: 0.5rem;
}

.module-code {
    font-weight: bold;
    color: #667eea;
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.module-name {
    font-size: 0.75rem;
    color: #6c757d;
}

/* Module Card */
.module-card {
    border: 1px solid #e9ecef;
    border-radius: 0.75rem;
    overflow: hidden;
    transition: box-shadow 0.2s ease;
}

.module-card:hover {
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.1);
}

.module-card-header {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e9ecef;
}

.module-card-header h6 {
    color: #495057;
    font-weight: 600;
}

.module-card-body {
    padding: 0;
}

/* Table Styles */
.table thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    font-size: 0.875rem;
    color: #495057;
    padding: 1rem 0.75rem;
}

.table tbody tr {
    transition: background-color 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.table tbody td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

.table tfoot td {
    padding: 1rem 0.75rem;
    font-size: 1rem;
}

/* Badge Styles */
.badge {
    font-weight: 500;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
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

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #6c757d;
}

.empty-state i {
    color: #dee2e6;
}

/* Button Group */
.btn-group .btn {
    border-radius: 0.5rem !important;
}

.btn-group .btn:not(:last-child) {
    margin-right: 0.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .module-badge {
        width: 100%;
    }
    
    .table {
        font-size: 0.875rem;
    }
    
    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.5rem;
    }
}

/* Shadow */
.shadow-sm {
    box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075) !important;
}
</style>
@endsection