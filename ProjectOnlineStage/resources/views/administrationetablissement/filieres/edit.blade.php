@extends('layouts.app')

@section('title', 'Modifier la Filière')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.filieres.index') }}">
                <i class="fas fa-graduation-cap"></i> Filières
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-edit"></i> Modifier
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Modifier la Filière : {{ $filiere->nom_filiere }}
                </h4>
            </div>

            <div class="card-body">
                <form action="{{ route('administration.etablissement.filieres.update', $filiere->code_filiere) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="code_filiere" class="form-label">
                            <i class="fas fa-hashtag me-1"></i>
                            Code de la Filière <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('code_filiere') is-invalid @enderror" 
                               id="code_filiere" 
                               name="code_filiere" 
                               value="{{ old('code_filiere', $filiere->code_filiere) }}"
                               required>
                        @error('code_filiere')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="nom_filiere" class="form-label">
                            <i class="fas fa-graduation-cap me-1"></i>
                            Nom de la Filière <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nom_filiere') is-invalid @enderror" 
                               id="nom_filiere" 
                               name="nom_filiere" 
                               value="{{ old('nom_filiere', $filiere->nom_filiere) }}"
                               required>
                        @error('nom_filiere')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="secteur_id" class="form-label">
                            <i class="fas fa-layer-group me-1"></i>
                            Secteur <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('secteur_id') is-invalid @enderror" 
                                id="secteur_id" 
                                name="secteur_id"
                                required>
                            <option value="">-- Sélectionner un secteur --</option>
                            @foreach($secteurs as $secteur)
                                <option value="{{ $secteur->id }}" 
                                    {{ old('secteur_id', $filiere->secteur_id) == $secteur->id ? 'selected' : '' }}>
                                    {{ $secteur->nom_secteur }}
                                </option>
                            @endforeach
                        </select>
                        @error('secteur_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($filiere->formations()->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Cette filière contient {{ $filiere->formations()->count() }} formation(s). 
                        Les modifications peuvent impacter les données existantes.
                    </div>
                    @endif

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('administration.etablissement.filieres.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>
                            Retour
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i>
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection