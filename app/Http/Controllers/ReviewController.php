<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'order_id'   => 'required|exists:orders,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
        ]);

        $order = \App\Models\Order::where('id', $request->order_id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($order->status !== 'delivered') {
            return back()->with('error', 'Hanya pesanan yang sudah diterima yang bisa diulas.');
        }

        // Make sure this product was in the order
        $orderItem = $order->items()->where('product_id', $request->product_id)->first();
        if (!$orderItem) {
            return back()->with('error', 'Produk tidak ditemukan dalam pesanan ini.');
        }

        \App\Models\Review::updateOrCreate(
            [
                'product_id' => $request->product_id,
                'order_id'   => $request->order_id,
                'user_id'    => auth()->id(),
            ],
            [
                'rating'  => $request->rating,
                'comment' => $request->comment,
            ]
        );

        return back()->with('success', 'Ulasan berhasil disimpan.');
    }
}
