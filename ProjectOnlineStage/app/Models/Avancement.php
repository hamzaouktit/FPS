<?php


// app/Models/Avancement.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Avancement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date_maj',
        'groupe',
        'code_module',
        'mode',
        'mle_presentiel',
        'mle_syn',
        // Heures S1
        'mhp_s1_drif',
        'mhsyn_s1_drif',
        'mhasyn_s1_drif',
        'mh_totale_s1_drif',
        // Heures S2
        'mhp_s2_drif',
        'mhsyn_s2_drif',
        'mhasyn_s2_drif',
        'mh_totale_s2_drif',
        // Totaux DRIF
        'mhp_totale_drif',
        'mhsyn_totale_drif',
        'mhasyn_totale_drif',
        'mh_totale_drif',
        // Affectées et réalisées
        'mh_affectee_presentiel',
        'mh_affectee_sync',
        'mh_affectee_globale',
        'mh_realisee_presentiel',
        'mh_realisee_sync',
        'mh_realisee_globale',
        // Taux et autres
        'taux_realisation_presentiel',
        'taux_realisation_syn',
        'taux_realisation_global',
        'moy_absence',
        'nb_cc',
        'seance_efm',
        'validation_efm',
        'classe_teams',
        'module_pie',
        'efp_pie',
    ];

    protected $casts = [
        'date_maj' => 'datetime',
    ];

    // Relation N-1 : un avancement appartient à un groupe
    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'groupe', 'groupe');
    }

    // Relation N-1 : un avancement appartient à un module
    public function module()
    {
        return $this->belongsTo(Module::class, 'code_module', 'code_module');
    }

    // Relation N-1 : un avancement peut avoir un formateur présentiel
    public function formateurPresentiel()
    {
        return $this->belongsTo(Formateur::class, 'mle_presentiel', 'mle');
    }

    // Relation N-1 : un avancement peut avoir un formateur synchrone
    public function formateurSynchrone()
    {
        return $this->belongsTo(Formateur::class, 'mle_syn', 'mle');
    }

    // Relations indirectes via groupe
    public function formation()
    {
        return $this->hasOneThrough(Formation::class, Groupe::class, 'groupe', 'id', 'groupe', 'id_formation');
    }

    public function etablissement()
    {
        return $this->hasOneThrough(
            Etablissement::class, 
            [Groupe::class, Formation::class], 
            'groupe', 
            'code_efp',
            'groupe', 
            ['id_formation', 'code_efp']
        );
    }
}
