<?php

// app/Models/Secteur.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secteur extends Model
{
    use HasFactory;

    protected $primaryKey = 'nom_secteur';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'nom_secteur',
    ];

    // Relation 1-N : un secteur peut avoir plusieurs filières
    public function filieres()
    {
        return $this->hasMany(Filiere::class, 'nom_secteur', 'nom_secteur');
    }

    // Relation N-N via filières : un secteur peut avoir plusieurs formations
    public function formations()
    {
        return $this->hasManyThrough(Formation::class, Filiere::class, 'nom_secteur', 'code_filiere', 'nom_secteur', 'code_filiere');
    }
}
