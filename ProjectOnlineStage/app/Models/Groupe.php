<?php

// ========================================
// 6. Model Groupe - Ajout relation établissement
// ========================================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'efp_code',
        'efp_nom',
        'effectif',
        'statut',
        'fusion_groupe',
        'code_fusion',
        'annee_formation',
        'annee',
        'filiere_id',
        'formation_id',
        'code_efp'
    ];

    protected $casts = [
        'effectif' => 'integer',
        'annee_formation' => 'integer',
        'annee' => 'integer'
    ];

    // Relations
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class);
    }

    public function getSecteurAttribute()
    {
        return $this->filiere->secteur;
    }

    public function getNiveauAttribute()
    {
        return $this->filiere->niveau;
    }
}

