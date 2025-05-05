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

// Computed untuk tenant yang difilter
const filteredTenants = computed(() => {
    if (!selectedKategori.value) return props.tenants;

    return props.tenants.filter((tenant) => tenant.kategori && tenant.kategori === selectedKategori.value);
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

            <!-- Filter Category Dropdown (Optional for home) -->
            <div class="w-full md:w-auto">
                <select
                    v-model="selectedKategori"
                    class="w-full rounded-lg border px-3 py-2 focus:ring-2 focus:ring-gray-200 focus:outline-none md:w-48"
                >
                    <option value="">Semua Kategori</option>
                    <option v-for="kategori in kategoriTenants" :key="kategori.id" :value="kategori.nama_kategori">
                        {{ kategori.nama_kategori }}
                    </option>
                </select>
            </div>
        </div>

        <!-- Tenant Cards -->
        <div v-if="filteredTenants.length > 0">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <TenantCard v-for="tenant in filteredTenants" :key="tenant.id" :tenant="tenant" />
            </div>

            <!-- Link to all tenants page -->
            <!-- <div v-if="showAllLink" class="mt-8 text-center">
                <Link
                    href="/tenants"
                    class="inline-flex items-center rounded-full bg-black px-5 py-2.5 text-sm font-medium text-white transition-colors hover:bg-gray-800"
                >
                    Lihat Semua UMKM
                    <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </Link>
            </div> -->
        </div>
        <div v-else class="rounded-lg bg-gray-50 py-8 text-center">
            <div class="mb-3 text-4xl">🏪</div>
            <h3 class="mb-1 text-lg font-semibold">Tidak ada UMKM yang ditemukan</h3>
            <p class="text-muted-foreground">Coba pilih kategori lain atau lihat semua UMKM</p>
        </div>
    </div>
</template>
