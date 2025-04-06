<?php

// app/Models/RendezVous.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rdv';
    protected $fillable = [
        'demande_id',
        'date_rdv',
        'duree',
        'statut',
    ];

    public function demande()
    {
        return $this->belongsTo(Demande::class);
    }
}
