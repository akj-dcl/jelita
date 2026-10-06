<x-mail::message>
# Pesanan Berhasil Dibuat

Halo {{ $order->customer->first_name }},

Terima kasih atas pesanan Anda di Jelita Saung. Pesanan Anda dengan nomor **{{ $order->order_number }}** sedang menunggu verifikasi pembayaran.

**Total Belanja: Rp {{ number_format($order->total_amount, 0, ',', '.') }}**
**Metode Pembayaran: {{ $order->payment->payment_method ?? 'Transfer' }}**

Harap pastikan Anda telah mengunggah bukti pembayaran jika Anda memilih transfer bank/QRIS.

Terima kasih,<br>
Tim Jelita Saung
</x-mail::message>
