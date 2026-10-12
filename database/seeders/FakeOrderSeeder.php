<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FakeOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            // 1. Data 12 Akun Pelanggan Baru
            $customersData = [
                [
                    'name' => 'Rina Kusuma',
                    'email' => 'rina.kusuma88@gmail.com',
                    'phone' => '081234567801',
                    'dob' => '1994-04-12',
                    'address' => 'Jl. Cihampelas No. 128',
                    'city' => 'Bandung',
                    'province' => 'Jawa Barat',
                    'postal_code' => '40131',
                    'kelurahan' => 'Cipaganti',
                ],
                [
                    'name' => 'Bayu Pratama',
                    'email' => 'bayupratama.id@gmail.com',
                    'phone' => '081234567802',
                    'dob' => '1992-08-25',
                    'address' => 'Jl. Fatmawati Raya No. 45',
                    'city' => 'Jakarta Selatan',
                    'province' => 'DKI Jakarta',
                    'postal_code' => '12410',
                    'kelurahan' => 'Cilandak Barat',
                ],
                [
                    'name' => 'Nadia Safitri',
                    'email' => 'nadiasafitri.ns@gmail.com',
                    'phone' => '081234567803',
                    'dob' => '1997-11-03',
                    'address' => 'Jl. Buah Batu No. 72',
                    'city' => 'Bandung',
                    'province' => 'Jawa Barat',
                    'postal_code' => '40265',
                    'kelurahan' => 'Turangga',
                ],
                [
                    'name' => 'Fajar Ramadhan',
                    'email' => 'fajarramadhan77@gmail.com',
                    'phone' => '081234567804',
                    'dob' => '1995-02-18',
                    'address' => 'Jl. Manyar Kertoarjo No. 15',
                    'city' => 'Surabaya',
                    'province' => 'Jawa Timur',
                    'postal_code' => '60285',
                    'kelurahan' => 'Mojo',
                ],
                [
                    'name' => 'Siti Aminah',
                    'email' => 'sitiaminah.bdg@gmail.com',
                    'phone' => '081234567805',
                    'dob' => '1991-07-30',
                    'address' => 'Jl. Dago No. 84',
                    'city' => 'Bandung',
                    'province' => 'Jawa Barat',
                    'postal_code' => '40132',
                    'kelurahan' => 'Lebak Siliwangi',
                ],
                [
                    'name' => 'Deni Kurniawan',
                    'email' => 'denikurniawan90@gmail.com',
                    'phone' => '081234567806',
                    'dob' => '1990-10-09',
                    'address' => 'Jl. Malioboro No. 56',
                    'city' => 'Yogyakarta',
                    'province' => 'DI Yogyakarta',
                    'postal_code' => '55271',
                    'kelurahan' => 'Suryatmajan',
                ],
                [
                    'name' => 'Putri Handayani',
                    'email' => 'putrihandayani.ph@gmail.com',
                    'phone' => '081234567807',
                    'dob' => '1996-05-14',
                    'address' => 'Jl. Pandanaran No. 102',
                    'city' => 'Semarang',
                    'province' => 'Jawa Tengah',
                    'postal_code' => '50241',
                    'kelurahan' => 'Mugassari',
                ],
                [
                    'name' => 'Rizky Hidayat',
                    'email' => 'rizkyhidayat.rh@gmail.com',
                    'phone' => '081234567808',
                    'dob' => '1993-09-20',
                    'address' => 'Jl. Ahmad Yani No. 33',
                    'city' => 'Bekasi',
                    'province' => 'Jawa Barat',
                    'postal_code' => '17141',
                    'kelurahan' => 'Marga Jaya',
                ],
                [
                    'name' => 'Wulan Sari',
                    'email' => 'wulansari.ws@gmail.com',
                    'phone' => '081234567809',
                    'dob' => '1998-03-22',
                    'address' => 'Jl. Boulevard Gading Serpong No. 8',
                    'city' => 'Tangerang',
                    'province' => 'Banten',
                    'postal_code' => '15810',
                    'kelurahan' => 'Pakulonan Barat',
                ],
                [
                    'name' => 'Andi Wijaya',
                    'email' => 'andiwijaya.aw@gmail.com',
                    'phone' => '081234567810',
                    'dob' => '1989-12-05',
                    'address' => 'Jl. Ijen No. 44',
                    'city' => 'Malang',
                    'province' => 'Jawa Timur',
                    'postal_code' => '65115',
                    'kelurahan' => 'Oro-oro Dowo',
                ],
                [
                    'name' => 'Lestari Dewi',
                    'email' => 'lestaridewi.ld@gmail.com',
                    'phone' => '081234567811',
                    'dob' => '1995-06-17',
                    'address' => 'Jl. Margonda Raya No. 210',
                    'city' => 'Depok',
                    'province' => 'Jawa Barat',
                    'postal_code' => '16423',
                    'kelurahan' => 'Kemiri Muka',
                ],
                [
                    'name' => 'Hendra Saputra',
                    'email' => 'hendrasaputra.hs@gmail.com',
                    'phone' => '081234567812',
                    'dob' => '1992-01-28',
                    'address' => 'Jl. Pajajaran No. 78',
                    'city' => 'Bogor',
                    'province' => 'Jawa Barat',
                    'postal_code' => '16128',
                    'kelurahan' => 'Bantarjati',
                ],
            ];

            $usersByEmail = [];

            foreach ($customersData as $c) {
                $user = User::firstOrCreate(
                    ['email' => $c['email']],
                    [
                        'name' => $c['name'],
                        'password' => Hash::make('jelita2026'),
                        'phone' => $c['phone'],
                        'date_of_birth' => $c['dob'],
                        'role' => 'customer',
                        'status' => 'active',
                        'email_verified_at' => Carbon::parse('2026-10-01 08:00:00'),
                        'created_at' => Carbon::parse('2026-10-01 08:00:00'),
                        'updated_at' => Carbon::parse('2026-10-01 08:00:00'),
                    ]
                );

                // Tambahkan Default Address
                Address::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'is_default' => 1,
                    ],
                    [
                        'label' => 'Rumah',
                        'recipient_name' => $user->name,
                        'phone' => $user->phone,
                        'address_line' => $c['address'],
                        'city' => $c['city'],
                        'province' => $c['province'],
                        'postal_code' => $c['postal_code'],
                        'kelurahan' => $c['kelurahan'],
                        'created_at' => Carbon::parse('2026-10-01 08:05:00'),
                        'updated_at' => Carbon::parse('2026-10-01 08:05:00'),
                    ]
                );

                $usersByEmail[$c['email']] = [
                    'user' => $user,
                    'address' => $c,
                ];
            }

            // 2. Ambil Data Produk
            $products = Product::whereIn('id', [24, 25, 26, 27, 28, 29, 30])->get()->keyBy('id');

            // 3. Konfigurasi 25 Pesanan (1 Okt - 11 Okt 2026)
            $ordersPlan = [
                // 1 Okt
                [
                    'date' => '2026-10-01 09:15:00',
                    'email' => 'rina.kusuma88@gmail.com',
                    'items' => [[25, 2], [29, 3]], // Pangsit(2), Donat(3)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'BCA',
                    'status' => 'delivered',
                    'review' => [
                        'product_id' => 25,
                        'rating' => 5,
                        'comment' => 'Pangsitnya garing dan gurih, ukurannya pas buat cemilan sore. Pasti beli lagi!',
                    ],
                ],
                [
                    'date' => '2026-10-01 14:30:00',
                    'email' => 'bayupratama.id@gmail.com',
                    'items' => [[27, 1], [28, 2]], // Sourdough(1), Choco Bread(2)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'Mandiri',
                    'status' => 'delivered',
                    'review' => [
                        'product_id' => 27,
                        'rating' => 5,
                        'comment' => 'Roti sourdough nya lembut banget, cheese cranberry nya berasa. Recommended!',
                    ],
                ],
                // 2 Okt
                [
                    'date' => '2026-10-02 10:00:00',
                    'email' => 'nadiasafitri.ns@gmail.com',
                    'items' => [[24, 2], [30, 1]], // Lilin Soy Wax(2), Lilin Upcycle(1)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'pos',
                    'payment_method' => 'qris',
                    'status' => 'delivered',
                    'review' => [
                        'product_id' => 24,
                        'rating' => 4,
                        'comment' => 'Aroma vanilla nya calming banget, bikin ruangan jadi wangi. Suka!',
                    ],
                ],
                [
                    'date' => '2026-10-02 16:45:00',
                    'email' => 'sitiaminah.bdg@gmail.com',
                    'items' => [[26, 3]], // Sirup Markisa(3)
                    'shipping_cost' => 18000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'BRI',
                    'status' => 'delivered',
                    'review' => [
                        'product_id' => 26,
                        'rating' => 5,
                        'comment' => 'Sirupnya asli markisa, rasa asam manisnya pas. Cocok buat es di rumah',
                    ],
                ],
                // 3 Okt
                [
                    'date' => '2026-10-03 08:20:00',
                    'email' => 'fajarramadhan77@gmail.com',
                    'items' => [[25, 3], [26, 1]], // Pangsit(3), Sirup(1)
                    'shipping_cost' => 18000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'BCA',
                    'status' => 'delivered',
                ],
                [
                    'date' => '2026-10-03 13:50:00',
                    'email' => 'denikurniawan90@gmail.com',
                    'items' => [[29, 5]], // Donat(5)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'cod',
                    'status' => 'delivered',
                    'review' => [
                        'product_id' => 29,
                        'rating' => 4,
                        'comment' => 'Donatnya empuk dan topping-nya ga pelit, anak-anak suka banget',
                    ],
                ],
                // 4 Okt
                [
                    'date' => '2026-10-04 11:30:00',
                    'email' => 'putrihandayani.ph@gmail.com',
                    'items' => [[24, 1], [27, 2]], // Lilin Soy Wax(1), Sourdough(2)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'pos',
                    'payment_method' => 'qris',
                    'status' => 'delivered',
                    'review' => [
                        'product_id' => 27,
                        'rating' => 5,
                        'comment' => 'Tekstur rotinya perfect, ga terlalu keras. Cranberry cheese-nya juara!',
                    ],
                ],
                [
                    'date' => '2026-10-04 15:00:00',
                    'email' => 'rina.kusuma88@gmail.com',
                    'items' => [[26, 2]], // Sirup Markisa(2)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'BCA',
                    'status' => 'delivered',
                ],
                // 5 Okt
                [
                    'date' => '2026-10-05 09:45:00',
                    'email' => 'rizkyhidayat.rh@gmail.com',
                    'items' => [[28, 3], [25, 1]], // Choco Bread(3), Pangsit(1)
                    'shipping_cost' => 18000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'BNI',
                    'status' => 'delivered',
                    'review' => [
                        'product_id' => 28,
                        'rating' => 5,
                        'comment' => 'Cokelatnya rich dan rotinya super soft. Worth every rupiah!',
                    ],
                ],
                [
                    'date' => '2026-10-05 12:10:00',
                    'email' => 'wulansari.ws@gmail.com',
                    'items' => [[30, 3]], // Lilin Upcycle(3)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'cod',
                    'status' => 'delivered',
                    'review' => [
                        'product_id' => 30,
                        'rating' => 4,
                        'comment' => 'Konsepnya keren dari minyak jelantah, aromanya juga enak. Go green!',
                    ],
                ],
                [
                    'date' => '2026-10-05 17:30:00',
                    'email' => 'andiwijaya.aw@gmail.com',
                    'items' => [[27, 1], [29, 2]], // Sourdough(1), Donat(2)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'pos',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'Mandiri',
                    'status' => 'delivered',
                    'review' => [
                        'product_id' => 29,
                        'rating' => 5,
                        'comment' => 'Donat terenak yang pernah saya coba. Tiramisu nya juara banget!',
                    ],
                ],
                // 6 Okt
                [
                    'date' => '2026-10-06 10:15:00',
                    'email' => 'lestaridewi.ld@gmail.com',
                    'items' => [[25, 2], [24, 1]], // Pangsit(2), Lilin Soy Wax(1)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'qris',
                    'status' => 'delivered',
                    'review' => [
                        'product_id' => 25,
                        'rating' => 5,
                        'comment' => 'Beli buat acara arisan, semua pada suka. Packing nya juga rapi',
                    ],
                ],
                [
                    'date' => '2026-10-06 14:00:00',
                    'email' => 'hendrasaputra.hs@gmail.com',
                    'items' => [[26, 1], [28, 1]], // Sirup(1), Choco Bread(1)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'BCA',
                    'status' => 'delivered',
                ],
                // 7 Okt
                [
                    'date' => '2026-10-07 08:30:00',
                    'email' => 'nadiasafitri.ns@gmail.com',
                    'items' => [[29, 4], [27, 1]], // Donat(4), Sourdough(1)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'BCA',
                    'status' => 'delivered',
                ],
                [
                    'date' => '2026-10-07 16:20:00',
                    'email' => 'bayupratama.id@gmail.com',
                    'items' => [[25, 2], [30, 2]], // Pangsit(2), Lilin Upcycle(2)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'pos',
                    'payment_method' => 'cod',
                    'status' => 'delivered',
                ],
                // 8 Okt
                [
                    'date' => '2026-10-08 09:00:00',
                    'email' => 'fajarramadhan77@gmail.com',
                    'items' => [[24, 3]], // Lilin Soy Wax(3)
                    'shipping_cost' => 18000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'BCA',
                    'status' => 'shipped',
                ],
                [
                    'date' => '2026-10-08 14:45:00',
                    'email' => 'sitiaminah.bdg@gmail.com',
                    'items' => [[28, 2], [29, 2]], // Choco Bread(2), Donat(2)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'qris',
                    'status' => 'shipped',
                ],
                // 9 Okt
                [
                    'date' => '2026-10-09 10:30:00',
                    'email' => 'putrihandayani.ph@gmail.com',
                    'items' => [[25, 1], [26, 2]], // Pangsit(1), Sirup(2)
                    'shipping_cost' => 18000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'BRI',
                    'status' => 'shipped',
                ],
                [
                    'date' => '2026-10-09 15:15:00',
                    'email' => 'rizkyhidayat.rh@gmail.com',
                    'items' => [[27, 2], [30, 1]], // Sourdough(2), Lilin Upcycle(1)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'pos',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'BNI',
                    'status' => 'shipped',
                ],
                // 10 Okt
                [
                    'date' => '2026-10-10 08:00:00',
                    'email' => 'wulansari.ws@gmail.com',
                    'items' => [[24, 1], [25, 2]], // Lilin Soy Wax(1), Pangsit(2)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'cod',
                    'status' => 'processing',
                ],
                [
                    'date' => '2026-10-10 11:30:00',
                    'email' => 'andiwijaya.aw@gmail.com',
                    'items' => [[26, 2], [29, 3]], // Sirup(2), Donat(3)
                    'shipping_cost' => 18000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'Mandiri',
                    'status' => 'processing',
                ],
                [
                    'date' => '2026-10-10 16:00:00',
                    'email' => 'hendrasaputra.hs@gmail.com',
                    'items' => [[28, 1], [27, 1]], // Choco Bread(1), Sourdough(1)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'pos',
                    'payment_method' => 'qris',
                    'status' => 'processing',
                ],
                // 11 Okt
                [
                    'date' => '2026-10-11 09:00:00',
                    'email' => 'lestaridewi.ld@gmail.com',
                    'items' => [[25, 3], [24, 1]], // Pangsit(3), Lilin Soy Wax(1)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'BCA',
                    'status' => 'processing',
                ],
                [
                    'date' => '2026-10-11 12:30:00',
                    'email' => 'denikurniawan90@gmail.com',
                    'items' => [[26, 1], [30, 2]], // Sirup(1), Lilin Upcycle(2)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'jne',
                    'payment_method' => 'bank_transfer',
                    'bank' => 'BCA',
                    'status' => 'pending_payment',
                ],
                [
                    'date' => '2026-10-11 15:00:00',
                    'email' => 'rina.kusuma88@gmail.com',
                    'items' => [[27, 2], [29, 1]], // Sourdough(2), Donat(1)
                    'shipping_cost' => 9000,
                    'shipping_method' => 'pos',
                    'payment_method' => 'qris',
                    'status' => 'pending_payment',
                ],
            ];

            $orderCount = 0;

            foreach ($ordersPlan as $plan) {
                $userContext = $usersByEmail[$plan['email']];
                $user = $userContext['user'];
                $addr = $userContext['address'];
                $createdAt = Carbon::parse($plan['date']);

                // Hitung Subtotal dari items
                $subtotal = 0;
                $orderItemsData = [];

                foreach ($plan['items'] as [$prodId, $qty]) {
                    $prod = $products[$prodId];
                    $price = (float) $prod->price;
                    $itemSubtotal = $price * $qty;
                    $subtotal += $itemSubtotal;

                    $orderItemsData[] = [
                        'product_id' => $prod->id,
                        'product_name' => $prod->name,
                        'product_price' => $price,
                        'quantity' => $qty,
                        'subtotal' => $itemSubtotal,
                    ];
                }

                $shippingCost = $plan['shipping_cost'];
                $tax = round($subtotal * 0.11);
                $total = $subtotal + $shippingCost + $tax;

                // Tentukan status dan updated_at
                $status = $plan['status'];
                $updatedAt = match ($status) {
                    'delivered' => $createdAt->copy()->addDays(rand(2, 3))->addHours(rand(1, 4)),
                    'shipped' => $createdAt->copy()->addDay()->addHours(rand(2, 5)),
                    'processing' => $createdAt->copy()->addHours(rand(1, 3)),
                    default => $createdAt->copy(),
                };

                // Tracking number jika shipped / delivered
                $trackingNumber = null;
                if (in_array($status, ['shipped', 'delivered'])) {
                    $trackingNumber = '2' . str_pad((string) rand(10000000000, 99999999999), 11, '0', STR_PAD_LEFT);
                }

                // Generate Order Number
                $orderNumber = 'ORD-' . strtoupper(Str::random(10));

                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => $orderNumber,
                    'status' => $status,
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'tax' => $tax,
                    'discount' => 0,
                    'total' => $total,
                    'shipping_method' => $plan['shipping_method'],
                    'notes' => null,
                    'ship_name' => $user->name,
                    'ship_phone' => $user->phone,
                    'ship_address' => $addr['address'],
                    'ship_city' => $addr['city'],
                    'ship_province' => $addr['province'],
                    'ship_postal_code' => $addr['postal_code'],
                    'tracking_number' => $trackingNumber,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);

                // Buat Order Items
                foreach ($orderItemsData as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['product_name'],
                        'product_price' => $item['product_price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }

                // Buat Payment
                $paymentMethod = $plan['payment_method'];
                $isPaid = in_array($status, ['shipped', 'delivered', 'processing']);
                $paymentStatus = $isPaid ? 'paid' : 'pending';

                $paymentData = [
                    'order_id' => $order->id,
                    'method' => $paymentMethod,
                    'status' => $paymentStatus,
                    'amount' => $total,
                    'created_at' => $createdAt,
                    'updated_at' => $isPaid ? $createdAt->copy()->addMinutes(rand(10, 45)) : $createdAt,
                ];

                if ($isPaid) {
                    $paidAt = $createdAt->copy()->addMinutes(rand(10, 30));
                    $verifiedAt = $paidAt->copy()->addMinutes(rand(15, 45));

                    $paymentData['paid_at'] = $paidAt;
                    $paymentData['verified_by'] = 1; // Admin ID
                    $paymentData['verified_at'] = $verifiedAt;

                    if ($paymentMethod === 'bank_transfer') {
                        $paymentData['bank_name'] = $plan['bank'] ?? 'BCA';
                        $paymentData['bank_account_number'] = (string) rand(1000000000, 9999999999);
                        $paymentData['bank_account_name'] = $user->name;
                    } elseif ($paymentMethod === 'qris') {
                        $paymentData['qris_reference'] = 'QRIS-' . strtoupper(Str::random(12));
                    }
                } else {
                    $paymentData['expired_at'] = $createdAt->copy()->addHours(24);
                    if ($paymentMethod === 'bank_transfer') {
                        $paymentData['bank_name'] = $plan['bank'] ?? 'BCA';
                        $paymentData['bank_account_number'] = '8271039482'; // Nomor rekening toko
                        $paymentData['bank_account_name'] = 'Jelita Saung Official';
                    }
                }

                Payment::create($paymentData);

                // Tambahkan Review jika ada di plan
                if (!empty($plan['review'])) {
                    $reviewData = $plan['review'];
                    $reviewCreatedAt = $updatedAt->copy()->addHours(rand(2, 24));

                    Review::create([
                        'product_id' => $reviewData['product_id'],
                        'user_id' => $user->id,
                        'order_id' => $order->id,
                        'rating' => $reviewData['rating'],
                        'comment' => $reviewData['comment'],
                        'created_at' => $reviewCreatedAt,
                        'updated_at' => $reviewCreatedAt,
                    ]);
                }

                $orderCount++;
            }

            // 4. Update rating agregat pada tabel products
            foreach ($products as $prod) {
                $avgRating = Review::where('product_id', $prod->id)->avg('rating');
                if ($avgRating) {
                    $prod->update(['rating' => round($avgRating, 1)]);
                }
            }
        });
    }
}
