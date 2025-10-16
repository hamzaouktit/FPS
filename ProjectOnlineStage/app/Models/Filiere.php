<?php

// ========================================
// 3. Model Filiere - Ajout relation établissement
// ========================================
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Filiere extends Model
{
    protected $fillable = ['code_filiere', 'nom_filiere', 'secteur_id', 'code_efp'];

    public function secteur()
    {
        return $this->belongsTo(Secteur::class);
    }

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function formations()
    {
        return $this->hasMany(Formation::class);
    }

    public function groupes()
    {
        return $this->hasMany(Groupe::class);
    }

    // ✅ RELATION MANY-TO-MANY avec Module
    public function modules()
    {
        return $this->belongsToMany(Module::class, 'filiere_module')
                    ->withTimestamps();
    }
}
