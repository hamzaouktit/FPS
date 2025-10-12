<?php

// app/Models/Affectation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Affectation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'groupe_id',
        'module_id',
        'mle_formateur',
        'mode',
        'mh_affectee',
        'date_affectation',
    ];

    protected $casts = [
        'date_affectation' => 'datetime',
        'mh_affectee' => 'decimal:2',
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

    public function formateur()
    {
        return $this->belongsTo(Formateur::class, 'mle_formateur', 'mle');
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
