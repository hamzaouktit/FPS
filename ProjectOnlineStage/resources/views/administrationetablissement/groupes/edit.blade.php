@extends('layouts.app')

@section('title', 'Modifier Groupe')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.groupes.index') }}">Groupes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Modifier</li>
        </ol>
    </nav>
@endsection

@section('content')
    <h2><i class="fas fa-edit me-2"></i>Modifier le Groupe</h2>

    <form action="{{ route('administration.etablissement.groupes.update', $groupe) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="code" class="form-label">Code</label>
                <input type="text" class="form-control" id="code" name="code" value="{{ $groupe->code }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="efp_code" class="form-label">EFP Code</label>
                <input type="text" class="form-control" id="efp_code" value="{{ $etablissement->code_efp }}" readonly>
                <input type="hidden" name="efp_code" value="{{ $etablissement->code_efp }}">
                <small class="form-text text-muted">Rempli automatiquement</small>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="efp_nom" class="form-label">EFP Nom</label>
                <input type="text" class="form-control" id="efp_nom" value="{{ $etablissement->nom_efp }}" readonly>
                <input type="hidden" name="efp_nom" value="{{ $etablissement->nom_efp }}">
                <small class="form-text text-muted">Rempli automatiquement</small>
            </div>
            <div class="col-md-6 mb-3">
                <label for="effectif" class="form-label">Effectif</label>
                <input type="number" class="form-control" id="effectif" name="effectif" value="{{ $groupe->effectif }}" min="0" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="statut" class="form-label">Statut</label>
                <select class="form-select" id="statut" name="statut" required>
                    <option value="Actif" {{ $groupe->statut === 'Actif' ? 'selected' : '' }}>Actif</option>
                    <option value="Inactif" {{ $groupe->statut === 'Inactif' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="fusion_groupe" class="form-label">Fusion Groupe</label>
                <input type="text" class="form-control" id="fusion_groupe" name="fusion_groupe" value="{{ $groupe->fusion_groupe }}">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="code_fusion" class="form-label">Code Fusion</label>
                <input type="text" class="form-control" id="code_fusion" name="code_fusion" value="{{ $groupe->code_fusion }}">
            </div>
            <div class="col-md-6 mb-3">
                <label for="annee_formation" class="form-label">Année Formation</label>
                <input type="number" class="form-control" id="annee_formation" name="annee_formation" value="{{ $groupe->annee_formation }}" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="annee" class="form-label">Année</label>
                <input type="number" class="form-control" id="annee" name="annee" value="{{ $groupe->annee }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="filiere_id" class="form-label">Filière</label>
                <select class="form-select" id="filiere_id" name="filiere_id" required>
                    @foreach ($filieres as $filiere)
                        <option value="{{ $filiere->id }}" {{ $groupe->filiere_id == $filiere->id ? 'selected' : '' }}>{{ $filiere->nom }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="formation_id" class="form-label">Formation</label>
            <select class="form-select" id="formation_id" name="formation_id" required>
                @foreach ($formations as $formation)
                    <option value="{{ $formation->id }}" {{ $groupe->formation_id == $formation->id ? 'selected' : '' }}>{{ $formation->type }} - {{ $formation->mode }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Mettre à jour</button>
        <a href="{{ route('administration.etablissement.groupes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </form>
@endsection