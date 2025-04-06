<?php

// app/Models/TatoueurSalon.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TatoueurSalon extends Model
{
    use HasFactory;

    protected $table = 'tatoueurs_salons';
    protected $fillable = [
        'tatoueur_id',
        'salon_id',
        'date_debut',
        'date_fin',
    ];

    public function tatoueur()
    {
        return $this->belongsTo(Tatoueur::class);
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }
}
