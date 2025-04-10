<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids; // <-- AJOUTER
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tatoueur extends Model
{
    use HasFactory;
    use HasUuids; // <-- AJOUTER (Gère automatiquement la clé primaire UUID)

    // Plus besoin de ça si on utilise HasUuids :
    // protected $keyType = 'string';
    // public $incrementing = false;

    protected $table = 'tatoueurs';

    // Assure-toi que 'user_id' est bien fillable si tu le définis manuellement
    protected $fillable = [
        'bio',
        'style',
        'localisation_actuelle',
        'disponibilites',
        'instagram',
        'user_id' // Nécessaire pour la création
    ];

    // !! ACTIVER LES CASTS !!
    protected $casts = [
        'style' => 'array',         // Convertit jsonb <-> array PHP
        'disponibilites' => 'array', // Convertit jsonb <-> array PHP
        // Si tu préfères des objets stdClass au lieu d'array associatifs pour disponibilités:
        // 'disponibilites' => 'object',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // !! AJOUTER LA RELATION MANQUANTE !!
    public function salonActuel()
    {
        // Relation vers le salon défini dans localisation_actuelle
        return $this->belongsTo(Salon::class, 'localisation_actuelle');
    }


    public function contractedSalons()
    {
        return $this->belongsToMany(Salon::class, 'tatoueurs_salons', 'tatoueur_id', 'salon_id')
        ->withPivot(['date_debut', 'date_fin'])
        ->withTimestamps();
    }

    public function salons() // Relation complexe via User - Ok si c'est bien ce que tu veux
    {
        return $this->hasManyThrough(
            Salon::class,
            User::class,
            'id',
            'gestionnaire_id',
            'user_id',
            'id'
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

     /**
      * Override: Colonnes qui doivent recevoir un UUID automatiquement.
      * Optionnel si seule 'id' est concernée (comportement par défaut de HasUuids).
      */
    // public function uniqueIds(): array
    // {
    //     return ['id'];
    // }
}