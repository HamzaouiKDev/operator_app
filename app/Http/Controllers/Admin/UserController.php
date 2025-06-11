<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs et les rôles pour les modales.
     */
    public function index()
    {
        $users = User::latest()->paginate(10); // Trie par les plus récents
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Enregistre un nouvel utilisateur (répond en JSON).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return response()->json(['success' => 'Utilisateur créé avec succès !']);
    }

    /**
     * Récupère les données d'un utilisateur pour la modale d'édition (répond en JSON).
     */
    public function edit(Request $request, User $user)
    {
        // On s'assure de répondre en JSON pour notre JavaScript
        if ($request->ajax() || $request->wantsJson()) {
            $roles = Role::all();
            $userRoles = $user->getRoleNames();
            
            return response()->json([
                'user' => $user,
                'roles' => $roles,
                'userRoles' => $userRoles
            ]);
        }
        
        // Fallback si l'URL est accédée directement
        abort(404);
    }

    /**
     * Met à jour un utilisateur (répond en JSON).
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user->update($request->only('name', 'email'));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        $user->syncRoles($request->role);

        return response()->json(['success' => 'Utilisateur mis à jour avec succès !']);
    }

    /**
     * Supprime un utilisateur.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'Vous ne pouvez pas supprimer votre propre compte !');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'Utilisateur supprimé avec succès !');
    }
}