<?php


// ========================================
// 9. Model Secteur - Ajout relation établissement
// ========================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Secteur extends Model
{
    protected $fillable = ['nom_secteur', 'code_efp'];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function filieres()
    {
        return $this->hasMany(Filiere::class);
    }

    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class, 'formateur_secteur');
    }
}
