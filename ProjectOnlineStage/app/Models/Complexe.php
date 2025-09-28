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

    // Relation 1-1 : un complexe appartient à un utilisateur (directeur)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation 1-N : un complexe peut avoir plusieurs établissements
    public function etablissements()
    {
        return $this->hasMany(Etablissement::class, 'complexe_id');
    }

    // Relation N-N via établissements : un complexe peut avoir plusieurs formations
    public function formations()
    {
        return $this->hasManyThrough(Formation::class, Etablissement::class, 'complexe_id', 'code_efp', 'id', 'code_efp');
    }
}