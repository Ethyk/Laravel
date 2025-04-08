<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class SalonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $user = auth()->guard('web')->user();
          // Récupérer l'utilisateur actuellement connecté
        $user = auth()->user();

        // Filtrer les salons qui appartiennent à cet utilisateur
        $salons = Salon::where('gestionnaire_id', $user->id)->get();

        
        // dd($salons->id); 
        // Retourner une vue avec les salons
        return inertia('Salons/Index', [
            'salons' => $salons,
            'csrf_token' => csrf_token(), // Injecte le token CSRF dans la réponse
        ]);

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
        if (!Gate::allows('create-salon')) {
            // dd(auth()->user()->role); // Vérifie ce que retourne le rôle ici
            abort(403, 'Vous n\'avez pas l\'autorisation de créer un salon.');
        }
        
        return inertia('Salons/Create', [
            'csrf_token' => csrf_token(), // Injecte le token CSRF dans la réponse
            'salon' => [
                'name' => '',
                'description' => '',
                'adresse' => '',
                'ville' => '',
                'code_postal' => '',
                'pays' => '',
                'gestionnaire_id' => null, // Si nécessaire, sinon laisse comme ''
            ],
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Vérification manuelle du token (optionnel)
       if ($request->input('_token') !== csrf_token()) {
           abort(419, 'Jeton CSRF invalide ou expiré.');
       }
       
        $user = auth()->user(); // Récupère l'utilisateur connecté


        // Validation des données
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'code_postal' => 'required|string|max:10',
            'pays' => 'required|string|max:255',
        ]);

        // Création du salon dans la base de données
        $salon = Salon::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'adresse' => $validatedData['adresse'],
            'ville' => $validatedData['ville'],
            'code_postal' => $validatedData['code_postal'],
            'pays' => $validatedData['pays'],
            'gestionnaire_id' => $user->id,
        ]);

        return to_route('salons.index')->with('success', 'Salon créé avec succès.');

        // return redirect()->route('salons.index')->with('success', 'Salon créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Salon $salon)
    {
        return inertia('Salons/Shows', [
            'salon' => $salon
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Salon $salon)
    {
        return Inertia::render('Salons/Edit', [
            'salon' => $salon,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Salon $salon)
    {
        if (!Gate::allows('manage-salon')) {
            abort(403, 'Action non autorisée.');
        }

        // Validation des données
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|min:3',
            'description' => 'nullable|string',
            'adresse' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'code_postal' => 'required|string|max:10',
            'pays' => 'required|string|max:255',
        ]);

        // dd($validatedData);
        
         // Mise à jour des données
        $salon->update($validatedData);
        // dd($validatedData);
        return redirect()->back();

        // return redirect()->route('salons.index', $salon)->with('success', 'Salon mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salon $salon)
    {
        //
    }
}
