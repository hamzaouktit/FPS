<?php

// app/Models/Module.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Module extends Model
{
    use HasFactory;

    protected $primaryKey = 'code_module';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code_module',
        'nom_module',
        'regional',
        'code_efp',
    ];

    // Relations
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function avancements()
    {
        return $this->hasMany(Avancement::class, 'code_module', 'code_module');
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'code_module', 'code_module');
    }

    public function groupes()
    {
        return $this->belongsToMany(Groupe::class, 'avancements', 'code_module', 'groupe');
    }

    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class, 'avancements', 'code_module', 'mle_presentiel')
                    ->orWhereColumn('avancements.mle_syn', 'formateurs.mle');
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
