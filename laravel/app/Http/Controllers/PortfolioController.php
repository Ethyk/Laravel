<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Portfolio $portfolio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Portfolio $portfolio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        //
        // Récupérer l'utilisateur connecté
        $user = Auth::guard('web')->user();

        // Récupérer le portfolio à modifier
        // $portfolio = Portfolio::findOrFail($id);
    
        // Vérification : Ce portfolio appartient-il au tatoueur associé à l'utilisateur ?
        if ($portfolio->tatoueur->user_id !== $user->id) {
            abort(403, 'Accès refusé. Vous ne pouvez modifier que votre propre portfolio.');
        }
    
        // Effectuer la mise à jour (assure-toi de valider les données avant)
        $validatedData = $request->validate([
            'description' => 'nullable|string|max:255',
            'image_url' => 'required|string|max:255',
        ]);
    
        $portfolio->update($validatedData);
    
        return redirect()->route('portfolios.index')->with('success', 'Portfolio mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Portfolio $portfolio)
    {
        //
    }
}
