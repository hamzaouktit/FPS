@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">Détails du niveau : {{ $niveauModel->niveau }}</h1>
                <p class="text-muted mb-0">{{ $etablissement->nom_efp }}</p>
            </div>
            <div>
                @if($formations->count() == 0)
                    <div class="alert alert-warning d-inline-block mb-0 me-2">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Ce niveau n'est pas encore utilisé dans le dashboard
                    </div>
                @endif
                <a href="{{ route('administration.etablissement.niveaux.edit', $niveauModel->niveau) }}" 
                   class="btn btn-primary me-2">
                    <i class="fas fa-edit me-2"></i>Modifier
                </a>
                <a href="{{ route('administration.etablissement.niveaux.index') }}" 
                   class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informations générales -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Niveau</label>
                        <p class="mb-0 fw-bold">{{ $niveauModel->niveau }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Nombre de formations</label>
                        <p class="mb-0">
                            <span class="badge bg-info">{{ $formations->count() }} formation(s)</span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Date de création</label>
                        <p class="mb-0">{{ $niveauModel->created_at ? $niveauModel->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small">Dernière modification</label>
                        <p class="mb-0">{{ $niveauModel->updated_at ? $niveauModel->updated_at->format('d/m/Y H:i') : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des formations -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Formations associées</h5>
                </div>
                <div class="card-body">
                    @if($formations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Année</th>
                                        <th>Filière</th>
                                        <th>Type</th>
                                        <th>Créneau</th>
                                        <th>Groupes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($formations as $formation)
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">{{ $formation->annee }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $formation->filiere->nom_filiere ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $formation->code_filiere }}</small>
                                            </td>
                                            <td>
                                                @if($formation->type_formation)
                                                    <span class="badge bg-info">{{ $formation->type_formation }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($formation->creneau)
                                                    <span class="badge bg-warning text-dark">{{ $formation->creneau }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ $formation->groupes->count() }} groupe(s)</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Aucune formation associée à ce niveau.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    @if($formations->count() > 0)
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistiques</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-primary mb-0">{{ $formations->count() }}</h3>
                                    <small class="text-muted">Formations</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-success mb-0">{{ $formations->sum(fn($f) => $f->groupes->count()) }}</h3>
                                    <small class="text-muted">Groupes totaux</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-info mb-0">{{ $formations->pluck('filiere.nom_secteur')->unique()->count() }}</h3>
                                    <small class="text-muted">Secteurs</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-3 border rounded">
                                    <h3 class="text-warning mb-0">{{ $formations->pluck('code_filiere')->unique()->count() }}</h3>
                                    <small class="text-muted">Filières</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection