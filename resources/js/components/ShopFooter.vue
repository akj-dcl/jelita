<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <!-- Footer -->
    <footer class="bg-surface-container-highest mt-8 border-t border-outline-variant/30">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-gutter px-margin-mobile md:px-margin-desktop py-8 max-w-[1440px] mx-auto">
            <div class="space-y-4 md:col-span-2">
                <div class="text-2xl font-bold text-primary flex items-center gap-1.5">
                    <span>Jelita Saung</span>
                    <span class="bg-primary text-white px-2 py-0.5 rounded text-base font-bold tracking-normal shadow-sm">ONE</span>
                </div>
                <p class="text-on-surface-variant leading-relaxed">
                    Memberdayakan melalui kerajinan. Setiap pembelian mendukung komunitas perajin perempuan kami.
                </p>
                <div class="flex gap-4">
                    <a class="text-on-surface-variant hover:text-primary transition-all" href="#"><span class="material-symbols-outlined">public</span></a>
                    <a class="text-on-surface-variant hover:text-primary transition-all" href="#"><span class="material-symbols-outlined">alternate_email</span></a>
                </div>
            </div>
            <div class="space-y-4">
                <h4 class="font-label-md text-primary font-bold uppercase tracking-wider">Tautan Cepat</h4>
                <ul class="space-y-2">
                    <li><Link :href="'/shop'" class="text-on-surface-variant hover:text-primary underline-offset-4 hover:underline transition-all">Semua Produk</Link></li>
                    <li><a class="text-on-surface-variant hover:text-primary underline-offset-4 hover:underline transition-all" href="#">Cerita Kami</a></li>
                    <li><a class="text-on-surface-variant hover:text-primary underline-offset-4 hover:underline transition-all" href="#">Produk Terbaru</a></li>
                    <li><a class="text-on-surface-variant hover:text-primary underline-offset-4 hover:underline transition-all" href="#">Grosir</a></li>
                </ul>
            </div>
            <div class="space-y-4">
                <h4 class="font-label-md text-primary font-bold uppercase tracking-wider">Informasi</h4>
                <ul class="space-y-2">
                    <li><a class="text-on-surface-variant hover:text-primary underline-offset-4 hover:underline transition-all" href="#">Kebijakan Privasi</a></li>
                    <li><a class="text-on-surface-variant hover:text-primary underline-offset-4 hover:underline transition-all" href="#">Syarat &amp; Ketentuan</a></li>
                    <li><a class="text-on-surface-variant hover:text-primary underline-offset-4 hover:underline transition-all" href="#">Pengiriman &amp; Pengembalian</a></li>
                    <li><a class="text-on-surface-variant hover:text-primary underline-offset-4 hover:underline transition-all" href="#">Hubungi Kami</a></li>
                </ul>
            </div>
            <div class="space-y-4">
                <h4 class="font-label-md text-primary font-bold uppercase tracking-wider">Kontak</h4>
                <p class="text-on-surface-variant">
                    Rutan Perempuan Bandung, Jawa Barat<br/>
                    Indonesia
                </p>
                <p class="text-on-surface-variant">
                    hello@jelitasaung.com<br/>
                    +62 812 3456 7890
                </p>
            </div>
        </div>
        <div class="border-t border-outline-variant/30 py-6 text-center px-margin-mobile flex flex-col md:flex-row justify-between items-center gap-4 max-w-[1440px] mx-auto">
            <p class="text-label-sm text-on-surface-variant-fixed-variant">© 2024 Jelita Saung Rutan Perempuan Kelas II A Bandung. Memberdayakan Kerajinan.</p>
            <div class="flex gap-4">
                <span class="material-symbols-outlined text-on-surface-variant-fixed-variant hover:text-primary cursor-pointer">share</span>
                <span class="material-symbols-outlined text-on-surface-variant-fixed-variant hover:text-primary cursor-pointer">language</span>
            </div>
        </div>
    </footer>
</template>
