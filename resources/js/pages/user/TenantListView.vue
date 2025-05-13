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

// State untuk filter dan dropdown
const selectedKategori = ref('');
const isOpen = ref(false);

// Computed untuk tenant yang difilter
const filteredTenants = computed(() => {
    if (!selectedKategori.value) return props.tenants;

    return props.tenants.filter((tenant) => tenant.kategori && tenant.kategori === selectedKategori.value);
});

// Method untuk menangani pemilihan kategori
const selectKategori = (kategori: string) => {
    selectedKategori.value = kategori;
    isOpen.value = false;
};

// Method untuk toggle dropdown
const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

// Method untuk mendapatkan label yang ditampilkan
const displayLabel = computed(() => {
    if (!selectedKategori.value) return 'Semua Kategori';
    return selectedKategori.value;
});
</script>

<template>
    <div class="space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
            <div>
                <h2 class="text-2xl font-bold">UMKM Terbaru</h2>
                <p class="text-muted-foreground">Temukan berbagai UMKM yang berpartisipasi di expo ini</p>
            </div>

            <!-- shadcn-style Dropdown -->
            <div class="relative w-full md:w-auto">
                <button
                    @click="toggleDropdown"
                    class="flex w-full items-center justify-between rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-900 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-gray-200 focus:outline-none md:w-48"
                >
                    <span>{{ displayLabel }}</span>
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        :class="{ 'rotate-180 transform': isOpen }"
                    >
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div v-if="isOpen" class="absolute z-10 mt-1 w-full rounded-md border border-gray-200 bg-white py-1 shadow-lg md:w-48">
                    <div
                        @click="selectKategori('')"
                        class="block cursor-pointer px-4 py-2 text-sm hover:bg-gray-100"
                        :class="{ 'bg-gray-100 font-medium': selectedKategori === '' }"
                    >
                        Semua Kategori
                    </div>
                    <div
                        v-for="kategori in kategoriTenants"
                        :key="kategori.id"
                        @click="selectKategori(kategori.nama_kategori)"
                        class="block cursor-pointer px-4 py-2 text-sm hover:bg-gray-100"
                        :class="{ 'bg-gray-100 font-medium': selectedKategori === kategori.nama_kategori }"
                    >
                        {{ kategori.nama_kategori }}
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
