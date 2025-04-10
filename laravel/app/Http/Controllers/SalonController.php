<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
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
        $salons = $salons = Salon::withTrashed()->where('gestionnaire_id', $user->id)->orderBy('created_at', 'desc')->get();

        $lastSalon = Salon::getLastCreated();
        
        // dd($lastSalon); 
        // dd($salons->id); 
        // Retourner une vue avec les salons
        return inertia('Salons/Index3', [
            'salons' => $salons,
            'last' => $lastSalon,
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

        try {
        // Validation des données
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|min:3',
            'description' => 'required|string|min:3',
            'adresse' => 'required|string|max:255|min:3',
            'ville' => 'required|string|max:255|min:3',
            'code_postal' => 'required|string|max:250|min:3',
            'pays' => 'required|string|max:255|min:3',
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

        // return to_route('salons.index', ['newSalonId' => $salon->id,])->with([
        //     'success' => 'Salon '. $salon->name .' créé avec succès.',
        //     // 'newSalonId' => $salon->id, // Inclure l'ID du salon créé
        // ]);
       
        // return Inertia::render('Salons/Index2', [
        //     'flash' => ['success' => 'Salon '. $salon->name .' créé avec succès.'],
        //     'newSalon' => $salon, // Inclure l'objet salon ou ses données nécessaires
        //     'salons' => Salon::all(), // Liste des salons si nécessaire
        // ])->with('success', 'Salon '. $salon->name .' créé avec succès.');
        // $lastSalon = Salon::getLastCreated();

        // return inertia('Salons/Index2', [
        //     'salons' => $salons,
        //     'last' => $lastSalon,
        //     'csrf_token' => csrf_token(), // Injecte le token CSRF dans la réponse
        // ]);

        // return Inertia::render('Salons/Index2', ['last' => $lastSalon])
        //     ->withViewData(['meta' => $lastSalon->meta]);

        // return response()->json([
        //     'dernier_salon' => $lastSalon
        // ], 201);
        // return to_route('salons.index')->with('success', 'Salon '. $salon->name .' créé avec succès.');
        // return to_route('tatoueur.index')->with('success', 'Salon '. $salon->name .' créé avec succès.');
        
        return back()->with('success', 'Salon '. $salon->name .' créé avec succès.');

    } catch (ValidationException $e) {
        // Si la validation échoue, redirigez avec des erreurs personnalisées
        // dd($e->getMessage());
        return back()
            ->withErrors($e->errors()) // Récupère les erreurs
            ->withInput()
            ->with('error', $e->getMessage());
             // Conserve les données saisies
    }
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

        try { 
            // Validation des données
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|min:3',
                'description' => 'required|string|max:255|min:3',
                'adresse' => 'required|string|max:255|min:3',
                'ville' => 'required|string|max:255|min:3',
                'code_postal' => 'required|string|max:10|min:3',
                'pays' => 'required|string|max:255|min:3',
            ]);

            // dd($validatedData);
            
            // Mise à jour des données
            $salon->update($validatedData);

            return redirect()->back()->with('success', 'Salon mis à jour avec succès.');

            // return redirect()->route('salons.index', $salon)->with('success', 'Salon mis à jour avec succès.');
        } catch (ValidationException $e) {
        // Si la validation échoue, redirigez avec des erreurs personnalisées
        // dd($e->getMessage());
        return back()
            ->withErrors($e->errors()) // Récupère les erreurs
            ->withInput()
            ->with('error', $e->getMessage());
             // Conserve les données saisies
    }
    }

    public function restore($id)
    {
        // Récupérer le salon supprimé
        $salon = Salon::withTrashed()->find($id);

        if (!$salon) {
            return redirect()->route('salons.index')->with('error', 'Salon non trouvé.');
        }

        // Restaurer le salon
        $salon->restore();

        return redirect()->route('salons.index')->with('success', 'Le salon a été restauré avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Salon $salon)
    {
        // $user = $request->user();
        if (auth()->user()->id == $salon->gestionnaire_id){
            $salon->delete();
            return redirect()->route('salons.index')->with('success', 'Salon ['.$salon->name.'] supprimé avec succès.');
        }
        return redirect()->route('salons.index')->with('error', 'Une erreur est survenue.');

        // dd(auth()->user());
        // dd($salon.gestionnaire_id);
    }

    public function forceDelete($id)
    {
        // Récupérer le salon supprimé (soft deleted ou non)
        $salon = Salon::withTrashed()->find($id);

        if (!$salon) {
            return redirect()->back()->with('error', 'Salon non trouvé.');

            // return response()->json(['message' => 'Salon non trouvé.'], 404);
        }

        // Supprimer définitivement le salon
        $salon->forceDelete();
        return redirect()->back()->with('success', 'Salon supprimé définitivement avec succès.');

        // return response()->json(['message' => 'Salon supprimé définitivement.']);
    }

}


// public function destroy(Request $request): RedirectResponse
// {
//     $request->validate([
//         'password' => ['required', 'current_password'],
//     ]);

//     $user = $request->user();

//     Auth::logout();

//     $user->delete();

//     $request->session()->invalidate();
//     $request->session()->regenerateToken();

//     return redirect('/');
// }
