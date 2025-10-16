@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gestion des Formateurs - {{ $etablissement->nom_efp }}</h5>
                    <a href="{{ route('administration.etablissement.formateurs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau Formateur
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>MLE</th>
                                    <th>Nom Complet</th>
                                    <th>Type</th>
                                    <th>Secteurs</th>
                                    <th>Modules</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($formateurs as $formateur)
                                    <tr>
                                        <td>{{ $formateur->mle }}</td>
                                        <td>{{ $formateur->nom_complet }}</td>
                                        <td>
                                            <span class="badge badge-{{ $formateur->type === 'permanent' ? 'success' : 'warning' }}">
                                                {{ $formateur->type }}
                                            </span>
                                        </td>
                                        <td>
                                            @foreach($formateur->secteurs as $secteur)
                                                <span class="badge badge-info">{{ $secteur->nom_secteur }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @foreach($formateur->modules as $module)
                                                <span class="badge badge-secondary">{{ $module->code_module }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            <a href="{{ route('administration.etablissement.formateurs.show', $formateur) }}" 
                                               class="btn btn-info btn-sm" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('administration.etablissement.formateurs.edit', $formateur) }}" 
                                               class="btn btn-warning btn-sm" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('administration.etablissement.formateurs.destroy', $formateur) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ?')"
                                                        title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucun formateur trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection