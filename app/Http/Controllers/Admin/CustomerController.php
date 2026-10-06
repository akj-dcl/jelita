<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index()
    {
        $query = User::where('role', 'customer')->latest();
        
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $customers = $query->paginate(10)->withQueryString();
            
        $stats = [
            'total' => User::where('role', 'customer')->count(),
            'verified' => User::where('role', 'customer')->whereNotNull('email_verified_at')->count(),
            'unverified' => User::where('role', 'customer')->whereNull('email_verified_at')->count(),
            'new_this_month' => User::where('role', 'customer')->whereMonth('created_at', now()->month)->count()
        ];
            
        return Inertia::render('admin/customers/Index', [
            'customers' => $customers,
            'stats' => $stats,
            'filters' => request()->only(['search'])
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/customers/Create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8',
        ]);
        
        $validated['role'] = 'customer';
        $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully');
    }

    public function edit(User $customer)
    {
        return Inertia::render('admin/customers/Edit', [
            'customer' => $customer
        ]);
    }

    public function update(\Illuminate\Http\Request $request, User $customer)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $customer->id,
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Preserve email_verified_at — admin-edited emails are trusted, 
        // no need to re-verify and lock the user out.
        $emailChanged = $customer->email !== $validated['email'];
        $customer->update($validated);
        if ($emailChanged) {
            $customer->forceFill(['email_verified_at' => now()])->save();
        }

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(User $customer)
    {
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully');
    }
}
