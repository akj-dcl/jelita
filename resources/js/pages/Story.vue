<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import ShopNavbar from '@/components/ShopNavbar.vue';
import ShopFooter from '@/components/ShopFooter.vue';
</script>

<template>
    <Head title="Our Story | Jelita Saung">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    </Head>

    <div class="welcome-theme min-h-screen bg-background text-on-background font-body-md overflow-x-clip">
        <ShopNavbar />

        <main class="max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop py-12 md:py-24">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h1 class="font-display-lg text-4xl md:text-5xl lg:text-6xl text-on-surface font-bold mb-6 tracking-tight">Cerita Kami</h1>
                <p class="text-on-surface-variant text-lg md:text-xl leading-relaxed">
                    Menciptakan karya agung tak lekang oleh waktu dengan penuh semangat, ketelitian, dan komitmen teguh terhadap kualitas.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-24 items-center mb-24">
                <div class="rounded-2xl overflow-hidden aspect-square shadow-[0px_12px_40px_rgba(0,0,0,0.08)]">
                    <img src="https://placehold.co/800x800/e2e8f0/64748b?text=Artisan+Workshop" alt="Our Workshop" class="w-full h-full object-cover" />
                </div>
                <div>
                    <h2 class="font-display-md text-3xl text-primary font-bold mb-6">Sejarah Singkat Jelita Saung</h2>
                    <p class="text-on-surface-variant mb-4 leading-relaxed">
                        Jelita Saung merupakan salah satu wadah kegiatan pembinaan di Rutan Perempuan Kelas IIA Bandung yang dikembangkan sebagai sarana bagi warga binaan untuk menyalurkan kreativitas, mengembangkan keterampilan, serta menghasilkan karya yang memiliki nilai guna dan nilai ekonomi.
                    </p>
                    <p class="text-on-surface-variant mb-4 leading-relaxed">
                        Berawal dari kegiatan pembinaan keterampilan yang dilaksanakan melalui Sub Seksi Bimbingan Kegiatan (Bimkeg). Jelita Saung terus dikembangkan dengan melibatkan warga binaan dalam berbagai kegiatan produktif, seperti pengolahan makanan dan bakery, kerajinan, serta kegiatan keterampilan lainnya. Produk yang dihasilkan merupakan bagian dari proses pembinaan sekaligus menjadi bentuk nyata dari potensi dan kreativitas warga binaan.
                    </p>
                    <p class="text-on-surface-variant mb-4 leading-relaxed">
                        Dalam perkembangannya, Jelita Saung tidak hanya menjadi tempat untuk menghasilkan karya, tetapi juga diarahkan sebagai sarana untuk meningkatkan kemandirian, membangun kepercayaan diri, dan mempersiapkan warga binaan agar memiliki keterampilan yang dapat dimanfaatkan setelah kembali ke masyarakat.
                    </p>
                    <p class="text-on-surface-variant leading-relaxed">
                        Melalui dukungan berbagai pihak dan pengembangan jejaring pemasaran, Jelita Saung diharapkan dapat terus menjadi wadah pembinaan yang produktif serta memperkenalkan karya warga binaan Rutan Perempuan Kelas IIA Bandung kepada masyarakat secara lebih luas.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 lg:gap-24 items-center flex-col-reverse md:flex-row">
                <div class="order-2 md:order-1">
                    <h2 class="font-display-md text-3xl text-primary font-bold mb-6">Filosofi Kami</h2>
                    <p class="text-on-surface-variant mb-4 leading-relaxed">
                        Kami hanya mengambil bahan-bahan terbaik, bekerja sama dengan perajin lokal dan pemasok berkelanjutan. Komitmen kami terhadap produksi etis berarti setiap pembelian mendukung praktik ketenagakerjaan yang adil dan pengadaan bahan yang bertanggung jawab terhadap lingkungan.
                    </p>
                    <p class="text-on-surface-variant leading-relaxed">
                        Bila Anda memilih Jelita Saung, Anda tidak sekadar membeli produk; Anda berinvestasi dalam sebuah karya seni yang membawa cerita, jiwa, dan janji kualitas yang bertahan lama.
                    </p>
                </div>
                <div class="order-1 md:order-2 rounded-2xl overflow-hidden aspect-square shadow-[0px_12px_40px_rgba(0,0,0,0.08)]">
                    <img src="https://placehold.co/800x800/e2e8f0/64748b?text=Quality+Materials" alt="Our Materials" class="w-full h-full object-cover" />
                </div>
            </div>
            
            <div class="mt-32 text-center bg-surface-container-low rounded-3xl p-12 shadow-sm">
                <h2 class="font-display-md text-3xl text-on-surface font-bold mb-6">Rasakan Pengalaman Kerajinan Kami</h2>
                <p class="text-on-surface-variant text-lg max-w-2xl mx-auto mb-10">
                    Jelajahi koleksi karya seni buatan tangan kami dan temukan barang sempurna yang sesuai dengan gaya Anda.
                </p>
                <Link href="/shop" class="bg-primary text-white hover:bg-primary-container px-8 py-4 rounded-full font-bold shadow-md transition-all hover:shadow-lg hover:-translate-y-1 inline-block">
                    Jelajahi Koleksi
                </Link>
            </div>
        </main>

        <ShopFooter />
    </div>
</template>

<style scoped>
.welcome-theme {
    /* Scoped theme variables matching the Jelita Saung palette */
    --background: #fbf9f8;
    --foreground: #1b1c1c;
    --primary: #8a4853;
    --primary-foreground: #ffffff;
    --secondary: #625d5f;
    --secondary-foreground: #ffffff;
    --border: #d7c1c3;
    --outline: #857374;
}

:global(.dark) .welcome-theme {
    /* Dark mode adjustments if the system theme is dark */
    --background: #141213;
    --foreground: #f3f0f0;
    --primary: #ffb2bc;
    --primary-foreground: #5a1b24;
    --secondary: #ccc4c7;
    --secondary-foreground: #332f31;
    --border: #524345;
    --outline: #9c8a8c;
}
</style>
