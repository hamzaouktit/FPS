<?php


// ========================================
// 7. Model Module - Ajout relation établissement
// ========================================

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'nom',
        'regional',
        'module_pie',
        'efp_pie',
        'filiere_id',
        'code_efp'
    ];

    protected $casts = [
        'module_pie' => 'boolean'
    ];

    // Relations
    public function etablissement()
    {
        return $this->belongsTo(Etablissement::class, 'code_efp', 'code_efp');
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function affectations()
    {
        return $this->hasMany(Affectation::class);
    }

    public function formateurs()
    {
        return $this->belongsToMany(Formateur::class, 'formateur_module');
    }

    public function scopeRegional($query)
    {
        return $query->where('regional', 'O');
    }

    public function scopePie($query)
    {
        return $query->where('module_pie', true);
    }
}