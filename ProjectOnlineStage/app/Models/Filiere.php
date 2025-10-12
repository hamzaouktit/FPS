<?php

// app/Models/Filiere.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_filiere',
        'nom_filiere',
        'secteur_id',
        'code_efp',
    ];

    // Relations
    public function secteur()
    {
        return $this->belongsTo(Secteur::class, 'secteur_id');
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function formations()
    {
        return $this->hasMany(Formation::class, 'filiere_id');
    }

    public function groupes()
    {
        return $this->hasManyThrough(Groupe::class, Formation::class, 'filiere_id', 'formation_id', 'id', 'id');
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
