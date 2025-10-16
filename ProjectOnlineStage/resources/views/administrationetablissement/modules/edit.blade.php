@extends('layouts.app')

@section('title', 'Modifier le Module')

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.dashboard') }}">Tableau de Bord</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.modules.index') }}">Modules</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('administration.etablissement.modules.show', $module->id) }}">{{ $module->code_module }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Modifier</li>
        </ol>
    </nav>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0">
            <i class="fas fa-edit me-2"></i>Modifier le Module
        </h5>
    </div>
    
    <div class="card-body">
        <form action="{{ route('administration.etablissement.modules.update', $module->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="code_module" class="form-label">Code Module *</label>
                        <input type="text" class="form-control @error('code_module') is-invalid @enderror" 
                               id="code_module" name="code_module" 
                               value="{{ old('code_module', $module->code_module) }}" required>
                        @error('code_module')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom_module" class="form-label">Nom du Module *</label>
                        <input type="text" class="form-control @error('nom_module') is-invalid @enderror" 
                               id="nom_module" name="nom_module" 
                               value="{{ old('nom_module', $module->nom_module) }}" required>
                        @error('nom_module')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="regional" class="form-label">Régional *</label>
                        <select class="form-select @error('regional') is-invalid @enderror" 
                                id="regional" name="regional" required>
                            <option value="">Sélectionner</option>
                            <option value="O" {{ old('regional', $module->regional) == 'O' ? 'selected' : '' }}>Oui</option>
                            <option value="N" {{ old('regional', $module->regional) == 'N' ? 'selected' : '' }}>Non</option>
                        </select>
                        @error('regional')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="module_pie" class="form-label">Module PIE *</label>
                        <select class="form-select @error('module_pie') is-invalid @enderror" 
                                id="module_pie" name="module_pie" required>
                            <option value="">Sélectionner</option>
                            <option value="O" {{ old('module_pie', $module->module_pie) == 'O' ? 'selected' : '' }}>Oui</option>
                            <option value="N" {{ old('module_pie', $module->module_pie) == 'N' ? 'selected' : '' }}>Non</option>
                        </select>
                        @error('module_pie')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="efp_pie" class="form-label">EFP PIE</label>
                        <input type="text" class="form-control @error('efp_pie') is-invalid @enderror" 
                               id="efp_pie" name="efp_pie" 
                               value="{{ old('efp_pie', $module->efp_pie) }}">
                        @error('efp_pie')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Filieres Associées</label>
                <div class="border rounded p-3">
                    @if($filieres->count() > 0)
                        <div class="row">
                            @foreach($filieres as $filiere)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="filieres[]" value="{{ $filiere->id }}" 
                                               id="filiere_{{ $filiere->id }}"
                                               {{ in_array($filiere->id, old('filieres', $module->filieres->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="filiere_{{ $filiere->id }}">
                                            {{ $filiere->code_filiere }} - {{ $filiere->nom_filiere }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucune filière disponible dans votre établissement.</p>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('administration.etablissement.modules.show', $module->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Annuler
                </a>
                <div>
                    <a href="{{ route('administration.etablissement.modules.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-list me-2"></i>Retour à la liste
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Mettre à jour
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection