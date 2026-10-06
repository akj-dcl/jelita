<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return Inertia::render('admin/Profile', [
            'mustVerifyEmail' => false,
            'status' => session('status'),
            'user' => $request->user()
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'required_with:current_password', Password::defaults(), 'confirmed'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return back()->with('status', 'profile-updated');
    }
}
