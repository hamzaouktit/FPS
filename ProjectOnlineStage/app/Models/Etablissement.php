<?php

// app/Models/Etablissement.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etablissement extends Model
{
    protected $primaryKey = 'code_efp';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['code_efp', 'nom_efp', 'complexe_id', 'user_id'];

    public function complexe()
    {
        return $this->belongsTo(Complexe::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function niveaux()
    {
        return $this->hasMany(Niveau::class, 'code_efp', 'code_efp');
    }

    public function secteurs()
    {
        return $this->hasMany(Secteur::class, 'code_efp', 'code_efp');
    }

    public function formations()
    {
        return $this->hasMany(Formation::class, 'code_efp', 'code_efp');
    }

    public function groupes()
    {
        return $this->hasMany(Groupe::class, 'code_efp', 'code_efp');
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'code_efp', 'code_efp');
    }

    // ✅ NOUVELLE RELATION Many-to-Many avec Formateur
    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class, 'etablissement_formateur', 'code_efp', 'formateur_id')
                    ->withTimestamps();
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class, 'code_efp', 'code_efp');
    }

    public function avancements()
    {
        return $this->hasMany(Avancement::class, 'code_efp', 'code_efp');
    }
    /**
     * ✅ NOUVELLE RELATION: Historiques des avancements
     */
    public function historiquesAvancements()
    {
        return $this->hasMany(HistoriqueAvancement::class, 'code_efp', 'code_efp');
    }
}
