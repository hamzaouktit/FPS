@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Modifier le Formateur - {{ $formateur->nom_complet }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.formateurs.update', $formateur) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mle">MLE *</label>
                                    <input type="text" class="form-control @error('mle') is-invalid @enderror" 
                                           id="mle" name="mle" value="{{ old('mle', $formateur->mle) }}" required>
                                    @error('mle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom_complet">Nom Complet *</label>
                                    <input type="text" class="form-control @error('nom_complet') is-invalid @enderror" 
                                           id="nom_complet" name="nom_complet" 
                                           value="{{ old('nom_complet', $formateur->nom_complet) }}" required>
                                    @error('nom_complet')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Type *</label>
                                    <select class="form-control @error('type') is-invalid @enderror" 
                                            id="type" name="type" required>
                                        <option value="">Sélectionnez le type</option>
                                        <option value="permanent" {{ old('type', $formateur->type) == 'permanent' ? 'selected' : '' }}>Permanent</option>
                                        <option value="vacataire" {{ old('type', $formateur->type) == 'vacataire' ? 'selected' : '' }}>Vacataire</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="secteurs">Secteurs</label>
                                    <select multiple class="form-control @error('secteurs') is-invalid @enderror" 
                                            id="secteurs" name="secteurs[]" size="5">
                                        @foreach($secteurs as $secteur)
                                            <option value="{{ $secteur->id }}" 
                                                {{ in_array($secteur->id, old('secteurs', $formateur->secteurs->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $secteur->nom_secteur }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('secteurs')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="modules">Modules (Compétences)</label>
                                    <select multiple class="form-control @error('modules') is-invalid @enderror" 
                                            id="modules" name="modules[]" size="5">
                                        @foreach($modules as $module)
                                            <option value="{{ $module->id }}" 
                                                {{ in_array($module->id, old('modules', $formateur->modules->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $module->code_module }} - {{ $module->nom_module }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('modules')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Modifier le Formateur
                            </button>
                            <a href="{{ route('administration.etablissement.formateurs.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection