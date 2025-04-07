<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Portfolio;
use App\Models\User;
use App\Policies\PortfolioPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Le tableau des associations entre modèles et policies.
     *
     * @var array
     */
    protected $policies = [
        Portfolio::class => PortfolioPolicy::class,
    ];

    /**
     * Enregistrer tous les services nécessaires.
     */
    public function register(): void
    {
        // Cette méthode peut être utilisée pour enregistrer des services,
        // mais ce n'est pas nécessaire ici car tout est défini dans boot().
    }

    /**
     * Bootstrap des services d'authentification et d'autorisation.
     */
    public function boot(): void
    {
        // Enregistrement des policies
        $this->registerPolicies();

        // Définir les Gates pour les salons
        Gate::define('create-tatoueur', function ($user) {
            return $user->hasRole([User::ROLE_GESTIONNAIRE, User::ROLE_ADMIN]);
        });

        // Définir les Gates pour les salons
        Gate::define('create-salon', function ($user) {
            return $user->hasRole([User::ROLE_GESTIONNAIRE, User::ROLE_ADMIN]);
        });

        Gate::define('manage-salon', function ($user) {
            return $user->hasRole([User::ROLE_GESTIONNAIRE, User::ROLE_ADMIN]);
        });

        Gate::define('view-salon', function ($user) {
            return $user->hasRole([
                User::ROLE_CLIENT,
                User::ROLE_TATOUEUR,
                User::ROLE_GESTIONNAIRE,
                User::ROLE_ADMIN
            ]);
        });

        // Définir les Gates pour les portfolios
        Gate::define('create-portfolio', function ($user) {
            return $user->hasRole([User::ROLE_TATOUEUR, User::ROLE_ADMIN]);
        });

        Gate::define('edit-portfolio', function ($user, $portfolio) {
            return $user->id === $portfolio->tatoueur->user_id || $user->role === User::ROLE_ADMIN;
        });

        Gate::define('view-portfolio', function ($user) {
            return $user->hasRole([
                User::ROLE_CLIENT,
                User::ROLE_TATOUEUR,
                User::ROLE_GESTIONNAIRE,
                User::ROLE_ADMIN
            ]);
        });

        Gate::define('delete-portfolio', function ($user, $portfolio) {
            return $user->id === $portfolio->tatoueur->user_id || $user->role === User::ROLE_ADMIN;
        });

        // Définir les Gates pour les flashs
        Gate::define('create-flash', function ($user) {
            return $user->hasRole([User::ROLE_TATOUEUR, User::ROLE_ADMIN]);
        });

        Gate::define('manage-flash', function ($user) {
            return $user->hasRole([User::ROLE_TATOUEUR, User::ROLE_ADMIN]);
        });

        Gate::define('view-flash', function ($user) {
            return $user->hasRole([
                User::ROLE_CLIENT,
                User::ROLE_TATOUEUR,
                User::ROLE_GESTIONNAIRE,
                User::ROLE_ADMIN
            ]);
        });

        // Définir les Gates pour les demandes
        Gate::define('create-demande', function ($user) {
            return $user->role === User::ROLE_CLIENT;
        });

        Gate::define('view-demande', function ($user) {
            return $user->hasRole([
                User::ROLE_CLIENT,
                User::ROLE_TATOUEUR,
                User::ROLE_GESTIONNAIRE,
                User::ROLE_ADMIN
            ]);
        });

        Gate::define('manage-demande', function ($user) {
            return $user->hasRole([User::ROLE_GESTIONNAIRE, User::ROLE_ADMIN]);
        });
    }
}
