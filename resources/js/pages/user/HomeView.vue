<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import HeroSectionView from '@/pages/user/HeroSectionView.vue';
import TenantList from '@/pages/user/TenantListView.vue';

// Props dari controller
const props = defineProps({
    tenants: Array,
    kategoriTenants: Array,
    stats: Object,
});
</script>

<template>
    <UserLayout title="Home" description="Your trusted partner in technology solutions">
        <template #hero>
            <HeroSectionView />
        </template>

        <!-- Featured UMKM Section -->
        <div class="mt-12">
            <TenantList :tenants="tenants" :kategoriTenants="kategoriTenants" />
        </div>

        <!-- Stats Section -->
        <div class="mt-16 rounded-xl bg-gray-50 p-8">
            <div class="mb-8 text-center">
                <h2 class="mb-2 text-2xl font-bold">EXPO UMKM dalam Angka</h2>
                <p class="text-muted-foreground">Bersama kita mendukung pertumbuhan UMKM lokal</p>
            </div>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="rounded-lg bg-white p-6 text-center shadow-sm">
                    <div class="mb-2 text-4xl font-bold text-gray-900">{{ stats.totalTenants }}</div>
                    <div class="text-gray-600">Total UMKM</div>
                </div>
                <div class="rounded-lg bg-white p-6 text-center shadow-sm">
                    <div class="mb-2 text-4xl font-bold text-gray-900">{{ kategoriTenants.length }}</div>
                    <div class="text-gray-600">Kategori Produk</div>
                </div>
                <div class="rounded-lg bg-white p-6 text-center shadow-sm">
                    <div class="mb-2 text-4xl font-bold text-gray-900">1000+</div>
                    <div class="text-gray-600">Pengunjung</div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
