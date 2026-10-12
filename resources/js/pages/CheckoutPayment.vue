<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import ShopNavbar from '@/components/ShopNavbar.vue';
import ShopFooter from '@/components/ShopFooter.vue';

const checkoutData = ref<any>(null);
const paymentMethod = ref<'bank_transfer' | 'qris' | 'cod'>('bank_transfer');
const paymentProof = ref<File | null>(null);
const isSubmitting = ref(false);
const orderSuccess = ref(false);
const orderNumber = ref('');

onMounted(() => {
    const data = localStorage.getItem('checkoutData');
    if (data) {
        checkoutData.value = JSON.parse(data);
        if (checkoutData.value?.deliveryType === 'cod' || checkoutData.value?.form?.courier === 'cod') {
            paymentMethod.value = 'cod';
        }
    } else {
        router.visit('/checkout');
    }
});

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        paymentProof.value = target.files[0];
    }
};

const formatRupiah = (val: number) => {
    return 'Rp ' + Math.round(val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

const submitOrder = async () => {
    if (paymentMethod.value !== 'cod' && !paymentProof.value) {
        alert('Mohon unggah bukti pembayaran.');
        return;
    }
    
    isSubmitting.value = true;
    
    const formData = new FormData();
    formData.append('email', checkoutData.value.form.email);
    formData.append('firstName', checkoutData.value.form.firstName);
    formData.append('lastName', checkoutData.value.form.lastName || '');
    formData.append('address', checkoutData.value.form.address);
    formData.append('phone', checkoutData.value.form.phone);
    formData.append('destination_id', checkoutData.value.form.destination_id || '');
    formData.append('destination_label', checkoutData.value.form.destination_label || '');
    formData.append('postalCode', checkoutData.value.form.postalCode || '');
    formData.append('courier', checkoutData.value.form.courier);
    
    formData.append('subtotal', checkoutData.value.subtotal.toString());
    formData.append('shippingCost', checkoutData.value.shippingCost.toString());
    formData.append('tax', checkoutData.value.tax.toString());
    formData.append('total', checkoutData.value.total.toString());
    
    formData.append('payment_method', paymentMethod.value);
    if (paymentProof.value) {
        formData.append('payment_proof', paymentProof.value);
    }

    try {
        const res = await fetch('/api/checkout/process', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const data = await res.json();
        
        if (res.ok) {
            orderSuccess.value = true;
            orderNumber.value = data.order_number;
            localStorage.removeItem('checkoutData');
        } else {
            alert(data.error || 'Terjadi kesalahan saat memproses pesanan.');
        }
    } catch (e) {
        console.error(e);
        alert('Gagal menghubungi server.');
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <Head title="Payment - Jelita Saung" />
    <div class="welcome-theme min-h-screen bg-background text-on-background font-body-md overflow-x-clip antialiased">
        <ShopNavbar />

        <main class="max-w-[800px] mx-auto px-margin-mobile md:px-margin-desktop py-12">
            <div v-if="orderSuccess" class="bg-surface-container-low p-8 rounded-3xl border border-outline-variant/30 text-center shadow-sm">
                <span class="material-symbols-outlined text-6xl text-primary mb-4">check_circle</span>
                <h2 class="text-3xl font-bold mb-2">Pesanan Berhasil Dibuat!</h2>
                <p class="text-secondary mb-6">Nomor Pesanan: <strong>{{ orderNumber }}</strong></p>
                <p v-if="paymentMethod === 'cod'" class="mb-8 text-on-surface">
                    Terima kasih atas pesanan Anda. Pesanan COD Anda sedang dipersiapkan. Mohon sediakan uang pas saat pesanan diserahkan.
                </p>
                <p v-else class="mb-8 text-on-surface">
                    Terima kasih atas pesanan Anda. Kami sedang memverifikasi pembayaran Anda.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <Link href="/" class="px-8 py-3 bg-primary text-white font-bold rounded-full hover:opacity-95 transition-all">
                        Kembali ke Beranda
                    </Link>
                    <Link href="/profile" class="px-8 py-3 bg-surface-container-high text-on-surface font-bold rounded-full hover:bg-surface-container-highest transition-all">
                        Lihat Riwayat Pesanan
                    </Link>
                </div>
            </div>
            
            <div v-else-if="checkoutData">
                <h1 class="font-headline-md text-3xl font-bold text-on-surface mb-8 text-center">Payment</h1>
                
                <section class="bg-surface-container-low p-6 sm:p-8 rounded-3xl border border-outline-variant/30 shadow-sm mb-8">
                    <h2 class="text-xl font-bold mb-4 border-b border-outline-variant/30 pb-2">Ringkasan Pesanan</h2>
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between text-sm"><span class="text-secondary">Subtotal</span><span>{{ formatRupiah(checkoutData.subtotal) }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-secondary">Ongkos Kirim</span><span>{{ formatRupiah(checkoutData.shippingCost) }}</span></div>
                        <div class="flex justify-between text-sm"><span class="text-secondary">Pajak (11%)</span><span>{{ formatRupiah(checkoutData.tax) }}</span></div>
                    </div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t border-outline-variant/30">
                        <span>Total Tagihan</span>
                        <span class="text-primary">{{ formatRupiah(checkoutData.total) }}</span>
                    </div>
                </section>

                <section class="bg-surface-container-low p-6 sm:p-8 rounded-3xl border border-outline-variant/30 shadow-sm mb-8">
                    <h2 class="text-xl font-bold mb-6">Pilih Metode Pembayaran</h2>
                    <div class="space-y-4">
                        <label class="flex items-center gap-4 p-4 border rounded-2xl cursor-pointer transition-all" :class="paymentMethod === 'bank_transfer' ? 'border-primary bg-primary/5' : 'border-outline-variant'">
                            <input type="radio" v-model="paymentMethod" value="bank_transfer" class="w-5 h-5 text-primary" />
                            <div>
                                <p class="font-bold">Transfer Bank (BRI)</p>
                                <p class="text-sm text-secondary">BRI a.n KOP KONSUMEN RUTAN PEREMPUAN : 035401003914568</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-4 p-4 border rounded-2xl cursor-pointer transition-all" :class="paymentMethod === 'qris' ? 'border-primary bg-primary/5' : 'border-outline-variant'">
                            <input type="radio" v-model="paymentMethod" value="qris" class="w-5 h-5 text-primary" />
                            <div>
                                <p class="font-bold">QRIS</p>
                                <p class="text-sm text-secondary">Scan QRIS dari aplikasi e-wallet Anda</p>
                            </div>
                        </label>
                        <label class="flex items-center gap-4 p-4 border rounded-2xl cursor-pointer transition-all" :class="paymentMethod === 'cod' ? 'border-primary bg-primary/5' : 'border-outline-variant'">
                            <input type="radio" v-model="paymentMethod" value="cod" class="w-5 h-5 text-primary" />
                            <div>
                                <p class="font-bold">COD (Cash on Delivery / Bayar di Tempat)</p>
                                <p class="text-sm text-secondary">Bayar langsung secara tunai saat produk diterima</p>
                            </div>
                        </label>
                    </div>

                    <div v-if="paymentMethod === 'qris'" class="mt-6 flex flex-col items-center border border-outline-variant p-6 rounded-2xl bg-white shadow-xs">
                        <p class="text-sm text-secondary-foreground mb-3 font-medium">Scan QR di bawah ini dengan mobile banking atau e-wallet Anda</p>
                        <img src="/images/qris-jelita.jpg" alt="QRIS Kantin Koperasi Jelita" class="w-64 max-w-full h-auto rounded-xl shadow-xs" />
                        <p class="text-xs font-semibold text-secondary-foreground mt-3 tracking-wide">KANTIN KOPERASI JELITA · NMID: ID1026489299812</p>
                    </div>

                    <div v-else-if="paymentMethod === 'cod'" class="mt-6 p-5 border border-primary/20 rounded-2xl bg-primary/5 flex items-start gap-4">
                        <span class="material-symbols-outlined text-primary text-2xl mt-0.5">payments</span>
                        <div>
                            <p class="font-bold text-on-surface mb-1">Bayar Tunai di Tempat (COD)</p>
                            <p class="text-sm text-secondary">
                                Anda dapat membayar secara tunai langsung saat pesanan diserahkan. Tidak perlu mengunggah bukti pembayaran sekarang.
                            </p>
                        </div>
                    </div>

                    <div v-if="paymentMethod !== 'cod'" class="mt-8">
                        <label class="block text-sm font-medium mb-2 text-on-surface-variant">Unggah Bukti Pembayaran <span class="text-red-500">*</span></label>
                        <input type="file" accept="image/*" @change="handleFileUpload" class="w-full text-sm text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20" />
                    </div>
                </section>

                <button 
                    @click="submitOrder" 
                    :disabled="isSubmitting"
                    class="w-full px-10 py-4 bg-primary text-white font-bold text-base rounded-full shadow-[0px_8px_30px_rgba(138,72,83,0.25)] hover:opacity-95 active:scale-[0.98] transition-all disabled:opacity-50"
                >
                    {{ isSubmitting ? 'Memproses...' : (paymentMethod === 'cod' ? 'Konfirmasi Pesanan COD' : 'Konfirmasi Pembayaran') }}
                </button>
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
    --color-surface-container-low: #f6f3f2;
    --color-on-surface: #1b1c1c;
    --color-on-surface-variant: #524345;
    --color-primary: #8a4853;
    --color-secondary: #625d5f;
    --color-outline-variant: #d7c1c3;
}
</style>
