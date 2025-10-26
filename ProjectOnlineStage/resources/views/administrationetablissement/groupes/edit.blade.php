@extends('layouts.app')

@section('title', 'Modifier le Groupe')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.groupes.index') }}">
                <i class="fas fa-users"></i> Groupes
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
    <div class="col-md-10">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Modifier le Groupe : {{ $groupe->code_groupe }}
                </h4>
            </div>

            <div class="card-body">
                <form action="{{ route('administration.etablissement.groupes.update', $groupe->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="code_groupe" class="form-label">
                                <i class="fas fa-hashtag me-1"></i>
                                Code du Groupe <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('code_groupe') is-invalid @enderror" 
                                   id="code_groupe" 
                                   name="code_groupe" 
                                   value="{{ old('code_groupe', $groupe->code_groupe) }}"
                                   required>
                            @error('code_groupe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="sous_groupe" class="form-label">
                                <i class="fas fa-users me-1"></i>
                                Sous-groupe <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('sous_groupe') is-invalid @enderror" 
                                   id="sous_groupe" 
                                   name="sous_groupe" 
                                   value="{{ old('sous_groupe', $groupe->sous_groupe) }}"
                                   required>
                            @error('sous_groupe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="effectif_groupe" class="form-label">
                                <i class="fas fa-user-graduate me-1"></i>
                                Effectif <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('effectif_groupe') is-invalid @enderror" 
                                   id="effectif_groupe" 
                                   name="effectif_groupe" 
                                   value="{{ old('effectif_groupe', $groupe->effectif_groupe) }}"
                                   min="0"
                                   required>
                            @error('effectif_groupe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="annee_formation" class="form-label">
                                <i class="fas fa-calendar me-1"></i>
                                Année de Formation <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('annee_formation') is-invalid @enderror" 
                                    id="annee_formation" 
                                    name="annee_formation" 
                                    required>
                                <option value="">Sélectionnez une année</option>
                                <option value="1" {{ old('annee_formation', $groupe->annee_formation) == '1' ? 'selected' : '' }}>1ère année</option>
                                <option value="2" {{ old('annee_formation', $groupe->annee_formation) == '2' ? 'selected' : '' }}>2ème année</option>
                            </select>
                            @error('annee_formation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="statut" class="form-label">
                                <i class="fas fa-toggle-on me-1"></i>
                                Statut <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('statut') is-invalid @enderror" 
                                    id="statut" 
                                    name="statut" 
                                    required>
                                <option value="Actif" {{ old('statut', $groupe->statut) == 'Actif' ? 'selected' : '' }}>Actif</option>
                                <option value="Inactif" {{ old('statut', $groupe->statut) == 'Inactif' ? 'selected' : '' }}>Inactif</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="statut_sous_groupe" class="form-label">
                                <i class="fas fa-toggle-on me-1"></i>
                                Statut Sous-groupe <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('statut_sous_groupe') is-invalid @enderror" 
                                    id="statut_sous_groupe" 
                                    name="statut_sous_groupe" 
                                    required>
                                <option value="Actif" {{ old('statut_sous_groupe', $groupe->statut_sous_groupe) == 'Actif' ? 'selected' : '' }}>Actif</option>
                                <option value="Inactif" {{ old('statut_sous_groupe', $groupe->statut_sous_groupe) == 'Inactif' ? 'selected' : '' }}>Inactif</option>
                            </select>
                            @error('statut_sous_groupe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="filiere_id" class="form-label">
                                <i class="fas fa-stream me-1"></i>
                                Filière <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('filiere_id') is-invalid @enderror" 
                                    id="filiere_id" 
                                    name="filiere_id" 
                                    required>
                                <option value="">Sélectionnez une filière</option>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" {{ old('filiere_id', $groupe->filiere_id) == $filiere->id ? 'selected' : '' }}>
                                        {{ $filiere->nom_filiere }} ({{ $filiere->code_filiere }})
                                    </option>
                                @endforeach
                            </select>
                            @error('filiere_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="formation_id" class="form-label">
                                <i class="fas fa-graduation-cap me-1"></i>
                                Formation <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('formation_id') is-invalid @enderror" 
                                    id="formation_id" 
                                    name="formation_id" 
                                    required>
                                <option value="">Sélectionnez une formation</option>
                                @foreach($formations as $formation)
                                    <option value="{{ $formation->id }}" {{ old('formation_id', $groupe->formation_id) == $formation->id ? 'selected' : '' }}>
                                        {{ $formation->type }} - {{ $formation->filiere->nom_filiere }} - {{ $formation->mode }} ({{ $formation->creneau }})
                                    </option>
                                @endforeach
                            </select>
                            @error('formation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if($groupe->affectations()->exists())
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Ce groupe contient {{ $groupe->affectations()->count() }} affectation(s). 
                        Les modifications peuvent impacter les données liées. Les informations de fusion de groupe sont gérées au niveau des affectations.
                    </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note :</strong> Les informations de fusion de groupe (fusion_groupe et code_fusion) sont désormais gérées au niveau des affectations et non plus au niveau du groupe.
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('administration.etablissement.groupes.index') }}" class="btn btn-secondary">
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