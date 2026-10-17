<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import ShopNavbar from '@/components/ShopNavbar.vue';
import ShopFooter from '@/components/ShopFooter.vue';

// Reactive order data simulation
const orderNumber = ref('#LFM-' + new Date().getFullYear() + '-' + Math.floor(1000 + Math.random() * 9000));
const isLoadingStatus = ref(false);

const handleTrackOrder = () => {
    if (isLoadingStatus.value) return;
    isLoadingStatus.value = true;
    setTimeout(() => {
        isLoadingStatus.value = false;
        alert(`Status Pesanan ${orderNumber.value}: Sedang Dikemas oleh Artisan Jelita Saung (In Production). Estimasi pengiriman 3 - 5 hari kerja.`);
    }, 1500);
};

const handlePrint = () => {
    if (typeof window !== 'undefined') {
        window.print();
    }
};
</script>

<template>
    <Head title="Order Confirmed - Jelita Saung">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    </Head>

    <div class="welcome-theme bg-surface text-on-surface min-h-screen flex flex-col font-sans antialiased selection:bg-primary/20 selection:text-primary">
        <!-- Top Navigation Bar -->
        <ShopNavbar />

        <!-- Main Content: Success Canvas -->
        <main class="flex-grow min-h-[820px] flex items-center justify-center px-4 sm:px-6 lg:px-20 py-16 relative overflow-hidden">
            <!-- Decorative Ambient Background Elements -->
            <div class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden z-0">
                <div class="absolute -top-[10%] -left-[10%] w-[500px] h-[500px] bg-primary/15 dark:bg-primary/10 opacity-60 blur-[140px] rounded-full"></div>
                <div class="absolute -bottom-[10%] -right-[10%] w-[500px] h-[500px] bg-[#e3c281]/20 dark:bg-[#e3c281]/10 opacity-50 blur-[140px] rounded-full"></div>
            </div>

            <section class="relative z-10 w-full max-w-3xl bg-surface-container-lowest rounded-3xl p-6 sm:p-10 md:px-14 md:py-12 shadow-[0px_12px_40px_rgba(0,0,0,0.08)] border border-outline-variant/40 text-center my-8">
                
                <!-- Elegant Animated Checkmark -->
                <div class="flex justify-center mb-6">
                    <div class="relative w-22 h-22 sm:w-24 sm:h-24 flex items-center justify-center bg-primary/15 text-primary rounded-full animate-float shadow-inner">
                        <svg class="w-11 h-11 sm:w-12 sm:h-12 text-primary" viewBox="0 0 52 52">
                            <path class="checkmark-draw" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" d="M14.1 27.2l7.1 7.2 16.7-16.8"></path>
                        </svg>
                        <!-- Pulse rings -->
                        <div class="absolute inset-0 border-2 border-primary/40 rounded-full animate-ping opacity-25"></div>
                    </div>
                </div>

                <h1 class="text-3xl sm:text-4xl md:text-[42px] font-extrabold tracking-tight mb-3 text-on-surface">
                    Terima kasih atas pesanan Anda
                </h1>
                <p class="text-base sm:text-lg text-secondary mb-10 leading-relaxed px-2 sm:px-6">
                    Pembayaran Anda berhasil dan pesanan Anda sedang disiapkan dengan cermat.
                </p>

                <!-- Order Details Board -->
                <div class="bg-surface-container-low/70 rounded-2xl p-6 mb-10 border border-outline-variant/40 shadow-xs">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                        <div class="text-center md:text-left">
                            <span class="text-xs text-secondary block font-bold uppercase tracking-widest mb-1">Nomor Pesanan</span>
                            <span class="text-xl sm:text-2xl font-black text-primary">{{ orderNumber }}</span>
                        </div>
                        <div class="h-10 w-px bg-outline-variant/60 hidden md:block"></div>
                        <div class="text-center md:text-left">
                            <span class="text-xs text-secondary block font-bold uppercase tracking-widest mb-1">Estimasi Pengiriman</span>
                            <span class="text-xl sm:text-2xl font-bold text-on-surface">3 - 5 Hari Kerja</span>
                        </div>
                    </div>
                </div>

                <!-- Artisan Message (Soft Luxury Bento Grid) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-10 text-left">
                    <div class="bg-surface p-5 rounded-2xl border border-outline-variant/30 flex gap-4 items-start shadow-xs hover:border-primary/40 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-[#e3c281]/20 text-tertiary flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-2xl">brush</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-on-surface mb-1">Pilihan Berdampak</h3>
                            <p class="text-xs sm:text-sm text-secondary leading-relaxed">Pembelian Anda secara langsung mendukung mata pencaharian perajin, melestarikan teknik tenun dan keramik kuno.</p>
                        </div>
                    </div>
                    <div class="bg-surface p-5 rounded-2xl border border-outline-variant/30 flex gap-4 items-start shadow-xs hover:border-primary/40 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-[#e3c281]/20 text-tertiary flex items-center justify-center flex-shrink-0">
                            <span class="material-symbols-outlined text-2xl">eco</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-on-surface mb-1">Kerajinan Berkelanjutan</h3>
                            <p class="text-xs sm:text-sm text-secondary leading-relaxed">Setiap barang dibuat dengan tangan menggunakan bahan bersumber secara etis, memastikan dampak lingkungan minimal.</p>
                        </div>
                    </div>
                </div>

                <!-- Action Cluster -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <button 
                        @click="handleTrackOrder"
                        :disabled="isLoadingStatus"
                        class="w-full sm:w-auto px-8 py-3.5 bg-primary text-white rounded-full font-bold text-sm shadow-lg hover:opacity-90 hover:shadow-primary/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2.5 cursor-pointer disabled:opacity-75"
                    >
                        <template v-if="isLoadingStatus">
                            <span class="material-symbols-outlined animate-spin text-xl">progress_activity</span>
                            <span>Mengecek Status...</span>
                        </template>
                        <template v-else>
                            <span class="material-symbols-outlined text-xl">local_shipping</span>
                            <span>Lacak Pesanan</span>
                        </template>
                    </button>
                    <Link 
                        href="/shop" 
                        class="w-full sm:w-auto px-8 py-3.5 bg-surface-container text-on-surface border border-outline-variant/50 rounded-full font-bold text-sm hover:bg-surface-container-high transition-all flex items-center justify-center text-center"
                    >
                        Lanjutkan Belanja
                    </Link>
                </div>

                <button 
                    @click="handlePrint" 
                    class="mt-8 text-secondary text-xs font-semibold hover:text-primary underline underline-offset-4 transition-colors cursor-pointer inline-flex items-center gap-1.5"
                >
                    <span class="material-symbols-outlined text-[16px]">print</span>
                    <span>Cetak Resi Pesanan</span>
                </button>
            </section>

            <!-- Product Preview (Side Detail - Asymmetric Floating Layout) -->
            <aside class="hidden xl:block absolute right-[5%] top-[28%] w-64 h-84 rounded-3xl overflow-hidden shadow-2xl rotate-3 hover:rotate-0 transition-transform duration-500 bg-surface border-4 border-surface-container-lowest z-10">
                <div class="h-full w-full bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&q=80&w=600');"></div>
                <div class="absolute bottom-0 left-0 right-0 p-5 bg-surface/90 backdrop-blur-md border-t border-outline-variant/20">
                    <p class="text-[11px] font-bold tracking-widest uppercase text-primary mb-0.5">Sedang Diproduksi</p>
                    <h4 class="text-sm font-black text-on-surface">Artisan Earth Vase</h4>
                </div>
            </aside>
        </main>

        <!-- Shared Footer Component -->
        <ShopFooter />
    </div>
</template>

<style scoped>
/* Icon font enforcement to avoid text word rendering */
.material-symbols-outlined {
    font-family: 'Material Symbols Outlined';
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    font-feature-settings: 'liga';
    display: inline-block;
    vertical-align: middle;
}

/* Floating animation for checkmark container */
@keyframes float {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
    100% { transform: translateY(0px); }
}
.animate-float {
    animation: float 4s ease-in-out infinite;
}

/* Checkmark drawing animation */
.checkmark-draw {
    stroke-dasharray: 100;
    stroke-dashoffset: 100;
    animation: draw 1s ease-out forwards 0.3s;
}
@keyframes draw {
    to { stroke-dashoffset: 0; }
}
</style>
