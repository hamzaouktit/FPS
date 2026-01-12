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

<!-- Modal Comparaison -->
<div class="modal fade" id="compareModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Comparer deux dates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="GET" action="{{ route('administration.etablissement.historique.compare') }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="date1" class="form-label">Date initiale</label>
                        <select name="date1" id="date1" class="form-select" required>
                            <option value="">Sélectionner...</option>
                            @foreach($datesDisponibles as $date)
                                <option value="{{ $date->format('Y-m-d') }}">
                                    {{ $date->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date2" class="form-label">Date de comparaison</label>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
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
    // Auto-submit sur changement de filtre (optionnel)
    document.querySelectorAll('#filterForm select').forEach(select => {
        select.addEventListener('change', function() {
            // Optionnel: décommenter pour auto-submit
            // document.getElementById('filterForm').submit();
        });
    });
</script>
@endpush