<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDemandeRequest;
use App\Models\Demande;
use Illuminate\Http\Request;

class DemandeController extends Controller
{
    public function index()
    {
        return Demande::with(['client', 'tatoueur'])->get();
    }

    public function store(StoreDemandeRequest $request)
    {
        return Demande::create($request->validated());
    }

    public function show(Demande $demande)
    {
        return $demande->load(['client', 'tatoueur', 'rendezVous']);
    }

    public function update(Request $request, Demande $demande)
    {
        $validated = $request->validate([
            'etat' => 'sometimes|in:en attente,acceptée,refusée,programmée,terminée',
            'description' => 'sometimes|string|min:10'
        ]);

        $demande->update($validated);
        return $demande;
    }

    public function destroy(Demande $demande)
    {
        $demande->delete();
        return response()->noContent();
    }
}
