<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewCustomerMail;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CustomerController extends Controller
{
    public function index()
    {
        return Customer::paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
          'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone' => 'nullable|string|max:20'
        ]);

        $password = Customer::generateRandomPassword();

        $customer = Customer::create([
            ...$validated,
            'password' => Hash::make($password),
            'is_active' => true
        ]);

        // Envoi du mail
        Mail::to($customer->email)->send(new NewCustomerMail($customer, $password));

        return response()->json([
            'message' => 'Client créé avec succès',
            'customer' => $customer
        ], 201);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:customers,email,'.$customer->id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean'
        ]);

        $customer->update($validated);

        return response()->json([
            'message' => 'Client mis à jour',
            'customer' => $customer
        ]);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json([
            'message' => 'Client supprimé'
        ]);
    }
}