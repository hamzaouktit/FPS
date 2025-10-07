@extends('layouts.app')

@section('title', 'Créer un Directeur - Système de Pilotage')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('administration.complexe.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('administration.complexe.directeurs.index') }}">Directeurs</a></li>
        <li class="breadcrumb-item active" aria-current="page">Créer</li>
    </ol>
</nav>
@endsection

@section('content')
<div class="card fade-in-up" style="border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
    <div class="card-body">
        <h2 class="h3 mb-4"><i class="fas fa-user-plus me-2"></i>Créer un Nouveau Directeur</h2>

        <form action="{{ route('administration.complexe.directeurs.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom" class="form-label" style="color: var(--ofppt-blue);">Nom Complet <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required style="transition: all 0.3s ease;">
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label" style="color: var(--ofppt-blue);">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required style="transition: all 0.3s ease;">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label" style="color: var(--ofppt-blue);">Mot de Passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required style="transition: all 0.3s ease;">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label" style="color: var(--ofppt-blue);">Confirmer le Mot de Passe <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required style="transition: all 0.3s ease;">
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <a href="{{ route('administration.complexe.directeurs.index') }}" class="btn btn-secondary me-2" style="transition: all 0.3s ease;">Annuler</a>
                <button type="submit" class="btn btn-primary" style="background: linear-gradient(45deg, var(--ofppt-blue), var(--ofppt-dark-blue)); border: none; transition: all 0.3s ease;">Créer le Directeur</button>
            </div>
        </form>
    </div>
</div>

<style>
    .form-control:focus { border-color: var(--ofppt-blue); box-shadow: 0 0 5px rgba(30, 95, 153, 0.3); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(30, 95, 153, 0.3); }
    .btn-secondary:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); }
</style>
@endsection