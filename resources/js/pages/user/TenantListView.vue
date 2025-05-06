<script setup lang="ts">
import TenantCard from '@/components/TenantCard.vue';
import { computed, ref } from 'vue';

// Define interfaces
interface Tenant {
    id: number;
    kategori?: string;
    nama_tenant: string;
    [key: string]: any;
}

interface KategoriTenant {
    id: number;
    nama_kategori: string;
}

// Props dari parent component
const props = defineProps<{
    tenants: Tenant[];
    kategoriTenants: KategoriTenant[];
    showAllLink?: boolean;
}>();

// State untuk filter (jika diperlukan di home)
const selectedKategori = ref('');
const isDropdownOpen = ref(false);

// Computed untuk tenant yang difilter
const filteredTenants = computed(() => {
    if (!selectedKategori.value) return props.tenants;

    return props.tenants.filter((tenant) => tenant.kategori && tenant.kategori === selectedKategori.value);
});

// Toggle dropdown
const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

// Close dropdown when clicking outside
const closeDropdown = () => {
    isDropdownOpen.value = false;
};

// Select kategori and close dropdown
const selectKategori = (kategori: string) => {
    selectedKategori.value = kategori;
    isDropdownOpen.value = false;
};
</script>

<template>
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
            <div>
                <h2 class="text-2xl font-bold">UMKM Terbaru</h2>
                <p class="text-muted-foreground">Temukan berbagai UMKM yang berpartisipasi di expo ini</p>
            </div>

            <!-- Filter Category Dropdown (shadcn style) -->
            <div class="relative w-full md:w-auto">
                <button
                    @click="toggleDropdown"
                    @blur="closeDropdown"
                    type="button"
                    class="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus:ring-ring flex w-full items-center justify-between rounded-md border px-3 py-2 text-sm focus:ring-2 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 md:w-48"
                >
                    <span>{{ selectedKategori || 'Semua Kategori' }}</span>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="h-4 w-4 opacity-50"
                    >
                        <path d="m6 9 6 6 6-6"></path>
                    </svg>
                </button>

                <div v-if="isDropdownOpen" class="absolute z-10 mt-1 w-full rounded-md border border-gray-200 bg-white shadow-lg">
                    <div class="max-h-60 overflow-auto py-1">
                        <button
                            @mousedown.prevent="selectKategori('')"
                            class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-100"
                            :class="{ 'bg-gray-100': selectedKategori === '' }"
                        >
                            Semua Kategori
                        </button>
                        <button
                            v-for="kategori in kategoriTenants"
                            :key="kategori.id"
                            @mousedown.prevent="selectKategori(kategori.nama_kategori)"
                            class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-100"
                            :class="{ 'bg-gray-100': selectedKategori === kategori.nama_kategori }"
                        >
                            {{ kategori.nama_kategori }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tenant Cards -->
        <div v-if="filteredTenants.length > 0">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <TenantCard v-for="tenant in filteredTenants" :key="tenant.id" :tenant="tenant" />
            </div>
        </div>
        <div v-else class="rounded-lg bg-gray-50 py-8 text-center">
            <div class="mb-3 text-4xl">🏪</div>
            <h3 class="mb-1 text-lg font-semibold">Tidak ada UMKM yang ditemukan</h3>
            <p class="text-muted-foreground">Coba pilih kategori lain atau lihat semua UMKM</p>
        </div>
    </div>
</template>
