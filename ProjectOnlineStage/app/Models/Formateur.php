<?php


// ========================================
// 4. Model Formateur - Ajout relation établissement
// ========================================


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formateur extends Model
{
    protected $fillable = ['mle', 'nom_complet', 'type', 'code_efp'];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function secteurs()
    {
        return $this->belongsToMany(Secteur::class, 'formateur_secteur');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'formateur_module');
    }

    public function affectationsPresentiel()
    {
        return $this->hasMany(Affectation::class, 'mle_affecte_presentiel', 'mle');
    }

    public function affectationsSyn()
    {
        return $this->hasMany(Affectation::class, 'mle_affecte_syn', 'mle');
    }
}