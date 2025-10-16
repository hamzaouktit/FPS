<?php
// ========================================
// 8. Model Niveau - Ajout relation établissement
// ========================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    protected $fillable = ['nom', 'code_efp'];

    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function formations()
    {
        return $this->hasMany(Formation::class);
    }
}
