<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';
import ShopNavbar from '@/components/ShopNavbar.vue';
import ShopFooter from '@/components/ShopFooter.vue';

const props = defineProps<{
    cartItems: any[];
    user: { email: string, name: string };
    savedAddresses?: any[];
}>();

// Shipping selection state
const selectedShipping = ref<'standard' | 'express'>('standard');
const discountCode = ref('');
const appliedDiscount = ref(0);
const isOrderSubmitted = ref(false);

// Form state
const selectedSavedAddressId = ref<number | null>(null);
const form = ref({
    email: props.user.email || '',
    newsletter: true,
    firstName: props.user.name || '',
    lastName: '',
    address: '',
    apartment: '',
    destination_id: '',
    destination_label: '',
    postalCode: '',
    phone: '',
    courier: 'jne'
});

const searchQuery = ref('');
const destinations = ref<any[]>([]);
const isSearching = ref(false);
const shippingOptions = ref<any[]>([]);
const isFetchingShipping = ref(false);
const selectedShippingService = ref('');
const shippingCost = ref(0);

const applySavedAddress = (address: any) => {
    selectedSavedAddressId.value = address.id;
    form.value.firstName = address.recipient_name || '';
    form.value.lastName = '';
    form.value.phone = address.phone || '';
    form.value.address = [address.address_line, address.kelurahan].filter(Boolean).join(', ');
    form.value.postalCode = address.postal_code || '';

    if (address.destination_id) {
        form.value.destination_id = address.destination_id;
        form.value.destination_label = address.destination_label || '';
        searchQuery.value = address.destination_label || '';
        shippingOptions.value = [];
        shippingCost.value = 0;
        if (form.value.courier) {
            fetchShippingCost();
        }
    } else {
        form.value.destination_id = '';
        form.value.destination_label = '';
        searchQuery.value = '';
        shippingOptions.value = [];
        shippingCost.value = 0;
    }
};

const totalWeight = computed(() => {
    return props.cartItems.reduce((acc, item) => acc + (item.weight * item.quantity), 0);
});

const subtotal = computed(() => {
    return props.cartItems.reduce((acc, item) => acc + item.price * item.quantity, 0);
});

const tax = computed(() => {
    // 11% estimated tax
    return Math.round(subtotal.value * 0.11);
});

const total = computed(() => {
    const calcTotal = subtotal.value + shippingCost.value + tax.value - appliedDiscount.value;
    return Math.max(0, calcTotal);
});

const applyPromo = () => {
    if (discountCode.value.trim().toUpperCase() === 'ARTISAN10') {
        appliedDiscount.value = Math.round(subtotal.value * 0.10);
        alert('Promo code ARTISAN10 applied successfully! (10% discount)');
    } else if (discountCode.value.trim() !== '') {
        alert('Invalid promo code. Tip: Try "ARTISAN10"');
    }
};

// Delivery type state
const deliveryType = ref<'shipping' | 'cod'>('shipping');

watch(deliveryType, (val) => {
    if (val === 'cod') {
        shippingCost.value = 0;
        selectedShippingService.value = 'COD';
    } else {
        if (shippingOptions.value.length > 0) {
            selectedShippingService.value = shippingOptions.value[0].service;
            shippingCost.value = shippingOptions.value[0].cost;
        } else {
            shippingCost.value = 0;
            selectedShippingService.value = '';
        }
    }
});

const handleContinueToPayment = () => {
    if (deliveryType.value === 'cod') {
        if (!form.value.email || !form.value.firstName || !form.value.phone || !form.value.address) {
            alert('Mohon lengkapi email, nama, nomor telepon, dan alamat untuk pesanan COD.');
            return;
        }
        shippingCost.value = 0;
        form.value.courier = 'cod';
        form.value.destination_id = 'cod';
        form.value.destination_label = 'COD / Ambil di Tempat';
    } else {
        if (!form.value.email || !form.value.firstName || !form.value.address || !form.value.phone || !form.value.destination_id || shippingCost.value === 0) {
            alert('Mohon lengkapi alamat pengiriman dan pilih metode pengiriman yang tersedia.');
            return;
        }
    }
    // Set to local storage to simulate passing data to payment
    localStorage.setItem('checkoutData', JSON.stringify({
        form: form.value,
        deliveryType: deliveryType.value,
        total: total.value,
        subtotal: subtotal.value,
        shippingCost: shippingCost.value,
        tax: tax.value
    }));
    router.visit('/checkout-payment');
};

const formatRupiah = (val: number) => {
    return 'Rp ' + Math.round(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

let searchTimeout: any = null;

const searchDestinations = () => {
    if (searchTimeout) clearTimeout(searchTimeout);
    if (searchQuery.value.length < 3) {
        destinations.value = [];
        return;
    }
    
    isSearching.value = true;
    searchTimeout = setTimeout(async () => {
        try {
            const res = await fetch(`/api/destinations?search=${encodeURIComponent(searchQuery.value)}`);
            const data = await res.json();
            destinations.value = data;
        } catch (e) {
            console.error("Failed to fetch destinations", e);
        } finally {
            isSearching.value = false;
        }
    }, 500);
};

const selectDestination = (destination: any) => {
    form.value.destination_id = destination.id;
    form.value.destination_label = destination.label;
    searchQuery.value = destination.label;
    destinations.value = [];
    shippingOptions.value = [];
    shippingCost.value = 0;
    
    // Automatically trigger cost fetch if courier is already selected
    if (form.value.courier) {
        fetchShippingCost();
    }
};

async function fetchShippingCost() {
    if (!form.value.destination_id || !form.value.courier) return;
    isFetchingShipping.value = true;
    try {
        const res = await fetch('/api/shipping-cost', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                origin: 4850, // Origin from Arcamanik, Bandung
                destination: form.value.destination_id,
                weight: totalWeight.value,
                courier: form.value.courier
            })
        });
        const data = await res.json();
        if(data && data.length > 0) {
            shippingOptions.value = data;
            // Auto select first option
            selectedShippingService.value = shippingOptions.value[0].service;
            shippingCost.value = shippingOptions.value[0].cost;
        } else {
            shippingOptions.value = [];
        }
    } catch (e) {
        console.error("Failed to fetch shipping costs", e);
    } finally {
        isFetchingShipping.value = false;
    }
}

watch(() => selectedShippingService.value, (newService) => {
    const selected = shippingOptions.value.find(s => s.service === newService);
    if (selected) {
        shippingCost.value = selected.cost;
    }
});

onMounted(() => {
    // Provisies no longer fetched on mount
});
</script>

<template>
    <Head title="Checkout - Jelita Saung">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    </Head>

    <div class="welcome-theme min-h-screen bg-background text-on-background font-body-md overflow-x-clip antialiased">
        <ShopNavbar />

        <main class="max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 xl:gap-16">
                <!-- Left Column: Checkout Form -->
                <div class="lg:col-span-7 space-y-10">
                    <!-- Saved Address Selector (if any saved addresses exist) -->
                    <section v-if="props.savedAddresses && props.savedAddresses.length > 0" class="bg-surface-container-low/40 p-6 sm:p-8 rounded-3xl border border-outline-variant/30 shadow-sm">
                        <h2 class="font-headline-md text-xl font-bold text-on-surface mb-4 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[22px]">bookmark</span>
                            Gunakan Alamat Tersimpan
                        </h2>
                        <div class="space-y-3">
                            <label
                                v-for="addr in props.savedAddresses"
                                :key="addr.id"
                                :class="[
                                    'flex items-start gap-4 p-4 rounded-2xl border-2 cursor-pointer transition-all',
                                    selectedSavedAddressId === addr.id
                                        ? 'border-primary bg-primary/5'
                                        : 'border-outline-variant/30 hover:border-primary/40 bg-surface'
                                ]"
                                @click="applySavedAddress(addr)"
                            >
                                <input
                                    type="radio"
                                    :value="addr.id"
                                    v-model="selectedSavedAddressId"
                                    class="mt-1 h-4 w-4 accent-primary flex-shrink-0"
                                    @change="applySavedAddress(addr)"
                                />
                                <div class="flex-grow min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-[14px] text-on-surface">{{ addr.label || 'Alamat' }}</span>
                                        <span v-if="addr.is_default" class="text-[10px] bg-primary/10 text-primary px-2 py-0.5 rounded-full font-semibold">Default</span>
                                    </div>
                                    <p class="text-[13px] text-on-surface font-semibold mt-0.5">{{ addr.recipient_name }}</p>
                                    <p class="text-[12px] text-on-surface-variant mt-0.5 truncate">
                                        {{ addr.phone }} · {{ addr.address_line }}
                                        <template v-if="addr.kelurahan">, {{ addr.kelurahan }}</template>,
                                        {{ addr.destination_label || addr.city }}, {{ addr.province }} {{ addr.postal_code }}
                                    </p>
                                </div>
                            </label>
                        </div>
                        <p class="text-[12px] text-on-surface-variant mt-3 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">info</span>
                            Pilih alamat di atas untuk mengisi otomatis, atau isi form di bawah secara manual.
                        </p>
                    </section>

                    <!-- Contact Information -->
                    <section class="bg-surface-container-low/40 p-6 sm:p-8 rounded-3xl border border-outline-variant/30 shadow-sm">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2 mb-6">
                            <h2 class="font-headline-md text-2xl font-bold text-on-surface">Informasi Kontak</h2>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium mb-2 text-on-surface-variant">Alamat Email <span class="text-red-500">*</span></label>
                                <input v-model="form.email" type="email" placeholder="nama@contoh.com" class="w-full h-14 px-4 rounded-2xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 bg-surface text-black dark:text-white transition-all duration-200 outline-none" required />
                            </div>
                        </div>
                    </section>

                    <!-- Delivery Method Type -->
                    <section class="bg-surface-container-low/40 p-6 sm:p-8 rounded-3xl border border-outline-variant/30 shadow-sm">
                        <h2 class="font-headline-md text-2xl font-bold text-on-surface mb-4">Opsi Pemesanan</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label 
                                class="flex items-start gap-4 p-5 rounded-2xl border-2 cursor-pointer transition-all"
                                :class="deliveryType === 'shipping' ? 'border-primary bg-primary/5 shadow-xs' : 'border-outline-variant/40 bg-surface hover:border-primary/40'"
                            >
                                <input type="radio" value="shipping" v-model="deliveryType" class="mt-1 h-5 w-5 text-primary accent-primary" />
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary text-[20px]">local_shipping</span>
                                        <p class="font-bold text-base text-on-surface">Pengiriman Ekspedisi</p>
                                    </div>
                                    <p class="text-xs text-on-surface-variant mt-1 leading-relaxed">Kirim ke alamat via kurir (JNE, POS, TIKI). Bayar via transfer atau QRIS.</p>
                                </div>
                            </label>

                            <label 
                                class="flex items-start gap-4 p-5 rounded-2xl border-2 cursor-pointer transition-all"
                                :class="deliveryType === 'cod' ? 'border-primary bg-primary/5 shadow-xs' : 'border-outline-variant/40 bg-surface hover:border-primary/40'"
                            >
                                <input type="radio" value="cod" v-model="deliveryType" class="mt-1 h-5 w-5 text-primary accent-primary" />
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-primary text-[20px]">payments</span>
                                        <p class="font-bold text-base text-on-surface">COD / Bayar di Tempat</p>
                                    </div>
                                    <p class="text-xs text-on-surface-variant mt-1 leading-relaxed">Bayar tunai di tempat / ambil langsung di Saung Jelita. Bebas ongkos kirim (Rp 0).</p>
                                </div>
                            </label>
                        </div>
                    </section>

                    <!-- Shipping Address -->
                    <section class="bg-surface-container-low/40 p-6 sm:p-8 rounded-3xl border border-outline-variant/30 shadow-sm">
                        <h2 class="font-headline-md text-2xl font-bold text-on-surface mb-6">
                            {{ deliveryType === 'cod' ? 'Informasi Penerima (COD)' : 'Alamat Pengiriman' }}
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-1">
                                <label class="block text-sm font-medium mb-2 text-on-surface-variant">Nama Depan <span class="text-red-500">*</span></label>
                                <input v-model="form.firstName" type="text" class="w-full h-14 px-4 rounded-2xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 bg-surface text-black dark:text-white transition-all outline-none" required />
                            </div>
                            <div class="sm:col-span-1">
                                <label class="block text-sm font-medium mb-2 text-on-surface-variant">Nama Belakang</label>
                                <input v-model="form.lastName" type="text" class="w-full h-14 px-4 rounded-2xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 bg-surface text-black dark:text-white transition-all outline-none" />
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium mb-2 text-on-surface-variant">
                                    {{ deliveryType === 'cod' ? 'Alamat Pengantaran / Lokasi Anda' : 'Alamat' }} <span class="text-red-500">*</span>
                                </label>
                                <input v-model="form.address" type="text" :placeholder="deliveryType === 'cod' ? 'Jl. Contoh No. 12 (atau Catatan Ambil di Tempat)' : 'Jl. Sudirman No. 45'" class="w-full h-14 px-4 rounded-2xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 bg-surface text-black dark:text-white transition-all outline-none" required />
                            </div>
                            <div v-if="deliveryType === 'shipping'" class="sm:col-span-2 relative">
                                <label class="block text-sm font-medium mb-2 text-on-surface-variant">Kota/Kecamatan Tujuan <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input 
                                        v-model="searchQuery" 
                                        @input="searchDestinations"
                                        type="text" 
                                        placeholder="Ketik nama Kecamatan atau Kota..." 
                                        class="w-full h-14 px-4 rounded-2xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 bg-surface text-black dark:text-white transition-all outline-none" 
                                        required 
                                    />
                                    <div v-if="isSearching" class="absolute right-4 top-4">
                                        <span class="material-symbols-outlined animate-spin text-primary">refresh</span>
                                    </div>
                                </div>
                                
                                <!-- Dropdown Results -->
                                <div v-if="destinations.length > 0" class="absolute z-10 w-full mt-2 bg-surface border border-outline-variant rounded-2xl shadow-lg max-h-60 overflow-y-auto">
                                    <div 
                                        v-for="dest in destinations" 
                                        :key="dest.id"
                                        @click="selectDestination(dest)"
                                        class="p-4 hover:bg-primary/5 cursor-pointer text-on-surface text-sm border-b border-outline-variant/30 last:border-0"
                                    >
                                        {{ dest.label }}
                                    </div>
                                </div>
                            </div>
                            <div class="sm:col-span-1">
                                <label class="block text-sm font-medium mb-2 text-on-surface-variant">Kode Pos</label>
                                <input v-model="form.postalCode" type="text" class="w-full h-14 px-4 rounded-2xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 bg-surface text-black dark:text-white transition-all outline-none" />
                            </div>
                            <div class="sm:col-span-1">
                                <label class="block text-sm font-medium mb-2 text-on-surface-variant">Nomor Telepon (WhatsApp) <span class="text-red-500">*</span></label>
                                <input v-model="form.phone" type="tel" class="w-full h-14 px-4 rounded-2xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 bg-surface text-black dark:text-white transition-all outline-none" required />
                            </div>
                        </div>
                    </section>

                    <!-- Shipping Method via RajaOngkir (Only for standard shipping) -->
                    <section v-if="deliveryType === 'shipping'" class="bg-surface-container-low/40 p-6 sm:p-8 rounded-3xl border border-outline-variant/30 shadow-sm">
                        <h2 class="font-headline-md text-2xl font-bold text-on-surface mb-6">Metode Pengiriman</h2>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium mb-2 text-on-surface-variant">Kurir Pengiriman</label>
                            <select v-model="form.courier" @change="fetchShippingCost" :disabled="!form.destination_id" class="w-full h-14 px-4 rounded-2xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 bg-surface text-black dark:text-white transition-all outline-none">
                                <option value="jne">JNE</option>
                                <option value="pos">POS Indonesia</option>
                                <option value="tiki">TIKI</option>
                            </select>
                        </div>

                        <div v-if="isFetchingShipping" class="flex justify-center p-4">
                            <span class="material-symbols-outlined animate-spin text-primary">refresh</span>
                            <span class="ml-2 text-secondary">Menghitung ongkos kirim...</span>
                        </div>
                        
                        <div v-else-if="shippingOptions.length > 0" class="space-y-4">
                            <label 
                                v-for="(option, index) in shippingOptions" :key="index"
                                class="flex items-center justify-between p-5 rounded-2xl border cursor-pointer transition-all duration-200"
                                :class="selectedShippingService === option.service ? 'border-primary bg-primary/5 shadow-xs' : 'border-outline-variant bg-surface hover:border-primary/50'"
                            >
                                <div class="flex items-center gap-4">
                                    <input 
                                        v-model="selectedShippingService" 
                                        type="radio" 
                                        name="shipping_service" 
                                        :value="option.service" 
                                        class="h-5 w-5 text-primary border-outline-variant focus:ring-primary cursor-pointer" 
                                    />
                                    <div>
                                        <p class="font-bold text-base text-on-surface">{{ option.service }} ({{ option.description }})</p>
                                        <p class="text-sm text-secondary">Estimasi: {{ option.etd }}</p>
                                    </div>
                                </div>
                                <span class="font-bold text-base text-primary">{{ formatRupiah(option.cost) }}</span>
                            </label>
                        </div>
                        <div v-else-if="form.destination_id" class="text-center p-4 text-error bg-error-container/20 rounded-xl">
                            Kurir tidak tersedia untuk tujuan ini.
                        </div>
                        <div v-else class="text-center p-4 text-secondary bg-surface-container rounded-xl">
                            Silakan isi Kota/Kecamatan tujuan terlebih dahulu.
                        </div>
                    </section>

                    <!-- COD Delivery Info Section -->
                    <section v-else class="bg-surface-container-low/40 p-6 sm:p-8 rounded-3xl border border-outline-variant/30 shadow-sm">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-amber-600 text-2xl">handshake</span>
                            </div>
                            <div>
                                <h2 class="font-headline-md text-xl font-bold text-on-surface">Layanan COD / Bayar di Tempat Aktif</h2>
                                <p class="text-sm text-on-surface-variant mt-1 leading-relaxed">
                                    Bebas ongkos kirim (Rp 0). Anda tidak perlu membayar sekarang — pembayaran tunai akan dilakukan saat barang diserahkan atau saat Anda mengambil di Saung Jelita (Rutan Perempuan Kelas IIA Bandung).
                                </p>
                            </div>
                        </div>
                    </section>

                    <div class="pt-2 flex justify-between items-center gap-4">
                        <button 
                            @click="handleContinueToPayment" 
                            class="w-full px-10 py-4 bg-primary text-white font-bold text-base rounded-full shadow-[0px_8px_30px_rgba(138,72,83,0.25)] hover:opacity-95 active:scale-[0.98] transition-all cursor-pointer"
                        >
                            {{ deliveryType === 'cod' ? 'Lanjutkan ke Pembayaran COD' : 'Lanjutkan ke Pembayaran' }}
                        </button>
                    </div>
                </div>

                <!-- Right Column: Order Summary -->
                <div class="lg:col-span-5">
                    <div class="bg-surface-container-low p-6 sm:p-8 rounded-3xl border border-outline-variant/30 sticky top-28 shadow-sm">
                        <h3 class="font-headline-md text-2xl font-bold text-on-surface mb-6 pb-4 border-b border-outline-variant/20">
                            Ringkasan Pesanan
                        </h3>

                        <div class="space-y-6 mb-8 max-h-[380px] overflow-y-auto pr-2">
                            <div v-for="item in props.cartItems" :key="item.id" class="flex gap-4 items-center">
                                <div class="relative flex-shrink-0">
                                    <div class="w-20 h-24 bg-surface-container-highest rounded-xl overflow-hidden border border-outline-variant/30 shadow-xs">
                                        <img :src="item.image" :alt="item.name" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-300" />
                                    </div>
                                    <span class="absolute -top-2 -right-2 bg-primary text-white text-xs w-6 h-6 flex items-center justify-center rounded-full font-bold shadow-sm">
                                        {{ item.quantity }}
                                    </span>
                                </div>
                                <div class="flex-grow">
                                    <h4 class="font-bold text-base text-on-surface">{{ item.name }}</h4>
                                    <p class="text-xs text-secondary mt-1">{{ item.category }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-base text-on-surface">{{ formatRupiah(item.price) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3.5 mb-8 text-sm text-secondary font-medium">
                            <div class="flex justify-between">
                                <span>Subtotal</span>
                                <span class="text-on-surface font-semibold">{{ formatRupiah(subtotal) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Ongkos Kirim</span>
                                <span class="text-on-surface font-semibold">
                                    {{ deliveryType === 'cod' ? 'Rp 0 (Bebas Ongkir / COD)' : formatRupiah(shippingCost) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span>Estimasi Pajak (PPN 11%)</span>
                                <span class="text-on-surface font-semibold">{{ formatRupiah(tax) }}</span>
                            </div>
                            <hr class="border-outline-variant/40 my-4" />
                            <div class="flex justify-between items-baseline pt-1">
                                <span class="text-xl font-bold text-on-surface">Total</span>
                                <div class="text-right flex items-baseline gap-2">
                                    <span class="text-xs text-secondary uppercase font-semibold tracking-wider">IDR</span>
                                    <span class="text-2xl font-black text-primary">{{ formatRupiah(total) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <ShopFooter />
    </div>
</template>

<style scoped>
/* Theme logic unchanged */
.welcome-theme {
    --background: #fbf9f8;
    --foreground: #1b1c1c;
    --primary: #8a4853;
    --primary-foreground: #ffffff;
    --secondary: #625d5f;
    --secondary-foreground: #ffffff;
    --border: #d7c1c3;
    --outline: #857374;
    --color-surface: #fbf9f8;
    --color-surface-dim: #dcd9d9;
    --color-surface-container-lowest: #ffffff;
    --color-surface-container-low: #f6f3f2;
    --color-surface-container: #f0eded;
    --color-surface-container-highest: #e4e2e1;
    --color-on-surface: #1b1c1c;
    --color-on-surface-variant: #524345;
    --color-primary: #8a4853;
    --color-on-primary: #ffffff;
    --color-secondary: #625d5f;
    --color-secondary-fixed-variant: #4a4548;
    --color-outline-variant: #d7c1c3;
    --color-background: #fbf9f8;
    --color-on-background: #1b1c1c;
}

:global(.dark) .welcome-theme {
    --background: #141213;
    --foreground: #f3f0f0;
    --primary: #ffb2bc;
    --primary-foreground: #5a1b24;
    --secondary: #ccc4c7;
    --secondary-foreground: #332f31;
    --border: #524345;
    --outline: #9c8a8c;
    --color-surface: #141213;
    --color-surface-dim: #1e1a1b;
    --color-surface-container-lowest: #1a1617;
    --color-surface-container-low: #221d1e;
    --color-surface-container: #262122;
    --color-surface-container-highest: #332d2e;
    --color-on-surface: #f3f0f0;
    --color-on-surface-variant: #ccc4c7;
    --color-primary: #ffb2bc;
    --color-on-primary: #5a1b24;
    --color-secondary: #ccc4c7;
    --color-secondary-fixed-variant: #e4e2e1;
    --color-outline-variant: #524345;
    --color-background: #141213;
    --color-on-background: #f3f0f0;
}
</style>
