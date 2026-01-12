<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriqueAvancement extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_capture',
        'affectation_id',
        'code_efp',
        'formateur_presentiel',
        'mle_presentiel',
        'formateur_syn',
        'mle_syn',
        'nom_module',
        'code_module',
        'code_groupe',
        'nom_filiere',
        'mh_affectee_presentiel',
        'mh_affectee_sync',
        'mh_affectee_globale',
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
        'classe_teams'
    ];

    protected $casts = [
        'date_capture' => 'date'
    ];

    // Relations
    public function affectation()
    {
        return $this->belongsTo(Affectation::class);
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    // Scopes pour faciliter les requêtes
    public function scopeForEtablissement($query, $codeEfp)
    {
        return $query->where('code_efp', $codeEfp);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date_capture', $date);
    }

    public function scopeForFormateur($query, $mle)
    {
        return $query->where(function($q) use ($mle) {
            $q->where('mle_presentiel', $mle)
              ->orWhere('mle_syn', $mle);
        });
    }

    public function scopeForModule($query, $codeModule)
    {
        return $query->where('code_module', $codeModule);
    }

    public function scopeForGroupe($query, $codeGroupe)
    {
        return $query->where('code_groupe', $codeGroupe);
    }
}