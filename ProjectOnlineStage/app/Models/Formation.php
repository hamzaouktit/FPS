<?php


// app/Models/Formation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = [
        'annee',
        'code_efp',
        'niveau',
        'code_filiere',
        'type_formation',
        'creneau',
    ];

    // Relation N-1 : une formation appartient à un établissement
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    // Relation N-1 : une formation appartient à un niveau
    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'niveau', 'niveau');
    }

    // Relation N-1 : une formation appartient à une filière
    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'code_filiere', 'code_filiere');
    }

    // Relation 1-N : une formation peut avoir plusieurs groupes
    public function groupes()
    {
        return $this->hasMany(Groupe::class, 'id_formation');
    }

    // Relation N-N via groupes : une formation peut avoir plusieurs avancements
    public function avancements()
    {
        return $this->hasManyThrough(Avancement::class, Groupe::class, 'id_formation', 'groupe', 'id', 'groupe');
    }

    // Relation N-N via groupes : une formation peut avoir plusieurs affectations
    public function affectations()
    {
        return $this->hasManyThrough(Affectation::class, Groupe::class, 'id_formation', 'groupe', 'id', 'groupe');
    }

    // Relation indirecte : secteur via filière
    public function secteur()
    {
        return $this->hasOneThrough(Secteur::class, Filiere::class, 'code_filiere', 'nom_secteur', 'code_filiere', 'nom_secteur');
    }

    // Relation indirecte : complexe via établissement
    public function complexe()
    {
        return $this->hasOneThrough(Complexe::class, Etablissement::class, 'code_efp', 'id', 'code_efp', 'complexe_id');
    }
}