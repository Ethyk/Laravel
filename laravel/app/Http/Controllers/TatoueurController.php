<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use App\Models\Tatoueur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
                    ->first();
        // dd($tatoueur);

        // $tatoueur = $user->tatoueur()
        //             ->with(['salonActuel', 'salons', 'flashs'])
        //             ->firstOrFail();
        // dd($tatoueur);
        // OU si vous avez défini la relation inverse dans User:
        // $tatoueur = $user->tatoueur()->with('salonActuel')->firstOrFail();
        // $salonActuel = $tatoueurs[0]->localisation_actuelle;
        // $tatoueur = Tatoueur::with('salonActuel')->find($tatoueurId);

        // $salon = $tatoueurs->load('salonActuel');
        // $salon = Salon::where('gestionnaire_id', $user->id)
        //             // ->with('salonActuel')
        //             ->firstOrFail();
        // $tatoueur['salons'] = $salon;
        // dd($tatoueur ? $tatoueur->disponibilites : []);
        // dd($salon);
        //  dd($tatoueur ?? [] ,);

        // localisation_actuelle
        // salon = Salon::get($tatoueurs->localisation_actuelle);


        // $salon = Salon::find($salon_id);
        // $tatoueurs = $salon->tatoueurs; // Liste des tatoueurs associés à ce salon
        // $tatoueur = Tatoueur::find($tatoueur_id);
        // $salons = $tatoueur->salons; // Liste des salons associés à ce tatoueur

        $tatoueur->load(['user', 'salons', 'flashs', 'portfolios']);
        // dd($tatoueur);

        return Inertia::render('tatoueur/index', [
            'tatoueur' => $tatoueur ?? null ,
            'disponibilites' => $tatoueur ? $tatoueur->disponibilites : null // Cela envoie les données JSON directement
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
        // $validated = $request->validate([
        //     // 'user_id' => 'required|exists:users,id',
        //     'bio' => 'nullable|string',
        //     'instagram' => 'nullable|string|max:255'
        // ]);
        // Validation des données
        $validated = $request->validate([
            'bio' => 'nullable|string',
            'style' => 'nullable|json', // Attend un tableau
            'localisation_actuelle' => 'nullable|uuid',
            'disponibilites' => 'nullable|json', // Attend un tableau
            'instagram' => 'nullable|url|max:255',
        ]);
        // Validation des données envoyées par le formulaire
        // $validated = $request->validate([
        //     'bio' => 'nullable|string|max:1000', // La bio est facultative et peut contenir jusqu'à 1000 caractères
        //     'style' => 'nullable|string|max:255', // Le style est obligatoire, limité à 255 caractères
        //     'localisation_actuelle' => 'nullable|string|max:255', // La localisation actuelle est facultative
        //     'disponibilites' => 'nullable|string|max:255', // Les disponibilités sont facultatives
        //     'instagram' => 'nullable|url|max:255', // Le compte Instagram doit être un lien valide
        // ]);
        $user = auth()->user(); // Récupère l'utilisateur connecté
        $validated['user_id'] = $user->id;

        // dd($validated);
        Tatoueur::create($validated);
        return to_route('tatoueurs.index')->with('success', 'tatoueur créé avec succès.');

    }

    // Affichage d'un tatoueur spécifique
    public function show(Tatoueur $tatoueur)
    {
        // dd($tatoueur->styles);
        // dd($tatoueur);

        return Inertia::render('tatoueur/Show', [
            'csrf_token' => csrf_token(), // Injecte le token CSRF
            'salons' => fn () => Salon::all(), // Liste des salons
            'tatoueur' => $tatoueur,
            'disponibilites' => $tatoueur ? $tatoueur->disponibilites : [], // Cela envoie les données JSON directement
            'style' => $tatoueur ? $tatoueur->style : [] // Cela envoie les données JSON directement
        ]);
        // return $tatoueur->load(['user', 'salons', 'flashs', 'portfolios']);
    }

    // Mise à jour d'un tatoueur
    public function update(Request $request, Tatoueur $tatoueur)
    {
        $validated = $request->validate([
            // 'disponibilites' => 'nullable|json',
            'bio' => 'nullable|string',
            'style' => 'nullable|json',
            'instagram' => 'nullable|string|max:255',
            // 'localisation_actuelle' => 'nullable|string|max:255',
            'localisation_actuelle' => 'nullable|exists:salons,id', // Valide comme clé 
            'disponibilites' => 'nullable|json',
            // 'updated_at' => 'nullable|string|max:255',
        ]);

        $tatoueur->update($validated);
        return redirect()->route('tatoueurs.show', $tatoueur)->with('success', 'tatoueur mis à jour avec succès.');
        
        // return $tatoueur;
    }

    // Suppression d'un tatoueur
    public function destroy(Tatoueur $tatoueur)
    {
        $tatoueur->delete();
        // return response(null, 204);
        return to_route('tatoueurs.index')->with('success', 'tatoueur supprimé avec succès.');

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
              // $user = auth()->user();
        $user = auth()->user(); // Récupère l'utilisateur connecté
        // dd(Gate::allows('create-salon', $user));
        // dd(Gate::allows('create-salon'));
        if (!Gate::allows('create-tatoueur')) {
            // dd(auth()->user()->role); // Vérifie ce que retourne le rôle ici
            abort(403, 'Vous n\'avez pas l\'autorisation de créer un profile tatoueur.');
        }
        // $salons = Salon::all();
        
        return Inertia::render('tatoueur/Create', [
            'salons' => fn () => Salon::all(), // Liste des salons
            'csrf_token' => csrf_token(), // Injecte le token CSRF
            'tatoueur' => [
                'bio' => '',
                'style' => [],
                'localisation_actuelle' => null, // UUID du salon (ou null si vide)
                'disponibilites' => [], // Tableau vide pour les disponibilités
                'instagram' => '',
            ],
        ]);
        

        // return Inertia::render('tatoueur/index', [
        //     'tatoueur' => $tatoueur ?? null ,
        //     'disponibilites' => $tatoueur ? $tatoueur->disponibilites : null // Cela envoie les données JSON directement
        //     // 'salons' => [$salon]
        // ]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tatoueur $tatoueur)
    {
        //
    }
}
