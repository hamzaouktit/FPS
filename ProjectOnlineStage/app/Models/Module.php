<?php


// ========================================
// 7. Model Module - Ajout relation établissement
// ========================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Module extends Model
{
    protected $fillable = [
        'code_module', 'nom_module', 'regional', 'module_pie',
        'efp_pie', 'code_efp'
    ];

    // ✅ RELATION MANY-TO-MANY avec Filiere
    public function filieres()
    {
        return $this->belongsToMany(Filiere::class, 'filiere_module')
                    ->withTimestamps();
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class);
    }

    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class, 'formateur_module')
                    ->withTimestamps();
    }
}