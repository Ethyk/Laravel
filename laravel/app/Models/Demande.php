<?php

// app/Models/Demande.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Demande extends Model
{
    use HasFactory;

    protected $table = 'demandes';
    protected $fillable = [
        'client_id',
        'tatoueur_id',
        'description',
        'reference_images',
        'style',
        'etat',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function tatoueur()
    {
        return $this->belongsTo(Tatoueur::class, 'tatoueur_id');
    }

    public function rendezVous()
    {
        return $this->hasOne(RendezVous::class);
    }
}
