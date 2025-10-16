@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Modifier l'Avancement</h3>
                    <a href="{{ route('administration.etablissement.avancements.index') }}" class="btn btn-default float-right">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                <form action="{{ route('administration.etablissement.avancements.update', $avancement) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        @php
                            $affectation = $avancement->affectation;
                            $groupe = $affectation->groupe;
                            $module = $affectation->module;
                        @endphp

                        <div class="alert alert-info">
                            <strong>Groupe:</strong> {{ $groupe->code_groupe }} | 
                            <strong>Module:</strong> {{ $module->nom_module }} | 
                            <strong>Formateur Présentiel:</strong> {{ $affectation->formateurPresentiel->nom_complet ?? 'N/A' }}
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mh_realisee_presentiel">MH Réalisée Présentiel *</label>
                                    <input type="number" step="0.01" name="mh_realisee_presentiel" id="mh_realisee_presentiel" 
                                           class="form-control @error('mh_realisee_presentiel') is-invalid @enderror" 
                                           value="{{ old('mh_realisee_presentiel', $avancement->mh_realisee_presentiel) }}" required>
                                    @error('mh_realisee_presentiel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mh_realisee_sync">MH Réalisée Sync *</label>
                                    <input type="number" step="0.01" name="mh_realisee_sync" id="mh_realisee_sync" 
                                           class="form-control @error('mh_realisee_sync') is-invalid @enderror" 
                                           value="{{ old('mh_realisee_sync', $avancement->mh_realisee_sync) }}" required>
                                    @error('mh_realisee_sync')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="mh_realisee_globale">MH Réalisée Globale *</label>
                                    <input type="number" step="0.01" name="mh_realisee_globale" id="mh_realisee_globale" 
                                           class="form-control @error('mh_realisee_globale') is-invalid @enderror" 
                                           value="{{ old('mh_realisee_globale', $avancement->mh_realisee_globale) }}" required>
                                    @error('mh_realisee_globale')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="taux_realisation_presentiel">Taux Réalisation Présentiel (%) *</label>
                                    <input type="number" step="0.01" name="taux_realisation_presentiel" id="taux_realisation_presentiel" 
                                           class="form-control @error('taux_realisation_presentiel') is-invalid @enderror" 
                                           value="{{ old('taux_realisation_presentiel', $avancement->taux_realisation_presentiel) }}" min="0" max="100" required>
                                    @error('taux_realisation_presentiel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="taux_realisation_syn">Taux Réalisation Syn (%) *</label>
                                    <input type="number" step="0.01" name="taux_realisation_syn" id="taux_realisation_syn" 
                                           class="form-control @error('taux_realisation_syn') is-invalid @enderror" 
                                           value="{{ old('taux_realisation_syn', $avancement->taux_realisation_syn) }}" min="0" max="100" required>
                                    @error('taux_realisation_syn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="taux_realisation_globale">Taux Réalisation Globale (%) *</label>
                                    <input type="number" step="0.01" name="taux_realisation_globale" id="taux_realisation_globale" 
                                           class="form-control @error('taux_realisation_globale') is-invalid @enderror" 
                                           value="{{ old('taux_realisation_globale', $avancement->taux_realisation_globale) }}" min="0" max="100" required>
                                    @error('taux_realisation_globale')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="moyenne_absence">Moyenne Absence (%) *</label>
                                    <input type="number" step="0.01" name="moyenne_absence" id="moyenne_absence" 
                                           class="form-control @error('moyenne_absence') is-invalid @enderror" 
                                           value="{{ old('moyenne_absence', $avancement->moyenne_absence) }}" min="0" max="100" required>
                                    @error('moyenne_absence')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nb_cc">Nombre CC *</label>
                                    <input type="number" name="nb_cc" id="nb_cc" 
                                           class="form-control @error('nb_cc') is-invalid @enderror" 
                                           value="{{ old('nb_cc', $avancement->nb_cc) }}" min="0" required>
                                    @error('nb_cc')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="date_maj">Date MAJ *</label>
                                    <input type="date" name="date_maj" id="date_maj" 
                                           class="form-control @error('date_maj') is-invalid @enderror" 
                                           value="{{ old('date_maj', $avancement->date_maj->format('Y-m-d')) }}" required>
                                    @error('date_maj')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="seance_efm">Séance EFM *</label>
                                    <select name="seance_efm" id="seance_efm" class="form-control @error('seance_efm') is-invalid @enderror" required>
                                        <option value="Oui" {{ old('seance_efm', $avancement->seance_efm) == 'Oui' ? 'selected' : '' }}>Oui</option>
                                        <option value="Non" {{ old('seance_efm', $avancement->seance_efm) == 'Non' ? 'selected' : '' }}>Non</option>
                                    </select>
                                    @error('seance_efm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="validation_efm">Validation EFM *</label>
                                    <select name="validation_efm" id="validation_efm" class="form-control @error('validation_efm') is-invalid @enderror" required>
                                        <option value="oui" {{ old('validation_efm', $avancement->validation_efm) == 'oui' ? 'selected' : '' }}>Oui</option>
                                        <option value="non" {{ old('validation_efm', $avancement->validation_efm) == 'non' ? 'selected' : '' }}>Non</option>
                                    </select>
                                    @error('validation_efm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="classe_teams">Classe Teams</label>
                                    <input type="text" name="classe_teams" id="classe_teams" 
                                           class="form-control @error('classe_teams') is-invalid @enderror" 
                                           value="{{ old('classe_teams', $avancement->classe_teams) }}">
                                    @error('classe_teams')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                        <a href="{{ route('administration.etablissement.avancements.index') }}" class="btn btn-default">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection