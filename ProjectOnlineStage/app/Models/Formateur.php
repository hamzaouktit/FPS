<?php

// app/Models/Formateur.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formateur extends Model
{
    use HasFactory;

    protected $primaryKey = 'mle';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'mle',
        'nom_formateur',
    ];

    // Relation 1-N : un formateur peut avoir plusieurs avancements en présentiel
    public function avancementsPresentiel()
    {
        return $this->hasMany(Avancement::class, 'mle_presentiel', 'mle');
    }

    // Relation 1-N : un formateur peut avoir plusieurs avancements en synchrone
    public function avancementsSynchrone()
    {
        return $this->hasMany(Avancement::class, 'mle_syn', 'mle');
    }

    // Relation 1-N : un formateur peut avoir plusieurs affectations
    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'mle_formateur', 'mle');
    }

    // Relation N-N : un formateur peut enseigner plusieurs modules
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'avancements', 'mle_presentiel', 'code_module')
                    ->orWhereColumn('avancements.mle_syn', 'formateurs.mle');
    }

    // Relation N-N : un formateur peut enseigner plusieurs groupes
    public function groupes()
    {
        return $this->belongsToMany(Groupe::class, 'avancements', 'mle_presentiel', 'groupe')
                    ->orWhereColumn('avancements.mle_syn', 'formateurs.mle');
    }

    // Méthode pour obtenir tous les avancements (présentiel + synchrone)
    public function tousAvancements()
    {
        return Avancement::where('mle_presentiel', $this->mle)
                         ->orWhere('mle_syn', $this->mle);
    }
}
