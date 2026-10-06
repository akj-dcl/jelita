<script setup lang="ts">
import { ref, reactive, computed } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps<{
    authUser: any;
    addresses: any[];
}>();

// ─────────────────────────────────────────
// Modal state
// ─────────────────────────────────────────
const showModal = ref(false);
const isEditing = ref(false);
const editingId = ref<number | null>(null);
const isSubmitting = ref(false);
const deleteConfirmId = ref<number | null>(null);
const destinationSearch = ref('');
const destinationResults = ref<any[]>([]);
const isSearchingDest = ref(false);
let destTimeout: any = null;

const emptyForm = () => ({
    label: '',
    recipient_name: '',
    phone: '',
    address_line: '',
    kelurahan: '',
    city: '',
    province: '',
    postal_code: '',
    destination_id: '',
    destination_label: '',
    is_default: false,
});

const form = reactive(emptyForm());

const openAddModal = () => {
    isEditing.value = false;
    editingId.value = null;
    Object.assign(form, emptyForm());
    destinationSearch.value = '';
    destinationResults.value = [];
    showModal.value = true;
};

const openEditModal = (address: any) => {
    isEditing.value = true;
    editingId.value = address.id;
    Object.assign(form, {
        label: address.label || '',
        recipient_name: address.recipient_name || '',
        phone: address.phone || '',
        address_line: address.address_line || '',
        kelurahan: address.kelurahan || '',
        city: address.city || '',
        province: address.province || '',
        postal_code: address.postal_code || '',
        destination_id: address.destination_id || '',
        destination_label: address.destination_label || '',
        is_default: !!address.is_default,
    });
    destinationSearch.value = address.destination_label || '';
    destinationResults.value = [];
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    deleteConfirmId.value = null;
};

// ─────────────────────────────────────────
// RajaOngkir destination search inside modal
// ─────────────────────────────────────────
const searchDestinations = () => {
    if (destTimeout) clearTimeout(destTimeout);
    if (destinationSearch.value.length < 3) {
        destinationResults.value = [];
        return;
    }
    isSearchingDest.value = true;
    destTimeout = setTimeout(async () => {
        try {
            const res = await fetch(`/api/destinations?search=${encodeURIComponent(destinationSearch.value)}`);
            destinationResults.value = await res.json();
        } catch {}
        finally { isSearchingDest.value = false; }
    }, 450);
};

const selectDestination = (dest: any) => {
    form.destination_id = String(dest.id);  // cast to string for validation
    form.destination_label = dest.label;
    destinationSearch.value = dest.label;
    // try to extract city & province from label (format: "Kecamatan, Kota, Provinsi")
    const parts = dest.label.split(',').map((s: string) => s.trim());
    if (parts.length >= 2) form.city = parts[1] || '';
    if (parts.length >= 3) form.province = parts[2] || '';
    destinationResults.value = [];
};


// ─────────────────────────────────────────
// Submit form
// ─────────────────────────────────────────
const handleSubmit = () => {
    if (isSubmitting.value) return;
    isSubmitting.value = true;

    const payload = { ...form };

    const options = {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
        onError: (errors: any) => {
            console.error('Address validation errors:', errors);
            isSubmitting.value = false;
            // Show first error to user
            const firstError = Object.values(errors)[0] as string;
            if (firstError) alert(firstError);
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    };

    if (isEditing.value && editingId.value) {
        router.put(`/addresses/${editingId.value}`, payload, options);
    } else {
        router.post('/addresses', payload, options);
    }
};


// ─────────────────────────────────────────
// Delete & set default
// ─────────────────────────────────────────
const handleDelete = (id: number) => {
    deleteConfirmId.value = id;
};

const confirmDelete = () => {
    if (!deleteConfirmId.value) return;
    router.delete(`/addresses/${deleteConfirmId.value}`, {
        preserveScroll: true,
        onSuccess: () => { deleteConfirmId.value = null; },
    });
};

const setDefault = (id: number) => {
    router.post(`/addresses/${id}/set-default`, {}, {
        preserveScroll: true,
    });
};

const labelIcons: Record<string, string> = {
    Rumah: 'home',
    Kantor: 'apartment',
    Kos: 'bed',
    Lainnya: 'location_on',
};

const getIcon = (label: string) => labelIcons[label] || 'location_on';
</script>

<template>
    <div class="space-y-[24px] animate-fade-in">
        <!-- Header -->
        <div class="bg-surface-container-lowest rounded-2xl p-[24px] sm:p-[32px] border border-outline-variant/20 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-[12px] pb-[16px] border-b border-outline-variant/30">
                <div>
                    <h2 class="text-[24px] font-bold text-on-surface">Alamat Tersimpan</h2>
                    <p class="text-[14px] text-on-surface-variant mt-1">Kelola alamat pengiriman tersimpan Anda.</p>
                </div>
                <button
                    @click="openAddModal"
                    class="bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-[13px] hover:opacity-90 active:scale-95 transition-all flex items-center gap-2 self-start sm:self-auto shadow-md"
                >
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>Tambah Alamat</span>
                </button>
            </div>

            <!-- Empty state -->
            <div v-if="!addresses || addresses.length === 0" class="text-center py-[48px] text-on-surface-variant">
                <span class="material-symbols-outlined text-[56px] opacity-30 block mb-3">location_off</span>
                <p class="font-semibold text-[16px]">Belum ada alamat tersimpan</p>
                <p class="text-[13px] mt-1 opacity-70">Tambahkan alamat pengiriman untuk mempercepat proses checkout.</p>
                <button @click="openAddModal" class="mt-5 px-6 py-2.5 bg-primary text-white rounded-xl font-bold text-[13px] hover:opacity-90 transition-all">
                    + Tambah Alamat Pertama
                </button>
            </div>

            <!-- Address Cards -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-[20px] mt-[20px]">
                <div
                    v-for="address in addresses"
                    :key="address.id"
                    :class="[
                        'p-[22px] rounded-2xl bg-surface-container-low border-2 relative flex flex-col justify-between transition-all',
                        address.is_default ? 'border-primary shadow-sm' : 'border-outline-variant/30 hover:border-primary/40'
                    ]"
                >
                    <!-- Default Badge -->
                    <span v-if="address.is_default" class="absolute top-4 right-4 bg-primary text-white px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider">
                        Default
                    </span>

                    <div>
                        <div class="flex items-center gap-2 font-extrabold text-[16px] mb-2" :class="address.is_default ? 'text-primary' : 'text-on-surface'">
                            <span class="material-symbols-outlined text-[20px]">{{ getIcon(address.label) }}</span>
                            <span>{{ address.label || 'Alamat' }}</span>
                        </div>
                        <p class="font-bold text-[15px] text-on-surface">{{ address.recipient_name }}</p>
                        <p class="text-[13px] text-on-surface-variant mt-1 leading-relaxed">
                            {{ address.phone }}<br />
                            {{ address.address_line }}
                            <template v-if="address.kelurahan">, {{ address.kelurahan }}</template><br />
                            <template v-if="address.destination_label">{{ address.destination_label }}, </template>
                            {{ address.province }} {{ address.postal_code }}
                        </p>
                    </div>

                    <div class="flex items-center gap-4 mt-5 pt-4 border-t border-outline-variant/25 text-[13px] font-bold flex-wrap">
                        <button @click="openEditModal(address)" class="text-primary hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">edit</span> Edit
                        </button>
                        <button
                            v-if="!address.is_default"
                            @click="setDefault(address.id)"
                            class="text-on-surface-variant hover:text-primary transition-colors"
                        >
                            Set Default
                        </button>
                        <button
                            @click="handleDelete(address.id)"
                            class="text-on-surface-variant hover:text-red-500 transition-colors ml-auto"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Delete Confirmation Modal ── -->
        <div
            v-if="deleteConfirmId"
            class="modal-overlay animate-fade-in"
            @click.self="deleteConfirmId = null"
        >
            <div class="bg-white dark:bg-[#1a1617] rounded-3xl p-6 sm:p-8 w-full shadow-2xl border border-gray-200 dark:border-gray-800 text-center text-on-surface" style="max-width: 400px;">
                <div class="w-14 h-14 bg-red-100 dark:bg-red-950/50 text-red-600 dark:text-red-400 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-[32px]">delete_forever</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Alamat?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Alamat ini akan dihapus permanen dari akun Anda.</p>
                <div class="flex gap-3">
                    <button type="button" @click="deleteConfirmId = null" class="flex-1 py-3 rounded-xl border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-100 dark:hover:bg-gray-800 transition-all text-sm">
                        Batal
                    </button>
                    <button type="button" @click="confirmDelete" class="flex-1 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold transition-all text-sm shadow-md">
                        Hapus
                    </button>
                </div>
            </div>
        </div>

        <!-- ── Add/Edit Modal ── -->
        <div
            v-if="showModal"
            class="modal-overlay animate-fade-in"
            @click.self="closeModal"
        >
            <div class="modal-card">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-100 dark:border-gray-800 shrink-0">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ isEditing ? 'Edit Alamat Pengiriman' : 'Tambah Alamat Baru' }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pastikan data alamat lengkap dan akurat</p>
                    </div>
                    <button @click="closeModal" class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400 transition-colors">
                        <span class="material-symbols-outlined text-[22px]">close</span>
                    </button>
                </div>

                <!-- Modal Body -->
                <form @submit.prevent="handleSubmit" class="p-6 space-y-4 overflow-y-auto flex-grow">
                    <!-- Label -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Label Alamat</label>
                        <div class="flex gap-2 flex-wrap">
                            <button
                                v-for="lbl in ['Rumah', 'Kantor', 'Kos', 'Lainnya']"
                                :key="lbl"
                                type="button"
                                @click="form.label = lbl"
                                :class="[
                                    'px-4 py-2 rounded-xl text-xs font-bold border transition-all',
                                    form.label === lbl
                                        ? 'bg-primary text-white border-primary shadow-sm'
                                        : 'border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-primary/50 bg-gray-50 dark:bg-gray-800/50'
                                ]"
                            >
                                {{ lbl }}
                            </button>
                        </div>
                    </div>

                    <!-- Recipient Name & Phone -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Nama Penerima <span class="text-red-500">*</span></label>
                            <input v-model="form.recipient_name" type="text" required class="w-full bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Contoh: Budi Santoso" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">No. Telepon / WhatsApp <span class="text-red-500">*</span></label>
                            <input v-model="form.phone" type="tel" required class="w-full bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="081234567890" />
                        </div>
                    </div>

                    <!-- Address Line -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Alamat Lengkap <span class="text-red-500">*</span></label>
                        <input v-model="form.address_line" type="text" required class="w-full bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Nama jalan, nomor rumah, RT/RW, patokan..." />
                    </div>

                    <!-- Kelurahan -->
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Kelurahan / Desa</label>
                        <input v-model="form.kelurahan" type="text" class="w-full bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Contoh: Kelurahan Cakranegara Barat" />
                    </div>

                    <!-- RajaOngkir Destination Search -->
                    <div class="relative">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Kecamatan / Kota Tujuan <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input
                                v-model="destinationSearch"
                                @input="searchDestinations"
                                type="text"
                                placeholder="Ketik minimal 3 huruf kecamatan/kota..."
                                class="w-full bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all pr-10"
                            />
                            <div v-if="isSearchingDest" class="absolute right-3 top-3.5">
                                <span class="material-symbols-outlined animate-spin text-primary text-[20px]">refresh</span>
                            </div>
                        </div>
                        <!-- Dropdown Search Results -->
                        <div v-if="destinationResults.length > 0" class="absolute z-[100] w-full mt-1 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-xl max-h-52 overflow-y-auto">
                            <div
                                v-for="dest in destinationResults"
                                :key="dest.id"
                                @click="selectDestination(dest)"
                                class="px-4 py-3 hover:bg-primary/10 cursor-pointer text-xs text-gray-800 dark:text-gray-200 border-b border-gray-100 dark:border-gray-800 last:border-0 font-medium"
                            >
                                {{ dest.label }}
                            </div>
                        </div>
                        <p v-if="form.destination_id" class="text-xs text-primary font-semibold mt-1.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">check_circle</span>
                            {{ form.destination_label }}
                        </p>
                    </div>

                    <!-- Province & Postal Code -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Provinsi <span class="text-red-500">*</span></label>
                            <input v-model="form.province" type="text" required class="w-full bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Contoh: Nusa Tenggara Barat" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5 uppercase tracking-wider">Kode Pos</label>
                            <input v-model="form.postal_code" type="text" class="w-full bg-gray-50 dark:bg-gray-800/60 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Contoh: 83232" />
                        </div>
                    </div>

                    <!-- Is Default Checkbox -->
                    <label class="flex items-center gap-3 cursor-pointer select-none pt-2">
                        <input v-model="form.is_default" type="checkbox" class="w-4 h-4 accent-primary rounded cursor-pointer" />
                        <span class="text-sm text-gray-800 dark:text-gray-200 font-medium">Jadikan sebagai alamat pengiriman default</span>
                    </label>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <button type="button" @click="closeModal" class="flex-1 py-3 rounded-xl border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-100 dark:hover:bg-gray-800 transition-all text-sm">
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="flex-1 py-3 rounded-xl bg-primary text-white font-bold hover:opacity-90 active:scale-95 transition-all text-sm disabled:opacity-60 flex items-center justify-center gap-2 shadow-md"
                        >
                            <span v-if="isSubmitting" class="material-symbols-outlined animate-spin text-[18px]">refresh</span>
                            <span>{{ isEditing ? 'Simpan Perubahan' : 'Tambah Alamat' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    background-color: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    overflow-y: auto;
}

.modal-card {
    background-color: #ffffff;
    border-radius: 1.5rem;
    width: min(90vw, 640px);
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    border: 1px solid rgba(209, 213, 219, 1);
    color: #1b1c1c;
    flex-shrink: 0;
    margin: auto;
}

@media (prefers-color-scheme: dark) {
    .modal-card {
        background-color: #1a1617;
        border-color: rgba(75, 85, 99, 1);
    }
}

:global(.dark) .modal-card {
    background-color: #1a1617 !important;
    border-color: rgba(75, 85, 99, 1) !important;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.97); }
    to   { opacity: 1; transform: scale(1); }
}

.animate-fade-in {
    animation: fadeIn 0.2s ease-out;
}
</style>
