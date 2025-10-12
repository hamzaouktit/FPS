<?php

// app/Models/Formation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = [
        'annee',
        'code_efp',
        'niveau_id',
        'filiere_id',
        'type_formation',
        'creneau',
    ];

    protected $casts = [
        'annee' => 'integer',
    ];

    // Relations
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function niveau()
    {
        return $this->belongsTo(Niveau::class, 'niveau_id');
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'filiere_id');
    }

    public function groupes()
    {
        return $this->hasMany(Groupe::class, 'formation_id');
    }

    public function avancements()
    {
        return $this->hasManyThrough(Avancement::class, Groupe::class, 'formation_id', 'groupe_id', 'id', 'id');
    }

    public function affectations()
    {
        return $this->hasManyThrough(Affectation::class, Groupe::class, 'formation_id', 'groupe_id', 'id', 'id');
    }

    public function secteur()
    {
        return $this->hasOneThrough(Secteur::class, Filiere::class, 'id', 'id', 'filiere_id', 'secteur_id');
    }

    public function complexe()
    {
        return $this->hasOneThrough(Complexe::class, Etablissement::class, 'code_efp', 'id', 'code_efp', 'complexe_id');
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