<?php

namespace App\Http\Controllers;

use App\Models\Salon;
use App\Models\Tatoueur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Illuminate\Validation\Rule; // Important pour exists et url

class TatoueurController extends Controller
{
    /**
     * Affiche le profil du tatoueur connecté ou invite à en créer un.
     * C'est la page principale de gestion du profil pour le tatoueur.
     */
    public function index()
    {
        $user = auth()->user(); // Ou auth()->guard('web')->user();

        // Trouve le profil tatoueur associé à l'utilisateur connecté
        $tatoueur = Tatoueur::where('user_id', $user->id)
            ->with([
                'user:id,name,email', // Charger l'utilisateur associé (champs spécifiques)
                'salonActuel:id,name,ville', // Charger le salon actuel (si défini et relation existe)
                'salons:id,name,ville', // Charger les salons auxquels il est lié (si relation many-to-many existe)
                'flashs', // Charger les flashs
                'portfolios', // Charger les portfolios
                'contractedSalons' // Charger les salons sous contrat
                ])
            ->first(); // Utilise first() car un utilisateur ne devrait avoir qu'un profil tatoueur

        // Récupère tous les salons pour un éventuel selecteur dans le modal
        // Utilisation de closure pour chargement paresseux (lazy loading) par Inertia
        $salonsDisponibles = fn () => Salon::select('id', 'name', 'ville')->get();

        // dd($tatoueur); // Pour débogage si besoin

        return Inertia::render('tatoueur/Index', [
            'tatoueur' => $tatoueur, // Peut être null si l'utilisateur n'a pas encore de profil
            'salonsDisponibles' => $salonsDisponibles, // Pour le champ 'localisation_actuelle'
            'csrf_token' => csrf_token(),
            // 'flash' est géré automatiquement par le middleware Inertia
        ]);
    }

    /**
     * Enregistre un nouveau profil tatoueur pour l'utilisateur connecté.
     * Appelée par le formulaire modal en mode création.
     */
    public function store(Request $request)
    {
        // Vérifier si l'utilisateur a déjà un profil (normalement géré par la logique front-end aussi)
        $user = auth()->user();
        if (Tatoueur::where('user_id', $user->id)->exists()) {
             return redirect()->back()->with('error', 'Vous avez déjà un profil tatoueur.');
        }

        // Vérifier la permission (si une policy existe)
        // Gate::authorize('create', Tatoueur::class);

        $validated = $request->validate([
            'bio' => 'nullable|string|max:2000',
            'style' => 'nullable|json', // S'assurer que le front envoie bien du JSON valide ou un objet/tableau
            'localisation_actuelle' => [
                'nullable',
                Rule::exists('salons', 'id') // Doit être un ID de salon valide ou null
            ],
            'disponibilites' => 'nullable|json', // S'assurer que le front envoie bien du JSON valide ou un objet/tableau
            'instagram' => ['nullable', 'string', 'max:255', 'url:http,https'], // Valider comme URL
        ]);

        // Associer l'ID de l'utilisateur connecté
        $validated['user_id'] = $user->id;

        // Gérer les champs JSON: s'assurer qu'ils sont bien encodés si besoin
        // Note: Laravel peut gérer automatiquement l'encodage/décodage si $casts est défini dans le modèle Tatoueur
        // Exemple: protected $casts = ['style' => 'array', 'disponibilites' => 'array'];
        // Si $casts n'est pas utilisé, il faudrait peut-être encoder ici:
        // $validated['style'] = isset($validated['style']) ? json_encode($validated['style']) : null;
        // $validated['disponibilites'] = isset($validated['disponibilites']) ? json_encode($validated['disponibilites']) : null;

        $tatoueur = Tatoueur::create($validated);

        // Redirection vers la page précédente (index) avec un message de succès
        return redirect()->back()->with('success', 'Profil tatoueur créé avec succès.');
    }


    /**
     * Met à jour le profil tatoueur existant de l'utilisateur connecté.
     * Appelée par le formulaire modal en mode édition.
     */
    public function update(Request $request, Tatoueur $tatoueur)
    {
        // Vérifier que l'utilisateur modifie bien son propre profil
        $user = auth()->user();
        if ($tatoueur->user_id !== $user->id) {
            abort(403, 'Action non autorisée.');
        }

        // Vérifier la permission (si une policy existe)
        // Gate::authorize('update', $tatoueur);

        $validated = $request->validate([
            'bio' => 'nullable|string|max:2000',
            'style' => 'nullable|json',
            'localisation_actuelle' => [
                'nullable',
                Rule::exists('salons', 'id') // Doit être un ID de salon valide ou null
            ],
            'disponibilites' => 'nullable|json',
            'instagram' => ['nullable', 'string', 'max:255', 'url:http,https'],
        ]);

        // Gérer les champs JSON si $casts n'est pas utilisé (voir commentaire dans store())
        // $validated['style'] = isset($validated['style']) ? json_encode($validated['style']) : null;
        // $validated['disponibilites'] = isset($validated['disponibilites']) ? json_encode($validated['disponibilites']) : null;

        $tatoueur->update($validated);

        // Redirection vers la page précédente (index) avec un message de succès
        // Inertia rechargera les données automatiquement avec le profil mis à jour
        return redirect()->back()->with('success', 'Profil tatoueur mis à jour avec succès.');
    }

    /**
     * Supprime le profil tatoueur de l'utilisateur connecté.
     * Appelée depuis la page index.
     */
    public function destroy(Tatoueur $tatoueur)
    {
        // Vérifier que l'utilisateur supprime bien son propre profil
        $user = auth()->user();
        if ($tatoueur->user_id !== $user->id) {
            abort(403, 'Action non autorisée.');
        }

        // Vérifier la permission (si une policy existe)
        // Gate::authorize('delete', $tatoueur);

        $tatoueur->delete(); // Utilise SoftDelete si le modèle l'a activé, sinon suppression permanente

        // Redirection vers la même page. Le profil aura disparu.
        // On pourrait aussi rediriger vers le dashboard : return redirect()->route('dashboard')->with(...)
        return redirect()->back()->with('success', 'Profil tatoueur supprimé avec succès.');
    }

    // --- Méthodes potentiellement non nécessaires si tout est géré via le modal sur Index ---
    // public function show(Tatoueur $tatoueur) -> Probablement pas utile si Index affiche déjà les détails
    // public function create() -> Remplacé par le modal sur Index
    // public function edit(Tatoueur $tatoueur) -> Remplacé par le modal sur Index
}