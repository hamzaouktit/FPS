@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">
                    <i class="fas fa-history me-2"></i>Détails de l'Historique d'Avancement (Complexe)
                </h2>
                <a href="{{ route('administration.complexe.historique.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations Générales -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations Générales</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-5">
                            <strong>Date de Capture :</strong>
                        </div>
                        <div class="col-sm-7">
                            {{ $historique->date_capture->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <hr>

                    <div class="row mb-3">
                        <div class="col-sm-5">
                            <strong>Établissement :</strong>
                        </div>
                        <div class="col-sm-7">
                            <span class="badge bg-danger">{{ $historique->code_efp }}</span>
                        </div>
                    </div>
                    <hr>

                    <div class="row mb-3">
                        <div class="col-sm-5">
                            <strong>Module :</strong>
                        </div>
                        <div class="col-sm-7">
                            <span class="badge bg-info">{{ $historique->code_module }}</span>
                            <br>
                            <small class="text-muted">{{ $historique->nom_module }}</small>
                        </div>
                    </div>
                    <hr>

                    <div class="row mb-3">
                        <div class="col-sm-5">
                            <strong>Groupe :</strong>
                        </div>
                        <div class="col-sm-7">
                            <span class="badge bg-success">{{ $historique->code_groupe }}</span>
                        </div>
                    </div>
                    <hr>

                    <div class="row mb-3">
                        <div class="col-sm-5">
                            <strong>Filière :</strong>
                        </div>
                        <div class="col-sm-7">
                            <span class="badge bg-warning text-dark">{{ $historique->nom_filiere }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formateurs -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Formateurs</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-5">
                            <strong>Formateur Présentiel :</strong>
                        </div>
                        <div class="col-sm-7">
                            {{ $historique->formateur_presentiel ?? 'N/A' }}
                            <br>
                            <small class="text-muted">MLE: {{ $historique->mle_presentiel ?? 'N/A' }}</small>
                        </div>
                    </div>
                    <hr>

                    <div class="row mb-3">
                        <div class="col-sm-5">
                            <strong>Formateur Synchrone :</strong>
                        </div>
                        <div class="col-sm-7">
                            {{ $historique->formateur_syn ?? 'N/A' }}
                            <br>
                            <small class="text-muted">MLE: {{ $historique->mle_syn ?? 'N/A' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Heures de Réalisation -->
        <div class="col-lg-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Heures de Réalisation - Détails</h5>
                </div>
                <div class="card-body">
                    <!-- Heures Affectées -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-primary mb-3"><i class="fas fa-calendar-check me-2"></i>Heures Affectées</h6>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block mb-2">Présentiel</small>
                                    <h4 class="mb-0">{{ $historique->mh_affectee_presentiel ?? 0 }} <small>h</small></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block mb-2">Synchrone</small>
                                    <h4 class="mb-0">{{ $historique->mh_affectee_sync ?? 0 }} <small>h</small></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-primary bg-opacity-10 border-0 border-left border-3 border-primary">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block mb-2">Globale</small>
                                    <h4 class="mb-0 text-primary">{{ $historique->mh_affectee_globale }} <small>h</small></h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Heures Réalisées -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h6 class="text-success mb-3"><i class="fas fa-check-circle me-2"></i>Heures Réalisées</h6>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block mb-2">Présentiel</small>
                                    <h4 class="mb-0">{{ $historique->mh_realisee_presentiel ?? 0 }} <small>h</small></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light border-0">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block mb-2">Synchrone</small>
                                    <h4 class="mb-0">{{ $historique->mh_realisee_sync ?? 0 }} <small>h</small></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success bg-opacity-10 border-0 border-left border-3 border-success">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block mb-2">Globale</small>
                                    <h4 class="mb-0 text-success">{{ $historique->mh_realisee_globale }} <small>h</small></h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Taux de Réalisation par Type -->
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="text-info mb-3"><i class="fas fa-chart-pie me-2"></i>Taux de Réalisation</h6>
                        </div>
                        <!-- Taux Présentiel -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small">Présentiel</label>
                            @php
                                $taux_p = $historique->taux_realisation_presentiel ?? 0;
                                $couleur_p = $taux_p >= 80 ? 'success' : ($taux_p >= 50 ? 'warning' : 'danger');
                            @endphp
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-{{ $couleur_p }}" role="progressbar" 
                                     style="width: {{ $taux_p }}%;" 
                                     aria-valuenow="{{ $taux_p }}" aria-valuemin="0" aria-valuemax="100">
                                    <strong>{{ $taux_p }}%</strong>
                                </div>
                            </div>
                        </div>
                        <!-- Taux Synchrone -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small">Synchrone</label>
                            @php
                                $taux_s = $historique->taux_realisation_syn ?? 0;
                                $couleur_s = $taux_s >= 80 ? 'success' : ($taux_s >= 50 ? 'warning' : 'danger');
                            @endphp
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-{{ $couleur_s }}" role="progressbar" 
                                     style="width: {{ $taux_s }}%;" 
                                     aria-valuenow="{{ $taux_s }}" aria-valuemin="0" aria-valuemax="100">
                                    <strong>{{ $taux_s }}%</strong>
                                </div>
                            </div>
                        </div>
                        <!-- Taux Global -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-bold small">Globale</label>
                            @php
                                $taux_g = $historique->taux_realisation_globale ?? 0;
                                $couleur_g = $taux_g >= 80 ? 'success' : ($taux_g >= 50 ? 'warning' : 'danger');
                            @endphp
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-{{ $couleur_g }}" role="progressbar" 
                                     style="width: {{ $taux_g }}%;" 
                                     aria-valuenow="{{ $taux_g }}" aria-valuemin="0" aria-valuemax="100">
                                    <strong>{{ $taux_g }}%</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Classe Teams -->
                    @if($historique->classe_teams)
                        <hr>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <small class="text-muted">
                                    <i class="fas fa-video me-2"></i><strong>Classe Teams :</strong> {{ $historique->classe_teams }}
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Données de Suivi -->
        <div class="col-lg-12 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Données de Suivi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block mb-2">Moyenne Absence</small>
                                    <h5 class="mb-0 text-danger">{{ $historique->moyenne_absence ?? 0 }}%</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block mb-2">Nombre de CC</small>
                                    <h5 class="mb-0 text-secondary">{{ $historique->nb_cc ?? 0 }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block mb-2">Séance EFM</small>
                                    @if($historique->seance_efm === 'Oui')
                                        <span class="badge bg-success fs-6">Oui</span>
                                    @else
                                        <span class="badge bg-secondary fs-6">Non</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body text-center">
                                    <small class="text-muted d-block mb-2">Validation EFM</small>
                                    @if($historique->validation_efm === 'oui')
                                        <span class="badge bg-success fs-6">Validée</span>
                                    @else
                                        <span class="badge bg-warning text-dark fs-6">En attente</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Actions -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('administration.complexe.historique.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list me-2"></i>Voir la liste
                        </a>
                        <a href="{{ route('administration.complexe.historique.compare') }}" class="btn btn-info">
                            <i class="fas fa-exchange-alt me-2"></i>Comparer avec une autre date
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }

    .card-header {
        border-radius: 8px 8px 0 0;
        font-weight: 600;
    }

    .badge {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .progress {
        height: 25px;
        border-radius: 4px;
    }

    .progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
    }

    hr {
        margin: 1rem 0;
        border-color: #e0e0e0;
    }
</style>
@endpush
@endsection
