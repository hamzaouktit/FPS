@extends('layouts.app')

    @section('title', 'Ajouter une Filière')

    @section('breadcrumb')
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.filieres.index') }}">Filières</a></li>
                <li class="breadcrumb-item active" aria-current="page">Ajouter</li>
            </ol>
        </nav>
    @endsection

    @section('content')
        <h2><i class="fas fa-plus-circle me-2"></i>Ajouter une Filière</h2>

        <form action="{{ route('administration.etablissement.filieres.store') }}" method="POST" class="needs-validation" novalidate>
            @csrf

            <div class="mb-3">
                <label for="code_filiere" class="form-label">Code Filière</label>
                <input type="text" class="form-control @error('code_filiere') is-invalid @enderror" id="code_filiere" name="code_filiere" required>
                @error('code_filiere')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="nom_filiere" class="form-label">Nom Filière</label>
                <input type="text" class="form-control @error('nom_filiere') is-invalid @enderror" id="nom_filiere" name="nom_filiere" required>
                @error('nom_filiere')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="nom_secteur" class="form-label">Secteur</label>
                <select class="form-select @error('nom_secteur') is-invalid @enderror" id="nom_secteur" name="nom_secteur" required>
                    <option value="">Sélectionner un secteur</option>
                    @foreach ($secteurs as $secteur)
                        <option value="{{ $secteur->nom_secteur }}">{{ $secteur->nom_secteur }}</option>
                    @endforeach
                </select>
                @error('nom_secteur')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Enregistrer
            </button>
            <a href="{{ route('administration.etablissement.filieres.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Annuler
            </a>
        </form>

        @push('scripts')
            <script>
                // Bootstrap form validation
                (function () {
                    'use strict'
                    var forms = document.querySelectorAll('.needs-validation')
                    Array.prototype.slice.call(forms).forEach(function (form) {
                        form.addEventListener('submit', function (event) {
                            if (!form.checkValidity()) {
                                event.preventDefault()
                                event.stopPropagation()
                            }
                            form.classList.add('was-validated')
                        }, false)
                    })
                })()
            </script>
        @endpush
    @endsection