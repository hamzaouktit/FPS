@extends('layouts.app')

@section('title', 'Détails du Directeur - Système de Pilotage')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('administration.complexe.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('administration.complexe.directeurs.index') }}">Directeurs</a></li>
        <li class="breadcrumb-item active" aria-current="page">Détails</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
    <h2 class="h3 mb-0"><i class="fas fa-user me-2"></i>Détails du Directeur : {{ $directeur->nom }}</h2>
    <div>
        <a href="{{ route('administration.complexe.directeurs.edit', $directeur) }}" class="btn btn-warning me-2" style="background: linear-gradient(45deg, var(--ofppt-green), #2E8B57); border: none; transition: all 0.3s ease;">Modifier</a>
        <a href="{{ route('administration.complexe.directeurs.index') }}" class="btn btn-secondary" style="transition: all 0.3s ease;">Retour</a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card fade-in-up" style="border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div class="card-header" style="background: var(--ofppt-blue); color: white;">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations Personnelles</h5>
            </div>
            <div class="card-body">
                <p><strong>Nom :</strong> {{ $directeur->nom }}</p>
                <p><strong>Email :</strong> {{ $directeur->email }}</p>
                <p><strong>Rôle :</strong>
                    <span class="badge" style="background: var(--ofppt-blue);">
                        <i class="fas fa-school me-1"></i>Directeur d'Établissement
                    </span>
                </p>
                <p><strong>Créé le :</strong> {{ $directeur->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card fade-in-up" style="border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <div class="card-header" style="background: var(--ofppt-green); color: white;">
                <h5 class="mb-0"><i class="fas fa-building me-2"></i>Association</h5>
            </div>
            <div class="card-body">
                <p><strong>Établissement :</strong>
                    @if($directeur->etablissement)
                        <span style="color: var(--ofppt-green);">{{ $directeur->etablissement->nom_efp }}</span>
                    @else
                        <span class="text-muted">Aucun établissement associé (à faire via Gestion des Établissements)</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-warning:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(46, 139, 87, 0.3); }
    .btn-secondary:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); }
</style>
@endsection