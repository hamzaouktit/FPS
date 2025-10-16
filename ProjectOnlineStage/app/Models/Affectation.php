<?php
// ========================================
// 1. Model Affectation - Ajout relation établissement
// ========================================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Affectation extends Model
{
    protected $fillable = [
        'groupe_id', 'module_id', 'code_efp',
        'mle_affecte_presentiel', 'formateur_affecte_presentiel',
        'mle_affecte_syn', 'formateur_affecte_syn',
        'mhp_s1_drif', 'mhsyn_s1_drif', 'mhasyn_s1_drif', 'mh_totale_s1_drif',
        'mhp_s2_drif', 'mhsyn_s2_drif', 'mhasyn_s2_drif', 'mh_totale_s2_drif',
        'mhp_totale_drif', 'mhsyn_totale_drif', 'mhasyn_totale_drif', 'mh_totale_drif',
        'mh_affectee_presentiel', 'mh_affectee_sync', 'mh_affectee_globale'
    ];

    public function groupe()
    {
        return $this->belongsTo(Groupe::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function formateurPresentiel()
    {
        return $this->belongsTo(Formateur::class, 'mle_affecte_presentiel', 'mle');
    }

    public function formateurSyn()
    {
        return $this->belongsTo(Formateur::class, 'mle_affecte_syn', 'mle');
    }

    public function avancement()
    {
        return $this->hasOne(Avancement::class);
    }
}
