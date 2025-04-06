<?php

namespace App\Http\Controllers;

use App\Models\Tatoueur;
use App\Models\Salon;
use App\Models\TatoueurSalon;
use Illuminate\Http\Request;

class TatoueurSalonController extends Controller
{
    // Associer un tatoueur à un salon
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tatoueur_id' => 'required|exists:tatoueurs,id',
            'salon_id' => 'required|exists:salons,id',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut'
        ]);

        return TatoueurSalon::create($validated);
    }

    // Historique des salons d'un tatoueur
    public function history(Tatoueur $tatoueur)
    {
        return $tatoueur->salons()->withPivot('date_debut', 'date_fin')->get();
    }
}
