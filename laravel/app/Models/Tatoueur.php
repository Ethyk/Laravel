<?php

// app/Models/Tatoueur.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tatoueur extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'tatoueurs';
    protected $fillable = [
        'bio',
        'style',
        'localisation_actuelle',
        'disponibilites',
        'instagram',
        'user_id'
    ];

    protected $casts = [
        'disponibilites' => 'array', // Laravel va convertir automatiquement JSON en tableau PHP
    ];

    public function user()
    {
        // return $this->belongsTo(User::class);
        return $this->belongsTo(User::class, 'user_id');
    }

    public function salons()
    {
        // return $this->belongsToMany(Salon::class, 'tatoueurs_salons', 'tatoueur_id', 'salon_id');
        return $this->belongsToMany(Salon::class, 'tatoueurs_salons', 'tatoueur_id', 'salon_id')
        ->withPivot(['date_debut', 'date_fin'])
        ->withTimestamps();
    }
    
    public function salonActuel()
    {
        // return $this->belongsTo(Salon::class, 'localisation_actuelle', 'id');
        return $this->belongsTo(Salon::class, 'localisation_actuelle')->withDefault();
    }

    public function flashs()
    {
        return $this->hasMany(Flash::class);
    }

    public function demandes()
    {
        return $this->hasMany(Demande::class, 'tatoueur_id');
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }
}
