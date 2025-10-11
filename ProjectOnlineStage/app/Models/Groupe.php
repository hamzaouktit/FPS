<?php

// app/Models/Groupe.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Groupe extends Model
{
    use HasFactory;

    protected $primaryKey = 'groupe';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'groupe',
        'id_formation',
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
        return $this->belongsTo(Formation::class, 'id_formation');
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function avancements()
    {
        return $this->hasMany(Avancement::class, 'groupe', 'groupe');
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'groupe', 'groupe');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'avancements', 'groupe', 'code_module');
    }

    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class, 'avancements', 'groupe', 'mle_presentiel')
                    ->orWhereColumn('avancements.mle_syn', 'formateurs.mle');
    }

    public function filiere()
    {
        return $this->hasOneThrough(Filiere::class, Formation::class, 'id', 'code_filiere', 'id_formation', 'code_filiere');
    }

    public function niveau()
    {
        return $this->hasOneThrough(Niveau::class, Formation::class, 'id', 'niveau', 'id_formation', 'niveau');
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