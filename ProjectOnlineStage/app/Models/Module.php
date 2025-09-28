<?php


// app/Models/Module.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $primaryKey = 'code_module';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code_module',
        'nom_module',
        'regional',
    ];

    // Relation 1-N : un module peut avoir plusieurs avancements
    public function avancements()
    {
        return $this->hasMany(Avancement::class, 'code_module', 'code_module');
    }

    // Relation 1-N : un module peut avoir plusieurs affectations
    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'code_module', 'code_module');
    }

    // Relation N-N : un module peut être enseigné dans plusieurs groupes
    public function groupes()
    {
        return $this->belongsToMany(Groupe::class, 'avancements', 'code_module', 'groupe');
    }

    // Relation N-N : un module peut être enseigné par plusieurs formateurs
    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class, 'avancements', 'code_module', 'mle_presentiel')
                    ->orWhereColumn('avancements.mle_syn', 'formateurs.mle');
    }
}

