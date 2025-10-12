<?php

// app/Models/Formateur.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Formateur extends Model
{
    use HasFactory;

    protected $primaryKey = 'mle';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'mle',
        'nom_formateur',
        'code_efp',
    ];

    // Relations
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function avancementsPresentiel()
    {
        return $this->hasMany(Avancement::class, 'mle_presentiel', 'mle');
    }

    public function avancementsSynchrone()
    {
        return $this->hasMany(Avancement::class, 'mle_syn', 'mle');
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'mle_formateur', 'mle');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'avancements', 'mle_presentiel', 'module_id');
    }

    public function groupes()
    {
        return $this->belongsToMany(Groupe::class, 'avancements', 'mle_presentiel', 'groupe_id');
    }

    // Méthode pour obtenir tous les avancements
    public function tousAvancements()
    {
        return Avancement::where('mle_presentiel', $this->mle)
                         ->orWhere('mle_syn', $this->mle);
    }

    // Scopes
    public function scopeForEtablissement(Builder $query, $code_efp)
    {
        return $query->where('code_efp', $code_efp);
    }

    public function scopeForComplexe(Builder $query, $complexe_id)
    {
        return $query->whereHas('etablissement', function ($q) use ($complexe_id) {
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
