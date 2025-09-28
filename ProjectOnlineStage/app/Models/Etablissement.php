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

    // Relation N-1 : un établissement appartient à un complexe
    public function complexe()
    {
        return $this->belongsTo(Complexe::class, 'complexe_id');
    }

    // Relation 1-1 : un établissement appartient à un utilisateur (directeur)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation 1-N : un établissement peut avoir plusieurs formations
    public function formations()
    {
        return $this->hasMany(Formation::class, 'code_efp', 'code_efp');
    }

    // Relation N-N via formations : un établissement peut avoir plusieurs groupes
    public function groupes()
    {
        return $this->hasManyThrough(Groupe::class, Formation::class, 'code_efp', 'id_formation', 'code_efp', 'id');
    }
}
