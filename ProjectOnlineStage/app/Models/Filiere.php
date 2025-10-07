<?php

// app/Models/Filiere.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    use HasFactory;

    protected $primaryKey = 'code_filiere';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'code_filiere',
        'nom_filiere',
        'nom_secteur',
    ];

    // Relation N-1 : une filière appartient à un secteur
    public function secteur()
    {
        return $this->belongsTo(Secteur::class, 'nom_secteur', 'nom_secteur');
    }

    // Relation 1-N : une filière peut avoir plusieurs formations
    public function formations()
    {
        return $this->hasMany(Formation::class, 'code_filiere', 'code_filiere');
    }

    // Relation N-N via formations : une filière peut avoir plusieurs groupes
    public function groupes()
    {
        return $this->hasManyThrough(Groupe::class, Formation::class, 'code_filiere', 'id_formation', 'code_filiere', 'id');
    }
        // Scope to filter filières associated with a specific establishment
    public function scopeForEtablissement($query, $code_efp)
    {
        return $query->whereHas('formations', function ($query) use ($code_efp) {
            $query->where('code_efp', $code_efp);
        });
    }
}
