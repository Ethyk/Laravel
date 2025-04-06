<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Portfolio;

class PortfolioPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Détermine si l'utilisateur peut visualiser le portfolio.
     */
    public function view(User $user, Portfolio $portfolio)
    {
        return true; // Tout le monde peut voir le portfolio
    }

    /**
     * Détermine si l'utilisateur peut créer un portfolio.
     */
    public function create(User $user)
    {
        return $user->role === User::ROLE_TATOUEUR || $user->role === User::ROLE_ADMIN;
    }

    /**
     * Détermine si l'utilisateur peut éditer le portfolio.
     */
    public function update(User $user, Portfolio $portfolio)
    {
        return $user->id === $portfolio->tatoueur->user_id || $user->role === User::ROLE_ADMIN;
    }

    /**
     * Détermine si l'utilisateur peut supprimer le portfolio.
     */
    public function delete(User $user, Portfolio $portfolio)
    {
        return $user->id === $portfolio->tatoueur->user_id || $user->role === User::ROLE_ADMIN;
    }
}
