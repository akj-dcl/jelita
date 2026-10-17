<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { ref } from 'vue'

const props = defineProps<{
    orders: any;
    stats: any;
}>()

const selectedOrder = ref<any>(null)
const showModal = ref(false)
const showShipModal = ref(false)
const trackingNumber = ref('')

const viewOrder = (order: any) => {
    selectedOrder.value = order
    showModal.value = true
}

const openShipModal = (order: any) => {
    selectedOrder.value = order
    trackingNumber.value = ''
    showShipModal.value = true
}

const closeShipModal = () => {
    showShipModal.value = false
    selectedOrder.value = null
}

const submitShip = () => {
    if (!trackingNumber.value) {
        alert('Nomor resi wajib diisi')
        return
    }
    router.post(`/admin/orders/${selectedOrder.value.id}/ship`, {
        tracking_number: trackingNumber.value
    }, {
        onSuccess: () => closeShipModal()
    })
}

const closeModal = () => {
    showModal.value = false
    selectedOrder.value = null
}

const confirmVerify = (order: any) => {
    if (confirm(`Verifikasi pembayaran untuk order #${order.order_number}?`)) {
        router.post(`/admin/orders/${order.id}/verify`)
    }
}

const confirmReject = (order: any) => {
    if (confirm(`Tolak pembayaran untuk order #${order.order_number}? Pesanan akan dibatalkan.`)) {
        router.post(`/admin/orders/${order.id}/reject`)
    }
}

const confirmDelete = (order: any) => {
    if (confirm(`Hapus order #${order.order_number}? Tindakan ini tidak bisa dibatalkan.`)) {
        router.delete(`/admin/orders/${order.id}`)
    }
}

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount)
}

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('id-ID', {
        timeZone: 'Asia/Jakarta',
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const statusColor: Record<string, string> = {
    pending_payment: 'bg-amber-100 text-amber-700 border-amber-200',
    processing: 'bg-blue-100 text-blue-700 border-blue-200',
    shipped:    'bg-indigo-100 text-indigo-700 border-indigo-200',
    delivered:  'bg-green-100 text-green-700 border-green-200',
    cancelled:  'bg-red-100 text-red-600 border-red-200',
    refunded:   'bg-purple-100 text-purple-700 border-purple-200',
}

const paymentStatusColor: Record<string, string> = {
    pending: 'bg-amber-100 text-amber-700',
    paid:    'bg-green-100 text-green-700',
    expired: 'bg-orange-100 text-orange-600',
    failed:  'bg-red-100 text-red-600',
}

const translateStatus = (status: string) => {
    const statusMap: Record<string, string> = {
        'pending_payment': 'Menunggu Pembayaran',
        'processing': 'Diproses',
        'shipped': 'Dikirim',
        'delivered': 'Diterima',
        'cancelled': 'Dibatalkan',
        'refunded': 'Dikembalikan',
    };
    return statusMap[status] || status;
};
</script>

<template>
    <Head title="Pesanan - Admin" />

    <AdminLayout>
        <template #header-title>
            <div class="flex items-center gap-xl hidden md:flex">
                <h2 class="font-headline-md text-headline-md text-primary font-bold">Manajemen Pesanan</h2>
            </div>
        </template>

        <div class="pt-8 px-lg pb-xl max-w-[1440px] mx-auto relative">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 mb-lg">
                <div>
                    <nav class="flex items-center gap-xs text-label-sm font-label-sm text-on-surface-variant mb-xs">
                        <Link href="/admin/dashboard" class="hover:text-primary transition-colors">Dasbor</Link>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="text-primary font-bold">Pesanan</span>
                    </nav>
                    <h1 class="font-display-lg text-display-lg text-on-surface font-bold">Manajemen Pesanan</h1>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-gutter mb-lg">
                <div class="bg-white rounded-xl shadow-sm border border-surface-container p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-amber-600 text-[20px]">pending</span>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant">Menunggu</p>
                        <p class="font-bold text-on-surface text-lg">{{ stats?.pending_payment || 0 }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-surface-container p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-blue-600 text-[20px]">manufacturing</span>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant">Diproses</p>
                        <p class="font-bold text-on-surface text-lg">{{ stats?.processing || 0 }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-surface-container p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-green-600 text-[20px]">shopping_bag</span>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant">Total Pesanan</p>
                        <p class="font-bold text-on-surface text-lg">{{ stats?.total || 0 }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-surface-container p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
                        <span class="material-symbols-outlined text-purple-600 text-[20px]">payments</span>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant">Pendapatan</p>
                        <p class="font-bold text-on-surface text-lg">{{ formatCurrency(stats?.revenue || 0) }}</p>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-surface-container overflow-hidden">
                <div class="p-4 border-b border-surface-container">
                    <h4 class="font-semibold text-on-surface text-lg">Daftar Pesanan</h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-surface-container-low/50 border-b border-surface-container">
                            <tr>
                                <th class="text-left px-4 py-3 text-on-surface-variant font-semibold text-xs uppercase tracking-wider">No. Order</th>
                                <th class="text-left px-4 py-3 text-on-surface-variant font-semibold text-xs uppercase tracking-wider">Pelanggan</th>
                                <th class="text-left px-4 py-3 text-on-surface-variant font-semibold text-xs uppercase tracking-wider">Tanggal</th>
                                <th class="text-left px-4 py-3 text-on-surface-variant font-semibold text-xs uppercase tracking-wider">Total</th>
                                <th class="text-left px-4 py-3 text-on-surface-variant font-semibold text-xs uppercase tracking-wider">Pembayaran</th>
                                <th class="text-left px-4 py-3 text-on-surface-variant font-semibold text-xs uppercase tracking-wider">Status</th>
                                <th class="text-right px-4 py-3 text-on-surface-variant font-semibold text-xs uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in orders.data" :key="order.id"
                                class="border-b border-surface-container/50 hover:bg-surface-container-low/30 transition-colors">
                                <!-- Order Number -->
                                <td class="px-4 py-4">
                                    <button @click="viewOrder(order)" class="font-bold text-primary hover:underline text-left">
                                        #{{ order.order_number }}
                                    </button>
                                    <p class="text-xs text-on-surface-variant mt-0.5 line-clamp-1 max-w-[220px]" :title="order.items?.map(i => `${i.product_name} (${i.quantity}x)`).join(', ')">
                                        {{ order.items?.map(i => `${i.product_name} (${i.quantity}x)`).join(', ') || `${order.items_count} produk` }}
                                    </p>
                                </td>

                                <!-- Customer -->
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-xs font-bold text-primary shrink-0">
                                            {{ (order.customer_name || 'NA').substring(0, 2).toUpperCase() }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-on-surface text-sm">{{ order.customer_name }}</p>
                                            <p class="text-xs text-on-surface-variant">{{ order.ship_city }}</p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Date -->
                                <td class="px-4 py-4 text-on-surface-variant text-xs">
                                    {{ formatDate(order.created_at) }}
                                </td>

                                <!-- Total -->
                                <td class="px-4 py-4 font-bold text-on-surface">
                                    {{ formatCurrency(order.total) }}
                                </td>

                                <!-- Payment -->
                                <td class="px-4 py-4">
                                    <span :class="['inline-flex items-center px-2 py-1 rounded-full text-xs font-medium', paymentStatusColor[order.payment?.status] || 'bg-gray-100 text-gray-600']">
                                        {{ order.payment?.status === 'paid' ? 'Terverifikasi' : order.payment?.status === 'failed' ? 'Ditolak' : 'Menunggu' }}
                                    </span>
                                    <p class="text-xs text-on-surface-variant mt-1">
                                        {{ order.payment?.method === 'bank_transfer' ? 'Transfer BRI' : order.payment?.method === 'qris' ? 'QRIS' : order.payment?.method === 'cod' ? 'COD' : '-' }}
                                    </p>
                                </td>

                                <!-- Fulfillment Status -->
                                <td class="px-4 py-4">
                                    <span :class="['inline-flex items-center px-2 py-1 rounded-full text-xs font-medium border', statusColor[order.status] || 'bg-gray-100 text-gray-600 border-gray-200']">
                                        {{ translateStatus(order.status) }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-4">
                                    <div class="flex justify-end items-center gap-1">
                                        <!-- View detail -->
                                        <button @click="viewOrder(order)"
                                            class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors" title="Lihat Detail">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </button>

                                        <!-- Verify (only pending) -->
                                        <button v-if="order.status === 'pending_payment'"
                                            @click="confirmVerify(order)"
                                            class="p-1.5 rounded-lg text-green-600 hover:bg-green-50 transition-colors" title="Verifikasi Pembayaran">
                                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                        </button>

                                        <!-- Reject (only pending) -->
                                        <button v-if="order.status === 'pending_payment'"
                                            @click="confirmReject(order)"
                                            class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition-colors" title="Tolak Pembayaran">
                                            <span class="material-symbols-outlined text-[18px]">cancel</span>
                                        </button>

                                        <!-- Ship (only processing) -->
                                        <button v-if="order.status === 'processing'"
                                            @click="openShipModal(order)"
                                            class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors" title="Kirim Pesanan">
                                            <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                                        </button>

                                        <!-- Delete -->
                                        <button @click="confirmDelete(order)"
                                            class="p-1.5 rounded-lg text-on-surface-variant hover:text-red-500 hover:bg-red-50 transition-colors" title="Hapus">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="orders.data.length === 0">
                                <td colspan="7" class="text-center py-16 text-on-surface-variant">
                                    <span class="material-symbols-outlined text-4xl mb-2 block">inbox</span>
                                    Belum ada pesanan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="p-4 bg-surface-container-low/30 border-t border-surface-container flex flex-col sm:flex-row items-center justify-between gap-4"
                    v-if="orders.links && orders.links.length > 3">
                    <p class="text-sm text-on-surface-variant">
                        Menampilkan {{ orders.from }}–{{ orders.to }} dari {{ orders.total }} pesanan
                    </p>
                    <div class="flex items-center gap-1">
                        <template v-for="(link, index) in orders.links" :key="index">
                            <Link v-if="link.url" :href="link.url" v-html="link.label"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-bold transition-colors"
                                :class="link.active ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-container-high'" />
                            <span v-else v-html="link.label"
                                class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-bold text-on-surface-variant opacity-30" />
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Detail Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="closeModal">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto text-gray-900">
                <div class="p-6 border-b border-gray-200 flex justify-between items-start">
                    <div>
                        <h2 class="font-bold text-xl text-gray-900">Order #{{ selectedOrder?.order_number }}</h2>
                        <p class="text-sm text-gray-500 mt-1">{{ selectedOrder ? formatDate(selectedOrder.created_at) : '' }}</p>
                    </div>
                    <button @click="closeModal" class="p-2 rounded-full hover:bg-gray-100 transition-colors">
                        <span class="material-symbols-outlined text-gray-500">close</span>
                    </button>
                </div>

                <div class="p-6 space-y-6" v-if="selectedOrder">
                    <!-- Customer Info -->
                    <div>
                        <h3 class="font-bold text-xs text-gray-500 uppercase tracking-wider mb-3">Informasi Pembeli</h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div><p class="text-gray-500 text-xs">Nama</p><p class="font-semibold text-gray-900">{{ selectedOrder.customer_name }}</p></div>
                            <div><p class="text-gray-500 text-xs">Email</p><p class="font-semibold text-gray-900">{{ selectedOrder.customer_email }}</p></div>
                            <div><p class="text-gray-500 text-xs">Telepon</p><p class="font-semibold text-gray-900">{{ selectedOrder.ship_phone }}</p></div>
                            <div><p class="text-gray-500 text-xs">Kurir</p><p class="font-semibold text-gray-900 uppercase">{{ selectedOrder.shipping_method }}</p></div>
                            <div v-if="selectedOrder.tracking_number"><p class="text-gray-500 text-xs">Nomor Resi</p><p class="font-semibold text-primary">{{ selectedOrder.tracking_number }}</p></div>
                            <div class="col-span-2"><p class="text-gray-500 text-xs">Alamat Pengiriman</p><p class="font-semibold text-gray-900">{{ selectedOrder.ship_address }}, {{ selectedOrder.ship_city }}</p></div>
                        </div>
                    </div>

                    <!-- Items -->
                    <div>
                        <h3 class="font-bold text-xs text-gray-500 uppercase tracking-wider mb-3">Produk Dipesan</h3>
                        <div class="space-y-2">
                            <div v-for="(item, i) in selectedOrder.items" :key="i" class="flex justify-between items-center p-3 bg-gray-50 rounded-xl text-sm gap-3 border border-gray-100">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center">
                                        <img v-if="item.product_image" :src="item.product_image" :alt="item.product_name" class="w-full h-full object-cover" />
                                        <span v-else class="material-symbols-outlined text-gray-400 text-xl">image</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 truncate">{{ item.product_name }}</p>
                                        <p class="text-xs text-gray-500">{{ item.quantity }}x {{ formatCurrency(item.product_price) }}</p>
                                    </div>
                                </div>
                                <p class="font-bold text-primary flex-shrink-0">{{ formatCurrency(item.subtotal) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="bg-gray-50 rounded-xl p-4 space-y-2 text-sm border border-gray-100">
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span class="font-semibold text-gray-900">{{ formatCurrency(selectedOrder.subtotal) }}</span></div>
                        <div class="flex justify-between text-gray-600"><span>Ongkir</span><span class="font-semibold text-gray-900">{{ formatCurrency(selectedOrder.shipping_cost) }}</span></div>
                        <div class="flex justify-between text-gray-600"><span>Pajak (11%)</span><span class="font-semibold text-gray-900">{{ formatCurrency(selectedOrder.tax) }}</span></div>
                        <div class="flex justify-between font-bold text-base border-t border-gray-200 pt-2 mt-2">
                            <span class="text-gray-900">Total</span><span class="text-primary font-bold">{{ formatCurrency(selectedOrder.total) }}</span>
                        </div>
                    </div>

                    <!-- Payment Proof -->
                    <div v-if="selectedOrder.payment">
                        <h3 class="font-bold text-xs text-gray-500 uppercase tracking-wider mb-3">Informasi Pembayaran</h3>
                        <div class="flex items-center gap-3 mb-3">
                            <span :class="['px-3 py-1 rounded-full text-xs font-semibold', paymentStatusColor[selectedOrder.payment.status] || 'bg-gray-100 text-gray-600']">
                                {{ selectedOrder.payment.status === 'paid' ? '✅ Terverifikasi' : selectedOrder.payment.status === 'failed' ? '❌ Ditolak' : '⏳ Menunggu Verifikasi' }}
                            </span>
                            <span class="text-xs font-semibold text-gray-700 uppercase px-2 py-0.5 bg-gray-100 rounded-md">
                                {{ selectedOrder.payment.method === 'bank_transfer' ? 'Transfer BRI' : selectedOrder.payment.method === 'qris' ? 'QRIS' : selectedOrder.payment.method === 'cod' ? 'COD (Bayar di Tempat)' : selectedOrder.payment.method }}
                            </span>
                        </div>
                        <a v-if="selectedOrder.payment.transfer_proof" :href="selectedOrder.payment.transfer_proof" target="_blank">
                            <img :src="selectedOrder.payment.transfer_proof" alt="Bukti Pembayaran"
                                class="max-w-full max-h-64 rounded-xl border border-gray-200 object-contain hover:opacity-90 transition-opacity" />
                        </a>
                        <p v-else class="text-sm text-gray-500 italic">
                            {{ selectedOrder.payment.method === 'cod' ? 'Pesanan COD: Pembayaran tunai saat barang diterima.' : 'Belum ada bukti pembayaran.' }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div v-if="selectedOrder.status === 'pending_payment'" class="flex gap-3">
                        <button @click="confirmVerify(selectedOrder); closeModal()"
                            class="flex-1 flex items-center justify-center gap-2 py-3 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">check_circle</span>
                            Verifikasi Pembayaran
                        </button>
                        <button @click="confirmReject(selectedOrder); closeModal()"
                            class="flex-1 flex items-center justify-center gap-2 py-3 bg-red-500 text-white rounded-xl font-semibold hover:bg-red-600 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">cancel</span>
                            Tolak Pembayaran
                        </button>
                    </div>
                    <div v-else class="flex gap-3">
                        <button v-if="selectedOrder.status === 'processing'" @click="openShipModal(selectedOrder); showModal = false"
                            class="flex-1 flex items-center justify-center gap-2 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                            Kirim Pesanan
                        </button>
                        <a :href="`/admin/orders/${selectedOrder.id}/invoice`" target="_blank"
                            class="flex-1 flex items-center justify-center gap-2 py-3 border border-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">print</span>
                            Cetak Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ship Order Modal -->
        <div v-if="showShipModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="closeShipModal">
            <div class="bg-white rounded-3xl shadow-2xl w-full max-w-[420px] text-gray-900">
                <div class="p-6 border-b border-gray-200 flex justify-between items-start">
                    <div>
                        <h2 class="font-bold text-xl text-gray-900">Kirim Pesanan</h2>
                        <p class="text-sm text-gray-500 mt-1">Order #{{ selectedOrder?.order_number }}</p>
                    </div>
                    <button @click="closeShipModal" class="p-2 rounded-full hover:bg-gray-100 transition-colors">
                        <span class="material-symbols-outlined text-gray-500">close</span>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label for="tracking_number" class="block text-sm font-semibold text-gray-700 mb-2">Nomor Resi / Tracking Number</label>
                        <input type="text" id="tracking_number" v-model="trackingNumber"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 bg-white text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all font-medium"
                            placeholder="Masukkan nomor resi pengiriman">
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button @click="closeShipModal"
                            class="flex-1 py-3 text-gray-700 border border-gray-200 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button @click="submitShip"
                            class="flex-1 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition-colors">
                            Simpan & Kirim
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
