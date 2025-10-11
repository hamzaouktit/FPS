@extends('layouts.app')

    @section('title', 'Détails de la Filière')

    @section('breadcrumb')
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.filieres.index') }}">Filières</a></li>
                <li class="breadcrumb-item active" aria-current="page">Détails</li>
            </ol>
        </nav>
    @endsection

    @section('content')
        <h2><i class="fas fa-stream me-2"></i>Détails de la Filière</h2>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Informations Générales
            </div>
            <div class="card-body">
                <p><strong>Code Filière :</strong> {{ $filiere->code_filiere }}</p>
                <p><strong>Nom Filière :</strong> {{ $filiere->nom_filiere }}</p>
                <p><strong>Secteur :</strong> {{ $filiere->secteur->nom_secteur ?? 'N/A' }}</p>
                <p><strong>Établissement :</strong> {{ $filiere->etablissement->nom_efp ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Formations Associées
            </div>
            <div class="card-body">
                @forelse ($filiere->formations as $formation)
                    <div class="mb-3">
                        <p><strong>Année :</strong> {{ $formation->annee }}</p>
                        <p><strong>Niveau :</strong> {{ $formation->niveau->niveau ?? 'N/A' }}</p>
                        <p><strong>Type de Formation :</strong> {{ $formation->type_formation }}</p>
                        <p><strong>Creneau :</strong> {{ $formation->creneau }}</p>
                        <hr>
                    </div>
                @empty
                    <p>Aucune formation associée.</p>
                @endforelse
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Groupes Associés
            </div>
            <div class="card-body">
                @forelse ($filiere->groupes as $groupe)
                    <div class="mb-3">
                        <p><strong>Groupe :</strong> {{ $groupe->groupe }}</p>
                        <p><strong>Effectif :</strong> {{ $groupe->effectif_groupe }}</p>
                        <p><strong>Année de Formation :</strong> {{ $groupe->annee_formation }}</p>
                        <hr>
                    </div>
                @empty
                    <p>Aucun groupe associé.</p>
                @endforelse
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Modules Associés
            </div>
            <div class="card-body">
                @forelse ($modules as $module)
                    <div class="mb-3">
                        <p><strong>Code Module :</strong> {{ $module->code_module }}</p>
                        <p><strong>Nom Module :</strong> {{ $module->nom_module }}</p>
                        <p><strong>Régional :</strong> {{ $module->regional ? 'Oui' : 'Non' }}</p>
                        <hr>
                    </div>
                @empty
                    <p>Aucun module associé.</p>
                @endforelse
            </div>
        </div>

        <a href="{{ route('administration.etablissement.filieres.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
        <a href="{{ route('administration.etablissement.filieres.edit', $filiere->code_filiere) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Modifier
        </a>
    @endsection