@extends('layouts.app')

@section('title', 'Créer une Formation')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- En-tête avec breadcrumb -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('administration.etablissement.formations.index') }}">Formations</a></li>
                <li class="breadcrumb-item active">Créer</li>
            </ol>
        </nav>
        <h1 class="h3 mb-2 text-gray-800">Créer une Nouvelle Formation</h1>
        <p class="text-muted">{{ $etablissement->nom_efp }} ({{ $etablissement->code_efp }})</p>
    </div>

    <!-- Messages d'erreur -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreurs de validation</h6>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Formulaire -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations de la Formation</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('administration.etablissement.formations.store') }}" method="POST" id="formationForm">
                        @csrf

                        <!-- Année -->
                        <div class="mb-4">
                            <label for="annee" class="form-label">Année de Formation <span class="text-danger">*</span></label>
                            <input type="number" name="annee" id="annee" 
                                   class="form-control @error('annee') is-invalid @enderror" 
                                   value="{{ old('annee', date('Y')) }}" 
                                   min="2020" max="2030" required>
                            @error('annee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Année académique de la formation
                            </div>
                        </div>

                        <!-- Filière -->
                        <div class="mb-4">
                            <label for="filiere_id" class="form-label">Filière <span class="text-danger">*</span></label>
                            <select name="filiere_id" id="filiere_id" class="form-select @error('filiere_id') is-invalid @enderror" required>
                                <option value="">-- Sélectionnez une filière --</option>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" {{ old('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                        {{ $filiere->code_filiere }} - {{ $filiere->nom_filiere }}
                                    </option>
                                @endforeach
                            </select>
                            @error('filiere_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Filière de formation
                            </div>
                        </div>

                        <!-- Niveau -->
                        <div class="mb-4">
                            <label for="niveau_id" class="form-label">Niveau <span class="text-danger">*</span></label>
                            <select name="niveau_id" id="niveau_id" class="form-select @error('niveau_id') is-invalid @enderror" required>
                                <option value="">-- Sélectionnez un niveau --</option>
                                @foreach($niveaux as $niveau)
                                    <option value="{{ $niveau->id }}" {{ old('niveau_id') == $niveau->id ? 'selected' : '' }}>
                                        {{ $niveau->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('niveau_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Niveau de la formation
                            </div>
                        </div>

                        <!-- Type de formation -->
                        <div class="mb-4">
                            <label for="type" class="form-label">Type de Formation <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="">-- Sélectionnez un type --</option>
                                <option value="Diplômante" {{ old('type') == 'Diplômante' ? 'selected' : '' }}>Diplômante</option>
                                <option value="Qualifiante" {{ old('type') == 'Qualifiante' ? 'selected' : '' }}>Qualifiante</option>
                                <option value="PP" {{ old('type') == 'PP' ? 'selected' : '' }}>PP (Passerelle)</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Type de certification délivrée
                            </div>
                        </div>

                        <!-- Mode de formation -->
                        <div class="mb-4">
                            <label for="mode" class="form-label">Mode de Formation <span class="text-danger">*</span></label>
                            <select name="mode" id="mode" class="form-select @error('mode') is-invalid @enderror" required>
                                <option value="">-- Sélectionnez un mode --</option>
                                <option value="Résidentiel" {{ old('mode') == 'Résidentiel' ? 'selected' : '' }}>Résidentiel</option>
                                <option value="Alterné" {{ old('mode') == 'Alterné' ? 'selected' : '' }}>Alterné</option>
                            </select>
                            @error('mode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Résidentiel : formation en présentiel continu / Alterné : alternance entreprise/centre
                            </div>
                        </div>

                        <!-- Créneau horaire -->
                        <div class="mb-4">
                            <label for="creneau" class="form-label">Créneau Horaire <span class="text-danger">*</span></label>
                            <select name="creneau" id="creneau" class="form-select @error('creneau') is-invalid @enderror" required>
                                <option value="">-- Sélectionnez un créneau --</option>
                                <option value="CDJ" {{ old('creneau') == 'CDJ' ? 'selected' : '' }}>CDJ (Cours de Jour)</option>
                                <option value="CDS" {{ old('creneau') == 'CDS' ? 'selected' : '' }}>CDS (Cours du Soir)</option>
                            </select>
                            @error('creneau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Plage horaire durant laquelle se déroule la formation
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('administration.etablissement.formations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Créer la Formation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panneau d'aide -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-question-circle me-2"></i>Aide
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold">Types de Formation</h6>
                    <ul class="small mb-3">
                        <li><strong>Diplômante :</strong> Formation menant à un diplôme reconnu</li>
                        <li><strong>Qualifiante :</strong> Formation menant à une qualification professionnelle</li>
                        <li><strong>PP :</strong> Passerelle permettant de passer d'un niveau à un autre</li>
                    </ul>

                    <h6 class="font-weight-bold">Modes de Formation</h6>
                    <ul class="small mb-3">
                        <li><strong>Résidentiel :</strong> Formation continue en centre</li>
                        <li><strong>Alterné :</strong> Alternance entre centre de formation et entreprise</li>
                    </ul>

                    <h6 class="font-weight-bold">Créneaux Horaires</h6>
                    <ul class="small mb-0">
                        <li><strong>CDJ :</strong> Cours de Jour (horaires standards)</li>
                        <li><strong>CDS :</strong> Cours du Soir (pour salariés/actifs)</li>
                    </ul>
                </div>
            </div>

            <!-- Aperçu -->
            <div class="card shadow mb-4" id="previewCard" style="display: none;">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-eye me-2"></i>Aperçu
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Année :</small>
                        <div id="preview-annee" class="font-weight-bold">-</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Filière :</small>
                        <div id="preview-filiere" class="font-weight-bold">-</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Niveau :</small>
                        <div id="preview-niveau" class="font-weight-bold">-</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Type :</small>
                        <div id="preview-type" class="font-weight-bold">-</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Mode :</small>
                        <div id="preview-mode" class="font-weight-bold">-</div>
                    </div>
                    <div>
                        <small class="text-muted">Créneau :</small>
                        <div id="preview-creneau" class="font-weight-bold">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const anneeInput = document.getElementById('annee');
    const filiereSelect = document.getElementById('filiere_id');
    const niveauSelect = document.getElementById('niveau_id');
    const typeSelect = document.getElementById('type');
    const modeSelect = document.getElementById('mode');
    const creneauSelect = document.getElementById('creneau');
    const previewCard = document.getElementById('previewCard');
    const previewAnnee = document.getElementById('preview-annee');
    const previewFiliere = document.getElementById('preview-filiere');
    const previewNiveau = document.getElementById('preview-niveau');
    const previewType = document.getElementById('preview-type');
    const previewMode = document.getElementById('preview-mode');
    const previewCreneau = document.getElementById('preview-creneau');

    // Fonction pour mettre à jour l'aperçu
    function updatePreview() {
        const annee = anneeInput.value;
        const filiereText = filiereSelect.options[filiereSelect.selectedIndex]?.text || '-';
        const niveauText = niveauSelect.options[niveauSelect.selectedIndex]?.text || '-';
        const type = typeSelect.value;
        const mode = modeSelect.value;
        const creneau = creneauSelect.value;

        if (annee || filiereSelect.value || niveauSelect.value || type || mode || creneau) {
            previewCard.style.display = 'block';
            previewAnnee.textContent = annee || '-';
            previewFiliere.textContent = filiereText;
            previewNiveau.textContent = niveauText;
            previewType.textContent = type || '-';
            previewMode.textContent = mode || '-';
            previewCreneau.textContent = creneau || '-';
        } else {
            previewCard.style.display = 'none';
        }
    }

    // Écouter les changements
    anneeInput.addEventListener('input', updatePreview);
    filiereSelect.addEventListener('change', updatePreview);
    niveauSelect.addEventListener('change', updatePreview);
    typeSelect.addEventListener('change', updatePreview);
    modeSelect.addEventListener('change', updatePreview);
    creneauSelect.addEventListener('change', updatePreview);

    // Initialiser l'aperçu si des valeurs existent
    updatePreview();

    // Validation du formulaire
    document.getElementById('formationForm').addEventListener('submit', function(e) {
        const annee = anneeInput.value;
        const filiere = filiereSelect.value;
        const niveau = niveauSelect.value;
        const type = typeSelect.value;
        const mode = modeSelect.value;
        const creneau = creneauSelect.value;

        if (!annee || !filiere || !niveau || !type || !mode || !creneau) {
            e.preventDefault();
            Swal.fire({
                title: 'Champs requis',
                text: 'Veuillez remplir tous les champs obligatoires.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return false;
        }
    });
});
</script>
@endpush
@endsection