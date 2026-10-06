<x-mail::message>
# Pembayaran Berhasil Diverifikasi

Halo {{ $order->customer->first_name }},

Kabar baik! Pembayaran untuk pesanan Anda dengan nomor **{{ $order->order_number }}** telah berhasil diverifikasi.
Pesanan Anda sekarang masuk ke tahap pemrosesan dan akan segera disiapkan untuk pengiriman.

Terima kasih,<br>
Tim Jelita Saung
</x-mail::message>
