<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; // <-- Make sure this is imported
use Illuminate\Support\Facades\Auth; // <-- Make sure this is imported

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * This is a fallback and will be overridden by our custom logic.
     */
    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * This is the correct method for handling redirection after a user is logged in.
     * It runs after successful authentication and directs the user based on their role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        // Check roles and return a full redirect response
        if ($user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('Superviseur')) {
            return redirect()->route('supervisor.dashboard');
        }

        if ($user->hasRole('Téléopérateur')) {
            return redirect()->route('home');
        }

        // Default redirect if no specific role matches
        return redirect($this->redirectTo);
    }
}