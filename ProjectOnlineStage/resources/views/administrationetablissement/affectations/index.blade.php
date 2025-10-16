@extends('layouts.app')

@section('title', 'Gestion des Affectations')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.dashboard') }}">Tableau de Bord</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Affectations</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-tasks me-2"></i>Liste des Affectations - {{ $etablissement->nom_efp }}
        </h5>
        <a href="{{ route('administration.etablissement.affectations.create') }}" class="btn btn-light">
            <i class="fas fa-plus me-2"></i>Nouvelle Affectation
        </a>
    </div>
    
    <div class="card-body">
        @if($affectations->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Groupe</th>
                            <th>Module</th>
                            <th>Filiere</th>
                            <th>Formateur Présentiel</th>
                            <th>Formateur Synchrone</th>
                            <th>MH Totale DRIF</th>
                            <th>MH Affectée</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($affectations as $affectation)
                            <tr>
                                <td>
                                    <strong>{{ $affectation->groupe->code_groupe }}</strong>
                                </td>
                                <td>
                                    <strong>{{ $affectation->module->code_module }}</strong><br>
                                    <small class="text-muted">{{ $affectation->module->nom_module }}</small>
                                </td>
                                <td>
                                    {{ $affectation->groupe->filiere->nom_filiere ?? 'N/A' }}
                                </td>
                                <td>
                                    @if($affectation->formateurPresentiel)
                                        <span class="badge bg-info">
                                            {{ $affectation->formateurPresentiel->nom_complet }}
                                        </span>
                                    @else
                                        <span class="text-muted">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    @if($affectation->formateurSyn)
                                        <span class="badge bg-warning text-dark">
                                            {{ $affectation->formateurSyn->nom_complet }}
                                        </span>
                                    @else
                                        <span class="text-muted">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $affectation->mh_totale_drif }}h</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $affectation->mh_affectee_globale }}h</span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('administration.etablissement.affectations.show', $affectation->id) }}" 
                                           class="btn btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('administration.etablissement.affectations.edit', $affectation->id) }}" 
                                           class="btn btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('administration.etablissement.affectations.destroy', $affectation->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette affectation ?')"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                Aucune affectation n'a été créée pour votre établissement.
                <a href="{{ route('administration.etablissement.affectations.create') }}" class="alert-link">
                    Créer la première affectation
                </a>
            </div>
        @endif
    </div>
</div>
@endsection