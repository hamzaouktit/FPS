<?php

// ========================================
// 2. Model Avancement - Ajout relation établissement
// ========================================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avancement extends Model
{
    protected $fillable = [
        'affectation_id', 
        'mh_realisee_presentiel', 'mh_realisee_sync', 'mh_realisee_globale',
        'taux_realisation_presentiel', 'taux_realisation_syn', 'taux_realisation_globale',
        'moyenne_absence', 'nb_cc', 'seance_efm', 'validation_efm',
        'classe_teams', 'date_maj', 'code_efp'
    ];

    protected $casts = [
        'date_maj' => 'date'
    ];

    public function affectation()
    {
        return $this->belongsTo(Affectation::class);
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }
}