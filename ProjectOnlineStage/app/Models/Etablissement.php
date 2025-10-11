<?php

// app/Models/Etablissement.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etablissement extends Model
{
    use HasFactory;

    protected $primaryKey = 'code_efp';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'code_efp',
        'nom_efp',
        'complexe_id',
        'user_id',
    ];

    // Relations
    public function complexe()
    {
        return $this->belongsTo(Complexe::class, 'complexe_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formations()
    {
        return $this->hasMany(Formation::class, 'code_efp', 'code_efp');
    }

    public function groupes()
    {
        return $this->hasMany(Groupe::class, 'code_efp', 'code_efp');
    }

    public function secteurs()
    {
        return $this->hasMany(Secteur::class, 'code_efp', 'code_efp');
    }

    public function filieres()
    {
        return $this->hasMany(Filiere::class, 'code_efp', 'code_efp');
    }

    public function niveaux()
    {
        return $this->hasMany(Niveau::class, 'code_efp', 'code_efp');
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'code_efp', 'code_efp');
    }

    public function formateurs()
    {
        return $this->hasMany(Formateur::class, 'code_efp', 'code_efp');
    }

    // Méthode pour obtenir tous les avancements de l'établissement
    public function avancements()
    {
        return Avancement::whereHas('groupe', function ($q) {
            $q->where('code_efp', $this->code_efp);
        });
    }

    // Méthode pour obtenir toutes les affectations de l'établissement
    public function affectations()
    {
        return Affectation::whereHas('groupe', function ($q) {
            $q->where('code_efp', $this->code_efp);
        });
    }
}
