<?php

// app/Models/Tatoueur.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tatoueur extends Model
{
    use HasFactory;

    protected $table = 'tatoueurs';
    protected $fillable = [
        'bio',
        'style',
        'localisation_actuelle',
        'disponibilites',
        'instagram',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salons()
    {
        return $this->belongsToMany(Salon::class, 'tatoueurs_salons', 'tatoueur_id', 'salon_id');
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
