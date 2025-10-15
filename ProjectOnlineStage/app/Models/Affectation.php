<?php
// ========================================
// 1. Model Affectation - Ajout relation établissement
// ========================================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affectation extends Model
{
    use HasFactory;

    protected $fillable = [
        'groupe_id',
        'module_id',
        'code_efp',
        'mle_affecte_presentiel',
        'formateur_affecte_presentiel',
        'mle_affecte_syn',
        'formateur_affecte_syn',
        'mhp_s1_drif',
        'mhsyn_s1_drif',
        'mhasyn_s1_drif',
        'mh_totale_s1_drif',
        'mhp_s2_drif',
        'mhsyn_s2_drif',
        'mhasyn_s2_drif',
        'mh_totale_s2_drif',
        'mhp_totale_drif',
        'mhsyn_totale_drif',
        'mhasyn_totale_drif',
        'mh_totale_drif',
        'mh_affectee_presentiel',
        'mh_affectee_sync',
        'mh_affectee_globale'
    ];

    protected $casts = [
        'mhp_s1_drif' => 'decimal:2',
        'mhsyn_s1_drif' => 'decimal:2',
        'mhasyn_s1_drif' => 'decimal:2',
        'mh_totale_s1_drif' => 'decimal:2',
        'mhp_s2_drif' => 'decimal:2',
        'mhsyn_s2_drif' => 'decimal:2',
        'mhasyn_s2_drif' => 'decimal:2',
        'mh_totale_s2_drif' => 'decimal:2',
        'mhp_totale_drif' => 'decimal:2',
        'mhsyn_totale_drif' => 'decimal:2',
        'mhasyn_totale_drif' => 'decimal:2',
        'mh_totale_drif' => 'decimal:2',
        'mh_affectee_presentiel' => 'decimal:2',
        'mh_affectee_sync' => 'decimal:2',
        'mh_affectee_globale' => 'decimal:2'
    ];

    // Relations
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function formateurPresentiel()
    {
        return $this->belongsTo(Formateur::class, 'mle_affecte_presentiel', 'mle');
    }

    public function formateurSynchrone()
    {
        return $this->belongsTo(Formateur::class, 'mle_affecte_syn', 'mle');
    }

    public function avancement()
    {
        return $this->hasOne(Avancement::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($affectation) {
            $affectation->mh_totale_s1_drif = 
                $affectation->mhp_s1_drif + 
                $affectation->mhsyn_s1_drif + 
                $affectation->mhasyn_s1_drif;

            $affectation->mh_totale_s2_drif = 
                $affectation->mhp_s2_drif + 
                $affectation->mhsyn_s2_drif + 
                $affectation->mhasyn_s2_drif;

            $affectation->mhp_totale_drif = 
                $affectation->mhp_s1_drif + 
                $affectation->mhp_s2_drif;

            $affectation->mhsyn_totale_drif = 
                $affectation->mhsyn_s1_drif + 
                $affectation->mhsyn_s2_drif;

            $affectation->mhasyn_totale_drif = 
                $affectation->mhasyn_s1_drif + 
                $affectation->mhasyn_s2_drif;

            $affectation->mh_totale_drif = 
                $affectation->mh_totale_s1_drif + 
                $affectation->mh_totale_s2_drif;

            $affectation->mh_affectee_globale = 
                $affectation->mh_affectee_presentiel + 
                $affectation->mh_affectee_sync;
        });
    }
}