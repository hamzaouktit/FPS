<?php

// app/Models/Affectation.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Affectation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'groupe',
        'code_module',
        'mle_formateur',
        'mode',
        'mh_affectee',
        'date_affectation',
    ];

    protected $casts = [
        'date_affectation' => 'datetime',
    ];

    // Relation N-1 : une affectation appartient à un groupe
    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'groupe', 'groupe');
    }

    // Relation N-1 : une affectation appartient à un module
    public function module()
    {
        return $this->belongsTo(Module::class, 'code_module', 'code_module');
    }

    // Relation N-1 : une affectation appartient à un formateur
    public function formateur()
    {
        return $this->belongsTo(Formateur::class, 'mle_formateur', 'mle');
    }

    // Relations indirectes via groupe
    public function formation()
    {
        return $this->hasOneThrough(Formation::class, Groupe::class, 'groupe', 'id', 'groupe', 'id_formation');
    }

    public function etablissement()
    {
        return $this->hasOneThrough(
            Etablissement::class, 
            [Groupe::class, Formation::class], 
            'groupe', 
            'code_efp',
            'groupe', 
            ['id_formation', 'code_efp']
        );
    }
}

