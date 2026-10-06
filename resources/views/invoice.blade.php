<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 40px;
            background: #fff;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
        }
        .header .logo h1 {
            margin: 0;
            color: #a44f5b;
            font-size: 28px;
        }
        .header .invoice-details text-align: right; {
            text-align: right;
        }
        .invoice-details h2 {
            margin: 0 0 5px 0;
            font-size: 24px;
            color: #555;
        }
        .invoice-details p {
            margin: 0;
            color: #777;
        }
        .info-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .info-box {
            width: 48%;
        }
        .info-box h3 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 16px;
            color: #555;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }
        .info-box p {
            margin: 0 0 5px 0;
            font-size: 14px;
        }
        table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        table th, table td {
            padding: 12px;
            vertical-align: top;
            border-bottom: 1px solid #eee;
        }
        table th {
            background: #f8f8f8;
            font-weight: bold;
            color: #555;
        }
        table td.right, table th.right {
            text-align: right;
        }
        table td.center, table th.center {
            text-align: center;
        }
        .totals {
            width: 50%;
            float: right;
        }
        .totals table th, .totals table td {
            padding: 8px 12px;
            border-bottom: none;
        }
        .totals table tr.total td {
            border-top: 2px solid #eee;
            font-weight: bold;
            font-size: 18px;
            color: #a44f5b;
        }
        .footer {
            clear: both;
            margin-top: 60px;
            text-align: center;
            color: #777;
            font-size: 12px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        @media print {
            body { padding: 0; }
            .invoice-box { box-shadow: none; border: none; padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="invoice-box">
        <div class="header">
            <div class="logo">
                <h1>LaraCommerce</h1>
            </div>
            <div class="invoice-details" style="text-align: right;">
                <h2>INVOICE</h2>
                <p><strong>Order #:</strong> {{ $order->order_number }}</p>
                <p><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y') }}</p>
                <p><strong>Status:</strong> <span style="text-transform: uppercase;">{{ $order->status }}</span></p>
            </div>
        </div>

        <div class="info-section">
            <div class="info-box">
                <h3>Informasi Pelanggan</h3>
                <p><strong>{{ $order->user ? $order->user->name : $order->ship_name }}</strong></p>
                @if($order->user)
                    <p>{{ $order->user->email }}</p>
                @endif
                <p>{{ $order->ship_phone }}</p>
            </div>
            <div class="info-box">
                <h3>Alamat Pengiriman</h3>
                <p><strong>{{ $order->ship_name }}</strong></p>
                <p>{{ $order->ship_address }}</p>
                <p>{{ $order->ship_city }}, {{ $order->ship_province }}</p>
                <p>{{ $order->ship_postal_code }}</p>
                <p style="margin-top: 10px;"><strong>Kurir:</strong> <span style="text-transform: uppercase;">{{ $order->shipping_method }}</span></p>
                @if($order->tracking_number)
                    <p><strong>Resi:</strong> {{ $order->tracking_number }}</p>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="center">Qty</th>
                    <th class="right">Harga</th>
                    <th class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">Rp {{ number_format($item->product_price, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td class="right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Ongkos Kirim</td>
                    <td class="right">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Pajak (11%)</td>
                    <td class="right">Rp {{ number_format($order->tax, 0, ',', '.') }}</td>
                </tr>
                <tr class="total">
                    <td>Total</td>
                    <td class="right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div style="clear: both; margin-top: 40px;">
            <h3>Informasi Pembayaran</h3>
            @if($order->payment)
                <p>Metode: <strong style="text-transform: uppercase;">{{ str_replace('_', ' ', $order->payment->method) }}</strong></p>
                <p>Status: <strong style="text-transform: uppercase;">{{ $order->payment->status }}</strong></p>
                @if($order->payment->paid_at)
                    <p>Waktu Pembayaran: {{ \Carbon\Carbon::parse($order->payment->paid_at)->format('d M Y H:i') }}</p>
                @endif
            @else
                <p>Menunggu Pembayaran</p>
            @endif
        </div>

        <div class="footer">
            <p>Terima kasih atas pesanan Anda.</p>
            <p>Jika ada pertanyaan, silakan hubungi customer service kami.</p>
        </div>
    </div>
</body>
</html>
