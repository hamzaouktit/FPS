<?php

// ========================================
// 2. Model Avancement - Ajout relation établissement
// ========================================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avancement extends Model
{
    use HasFactory;

    protected $fillable = [
        'affectation_id',
        'code_efp',
        'mh_realisee_presentiel',
        'mh_realisee_sync',
        'mh_realisee_globale',
        'taux_realisation_presentiel',
        'taux_realisation_syn',
        'taux_realisation_globale',
        'moyenne_absence',
        'nb_cc',
        'seance_efm',
        'validation_efm',
        'classe_teams',
        'date_maj'
    ];

    protected $casts = [
        'mh_realisee_presentiel' => 'decimal:2',
        'mh_realisee_sync' => 'decimal:2',
        'mh_realisee_globale' => 'decimal:2',
        'taux_realisation_presentiel' => 'decimal:2',
        'taux_realisation_syn' => 'decimal:2',
        'taux_realisation_globale' => 'decimal:2',
        'moyenne_absence' => 'decimal:2',
        'nb_cc' => 'integer',
        'date_maj' => 'date'
    ];

    // Relations
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function affectation()
    {
        return $this->belongsTo(Affectation::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($avancement) {
            $affectation = $avancement->affectation;

            $avancement->mh_realisee_globale = 
                $avancement->mh_realisee_presentiel + 
                $avancement->mh_realisee_sync;

            if ($affectation->mh_affectee_presentiel > 0) {
                $avancement->taux_realisation_presentiel = 
                    ($avancement->mh_realisee_presentiel / $affectation->mh_affectee_presentiel) * 100;
            }

            if ($affectation->mh_affectee_sync > 0) {
                $avancement->taux_realisation_syn = 
                    ($avancement->mh_realisee_sync / $affectation->mh_affectee_sync) * 100;
            }

            if ($affectation->mh_affectee_globale > 0) {
                $avancement->taux_realisation_globale = 
                    ($avancement->mh_realisee_globale / $affectation->mh_affectee_globale) * 100;
            }
        });
    }

    public function scopeValide($query)
    {
        return $query->where('validation_efm', 'oui');
    }

    public function scopeAvecEfm($query)
    {
        return $query->where('seance_efm', 'Oui');
    }
}
