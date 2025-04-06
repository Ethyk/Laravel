<?php

// app/Models/Flash.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Flash extends Model
{
    use HasFactory;

    protected $table = 'flashs';
    protected $fillable = [
        'tatoueur_id',
        'image_url',
        'prix',
        'description',
    ];

    public function tatoueur()
    {
        return $this->belongsTo(Tatoueur::class);
    }
}
