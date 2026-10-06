<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import ShopNavbar from '@/components/ShopNavbar.vue';
import ShopFooter from '@/components/ShopFooter.vue';
</script>

<template>
    <Head title="About Us | Jelita Saung">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet" />
    </Head>

    <div class="welcome-theme min-h-screen bg-background text-on-background font-body-md overflow-x-clip flex flex-col">
        <ShopNavbar />

        <main class="flex-grow max-w-[1440px] mx-auto px-margin-mobile md:px-margin-desktop py-12 md:py-24 w-full">
            <div class="max-w-4xl mx-auto">
                <div class="text-center mb-16">
                    <h1 class="font-display-lg text-4xl md:text-5xl text-on-surface font-bold mb-6 tracking-tight">Tentang Jelita Saung</h1>
                    <p class="text-on-surface-variant text-lg">
                        Lebih dari sekadar toko, kami adalah destinasi terkurasi bagi mereka yang menghargai hal-hal terbaik dalam hidup.
                    </p>
                </div>

                <div class="bg-surface-container-lowest rounded-3xl p-8 md:p-12 shadow-[0px_12px_40px_rgba(0,0,0,0.04)] mb-16">
                    <div class="prose prose-lg max-w-none text-on-surface-variant">
                        <p class="mb-6">
                            Jelita Saung berdedikasi untuk mengkurasi dan menciptakan produk gaya hidup yang luar biasa. Kami percaya bahwa barang-barang yang Anda gunakan setiap hari harus membawa kegembiraan, keanggunan, dan fungsionalitas dalam hidup Anda.
                        </p>
                        <h3 class="font-display-sm text-2xl text-primary font-bold mt-10 mb-4">Misi Kami</h3>
                        <ol class="list-decimal pl-6 space-y-3 mb-6">
                            <li>Mengembangkan keterampilan dan kreativitas warga binaan melalui berbagai kegiatan pembinaan yang produktif.</li>
                            <li>Meningkatkan kualitas dan nilai jual produk yang dihasilkan melalui proses pembinaan dan pendampingan.</li>
                            <li>Mendorong kemandirian warga binaan sebagai bekal untuk menjalani kehidupan setelah selesai mengikuti masa pembinaan.</li>
                            <li>Membangun kerja sama dan jejaring pemasaran dengan berbagai pihak dalam mendukung pengembangan produk karya warga binaan.</li>
                            <li>Menghasilkan dan mengenalkan produk unggulan yang menjadi identitas dari Rutan Perempuan Kelas IIA Bandung, dengan mengedepankan kualitas, kreativitas, dan keunikan karya warga binaan kepada masyarakat sebagai bagian reintegrasi sosial.</li>
                        </ol>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                    <div class="p-6">
                        <span class="material-symbols-outlined text-5xl text-primary mb-4">verified</span>
                        <h4 class="font-bold text-lg mb-2">Keaslian</h4>
                        <p class="text-sm text-on-surface-variant">Setiap barang 100% asli, bersumber langsung dari pencipta atau mitra terverifikasi.</p>
                    </div>
                    <div class="p-6">
                        <span class="material-symbols-outlined text-5xl text-primary mb-4">local_shipping</span>
                        <h4 class="font-bold text-lg mb-2">Pengiriman Global</h4>
                        <p class="text-sm text-on-surface-variant">Kami mengirimkan koleksi pilihan kami ke pelanggan di seluruh dunia dengan penuh kehati-hatian dan kecepatan.</p>
                    </div>
                    <div class="p-6">
                        <span class="material-symbols-outlined text-5xl text-primary mb-4">support_agent</span>
                        <h4 class="font-bold text-lg mb-2">Dukungan Khusus</h4>
                        <p class="text-sm text-on-surface-variant">Tim pramutamu kami hadir untuk membantu Anda dengan saran gaya, pesanan, dan pertanyaan.</p>
                    </div>
                </div>
            </div>
        </main>

        <ShopFooter />
    </div>
</template>

<style scoped>
.welcome-theme {
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
