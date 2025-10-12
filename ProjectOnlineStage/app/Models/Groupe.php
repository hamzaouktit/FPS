<?php

// app/Models/Groupe.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Groupe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_groupe',
        'formation_id',
        'effectif_groupe',
        'sous_groupe',
        'statut_sous_groupe',
        'fusion_groupe',
        'code_fusion',
        'annee_formation',
        'code_efp',
    ];

    protected $casts = [
        'effectif_groupe' => 'integer',
        'annee_formation' => 'integer',
    ];

    // Relations
    public function formation()
    {
        return $this->belongsTo(Formation::class, 'formation_id');
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function avancements()
    {
        return $this->hasMany(Avancement::class, 'groupe_id');
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'groupe_id');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'avancements', 'groupe_id', 'module_id');
    }

    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class, 'avancements', 'groupe_id', 'mle_presentiel', 'id', 'mle');
    }

    public function filiere()
    {
        return $this->hasOneThrough(Filiere::class, Formation::class, 'id', 'id', 'formation_id', 'filiere_id');
    }

    public function niveau()
    {
        return $this->hasOneThrough(Niveau::class, Formation::class, 'id', 'id', 'formation_id', 'niveau_id');
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
