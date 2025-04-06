<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use App\Models\Tatoueur;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TatoueurController extends Controller
{
    // Liste des tatoueurs
    public function index()
    {
        // return Tatoueur::with(['user', 'salons', 'flashs'])->get();
        // return Inertia::render('auth/ConfirmPassword');

        // $tatoueurs = Tatoueur::with(['user', 'salons', 'flashs'])->get();
    
        // // dd($tatoueurs);
        // return Inertia::render('tatoueur/index', [
        //     'tatoueurs' => $tatoueurs,
        //     // 'salons' => [$salon]
        // ]);

        // $tatoueurs = Tatoueur::with(['user', 'salons', 'flashs'])->get();
        // $tatoueurs = Tatoueur::get();

        // 1. Obtenez l'utilisateur connecté
        $user = auth()->guard('web')->user();

        // dd($user->id);
        // 2. Trouvez le tatoueur associé à cet utilisateur
        $tatoueur = Tatoueur::where('user_id', $user->id)
                    // ->with('salonActuel')
                    ->firstOrFail();
        // $tatoueur = $user->tatoueur()
        //             ->with(['salonActuel', 'salons', 'flashs'])
        //             ->firstOrFail();
        // dd($tatoueur);
        // OU si vous avez défini la relation inverse dans User:
        // $tatoueur = $user->tatoueur()->with('salonActuel')->firstOrFail();
        // $salonActuel = $tatoueurs[0]->localisation_actuelle;
        // $tatoueur = Tatoueur::with('salonActuel')->find($tatoueurId);

        // $salon = $tatoueurs->load('salonActuel');
        $salon = Salon::where('gestionnaire_id', $user->id)
                    // ->with('salonActuel')
                    ->firstOrFail();
        // $tatoueur['salons'] = $salon;
        // dd($tatoueur);
        // dd($salon);
        // localisation_actuelle
        // salon = Salon::get($tatoueurs->localisation_actuelle);
        // dd($tatoueurs);
        return Inertia::render('tatoueur/index', [
            'tatoueur' => $tatoueur,
            'disponibilites' => $tatoueur->disponibilites // Cela envoie les données JSON directement
            // 'salons' => [$salon]
        ]);

        // $tatoueurs = Tatoueur::with([
        //     'user:id,name,email', // Seulement les champs nécessaires
        //     'salons:id,name,ville', 
        //     // 'flashs:id,tatoueur_id,image_url,prix'
        // ])->get();

        // return Inertia::render('Tatoueur/Index', [
        //     'tatoueurs' => $tatoueurs->map(function ($tatoueur) {
        //         return [
        //             'id' => $tatoueur->id,
        //             'bio' => $tatoueur->bio,
        //             'styles' => $tatoueur->style,
        //             'instagram' => $tatoueur->instagram,
        //             'user' => $tatoueur->user,
        //             'salons' => $tatoueur->salons,
        //             // 'flashs' => $tatoueur->flashs,
        //             'created_at' => $tatoueur->created_at->format('d/m/Y'),
        //         ];
        //     })
        // ]);
    }

    // Création d'un tatoueur
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'bio' => 'nullable|string',
            'instagram' => 'nullable|string|max:255'
        ]);

        return Tatoueur::create($validated);
    }

    // Affichage d'un tatoueur spécifique
    public function show(Tatoueur $tatoueur)
    {
        return $tatoueur->load(['user', 'salons', 'flashs', 'portfolios']);
    }

    // Mise à jour d'un tatoueur
    public function update(Request $request, Tatoueur $tatoueur)
    {
        $validated = $request->validate([
            'bio' => 'nullable|string',
            'style' => 'nullable|json',
            'instagram' => 'nullable|string|max:255'
        ]);

        $tatoueur->update($validated);
        return $tatoueur;
    }

    // Suppression d'un tatoueur
    public function destroy(Tatoueur $tatoueur)
    {
        $tatoueur->delete();
        return response(null, 204);
    }

    // Méthodes supplémentaires si besoin
    public function getFlashs(Tatoueur $tatoueur)
    {
        return $tatoueur->flashs;
    }

        /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tatoueur $tatoueur)
    {
        //
    }
}
