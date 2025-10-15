<?php

// ========================================
// 5. Model Formation - Ajout relation établissement
// ========================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'mode',
        'creneau',
        'code_efp'
    ];

    // Relations
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function groupes()
    {
        return $this->hasMany(Groupe::class);
    }
}
