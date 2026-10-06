<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'items', 'payment'])->latest()->paginate(15);
        
        $stats = [
            'pending_payment' => Order::where('status', 'pending_payment')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'total' => Order::count(),
            'revenue' => Order::whereIn('status', ['processing', 'shipped', 'delivered'])->sum('total')
        ];

        $ordersData = $orders->through(function ($order) {
            $payment = $order->payment;
            return [
                'id'              => $order->id,
                'order_number'    => $order->order_number,
                'tracking_number' => $order->tracking_number,
                'status'          => $order->status,
                'total'           => $order->total,
                'subtotal'        => $order->subtotal,
                'shipping_cost'   => $order->shipping_cost,
                'tax'             => $order->tax,
                'shipping_method' => $order->shipping_method,
                'ship_name'       => $order->ship_name,
                'ship_phone'      => $order->ship_phone,
                'ship_address'    => $order->ship_address,
                'ship_city'       => $order->ship_city,
                'ship_province'   => $order->ship_province,
                'ship_postal_code'=> $order->ship_postal_code,
                'notes'           => $order->notes,
                'created_at'      => $order->created_at,
                'customer_name'   => $order->user ? $order->user->name : $order->ship_name,
                'customer_email'  => $order->user ? $order->user->email : '',
                'items_count'     => $order->items->count(),
                'items'           => $order->items->map(fn($i) => [
                    'product_name'  => $i->product_name,
                    'product_price' => $i->product_price,
                    'quantity'      => $i->quantity,
                    'subtotal'      => $i->subtotal,
                ]),
                'payment' => $payment ? [
                    'id'             => $payment->id,
                    'method'         => $payment->method,
                    'status'         => $payment->status,
                    'amount'         => $payment->amount,
                    'transfer_proof' => $payment->transfer_proof
                        ? asset('storage/' . $payment->transfer_proof)
                        : null,
                    'paid_at'        => $payment->paid_at,
                    'verified_at'    => $payment->verified_at,
                ] : null,
            ];
        });

        return Inertia::render('admin/orders/Index', [
            'orders' => $ordersData,
            'stats' => $stats,
        ]);
    }

    public function destroy(Order $order)
    {
        $order->items()->delete();
        $order->payment()->delete();
        $order->delete();
        return redirect()->back()->with('success', 'Pesanan berhasil dihapus.');
    }

    public function verify(Order $order)
    {
        $order->update(['status' => 'processing']);

        $payment = $order->payment;
        if ($payment) {
            $payment->update([
                'status'      => 'paid',
                'paid_at'     => now(),
                'verified_by' => auth()->id(),
                'verified_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function reject(Order $order)
    {
        $order->update(['status' => 'cancelled']);

        $payment = $order->payment;
        if ($payment) {
            $payment->update([
                'status'           => 'failed',
                'rejection_reason' => 'Bukti pembayaran tidak valid.',
            ]);
        }

        return redirect()->back()->with('success', 'Pembayaran ditolak dan pesanan dibatalkan.');
    }

    public function ship(Request $request, Order $order)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:255',
        ]);

        $order->update([
            'status' => 'shipped',
            'tracking_number' => $request->tracking_number,
        ]);

        return redirect()->back()->with('success', 'Pesanan berhasil ditandai sebagai dikirim.');
    }

    public function invoice(Order $order)
    {
        $order->load(['user', 'items', 'payment']);
        return view('invoice', compact('order'));
    }
}
