<?php

// ========================================
// 3. Model Filiere - Ajout relation établissement
// ========================================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Filiere extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'nom',
        'secteur_id',
        'niveau_id',
        'code_efp'
    ];

    // Relations
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function secteur()
    {
        return $this->belongsTo(Secteur::class);
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function groupes()
    {
        return $this->hasMany(Groupe::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }
}