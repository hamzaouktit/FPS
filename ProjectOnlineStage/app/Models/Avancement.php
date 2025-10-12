<?php

// app/Models/Avancement.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Avancement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'date_maj',
        'groupe_id',
        'module_id',
        'mode',
        'mle_presentiel',
        'mle_syn',
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
        'mh_affectee_globale',
        'mh_realisee_presentiel',
        'mh_realisee_sync',
        'mh_realisee_globale',
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
        'mh_affectee_globale' => 'decimal:2',
        'mh_realisee_presentiel' => 'decimal:2',
        'mh_realisee_sync' => 'decimal:2',
        'mh_realisee_globale' => 'decimal:2',
        'taux_realisation_presentiel' => 'decimal:2',
        'taux_realisation_syn' => 'decimal:2',
        'taux_realisation_global' => 'decimal:2',
        'moy_absence' => 'decimal:2',
        'nb_cc' => 'integer',
    ];

    // Relations
    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'groupe_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function formateurPresentiel()
    {
        return $this->belongsTo(Formateur::class, 'mle_presentiel', 'mle');
    }

    public function formateurSynchrone()
    {
        return $this->belongsTo(Formateur::class, 'mle_syn', 'mle');
    }

    public function formation()
    {
        return $this->hasOneThrough(Formation::class, Groupe::class, 'id', 'id', 'groupe_id', 'formation_id');
    }

    public function etablissement()
    {
        return $this->hasOneThrough(Etablissement::class, Groupe::class, 'id', 'code_efp', 'groupe_id', 'code_efp');
    }

    // Scopes pour l'isolation par établissement
    public function scopeForEtablissement(Builder $query, $code_efp)
    {
        return $query->whereHas('groupe', function ($q) use ($code_efp) {
            $q->where('code_efp', $code_efp);
        });
    }

    public function scopeForComplexe(Builder $query, $complexe_id)
    {
        return $query->whereHas('groupe.etablissement', function ($q) use ($complexe_id) {
            $q->where('complexe_id', $complexe_id);
        });
    }

    public function scopeForUser(Builder $query, $user)
    {
        if ($user->role === 'directeur_etablissement') {
            return $query->forEtablissement($user->etablissement->code_efp);
        } elseif ($user->role === 'directeur_complexe') {
            return $query->forComplexe($user->complexe->id);
        }
        return $query;
    }
}
