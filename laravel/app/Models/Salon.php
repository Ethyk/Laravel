<?php

// app/Models/Salon.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Salon extends Model
{
    use HasFactory;

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

    public function tatoueurs()
    {
        return $this->belongsToMany(Tatoueur::class, 'tatoueurs_salons', 'salon_id', 'tatoueur_id');
    }
}

