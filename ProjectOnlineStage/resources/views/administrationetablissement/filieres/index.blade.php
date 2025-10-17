@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 d-inline-block fw-bold">Gestion des Filières</h1>
            <p class="text-muted">{{ auth()->user()->etablissement->nom_efp }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('administration.etablissement.filieres.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter une Filière
            </a>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Rechercher..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="secteur_id" class="form-select">
                        <option value="">Tous les secteurs</option>
                        @foreach($secteurs as $secteur)
                            <option value="{{ $secteur->id }}" 
                                    @if(request('secteur_id') == $secteur->id) selected @endif>
                                {{ $secteur->nom_secteur }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($filieres->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">Code Filière</th>
                                <th width="30%">Nom Filière</th>
                                <th width="20%">Secteur</th>
                                <th width="15%">Groupes</th>
                                <th width="10%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($filieres as $filiere)
                                <tr>
                                    <td class="fw-bold text-primary">{{ $filiere->id }}</td>
                                    <td>{{ $filiere->code_filiere }}</td>
                                    <td>{{ $filiere->nom_filiere }}</td>
                                    <td>{{ $filiere->secteur->nom_secteur }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $filiere->groupes->count() }}</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons-container">
                                            <div class="action-buttons">
                                                <a href="{{ route('administration.etablissement.filieres.show', $filiere->id) }}" 
                                                   class="action-btn action-btn-view" 
                                                   data-bs-toggle="tooltip" 
                                                   data-bs-placement="top" 
                                                   title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                    <span class="action-label">Voir</span>
                                                </a>
                                                <a href="{{ route('administration.etablissement.filieres.edit', $filiere->id) }}" 
                                                   class="action-btn action-btn-edit" 
                                                   data-bs-toggle="tooltip" 
                                                   data-bs-placement="top" 
                                                   title="Modifier la filière">
                                                    <i class="fas fa-edit"></i>
                                                    <span class="action-label">Modifier</span>
                                                </a>
                                                <button type="button" 
                                                        class="action-btn action-btn-delete" 
                                                        onclick="confirmDelete({{ $filiere->id }})" 
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top" 
                                                        title="Supprimer la filière">
                                                    <i class="fas fa-trash-alt"></i>
                                                    <span class="action-label">Supprimer</span>
                                                </button>
                                                <form id="delete-form-{{ $filiere->id }}" 
                                                      action="{{ route('administration.etablissement.filieres.destroy', $filiere->id) }}" 
                                                      method="POST" 
                                                      style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $filieres->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="alert alert-info text-center" role="alert">
                    <i class="fas fa-info-circle"></i> Aucune filière trouvée
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Conteneur des actions */
.action-buttons-container {
    display: flex;
    justify-content: center;
    align-items: center;
}

.action-buttons {
    display: inline-flex;
    gap: 8px;
    padding: 4px;
    background: #f8f9fa;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Boutons d'action stylisés */
.action-btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    text-decoration: none;
}

.action-btn i {
    position: relative;
    z-index: 2;
    transition: transform 0.3s ease;
}

.action-btn .action-label {
    position: absolute;
    left: 50%;
    bottom: -25px;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.8);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: all 0.3s ease;
    z-index: 1000;
}

.action-btn .action-label::before {
    content: '';
    position: absolute;
    top: -4px;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 0;
    border-left: 4px solid transparent;
    border-right: 4px solid transparent;
    border-bottom: 4px solid rgba(0, 0, 0, 0.8);
}

/* Bouton Voir (Info) */
.action-btn-view {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    color: #1976d2;
}

.action-btn-view:hover {
    background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(25, 118, 210, 0.4);
}

/* Bouton Modifier (Warning) */
.action-btn-edit {
    background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
    color: #f57c00;
}

.action-btn-edit:hover {
    background: linear-gradient(135deg, #f57c00 0%, #ef6c00 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 124, 0, 0.4);
}

/* Bouton Supprimer (Danger) */
.action-btn-delete {
    background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
    color: #d32f2f;
}

.action-btn-delete:hover {
    background: linear-gradient(135deg, #d32f2f 0%, #c62828 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(211, 47, 47, 0.4);
}

/* Animation de l'icône au survol */
.action-btn:hover i {
    transform: scale(1.15);
}

/* Effet de pulsation au survol */
.action-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.3s ease, height 0.3s ease;
}

.action-btn:hover::before {
    width: 100%;
    height: 100%;
}

/* Afficher le label au survol */
.action-btn:hover .action-label {
    opacity: 1;
    bottom: -30px;
}

/* Animation lors du clic */
.action-btn:active {
    transform: translateY(0) scale(0.95);
}

/* Style responsive */
@media (max-width: 768px) {
    .action-buttons {
        gap: 6px;
        padding: 3px;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        font-size: 12px;
    }
    
    .action-btn .action-label {
        display: none;
    }
}

/* Animation d'entrée */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.action-buttons {
    animation: fadeInUp 0.3s ease;
}

/* Style du tableau pour compléter */
.table tbody tr {
    transition: background-color 0.2s ease;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.table tbody tr:hover .action-buttons-container {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}
</style>

<script>
    function confirmDelete(id) {
        // Confirmation stylisée avec SweetAlert2 (si disponible) ou alert standard
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Cette action est irréversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer!',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        } else {
            // Fallback vers confirm standard
            if (confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette filière ?\n\nCette action est irréversible!')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    }

    // Initialiser les tooltips Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection