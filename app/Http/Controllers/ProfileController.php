<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $user   = auth()->user();

        // Fetch orders for the logged-in user with items and payment
        $orders = Order::where('user_id', $userId)
            ->with(['items', 'payment', 'reviews'])
            ->latest()
            ->get()
            ->map(function ($order) {
                $payment = $order->payment;
                return [
                    'id'               => $order->id,
                    'order_number'     => $order->order_number,
                    'tracking_number'  => $order->tracking_number,
                    'status'           => $order->status,
                    'subtotal'         => $order->subtotal,
                    'shipping_cost'    => $order->shipping_cost,
                    'tax'              => $order->tax,
                    'total'            => $order->total,
                    'shipping_method'  => $order->shipping_method,
                    'ship_name'        => $order->ship_name,
                    'ship_phone'       => $order->ship_phone,
                    'ship_address'     => $order->ship_address,
                    'ship_city'        => $order->ship_city,
                    'ship_province'    => $order->ship_province,
                    'ship_postal_code' => $order->ship_postal_code,
                    'notes'            => $order->notes,
                    'created_at'       => $order->created_at->format('d M Y, H:i'),
                    'items'            => $order->items->map(function($i) use ($order) {
                        $review = $order->reviews->where('product_id', $i->product_id)->first();
                        return [
                            'id'            => $i->id,
                            'product_id'    => $i->product_id,
                            'product_name'  => $i->product_name,
                            'product_price' => $i->product_price,
                            'quantity'      => $i->quantity,
                            'subtotal'      => $i->subtotal,
                            'review'        => $review ? [
                                'rating' => $review->rating,
                                'comment' => $review->comment
                            ] : null
                        ];
                    }),
                    'payment' => $payment ? [
                        'method'         => $payment->method,
                        'status'         => $payment->status,
                        'amount'         => $payment->amount,
                        'transfer_proof' => $payment->transfer_proof
                            ? asset('storage/' . $payment->transfer_proof)
                            : null,
                        'paid_at'        => $payment->paid_at?->format('d M Y'),
                    ] : null,
                ];
            });

        $addresses = Address::where('user_id', $userId)
            ->orderByDesc('is_default')
            ->orderByDesc('created_at')
            ->get();

        return inertia('MyProfile', [
            'orders'    => $orders,
            'addresses' => $addresses,
            'authUser'  => [
                'id'            => $user->id,
                'name'          => $user->name,
                'email'         => $user->email,
                'phone'         => $user->phone,
                'profile_photo' => $user->profile_photo
                    ? asset('storage/' . $user->profile_photo)
                    : null,
                'date_of_birth' => $user->date_of_birth,
                'role'          => $user->role,
            ],
        ]);
    }

    /**
     * Update profile info (name, phone, photo).
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'phone'         => 'nullable|string|max:20',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo'] = $path;
        } else {
            unset($data['profile_photo']);
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Password berhasil diperbarui.');
    }

    public function invoice(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load(['user', 'items', 'payment']);
        return view('invoice', compact('order'));
    }

    public function confirmReceived(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($order->status !== 'shipped') {
            return back()->with('error', 'Status pesanan tidak valid untuk dikonfirmasi.');
        }

        $order->update(['status' => 'delivered']);

        return back()->with('success', 'Pesanan telah diterima.');
    }
}
