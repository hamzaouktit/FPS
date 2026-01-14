@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-history"></i> Historique des Avancements</h2>
                <a href="{{ route('administration.etablissement.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour au Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-filter"></i> Filtres de recherche
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('administration.etablissement.historique.index') }}" id="filterForm">
                <div class="row">
                    <!-- Filtre Date -->
                    <div class="col-md-3 mb-3">
                        <label for="date" class="form-label">Date de capture</label>
                        <select name="date" id="date" class="form-select">
                            <option value="">Toutes les dates</option>
                            @foreach($datesDisponibles as $date)
                                <option value="{{ $date->format('Y-m-d') }}" 
                                    {{ request('date') == $date->format('Y-m-d') ? 'selected' : '' }}>
                                    {{ $date->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtre Formateur -->
                    <div class="col-md-3 mb-3">
                        <label for="formateur" class="form-label">Formateur</label>
                        <select name="formateur" id="formateur" class="form-select">
                            <option value="">Tous les formateurs</option>
                            @foreach($formateurs as $formateur)
                                <option value="{{ $formateur->mle }}" 
                                    {{ request('formateur') == $formateur->mle ? 'selected' : '' }}>
                                    {{ $formateur->nom_complet }} ({{ $formateur->mle }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtre Module -->
                    <div class="col-md-3 mb-3">
                        <label for="module" class="form-label">Module</label>
                        <select name="module" id="module" class="form-select">
                            <option value="">Tous les modules</option>
                            @foreach($modules as $module)
                                <option value="{{ $module->code_module }}" 
                                    {{ request('module') == $module->code_module ? 'selected' : '' }}>
                                    {{ $module->code_module }} - {{ $module->nom_module }} 
                                    @if($module->filieres->isNotEmpty())
                                        ({{ $module->filieres->pluck('nom_filiere')->join(', ') }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filtre Groupe -->
                    <div class="col-md-3 mb-3">
                        <label for="groupe" class="form-label">Groupe</label>
                        <select name="groupe" id="groupe" class="form-select">
                            <option value="">Tous les groupes</option>
                            @foreach($groupes as $groupe)
                                <option value="{{ $groupe->code_groupe }}" 
                                    {{ request('groupe') == $groupe->code_groupe ? 'selected' : '' }}>
                                    {{ $groupe->code_groupe }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                        <a href="{{ route('administration.etablissement.historique.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Réinitialiser
                        </a>
                        <a href="{{ route('administration.etablissement.historique.export', request()->all()) }}" 
                           class="btn btn-success">
                            <i class="fas fa-file-excel"></i> Exporter CSV
                        </a>
                        @if($datesDisponibles->count() >= 2)
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#compareModal">
                            <i class="fas fa-exchange-alt"></i> Comparer deux dates
                        </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Résultats -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <i class="fas fa-list"></i> Résultats 
            <span class="badge bg-light text-dark">{{ $historiques->total() }} enregistrement(s)</span>
        </div>
        <div class="card-body">
            @if($historiques->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Formateur Présentiel</th>
                                <th>Formateur Synchrone</th>
                                <th>Module</th>
                                <th>Groupe</th>
                                <th>Filière</th>
                                <th>MH Affectée</th>
                                <th>MH Réalisée</th>
                                <th>Taux Global</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($historiques as $h)
                            <tr>
                                <td>{{ $h->date_capture->format('d/m/Y') }}</td>
                                <td>
                                    @if($h->formateur_presentiel)
                                        <small class="text-muted d-block">{{ $h->mle_presentiel }}</small>
                                        {{ $h->formateur_presentiel }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($h->formateur_syn)
                                        <small class="text-muted d-block">{{ $h->mle_syn }}</small>
                                        {{ $h->formateur_syn }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted d-block">{{ $h->code_module }}</small>
                                    {{ Str::limit($h->nom_module, 40) }}
                                </td>
                                <td>{{ $h->code_groupe }}</td>
                                <td>{{ Str::limit($h->nom_filiere, 30) }}</td>
                                <td class="text-end">{{ number_format($h->mh_affectee_globale, 2) }}</td>
                                <td class="text-end">
                                    <strong>{{ number_format($h->mh_realisee_globale, 2) }}</strong>
                                </td>
                                <td class="text-center">
                                    @php
                                        $taux = $h->taux_realisation_globale;
                                        $color = $taux >= 75 ? 'success' : ($taux >= 50 ? 'warning' : 'danger');
                                    @endphp
                                    <span class="badge bg-{{ $color }}">
                                        {{ number_format($taux, 2) }}%
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('administration.etablissement.historique.show', $h->id) }}" 
                                       class="btn btn-sm btn-info" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $historiques->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aucun historique trouvé avec les filtres sélectionnés.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Comparaison Amélioré -->
<div class="modal fade" id="compareModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt"></i> Comparer deux dates
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('administration.etablissement.historique.compare') }}" id="compareForm">
                <div class="modal-body">
                    <!-- Section Dates -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-primary"><i class="fas fa-calendar-alt"></i> Sélection des dates</h6>
                            <hr>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date1" class="form-label">Date initiale <span class="text-danger">*</span></label>
                            <select name="date1" id="date1" class="form-select" required>
                                <option value="">Sélectionner...</option>
                                @foreach($datesDisponibles as $date)
                                    <option value="{{ $date->format('Y-m-d') }}">
                                        {{ $date->format('d/m/Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date2" class="form-label">Date de comparaison <span class="text-danger">*</span></label>
                            <select name="date2" id="date2" class="form-select" required>
                                <option value="">Sélectionner...</option>
                                @foreach($datesDisponibles as $date)
                                    <option value="{{ $date->format('Y-m-d') }}">
                                        {{ $date->format('d/m/Y') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Section Filtres Optionnels -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-secondary">
                                <i class="fas fa-filter"></i> Filtres optionnels
                                <small class="text-muted">(Laisser vide pour comparer tout)</small>
                            </h6>
                            <hr>
                        </div>
                        
                        <!-- Filtre Formateur -->
                        <div class="col-md-12 mb-3">
                            <label for="compare_formateur" class="form-label">
                                <i class="fas fa-user-tie"></i> Formateur spécifique
                            </label>
                            <select name="formateur" id="compare_formateur" class="form-select">
                                <option value="">Tous les formateurs</option>
                                @foreach($formateurs as $formateur)
                                    <option value="{{ $formateur->mle }}">
                                        {{ $formateur->nom_complet }} ({{ $formateur->mle }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">
                                Comparez l'avancement d'un formateur spécifique entre deux dates
                            </small>
                        </div>

                        <!-- Filtre Module -->
                        <div class="col-md-6 mb-3">
                            <label for="compare_module" class="form-label">
                                <i class="fas fa-book"></i> Module spécifique
                            </label>
                            <select name="module" id="compare_module" class="form-select">
                                <option value="">Tous les modules</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module->code_module }}">
                                        {{ $module->code_module }} - {{ Str::limit($module->nom_module, 40) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtre Groupe -->
                        <div class="col-md-6 mb-3">
                            <label for="compare_groupe" class="form-label">
                                <i class="fas fa-users"></i> Groupe spécifique
                            </label>
                            <select name="groupe" id="compare_groupe" class="form-select">
                                <option value="">Tous les groupes</option>
                                @foreach($groupes as $groupe)
                                    <option value="{{ $groupe->code_groupe }}">
                                        {{ $groupe->code_groupe }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Astuce:</strong> Utilisez les filtres pour analyser l'évolution d'un formateur, 
                        module ou groupe spécifique entre deux dates.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-exchange-alt"></i> Comparer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Validation des dates dans le modal
    document.getElementById('compareForm').addEventListener('submit', function(e) {
        const date1 = document.getElementById('date1').value;
        const date2 = document.getElementById('date2').value;
        
        if (!date1 || !date2) {
            e.preventDefault();
            alert('Veuillez sélectionner les deux dates');
            return false;
        }
        
        if (date1 >= date2) {
            e.preventDefault();
            alert('La date de comparaison doit être postérieure à la date initiale');
            return false;
        }
    });
    
    // Auto-remplissage du formateur si déjà filtré
    @if(request('formateur'))
        document.getElementById('compare_formateur').value = '{{ request('formateur') }}';
    @endif
    
    @if(request('module'))
        document.getElementById('compare_module').value = '{{ request('module') }}';
    @endif
    
    @if(request('groupe'))
        document.getElementById('compare_groupe').value = '{{ request('groupe') }}';
    @endif
</script>
@endpush