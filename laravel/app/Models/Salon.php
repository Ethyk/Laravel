<?php

// app/Models/Salon.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salon extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'salons';
    protected $fillable = [
        'name',
        'description',
        'adresse',
        'ville',
        'code_postal',
        'pays',
        'gestionnaire_id',
    ];

    public function gestionnaire()
    {
        return $this->belongsTo(User::class, 'gestionnaire_id');
    }

    // public function tatoueurs()
    // {
    //     return $this->belongsToMany(Tatoueur::class, 'tatoueurs_salons', 'salon_id', 'tatoueur_id');
    // }

    public function tatoueurs()
    {
        return $this->belongsToMany(Tatoueur::class, 'tatoueurs_salons', 'salon_id', 'tatoueur_id')
            ->withPivot(['date_debut', 'date_fin'])
            ->withTimestamps();
    }

    public static function getLastCreated()
    {
        return self::latest('created_at')->first();
    }
}

