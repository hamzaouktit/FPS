<?php

// ========================================
// 6. Model Groupe - Ajout relation établissement
// ========================================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Groupe extends Model
{
    protected $fillable = [
        'code_groupe', 'effectif_groupe', 'statut', 'sous_groupe',
        'statut_sous_groupe', 'annee_formation', 'filiere_id', 
        'formation_id', 'code_efp'
        // ❌ SUPPRIMÉ: 'fusion_groupe', 'code_fusion'
    ];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class);
    }
}