<?php

// ========================================
// 5. Model Formation - Ajout relation établissement
// ========================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Formation extends Model
{
    protected $fillable = [
        'annee', 'filiere_id', 'niveau_id', 'type', 
        'mode', 'creneau', 'code_efp'
    ];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function groupes()
    {
        return $this->hasMany(Groupe::class);
    }
}
