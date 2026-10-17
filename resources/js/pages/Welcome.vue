<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ShopNavbar from '@/components/ShopNavbar.vue';
import ShopFooter from '@/components/ShopFooter.vue';

const props = defineProps<{
    categories: Array<any>;
    products: Array<any>;
    reviews?: Array<any>;
}>();

// Duplikasi review secara seamless jika data ulasan sedikit agar marquee bergerak tanpa jeda kosong
const displayReviews = computed(() => {
    if (!props.reviews || props.reviews.length === 0) return [];
    let list = [...props.reviews];
    while (list.length < 8) {
        list = [...list, ...props.reviews];
    }
    return list;
});

// FAQ Accordion state
const openFaqIndex = ref<number | null>(null);

const toggleFaq = (index: number) => {
    if (openFaqIndex.value === index) {
        openFaqIndex.value = null;
    } else {
        openFaqIndex.value = index;
    }
};
</script>

<template>
    <Head title="Jelita Saung | Artisanal Elegance">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    </Head>

    <div class="welcome-theme min-h-screen bg-background text-on-background font-body-md overflow-x-clip">
        <!-- TopNavBar -->
        <ShopNavbar />

        <main>
            <!-- Hero Banner -->
            <section class="relative min-h-[600px] md:min-h-[819px] flex items-center overflow-hidden">
                <div class="absolute inset-0 z-0">
                    <div class="w-full h-full bg-cover bg-center" style="background-image: url('/storage/template/banner-utama-home.jpg')"></div>
                    <div class="absolute inset-0 bg-black/20 dark:bg-black/60"></div>
                </div>
                <div class="relative z-10 w-full max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop py-16">
                    <div class="max-w-2xl bg-white/40 dark:bg-card/75 backdrop-blur-md p-6 md:p-16 rounded-xl animate-in fade-in slide-in-from-bottom duration-500 border border-white/20 dark:border-outline-variant/30">
                        <h1 class="font-display-lg text-[40px] md:text-[48px] text-on-background mb-2 leading-tight">Karya Berkualitas yang Menginspirasi</h1>
                        <p class="text-lg text-secondary mb-10 leading-relaxed">
                            Setiap produk merupakan hasil pembinaan keterampilan yang dibuat dengan dedikasi tinggi, menghadirkan kualitas terbaik sekaligus mendukung pemberdayaan perempuan.
                        </p>
                        <div class="flex flex-wrap gap-4 sm:gap-6">
                            <Link href="/shop" class="bg-dusty-rose text-white dark:bg-primary dark:text-primary-foreground px-8 sm:px-10 py-3 rounded-full font-label-md text-label-md hover:opacity-90 transition-all active:scale-95 shadow-sm cursor-pointer inline-flex items-center justify-center font-medium">
                                Belanja Koleksi
                            </Link>
                            <Link href="/story" class="bg-pale-pink text-dusty-rose dark:bg-surface-container-highest dark:text-primary dark:hover:bg-surface-container-high px-8 sm:px-10 py-3 rounded-full font-label-md text-label-md hover:bg-white transition-all active:scale-95 shadow-sm cursor-pointer inline-flex items-center justify-center font-medium">
                                Cerita Kami
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Featured Categories: Bento-ish Grid -->
            <section class="py-16 max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop">
                <div class="text-center mb-16">
                    <span class="text-gold font-label-md uppercase tracking-[0.2em] mb-1 block">Temukan</span>
                    <h2 class="text-3xl font-bold text-on-background">Jelajahi Berdasarkan Kategori</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-gutter h-[600px] md:h-[500px]">
                    <Link v-for="(category, index) in props.categories.slice(0, 4)" :key="category.id"
                         :href="`/shop?category=${category.slug}`"
                         :class="['relative group overflow-hidden rounded-xl shadow-[0px_4px_20px_rgba(0,0,0,0.04)] cursor-pointer block', index === 0 ? 'md:col-span-2' : (index === 3 ? 'md:col-span-4 h-[200px] md:h-auto' : '')]">
                        <div class="absolute inset-0 bg-cover bg-center transition-transform duration-700 group-hover:scale-110" :style="`background-image: url('${category.image ? '/storage/' + category.image : 'https://placehold.co/600x400/e2e8f0/64748b?text=' + encodeURIComponent(category.name)}')`"></div>
                        <div class="absolute inset-0 bg-black/10 group-hover:bg-black/20 transition-all"></div>
                        <div class="absolute inset-0 flex items-center justify-center p-6">
                            <div class="bg-white/90 dark:bg-card/90 px-10 py-6 rounded-lg text-center backdrop-blur-sm shadow-sm border border-transparent dark:border-outline-variant/30">
                                <span class="text-2xl font-bold text-on-surface">{{ category.name }}</span>
                            </div>
                        </div>
                    </Link>
                    <div v-if="props.categories.length === 0" class="md:col-span-4 text-center py-10 text-secondary">
                        Belum ada kategori yang tersedia.
                    </div>
                </div>
            </section>

            <!-- Featured Products / New Arrivals -->
            <section class="py-16 bg-surface-container-low">
                <div class="max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop">
                    <div class="flex justify-between items-end mb-16">
                        <div>
                            <span class="text-gold font-label-md uppercase tracking-[0.2em] mb-1 block">Produk Terbaru</span>
                            <h2 class="text-3xl font-bold text-on-background">Koleksi Terbaru</h2>
                        </div>
                        <a class="text-dusty-rose font-label-md underline underline-offset-4 hover:opacity-70 transition-all animate-pulse" href="#">Lihat Semua</a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-gutter">
                        <Link v-for="product in props.products" :key="product.id" :href="`/product/${product.id}`" class="product-card bg-white dark:bg-card rounded-xl overflow-hidden transition-all-300 cursor-pointer block border border-transparent dark:border-outline-variant/30">
                            <div class="relative h-64 md:h-80 bg-surface-container overflow-hidden p-2">
                                <img class="w-full h-full object-cover rounded-lg" :src="product.image ? `/storage/${product.image}` : 'https://placehold.co/400x500/e2e8f0/64748b?text=No+Image'" :alt="product.name"/>
                            </div>
                            <div class="p-6">
                                <h3 class="font-label-md text-on-background mb-1">{{ product.name }}</h3>
                                <p class="text-dusty-rose font-medium">Rp {{ parseInt(product.price).toLocaleString('id-ID') }}</p>
                            </div>
                        </Link>
                        <div v-if="props.products.length === 0" class="col-span-2 md:col-span-4 text-center py-10 text-secondary">
                            Belum ada produk yang tersedia.
                        </div>
                    </div>
                </div>
            </section>

            <!-- Our Story -->
            <section class="py-16 max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
                    <div class="relative rounded-xl overflow-hidden shadow-xl aspect-square">
                        <img class="w-full h-full object-cover" src="/storage/template/home-misi-kami.jpg" alt="Weaving process"/>
                    </div>
                    <div class="space-y-10">
                        <span class="text-gold font-label-md uppercase tracking-[0.2em] block">Misi Kami</span>
                        <h2 class="text-3xl md:text-4xl font-bold leading-tight text-on-background">Pemberdayaan Melalui Seni &amp; Kerajinan</h2>
                        <p class="text-lg text-secondary leading-relaxed">
                            Jelita Saung Rutan Perempuan Kelas II A Bandung lahir dari visi untuk memberikan harapan dan keterampilan baru. Setiap produk yang Anda lihat adalah hasil karya tangan-tangan terampil para wanita di bawah pembinaan kami, yang belajar mengubah dedikasi menjadi karya seni yang bernilai tinggi.
                        </p>
                        <p class="text-md text-secondary leading-relaxed">
                            Dengan memilih Jelita Saung, Anda tidak hanya membawa pulang produk berkualitas premium, tetapi juga ikut berkontribusi dalam program rehabilitasi dan pemberdayaan ekonomi yang berkelanjutan.
                        </p>
                        <Link href="/story" class="text-dusty-rose border border-dusty-rose px-10 py-3 rounded-full hover:bg-dusty-rose hover:text-white transition-all cursor-pointer inline-block text-center">Baca Selengkapnya</Link>
                    </div>
                </div>
            </section>

            <!-- Why Buy From Us -->
            <section class="py-16 bg-pale-pink/30 dark:bg-surface-container-low/50">
                <div class="max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop grid grid-cols-2 md:grid-cols-4 gap-10 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-white dark:bg-card rounded-full flex items-center justify-center text-dusty-rose shadow-sm mb-6 border border-transparent dark:border-outline-variant/30">
                            <span class="material-symbols-outlined text-[32px]">back_hand</span>
                        </div>
                        <h4 class="font-bold text-on-background text-[18px] mb-1">Buatan Tangan</h4>
                        <p class="text-sm text-secondary px-4">Dibuat dengan ketelitian tinggi oleh tangan, bukan mesin.</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-white dark:bg-card rounded-full flex items-center justify-center text-dusty-rose shadow-sm mb-6 border border-transparent dark:border-outline-variant/30">
                            <span class="material-symbols-outlined text-[32px]">female</span>
                        </div>
                        <h4 class="font-bold text-on-background text-[18px] mb-1">Pemberdayaan</h4>
                        <p class="text-sm text-secondary px-4">Mendukung kemandirian perempuan lokal.</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-white dark:bg-card rounded-full flex items-center justify-center text-dusty-rose shadow-sm mb-6 border border-transparent dark:border-outline-variant/30">
                            <span class="material-symbols-outlined text-[32px]">eco</span>
                        </div>
                        <h4 class="font-bold text-on-background text-[18px] mb-1">Berkelanjutan</h4>
                        <p class="text-sm text-secondary px-4">Menggunakan bahan lokal yang ramah lingkungan.</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-white dark:bg-card rounded-full flex items-center justify-center text-dusty-rose shadow-sm mb-6 border border-transparent dark:border-outline-variant/30">
                            <span class="material-symbols-outlined text-[32px]">workspace_premium</span>
                        </div>
                        <h4 class="font-bold text-on-background text-[18px] mb-1">Kualitas Premium</h4>
                        <p class="text-sm text-secondary px-4">Hasil akhir halus dengan bahan berkualitas tinggi.</p>
                    </div>
                </div>
            </section>

            <!-- Testimonials -->
            <section class="py-20 overflow-hidden text-center bg-surface-container-low/40">
                <div class="max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop mb-12">
                    <span class="text-gold font-label-md uppercase tracking-[0.2em] mb-2 block font-semibold">Ulasan Pelanggan</span>
                    <h2 class="text-3xl md:text-4xl font-bold text-on-background">Suara Apresiasi</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base mt-2 max-w-[600px] mx-auto leading-relaxed">Cerita dan pengalaman autentik dari para pembeli produk Jelita Saung</p>
                </div>

                <div v-if="props.reviews && props.reviews.length > 0" class="relative w-full overflow-hidden marquee-mask py-4">
                    <div class="flex w-max gap-6 animate-marquee hover:[animation-play-state:paused]">
                        <!-- Track Set 1 -->
                        <div v-for="(review, index) in displayReviews" :key="'r1-' + index"
                            class="w-[320px] sm:w-[380px] bg-white dark:bg-surface-container p-8 rounded-2xl shadow-[0px_4px_24px_rgba(0,0,0,0.04)] border border-outline-variant/40 flex flex-col justify-between text-left shrink-0 transition-transform duration-300 hover:scale-[1.02]">
                            <div>
                                <div class="flex text-gold mb-4 gap-1">
                                    <span v-for="i in 5" :key="i"
                                        class="material-symbols-outlined text-[18px]"
                                        :style="i <= review.rating ? 'font-variation-settings: \'FILL\' 1;' : ''">
                                        star
                                    </span>
                                </div>
                                <p class="text-on-surface-variant mb-6 text-sm leading-relaxed italic line-clamp-4">
                                    "{{ review.comment }}"
                                </p>
                            </div>
                            <div class="pt-4 border-t border-surface-variant/40 flex items-center justify-between">
                                <div>
                                    <span class="font-semibold text-primary text-sm block">
                                        {{ review.user_name }}
                                    </span>
                                    <p v-if="review.product_name" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1 max-w-[200px]" :title="review.product_name">
                                        {{ review.product_name }}
                                    </p>
                                </div>
                                <span v-if="review.created_at" class="text-[11px] text-gray-400">
                                    {{ review.created_at }}
                                </span>
                            </div>
                        </div>

                        <!-- Track Set 2 (Clone untuk continuous seamless looping) -->
                        <div v-for="(review, index) in displayReviews" :key="'r2-' + index"
                            class="w-[320px] sm:w-[380px] bg-white dark:bg-surface-container p-8 rounded-2xl shadow-[0px_4px_24px_rgba(0,0,0,0.04)] border border-outline-variant/40 flex flex-col justify-between text-left shrink-0 transition-transform duration-300 hover:scale-[1.02]">
                            <div>
                                <div class="flex text-gold mb-4 gap-1">
                                    <span v-for="i in 5" :key="i"
                                        class="material-symbols-outlined text-[18px]"
                                        :style="i <= review.rating ? 'font-variation-settings: \'FILL\' 1;' : ''">
                                        star
                                    </span>
                                </div>
                                <p class="text-on-surface-variant mb-6 text-sm leading-relaxed italic line-clamp-4">
                                    "{{ review.comment }}"
                                </p>
                            </div>
                            <div class="pt-4 border-t border-surface-variant/40 flex items-center justify-between">
                                <div>
                                    <span class="font-semibold text-primary text-sm block">
                                        {{ review.user_name }}
                                    </span>
                                    <p v-if="review.product_name" class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1 max-w-[200px]" :title="review.product_name">
                                        {{ review.product_name }}
                                    </p>
                                </div>
                                <span v-if="review.created_at" class="text-[11px] text-gray-400">
                                    {{ review.created_at }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-gray-500 py-8 italic">
                    Belum ada ulasan dari pelanggan.
                </div>
            </section>

            <!-- FAQ Accordion -->
            <section class="py-16 bg-surface-container-low">
                <div class="max-w-3xl mx-auto px-margin-mobile md:px-0">
                    <h2 class="text-3xl font-bold text-center mb-16">Pertanyaan yang Sering Diajukan</h2>
                    <div class="space-y-6">
                        <div class="border-b border-outline-variant pb-6 cursor-pointer group" @click="toggleFaq(0)">
                            <div class="flex justify-between items-center py-2">
                                <h3 class="font-label-md text-on-background font-semibold">Bagaimana cara memesan produk di website Jelita Saung?</h3>
                                <span class="material-symbols-outlined group-hover:text-primary transition-colors">
                                    {{ openFaqIndex === 0 ? 'remove' : 'add' }}
                                </span>
                            </div>
                            <p v-show="openFaqIndex === 0" class="text-secondary mt-2 animate-in fade-in duration-200 leading-relaxed">
                                Pilih produk yang diinginkan, tentukan jumlah pesanan, lalu masukkan ke keranjang belanja dan lanjutkan ke proses checkout sesuai petunjuk pada website.
                            </p>
                        </div>
                        <div class="border-b border-outline-variant pb-6 cursor-pointer group" @click="toggleFaq(1)">
                            <div class="flex justify-between items-center py-2">
                                <h3 class="font-label-md text-on-background font-semibold">Apakah produk Jelita Saung selalu tersedia?</h3>
                                <span class="material-symbols-outlined group-hover:text-primary transition-colors">
                                    {{ openFaqIndex === 1 ? 'remove' : 'add' }}
                                </span>
                            </div>
                            <p v-show="openFaqIndex === 1" class="text-secondary mt-2 animate-in fade-in duration-200 leading-relaxed">
                                Ketersediaan produk bergantung pada stok dan proses produksi. Pelanggan dapat menghubungi admin untuk memastikan ketersediaan produk sebelum melakukan pemesanan.
                            </p>
                        </div>
                        <div class="border-b border-outline-variant pb-6 cursor-pointer group" @click="toggleFaq(2)">
                            <div class="flex justify-between items-center py-2">
                                <h3 class="font-label-md text-on-background font-semibold">Bagaimana cara melakukan pembayaran pesanan?</h3>
                                <span class="material-symbols-outlined group-hover:text-primary transition-colors">
                                    {{ openFaqIndex === 2 ? 'remove' : 'add' }}
                                </span>
                            </div>
                            <p v-show="openFaqIndex === 2" class="text-secondary mt-2 animate-in fade-in duration-200 leading-relaxed">
                                Pelanggan dapat melakukan pembayaran melalui metode yang tersedia pada website. Pastikan mengikuti petunjuk pembayaran dan melakukan konfirmasi apabila diperlukan.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Newsletter -->
            <section class="py-16">
                <div class="max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop">
                    <div class="bg-dusty-rose dark:bg-card dark:border dark:border-primary/20 rounded-3xl p-10 md:p-16 text-center text-white dark:text-foreground relative overflow-hidden">
                        <!-- Subtle pattern overlay -->
                        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
                        <div class="relative z-10 max-w-2xl mx-auto py-4">
                            <h2 class="text-2xl md:text-3xl font-bold mb-3 tracking-tight">Bergabung dalam Perjalanan Kerajinan</h2>
                            <p class="text-sm md:text-base text-white/90 dark:text-muted-foreground mb-8 max-w-2xl mx-auto leading-relaxed">
                                Berlangganan untuk menerima koleksi eksklusif, cerita perajin, dan penawaran spesial.
                            </p>
                            <form class="flex flex-col sm:flex-row gap-3 items-stretch justify-center max-w-[520px] mx-auto" @submit.prevent>
                                <input class="flex-grow px-6 py-3.5 rounded-full bg-white/10 dark:bg-input border border-white/25 dark:border-outline-variant text-white dark:text-foreground placeholder:text-white/60 dark:placeholder:text-muted-foreground focus:outline-none focus:bg-white/20 dark:focus:bg-input focus:border-white/40 dark:focus:border-primary transition-all text-sm" placeholder="Alamat email Anda" type="email" required/>
                                <button class="bg-white dark:bg-primary text-dusty-rose dark:text-primary-foreground hover:bg-pale-pink dark:hover:opacity-90 px-8 py-3.5 rounded-full font-semibold text-sm hover:shadow-lg transition-all active:scale-95 cursor-pointer whitespace-nowrap" type="submit">
                                    Berlangganan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <ShopFooter />
    </div>
</template>
<style scoped>
.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0px 12px 40px rgba(0, 0, 0, 0.06);
}

.transition-all-300 {
    transition: all 0.3s ease;
}

/* Infinite Marquee */
@keyframes marquee {
    0% {
        transform: translateX(0%);
    }
    100% {
        transform: translateX(-50%);
    }
}

.animate-marquee {
    display: flex;
    width: max-content;
    animation: marquee 35s linear infinite;
}

.animate-marquee:hover {
    animation-play-state: paused;
}

.marquee-mask {
    -webkit-mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
    mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
}
</style>
