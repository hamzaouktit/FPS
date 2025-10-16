@extends('layouts.app')

@section('title', 'Gestion des Modules')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.dashboard') }}">Tableau de Bord</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Modules</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-cubes me-2"></i>Liste des Modules - {{ $etablissement->nom_efp }}
        </h5>
        <a href="{{ route('administration.etablissement.modules.create') }}" class="btn btn-light">
            <i class="fas fa-plus me-2"></i>Nouveau Module
        </a>
    </div>
    
    <div class="card-body">
        @if($modules->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Code Module</th>
                            <th>Nom Module</th>
                            <th>Régional</th>
                            <th>Module PIE</th>
                            <th>Filieres</th>
                            <th>Formateurs</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($modules as $module)
                            <tr>
                                <td>
                                    <strong>{{ $module->code_module }}</strong>
                                </td>
                                <td>{{ $module->nom_module }}</td>
                                <td>
                                    <span class="badge bg-{{ $module->regional == 'O' ? 'success' : 'secondary' }}">
                                        {{ $module->regional == 'O' ? 'Oui' : 'Non' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $module->module_pie == 'O' ? 'warning' : 'secondary' }}">
                                        {{ $module->module_pie == 'O' ? 'Oui' : 'Non' }}
                                    </span>
                                </td>
                                <td>
                                    @if($module->filieres->count() > 0)
                                        <span class="badge bg-info">
                                            {{ $module->filieres->count() }} filière(s)
                                        </span>
                                    @else
                                        <span class="text-muted">Aucune</span>
                                    @endif
                                </td>
                                <td>
                                    @if($module->formateurs->count() > 0)
                                        <span class="badge bg-primary">
                                            {{ $module->formateurs->count() }} formateur(s)
                                        </span>
                                    @else
                                        <span class="text-muted">Aucun</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('administration.etablissement.modules.show', $module->id) }}" 
                                           class="btn btn-info" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('administration.etablissement.modules.edit', $module->id) }}" 
                                           class="btn btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('administration.etablissement.modules.destroy', $module->id) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce module ?')"
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
                Aucun module n'a été créé pour votre établissement.
                <a href="{{ route('administration.etablissement.modules.create') }}" class="alert-link">
                    Créer le premier module
                </a>
            </div>
        @endif
    </div>
</div>
@endsection