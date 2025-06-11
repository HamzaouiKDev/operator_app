<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Définit la destination de redirection après une connexion réussie,
     * en fonction du rôle de l'utilisateur.
     *
     * @return string
     */
    protected function redirectTo()
    {
        $user = Auth::user();

        // On vérifie que l'utilisateur existe avant de tester ses rôles
        if ($user) {
            // Si l'utilisateur a le rôle 'Admin', on le redirige vers le dashboard admin
            if ($user->hasRole('Admin')) {
                return route('admin.dashboard');
            }

            // Si l'utilisateur a le rôle 'Téléopérateur', on le redirige vers sa page d'accueil
            if ($user->hasRole('Téléopérateur')) {
                return route('home');
            }
        }

        // Redirection par défaut si l'utilisateur n'a pas de rôle spécifique ou n'existe pas
        return '/';
    }
}