<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects']
            ]);
        }

        // Vérifier si le compte est actif
        if (!$customer->is_active) {
            return response()->json([
                'message' => 'Votre compte est désactivé'
            ], 403);
        }

        // Enregistrer l'activité de connexion
        Activity::create([
            'customer_id' => $customer->id,
            'type' => 'login',
            'description' => 'Connexion au compte',
            'ip_address' => $request->ip()
        ]);

        // Mettre à jour la dernière connexion
        $customer->update(['last_login' => now()]);

        // Générer un token
        $token = $customer->createToken('customer_token')->plainTextToken;

        return response()->json([
            'customer' => $customer,
            'token' => $token
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed'
        ]);

        $customer = Auth::user();

        // Vérifier le mot de passe actuel
        if (!Hash::check($request->current_password, $customer->password)) {
            return response()->json([
                'message' => 'Le mot de passe actuel est incorrect'
            ], 400);
        }

        $customerToUpdate = Customer::find($customer->id);

        // Mettre à jour le mot de passe
        $customerToUpdate->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Enregistrer l'activité
        Activity::create([
            'customer_id' => $customer->id,
            'type' => 'password_change',
            'description' => 'Changement de mot de passe',
            'ip_address' => $request->ip()
        ]);

        return response()->json([
            'message' => 'Mot de passe modifié avec succès'
        ]);
    }

    public function logout(Request $request)
    {
        // Enregistrer l'activité de déconnexion
        Activity::create([
            'client_id' => Auth::id(),
            'type' => 'logout',
            'description' => 'Déconnexion du compte',
            'ip_address' => $request->ip()
        ]);

        // Révoquer tous les tokens du client
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }

    public function profile()
    {
        return response()->json(Auth::user());
    }

}