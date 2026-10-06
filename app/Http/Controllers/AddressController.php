<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Return all addresses for the authenticated user (JSON for API calls).
     */
    public function index()
    {
        $addresses = Address::where('user_id', auth()->id())
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($addresses);
    }

    /**
     * Store a new address.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'label'             => 'nullable|string|max:100',
            'recipient_name'    => 'required|string|max:255',
            'phone'             => 'required|string|max:20',
            'address_line'      => 'required|string|max:500',
            'kelurahan'         => 'nullable|string|max:100',
            'city'              => 'nullable|string|max:100',
            'province'          => 'required|string|max:100',
            'postal_code'       => 'nullable|string|max:10',
            'destination_id'    => 'nullable|string',
            'destination_label' => 'nullable|string|max:255',
            'is_default'        => 'boolean',
        ]);

        $userId = auth()->id();
        $data['user_id'] = $userId;

        // If this is the first address or user wants it as default
        if (!empty($data['is_default']) && $data['is_default']) {
            Address::where('user_id', $userId)->update(['is_default' => false]);
        }

        // If no addresses exist yet, make this one default
        if (Address::where('user_id', $userId)->count() === 0) {
            $data['is_default'] = true;
        }

        $address = Address::create($data);

        return back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    /**
     * Update an existing address.
     */
    public function update(Request $request, Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $data = $request->validate([
            'label'             => 'nullable|string|max:100',
            'recipient_name'    => 'required|string|max:255',
            'phone'             => 'required|string|max:20',
            'address_line'      => 'required|string|max:500',
            'kelurahan'         => 'nullable|string|max:100',
            'city'              => 'nullable|string|max:100',
            'province'          => 'required|string|max:100',
            'postal_code'       => 'nullable|string|max:10',
            'destination_id'    => 'nullable|string',
            'destination_label' => 'nullable|string|max:255',
            'is_default'        => 'boolean',
        ]);

        if (!empty($data['is_default']) && $data['is_default']) {
            Address::where('user_id', auth()->id())
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($data);

        return back()->with('success', 'Alamat berhasil diperbarui.');
    }

    /**
     * Delete an address.
     */
    public function destroy(Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        $wasDefault = $address->is_default;
        $address->delete();

        // If deleted address was default, promote the next one
        if ($wasDefault) {
            $next = Address::where('user_id', auth()->id())->first();
            if ($next) {
                $next->update(['is_default' => true]);
            }
        }

        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    /**
     * Set an address as the default.
     */
    public function setDefault(Address $address)
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }

        Address::where('user_id', auth()->id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Alamat default diperbarui.');
    }
}
