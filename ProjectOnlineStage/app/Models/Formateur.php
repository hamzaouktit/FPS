<?php


// ========================================
// 4. Model Formateur - Ajout relation établissement
// ========================================


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formateur extends Model
{
    use HasFactory;

    protected $fillable = [
        'mle',
        'nom_complet',
        'type',
        'code_efp'
    ];

    // Relations
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

    public function affectationsSynchrone()
    {
        return $this->hasMany(Affectation::class, 'mle_affecte_syn', 'mle');
    }

    public function affectations()
    {
        return Affectation::where('mle_affecte_presentiel', $this->mle)
            ->orWhere('mle_affecte_syn', $this->mle)
            ->get();
    }

    public function scopePermanent($query)
    {
        return $query->where('type', 'permanent');
    }

    public function scopeVacataire($query)
    {
        return $query->where('type', 'vacataire');
    }
}

