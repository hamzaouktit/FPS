@extends('layouts.app')

@section('title', 'Gestion des Groupes')

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ route('administration.etablissement.dashboard') }}">
                <i class="fas fa-home"></i> Tableau de bord
            </a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-users"></i> Groupes
        </li>
    </ol>
</nav>
@endsection

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            <i class="fas fa-users me-2"></i>
            Liste des Groupes de mon Établissement
        </h4>
        <a href="{{ route('administration.etablissement.groupes.create') }}" class="btn btn-light btn-sm">
            <i class="fas fa-plus-circle me-1"></i>
            Nouveau Groupe
        </a>
    </div>

    <div class="card-body">
        <!-- Formulaire de filtrage -->
        <div class="card mb-4 border-0 bg-light">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>
                        Filtres de recherche
                    </h5>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>

                <div class="collapse show" id="filterCollapse">
                    <form method="GET" action="{{ route('administration.etablissement.groupes.index') }}" id="filterForm">
                        <div class="row g-3">
                            <!-- Code Groupe -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-hashtag me-1"></i> Code Groupe
                                </label>
                                <input type="text" 
                                       name="code_groupe" 
                                       class="form-control" 
                                       placeholder="Rechercher un code..."
                                       value="{{ request('code_groupe') }}">
                            </div>

                            <!-- Secteur -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-industry me-1"></i> Secteur
                                </label>
                                <select name="secteur_id" class="form-select" id="secteurFilter">
                                    <option value="">Tous les secteurs</option>
                                    @foreach($secteurs as $secteur)
                                        <option value="{{ $secteur->id }}" {{ request('secteur_id') == $secteur->id ? 'selected' : '' }}>
                                            {{ $secteur->nom_secteur }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filière -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-stream me-1"></i> Filière
                                </label>
                                <select name="filiere_id" class="form-select" id="filiereFilter">
                                    <option value="">Toutes les filières</option>
                                    @foreach($filieres as $filiere)
                                        <option value="{{ $filiere->id }}" 
                                                data-secteur="{{ $filiere->secteur_id }}"
                                                {{ request('filiere_id') == $filiere->id ? 'selected' : '' }}>
                                            {{ $filiere->nom_filiere }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Niveau -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-layer-group me-1"></i> Niveau
                                </label>
                                <select name="niveau_id" class="form-select">
                                    <option value="">Tous les niveaux</option>
                                    @foreach($niveaux as $niveau)
                                        <option value="{{ $niveau->id }}" {{ request('niveau_id') == $niveau->id ? 'selected' : '' }}>
                                            {{ $niveau->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Année de Formation -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-calendar me-1"></i> Année Formation
                                </label>
                                <select name="annee_formation" class="form-select">
                                    <option value="">Toutes les années</option>
                                    <option value="1" {{ request('annee_formation') == '1' ? 'selected' : '' }}>1ère année</option>
                                    <option value="2" {{ request('annee_formation') == '2' ? 'selected' : '' }}>2ème année</option>
                                </select>
                            </div>

                            <!-- Type de Formation -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-graduation-cap me-1"></i> Type Formation
                                </label>
                                <select name="type_formation" class="form-select">
                                    <option value="">Tous les types</option>
                                    @foreach($typesFormation as $type)
                                        <option value="{{ $type }}" {{ request('type_formation') == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Statut -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-toggle-on me-1"></i> Statut
                                </label>
                                <select name="statut" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="Actif" {{ request('statut') == 'Actif' ? 'selected' : '' }}>Actif</option>
                                    <option value="Inactif" {{ request('statut') == 'Inactif' ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>

                            <!-- Tri -->
                            <div class="col-md-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-sort me-1"></i> Trier par
                                </label>
                                <select name="sort_by" class="form-select">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date de création</option>
                                    <option value="code_groupe" {{ request('sort_by') == 'code_groupe' ? 'selected' : '' }}>Code groupe</option>
                                    <option value="annee_formation" {{ request('sort_by') == 'annee_formation' ? 'selected' : '' }}>Année formation</option>
                                    <option value="effectif_groupe" {{ request('sort_by') == 'effectif_groupe' ? 'selected' : '' }}>Effectif</option>
                                    <option value="statut" {{ request('sort_by') == 'statut' ? 'selected' : '' }}>Statut</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i> Rechercher
                            </button>
                            <a href="{{ route('administration.etablissement.groupes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Réinitialiser
                            </a>
                            @if(request()->hasAny(['code_groupe', 'secteur_id', 'filiere_id', 'niveau_id', 'annee_formation', 'statut', 'type_formation']))
                                <span class="badge bg-info align-self-center">
                                    Filtres actifs
                                </span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Résumé des résultats -->
        @if(!$groupes->isEmpty())
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>{{ $groupes->total() }}</strong> groupe(s) trouvé(s)
                </span>
                <span class="text-muted">Page {{ $groupes->currentPage() }} sur {{ $groupes->lastPage() }}</span>
            </div>
        @endif

        @if($groupes->isEmpty())
            <div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                <p class="mb-0">
                    @if(request()->hasAny(['code_groupe', 'secteur_id', 'filiere_id', 'niveau_id', 'annee_formation', 'statut', 'type_formation']))
                        Aucun groupe ne correspond aux critères de recherche.
                    @else
                        Aucun groupe enregistré pour votre établissement.
                    @endif
                </p>
                @if(request()->hasAny(['code_groupe', 'secteur_id', 'filiere_id', 'niveau_id', 'annee_formation', 'statut', 'type_formation']))
                    <a href="{{ route('administration.etablissement.groupes.index') }}" class="btn btn-secondary mt-3">
                        <i class="fas fa-redo me-1"></i> Réinitialiser les filtres
                    </a>
                @else
                    <a href="{{ route('administration.etablissement.groupes.create') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus-circle me-1"></i> Ajouter un premier groupe
                    </a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-hashtag me-1"></i> Code</th>
                            <th><i class="fas fa-stream me-1"></i> Filière</th>
                            <th><i class="fas fa-layer-group me-1"></i> Niveau</th>
                            <th><i class="fas fa-graduation-cap me-1"></i> Formation</th>
                            <th><i class="fas fa-user-graduate me-1"></i> Effectif</th>
                            <th><i class="fas fa-calendar me-1"></i> Année</th>
                            <th><i class="fas fa-toggle-on me-1"></i> Statut</th>
                            <th class="text-center"><i class="fas fa-cogs me-1"></i> Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupes as $groupe)
                        <tr>
                            <td>
                                <strong>{{ $groupe->code_groupe }}</strong>
                                @if($groupe->sous_groupe)
                                    <br><small class="text-muted">Sous-groupe: {{ $groupe->sous_groupe }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $groupe->filiere->nom_filiere ?? 'N/A' }}
                                <br>
                                <small class="text-muted">{{ $groupe->filiere->secteur->nom_secteur ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $groupe->formation->niveau->nom ?? 'N/A' }}</span>
                            </td>
                            <td>
                                {{ $groupe->formation->type ?? 'N/A' }}
                                <br>
                                <small class="text-muted">{{ $groupe->formation->mode ?? 'N/A' }} - {{ $groupe->formation->creneau ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $groupe->effectif_groupe }} stagiaires</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $groupe->annee_formation }}</span>
                            </td>
                            <td>
                                @if($groupe->statut === 'Actif')
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('administration.etablissement.groupes.show', $groupe->id) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('administration.etablissement.groupes.edit', $groupe->id) }}" 
                                       class="btn btn-sm btn-warning" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete('{{ $groupe->id }}', '{{ $groupe->code_groupe }}')"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Navigation de la pagination">
                    {{ $groupes->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmation de suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le groupe <strong id="groupeNameToDelete"></strong> ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Cette action est irréversible. Assurez-vous qu'aucune affectation n'est liée à ce groupe.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Annuler
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(groupeId, groupeName) {
        document.getElementById('groupeNameToDelete').textContent = groupeName;
        document.getElementById('deleteForm').action = 
            "{{ route('administration.etablissement.groupes.destroy', ':groupe') }}".replace(':groupe', groupeId);
        
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    // Filtrage dynamique des filières par secteur
    document.getElementById('secteurFilter').addEventListener('change', function() {
        const secteurId = this.value;
        const filiereSelect = document.getElementById('filiereFilter');
        const options = filiereSelect.querySelectorAll('option');
        
        options.forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }
            
            const optionSecteur = option.getAttribute('data-secteur');
            if (secteurId === '' || optionSecteur === secteurId) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
        
        // Réinitialiser la sélection si la filière n'est plus visible
        if (filiereSelect.selectedOptions[0] && filiereSelect.selectedOptions[0].style.display === 'none') {
            filiereSelect.value = '';
        }
    });

    // Déclencher le filtrage au chargement si un secteur est sélectionné
    if (document.getElementById('secteurFilter').value) {
        document.getElementById('secteurFilter').dispatchEvent(new Event('change'));
    }
</script>
@endpush

@push('styles')
<style>
    #filterCollapse {
        transition: all 0.3s ease;
    }
    
    .table-responsive {
        border-radius: 0.5rem;
    }
    
    .form-select, .form-control {
        border-radius: 0.375rem;
    }
    
    .badge {
        font-weight: 500;
    }
    
    /* Styles de pagination personnalisés OFPPT */
    .pagination {
        margin: 0;
        gap: 5px;
    }
    
    .pagination .page-item {
        margin: 0 2px;
    }
    
    .pagination .page-link {
        color: #1E5F99;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 8px 12px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .pagination .page-link:hover {
        color: #fff;
        background: linear-gradient(135deg, #1E5F99 0%, #1a4a75 100%);
        border-color: #1E5F99;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(30, 95, 153, 0.3);
    }
    
    .pagination .page-item.active .page-link {
        color: #fff;
        background: linear-gradient(135deg, #2E8B57 0%, #257045 100%);
        border-color: #2E8B57;
        box-shadow: 0 4px 12px rgba(46, 139, 87, 0.4);
        font-weight: 600;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    .pagination .page-link:focus {
        box-shadow: 0 0 0 0.2rem rgba(30, 95, 153, 0.25);
        outline: none;
    }
    
    /* Styles spécifiques pour les boutons Previous/Next */
    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        font-weight: 600;
        padding: 8px 15px;
    }
    
    .pagination .page-item:first-child .page-link:hover,
    .pagination .page-item:last-child .page-link:hover {
        background: linear-gradient(135deg, #2E8B57 0%, #257045 100%);
        border-color: #2E8B57;
    }
</style>
@endpush