<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import ShopNavbar from '@/components/ShopNavbar.vue';
import ShopFooter from '@/components/ShopFooter.vue';

// Import newly created components
import ProfileHome from '@/components/profile/ProfileHome.vue';
import OrderList from '@/components/profile/OrderList.vue';
import OrderDetail from '@/components/profile/OrderDetail.vue';
import SavedAddresses from '@/components/profile/SavedAddresses.vue';
import EditProfile from '@/components/profile/EditProfile.vue';

defineOptions({ layout: null });

const page = usePage();

const props = defineProps<{
    orders?: any[];
    addresses?: any[];
    authUser?: any;
    debug_info?: any;
}>();

// authUser: prefer the enriched version sent by ProfileController (includes profile_photo, phone)
// fall back to Inertia shared auth.user
const authUser = computed(() => props.authUser ?? page.props.auth?.user);

const activeTab = ref('profile');
const selectedOrder = ref<any>(null);

// Fallback image urls in case google user content links rotate/expire
const fallbackAvatar = 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=400';
const fallbackVase = 'https://images.unsplash.com/photo-1578749556568-bc2c40e68b61?auto=format&fit=crop&q=80&w=600';
const fallbackBanner = 'https://images.unsplash.com/photo-1617791160505-6f00b046a874?auto=format&fit=crop&q=80&w=1200';

const handleImageError = (e: any, fallbackUrl: string) => {
    if (e.target.src !== fallbackUrl) {
        e.target.src = fallbackUrl;
    }
};

const handleLogout = () => {
    if (authUser.value) {
        router.post('/logout');
    } else {
        router.visit('/');
    }
};

const navigateToWishlist = () => {
    router.visit('/wishlist');
};

const getIconStyle = (tab: string) => ({
    fontVariationSettings: activeTab.value === tab ? "'FILL' 1" : "'FILL' 0"
});

const orders = computed(() => props.orders || []);
const latestOrder = computed(() => orders.value.length > 0 ? orders.value[0] : null);

const statusConfig: Record<string, { label: string; dot: string; badge: string }> = {
    pending_payment: { label: 'Menunggu Pembayaran', dot: 'bg-amber-400', badge: 'bg-amber-50 text-amber-700 border-amber-100' },
    processing:     { label: 'Diproses', dot: 'bg-blue-500', badge: 'bg-blue-50 text-blue-700 border-blue-100' },
    shipped:        { label: 'Dikirim', dot: 'bg-indigo-500 animate-pulse', badge: 'bg-indigo-50 text-indigo-700 border-indigo-100' },
    delivered:      { label: 'Selesai', dot: 'bg-emerald-500', badge: 'bg-emerald-50 text-emerald-700 border-emerald-100' },
    cancelled:      { label: 'Dibatalkan', dot: 'bg-red-400', badge: 'bg-red-50 text-red-600 border-red-100' },
    refunded:       { label: 'Dikembalikan', dot: 'bg-purple-400', badge: 'bg-purple-50 text-purple-700 border-purple-100' },
};

// Timeline steps per status
const timelineSteps = [
    { key: 'pending_payment', label: 'Pesanan Dibuat' },
    { key: 'processing',      label: 'Diverifikasi' },
    { key: 'shipped',         label: 'Dikirim' },
    { key: 'delivered',       label: 'Tiba' },
];
const statusOrder = ['pending_payment', 'processing', 'shipped', 'delivered'];

const viewOrderDetail = (order: any) => {
    selectedOrder.value = order;
    activeTab.value = 'order_details';
};

const confirmOrderReceived = (orderId: number) => {
    router.post(`/orders/${orderId}/confirm-received`, {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: You could show a toast here
            if (activeTab.value === 'order_details' && selectedOrder.value) {
                // The prop will be updated from inertia, so let's update selectedOrder if needed
                const updated = orders.value.find((o: any) => o.id === orderId);
                if (updated) {
                    selectedOrder.value = updated;
                }
            }
        }
    });
};

const changeTab = (tab: string) => {
    activeTab.value = tab;
};
</script>

<template>
    <div class="profile-theme antialiased min-h-screen flex flex-col selection:bg-[#8a4853] selection:text-white">
        <Head title="Profil Pelanggan | Jelita Saung" />

        <!-- Navigation Bar -->
        <ShopNavbar />

        <!-- Main Workspace -->
        <main class="flex-grow max-w-[1440px] mx-auto w-full px-4 sm:px-8 md:px-[80px] py-5 sm:py-8 md:py-12">
            
            <!-- Mobile Compact Header & Tab Bar (lg:hidden) -->
            <div class="lg:hidden w-full mb-6">
                <!-- User mini info bar on mobile -->
                <div class="bg-surface-container-lowest rounded-2xl p-3 sm:p-4 border border-outline-variant/20 shadow-sm flex items-center justify-between gap-3 mb-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-full overflow-hidden border-2 border-primary shrink-0 bg-primary/10 flex items-center justify-center">
                            <img 
                                v-if="authUser?.profile_photo"
                                :src="authUser.profile_photo" 
                                @error="handleImageError($event, fallbackAvatar)"
                                alt="Avatar"
                                class="w-full h-full object-cover"
                            />
                            <span v-else class="font-bold text-primary text-base">
                                {{ (authUser?.name || 'C').charAt(0).toUpperCase() }}
                            </span>
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-bold text-sm text-on-surface truncate">{{ authUser?.name || 'Pelanggan' }}</h3>
                            <p class="text-[11px] text-primary font-medium truncate">{{ authUser?.email || '' }}</p>
                        </div>
                    </div>
                    <button 
                        @click="handleLogout"
                        class="p-2 rounded-xl text-error hover:bg-error/10 transition-all shrink-0 cursor-pointer active:scale-95 flex items-center gap-1 text-xs font-semibold"
                        title="Keluar"
                    >
                        <span class="material-symbols-outlined text-[18px]">logout</span>
                        <span class="hidden sm:inline">Keluar</span>
                    </button>
                </div>

                <!-- Mobile Horizontal Tabs Bar -->
                <div class="bg-surface-container-lowest rounded-2xl p-1.5 border border-outline-variant/20 shadow-sm flex items-center gap-1.5 overflow-x-auto no-scrollbar">
                    <button 
                        @click="activeTab = 'profile'"
                        class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all shrink-0 cursor-pointer active:scale-95"
                        :class="activeTab === 'profile' ? 'bg-primary-container text-white shadow-xs' : 'text-on-surface-variant hover:bg-surface-container-low'"
                    >
                        <span class="material-symbols-outlined text-[18px]">person</span>
                        <span>Profil</span>
                    </button>

                    <button 
                        @click="activeTab = 'orders'"
                        class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all shrink-0 cursor-pointer active:scale-95"
                        :class="(activeTab === 'orders' || activeTab === 'order_details') ? 'bg-primary-container text-white shadow-xs' : 'text-on-surface-variant hover:bg-surface-container-low'"
                    >
                        <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                        <span>Pesanan Saya</span>
                        <span v-if="orders.length > 0" class="ml-0.5 px-1.5 py-0.2 rounded-full text-[10px] font-bold" :class="(activeTab === 'orders' || activeTab === 'order_details') ? 'bg-white dark:bg-card text-primary' : 'bg-primary/10 text-primary'">{{ orders.length }}</span>
                    </button>

                    <button 
                        @click="activeTab = 'addresses'"
                        class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold transition-all shrink-0 cursor-pointer active:scale-95"
                        :class="activeTab === 'addresses' ? 'bg-primary-container text-white shadow-xs' : 'text-on-surface-variant hover:bg-surface-container-low'"
                    >
                        <span class="material-symbols-outlined text-[18px]">location_on</span>
                        <span>Alamat</span>
                    </button>

                    <button 
                        @click="navigateToWishlist"
                        class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold text-on-surface-variant hover:bg-surface-container-low transition-all shrink-0 cursor-pointer active:scale-95"
                    >
                        <span class="material-symbols-outlined text-[18px]">favorite</span>
                        <span>Favorit</span>
                    </button>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-6 lg:gap-8 items-start">
                
                <!-- Desktop Sidebar Navigation (hidden on mobile, sticky on desktop) -->
                <aside class="hidden lg:block w-[290px] shrink-0 sticky top-24">
                    <div class="bg-surface-container-lowest rounded-2xl p-[20px] sm:p-[24px] shadow-[0px_4px_20px_rgba(0,0,0,0.04)] dark:shadow-none border border-outline-variant/20">
                        <div class="space-y-[6px]">
                            <!-- Profile Tab Button -->
                            <button 
                                @click="activeTab = 'profile'"
                                :class="[
                                    'flex items-center gap-[12px] px-[18px] py-[14px] rounded-xl font-medium text-[14px] tracking-wide transition-all duration-200 cursor-pointer w-full text-left select-none',
                                    activeTab === 'profile' 
                                        ? 'bg-primary-container text-white active-nav-indicator shadow-sm font-semibold' 
                                        : 'text-on-surface-variant hover:bg-surface-container-low'
                                ]"
                            >
                                <span class="material-symbols-outlined text-[22px]" :style="getIconStyle('profile')">person</span>
                                <span>Profil</span>
                            </button>

                            <!-- My Orders Tab Button -->
                            <button 
                                @click="activeTab = 'orders'"
                                :class="[
                                    'flex items-center gap-[12px] px-[18px] py-[14px] rounded-xl font-medium text-[14px] tracking-wide transition-all duration-200 cursor-pointer w-full text-left select-none',
                                    (activeTab === 'orders' || activeTab === 'order_details')
                                        ? 'bg-primary-container text-white active-nav-indicator shadow-sm font-semibold' 
                                        : 'text-on-surface-variant hover:bg-surface-container-low'
                                ]"
                            >
                                <span class="material-symbols-outlined text-[22px]" :style="getIconStyle('orders')">inventory_2</span>
                                <span>Pesanan Saya</span>
                            </button>

                            <!-- Wishlist Direct Link / Action -->
                            <button 
                                @click="navigateToWishlist"
                                class="flex items-center gap-[12px] px-[18px] py-[14px] rounded-xl font-medium text-[14px] tracking-wide text-on-surface-variant hover:bg-surface-container-low transition-all duration-200 cursor-pointer w-full text-left select-none group"
                            >
                                <span class="material-symbols-outlined text-[22px] group-hover:text-primary transition-colors">favorite</span>
                                <span class="flex-grow">Favorit</span>
                                <span class="material-symbols-outlined text-[16px] opacity-60 group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                            </button>

                            <!-- Saved Addresses Tab Button -->
                            <button 
                                @click="activeTab = 'addresses'"
                                :class="[
                                    'flex items-center gap-[12px] px-[18px] py-[14px] rounded-xl font-medium text-[14px] tracking-wide transition-all duration-200 cursor-pointer w-full text-left select-none',
                                    activeTab === 'addresses' 
                                        ? 'bg-primary-container text-white active-nav-indicator shadow-sm font-semibold' 
                                        : 'text-on-surface-variant hover:bg-surface-container-low'
                                ]"
                            >
                                <span class="material-symbols-outlined text-[22px]" :style="getIconStyle('addresses')">location_on</span>
                                <span>Alamat Tersimpan</span>
                            </button>

                        </div>

                        <!-- Logout Action -->
                        <div class="mt-[32px] pt-[20px] border-t border-outline-variant/30">
                            <button 
                                @click="handleLogout"
                                class="flex items-center gap-[12px] px-[18px] py-[14px] w-full text-left font-medium text-[14px] text-[#ba1a1a] dark:text-[#ffb4ab] hover:bg-[#ba1a1a]/10 dark:hover:bg-[#ffb4ab]/15 rounded-xl transition-all duration-200 cursor-pointer select-none group"
                            >
                                <span class="material-symbols-outlined text-[22px] group-hover:-translate-x-0.5 transition-transform">logout</span>
                                <span>Keluar</span>
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- Dashboard Content Section -->
                <section class="flex-grow min-w-0 w-full">
                    
                    <ProfileHome 
                        v-if="activeTab === 'profile'" 
                        :authUser="authUser"
                        :latestOrder="latestOrder"
                        :statusConfig="statusConfig"
                        :timelineSteps="timelineSteps"
                        :fallbackAvatar="fallbackAvatar"
                        :fallbackBanner="fallbackBanner"
                        @change-tab="changeTab"
                        @handle-image-error="handleImageError"
                    />

                    <OrderList 
                        v-else-if="activeTab === 'orders'"
                        :orders="orders"
                        :statusConfig="statusConfig"
                        :timelineSteps="timelineSteps"
                        :statusOrder="statusOrder"
                        @change-tab="changeTab"
                        @view-order="viewOrderDetail"
                        @confirm-received="confirmOrderReceived"
                    />

                    <OrderDetail 
                        v-else-if="activeTab === 'order_details'"
                        :selectedOrder="selectedOrder"
                        :statusConfig="statusConfig"
                        @change-tab="changeTab"
                        @confirm-received="confirmOrderReceived"
                    />

                    <SavedAddresses 
                        v-else-if="activeTab === 'addresses'"
                        :authUser="authUser"
                        :addresses="props.addresses || []"
                    />

                    <EditProfile 
                        v-else-if="activeTab === 'edit_profile'"
                        :authUser="authUser"
                        :fallbackAvatar="fallbackAvatar"
                        @change-tab="changeTab"
                        @handle-image-error="handleImageError"
                    />

                </section>
            </div>
        </main>

        <!-- Footer -->
        <ShopFooter />
    </div>
</template>

<style scoped>
/* Icon ligature protection and explicit font-family to prevent text fallback */
.material-symbols-outlined {
    font-family: 'Material Symbols Outlined' !important;
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    font-feature-settings: 'liga' !important;
    display: inline-block;
    vertical-align: middle;
    white-space: nowrap;
    word-wrap: normal;
    direction: ltr;
    -webkit-font-smoothing: antialiased;
}

/* Active navigation vertical accent marker */
.active-nav-indicator {
    position: relative;
}
.active-nav-indicator::after {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    height: 1.6rem;
    width: 3.5px;
    background-color: var(--color-primary, #8a4853);
    border-radius: 0 4px 4px 0;
}

/* Bento card interactive hover behavior */
:deep(.bento-card) {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease;
}
:deep(.bento-card:hover) {
    transform: translateY(-3px);
    box-shadow: 0px 16px 48px rgba(0, 0, 0, 0.08);
}

:deep(.animate-fade-in) {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(4px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

</style>
