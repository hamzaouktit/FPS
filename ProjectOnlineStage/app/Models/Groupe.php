<?php


// app/Models/Groupe.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    use HasFactory;

    protected $primaryKey = 'groupe';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'groupe',
        'id_formation',
        'effectif_groupe',
        'sous_groupe',
        'statut_sous_groupe',
        'fusion_groupe',
        'code_fusion',
        'annee_formation',
    ];

    // Relation N-1 : un groupe appartient à une formation
    public function formation()
    {
        return $this->belongsTo(Formation::class, 'id_formation');
    }

    // Relation 1-N : un groupe peut avoir plusieurs avancements
    public function avancements()
    {
        return $this->hasMany(Avancement::class, 'groupe', 'groupe');
    }

    // Relation 1-N : un groupe peut avoir plusieurs affectations
    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'groupe', 'groupe');
    }

    // Relation N-N : un groupe peut être lié à plusieurs modules via avancements
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'avancements', 'groupe', 'code_module');
    }

    // Relation N-N : un groupe peut être lié à plusieurs formateurs via avancements
    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class, 'avancements', 'groupe', 'mle_presentiel')
                    ->orWhereColumn('avancements.mle_syn', 'formateurs.mle');
    }

    // Relations indirectes via formation
    public function etablissement()
    {
        return $this->hasOneThrough(Etablissement::class, Formation::class, 'id', 'code_efp', 'id_formation', 'code_efp');
    }

    public function filiere()
    {
        return $this->hasOneThrough(Filiere::class, Formation::class, 'id', 'code_filiere', 'id_formation', 'code_filiere');
    }

    public function niveau()
    {
        return $this->hasOneThrough(Niveau::class, Formation::class, 'id', 'niveau', 'id_formation', 'niveau');
    }
}

