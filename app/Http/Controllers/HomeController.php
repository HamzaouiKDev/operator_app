<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Ajout nécessaire

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        $user = Auth::user();

        // Rediriger l'utilisateur en fonction de son rôle
        if ($user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('Téléopérateur')) {
            // Pour un téléopérateur, la page principale qui gère l'attribution
            // est gérée par EchantillonController. On le redirige donc là-bas.
            return redirect()->route('echantillons.index');
        }

        // Une vue par défaut pour tout autre cas
        return view('home');
    }
}
