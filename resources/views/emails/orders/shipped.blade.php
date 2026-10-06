<x-mail::message>
# Pesanan Dikirim

Halo {{ $order->customer->first_name }},

Pesanan Anda dengan nomor **{{ $order->order_number }}** telah dikirim melalui kurir **{{ strtoupper($order->courier) }}**.

Terima kasih atas pesanan Anda. Kami harap Anda menyukai produk kami!

Terima kasih,<br>
Tim Jelita Saung
</x-mail::message>
