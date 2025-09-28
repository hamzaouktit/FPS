<?php


// app/Models/Niveau.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    use HasFactory;

    protected $primaryKey = 'niveau';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'niveau',
    ];

    // Relation 1-N : un niveau peut avoir plusieurs formations
    public function formations()
    {
        return $this->hasMany(Formation::class, 'niveau', 'niveau');
    }

    // Relation N-N via formations : un niveau peut avoir plusieurs groupes
    public function groupes()
    {
        return $this->hasManyThrough(Groupe::class, Formation::class, 'niveau', 'id_formation', 'niveau', 'id');
    }
}

