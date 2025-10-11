<?php

// app/Models/Complexe.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complexe extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'user_id',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function etablissements()
    {
        return $this->hasMany(Etablissement::class, 'complexe_id');
    }

    public function formations()
    {
        return $this->hasManyThrough(Formation::class, Etablissement::class, 'complexe_id', 'code_efp', 'id', 'code_efp');
    }

    // Méthode pour obtenir tous les groupes du complexe
    public function groupes()
    {
        return Groupe::whereHas('etablissement', function ($q) {
            $q->where('complexe_id', $this->id);
        });
    }

    // Méthode pour obtenir tous les formateurs du complexe
    public function formateurs()
    {
        return Formateur::whereHas('etablissement', function ($q) {
            $q->where('complexe_id', $this->id);
        });
    }

    // Méthode pour obtenir tous les modules du complexe
    public function modules()
    {
        return Module::whereHas('etablissement', function ($q) {
            $q->where('complexe_id', $this->id);
        });
    }
}
