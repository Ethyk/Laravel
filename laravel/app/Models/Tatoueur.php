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

    // protected $casts = [
    //     'disponibilites' => 'array', // Laravel va convertir automatiquement JSON en tableau PHP
    // ];

    public function user()
    {
        // return $this->belongsTo(User::class);
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contractedSalons()
    {
        // return $this->belongsToMany(Salon::class, 'tatoueurs_salons', 'tatoueur_id', 'salon_id');
        return $this->belongsToMany(Salon::class, 'tatoueurs_salons', 'tatoueur_id', 'salon_id')
        ->withPivot(['date_debut', 'date_fin'])
        ->withTimestamps();
    }
    
    // public function salons()
    // {
    //     // return $this->belongsTo(Salon::class, 'localisation_actuelle', 'id');
    //     return $this->hasMany(Salon::class, 'id', 'localisation_actuelle');
    // }

    // public function salonsDuTatoueur()
    // {
    // // On part du principe que le tatoueur est lié à un user via user_id
    // // Et que dans Salon, la colonne gestionnaire_id correspond à l'id du user
    // return Salon::where('gestionnaire_id', $this->user_id);
    // }

    public function salons()
    {
        return $this->hasManyThrough(
            Salon::class,      // Modèle final : Salon
            User::class,       // Modèle intermédiaire : User
            'id',              // Clé locale sur le modèle User, ici "id"
            'gestionnaire_id', // Clé étrangère dans Salon qui réfère à User.id
            'user_id',         // Clé étrangère dans Tatoueur qui réfère à User (ici user_id)
            'id'               // Clé locale sur le modèle User (généralement "id")
        );
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
