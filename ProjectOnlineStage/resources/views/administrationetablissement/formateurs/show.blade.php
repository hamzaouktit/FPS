@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Détails du Formateur</h2>
                    <p class="text-muted mb-0">{{ $formateur->nom_complet }} ({{ $formateur->mle }})</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('administration.etablissement.formateurs.edit', $formateur) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                    <a href="{{ route('administration.etablissement.formateurs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4 g-3">
        <!-- Offre -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="opacity-75 d-block mb-1">Offre (Masse Horaire)</small>
                            <h3 class="mb-0">{{ number_format($statsGlobales['offre'], 0) }} h</h3>
                            <small class="opacity-75 d-block mb-1">{{ $formateur->description }}</small>
                            
                        </div>
                        <i class="fas fa-gift fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Demande -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="opacity-75 d-block mb-1">Demande Totale</small>
                            <h3 class="mb-0">{{ number_format($statsGlobales['demande'], 0) }} h</h3>
                        </div>
                        <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Heures Affectées -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="opacity-75 d-block mb-1">Heures Affectées</small>
                            <h3 class="mb-0">{{ number_format($statsGlobales['heures_affectees'], 0) }} h</h3>
                        </div>
                        <i class="fas fa-check-double fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manque -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="opacity-75 d-block mb-1">Manque</small>
                            <h3 class="mb-0">{{ number_format($statsGlobales['manque'], 0) }} h</h3>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Deuxième ligne de statistiques -->
    <div class="row mb-4 g-3">
        <!-- Taux d'affectation -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="opacity-75 d-block mb-1">Taux d'Affectation</small>
                            <h3 class="mb-0">{{ $statsGlobales['taux_affectation'] }} %</h3>
                            <small class="mt-2 d-block">
                                {{ number_format($statsGlobales['heures_affectees'], 0) }} / 
                                {{ number_format($statsGlobales['demande'], 0) }} h
                            </small>
                        </div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Disponibilité -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block mb-1">Disponibilité</small>
                            <h3 class="mb-0 {{ $statsGlobales['disponibilite'] < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format($statsGlobales['disponibilite'], 0) }} h
                            </h3>
                            @if($statsGlobales['disponibilite'] < 0)
                                <small class="text-danger">Surcharge</small>
                            @endif
                        </div>
                        <i class="fas fa-battery-three-quarters fa-3x text-muted opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Heures Restantes -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" 
                 style="background: linear-gradient(135deg, #ffd89b 0%, #19547b 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="opacity-75 d-block mb-1">Heures Restantes</small>
                            <h3 class="mb-0">{{ number_format($statsGlobales['heures_restantes'], 0) }} h</h3>
                        </div>
                        <i class="fas fa-clock fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations détaillées -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations</h5>
                </div>
                <div class="card-body">
                    <p><strong>Type :</strong> 
                        <span class="badge bg-{{ $formateur->type === 'permanent' ? 'success' : 'warning' }}">
                            {{ ucfirst($formateur->type) }}
                        </span>
                    </p>
                    <p><strong>Établissement(s) :</strong></p>
                    @foreach($formateur->etablissements as $etab)
                        <div class="ms-3">
                            <i class="fas fa-building text-primary me-2"></i>
                            {{ $etab->nom_efp }} <small class="text-muted">({{ $etab->code_efp }})</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Compétences</h5>
                </div>
                <div class="card-body">
                    <p><strong>Secteurs :</strong></p>
                    @forelse($formateur->secteurs as $secteur)
                        <span class="badge bg-info me-1 mb-1">{{ $secteur->nom_secteur }}</span>
                    @empty
                        <p class="text-muted">Aucun secteur</p>
                    @endforelse
                    
                    <p class="mt-3"><strong>Modules :</strong> {{ $formateur->modules->count() }} module(s)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Détail par module -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-book me-2"></i>Détail des Affectations par Module</h5>
                </div>
                <div class="card-body">
                    @if(count($heuresParModule) > 0)
                        @foreach($heuresParModule as $data)
                            <div class="module-section mb-4 p-3 border rounded">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-book-open me-2"></i>
                                    {{ $data['module_code'] }} - {{ $data['module_nom'] }}
                                </h6>
                                
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="stat-box bg-light p-2 rounded">
                                            <small class="text-muted">Demande totale</small>
                                            <div class="h5 mb-0">{{ number_format($data['total_demande'], 0) }} h</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stat-box bg-light p-2 rounded">
                                            <small class="text-muted">Heures affectées</small>
                                            <div class="h5 mb-0">{{ number_format($data['total_affecte'], 0) }} h</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="stat-box bg-light p-2 rounded">
                                            <small class="text-muted">Manquant</small>
                                            <div class="h5 mb-0 text-danger">{{ number_format($data['total_manquant'], 0) }} h</div>
                                        </div>
                                    </div>
                                </div>

                                <table class="table table-sm table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Groupe</th>
                                            <th>Filière</th>
                                            <th>Type</th>
                                            <th>Demande</th>
                                            <th>Affecté</th>
                                            <th>Manquant</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['groupes'] as $groupeNom => $groupe)
                                            <tr>
                                                <td><strong>{{ $groupeNom }}</strong></td>
                                                <td>{{ $groupe['filiere'] }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $groupe['type_affectation'] }}</span>
                                                </td>
                                                <td>{{ number_format($groupe['demande'], 0) }} h</td>
                                                <td>{{ number_format($groupe['affecte'], 0) }} h</td>
                                                <td>
                                                    <span class="badge bg-{{ $groupe['manquant'] > 0 ? 'danger' : 'success' }}">
                                                        {{ number_format($groupe['manquant'], 0) }} h
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center py-4">
                            <i class="fas fa-inbox fa-3x mb-3"></i><br>
                            Aucune affectation pour ce formateur
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.module-section {
    background: #f8f9fa;
}
.stat-box {
    text-align: center;
}
</style>
@endsection