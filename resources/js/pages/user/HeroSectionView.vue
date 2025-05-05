<script setup lang="ts">
// Definisikan prop untuk menerima data dari komponen induk
defineProps({
    tahunExpo: {
        type: Object,
        required: true,
        default: () => ({
            tahun: '2025',
            deskripsi: 'SELAMAT DATANG DI WEBSITE EXPO TECHNOPRENEURSHIP.',
        }),
    },
    stats: {
        type: Object,
        required: true,
        default: () => ({
            totalTenants: 0,
            totalOrders: 0,
        }),
    },
});

// Fungsi untuk smooth scroll ke tenant section
const scrollToTenants = (event: Event) => {
    event.preventDefault();
    const tenantSection = document.getElementById('tenant-section');

    if (tenantSection) {
        tenantSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });
    }
};
</script>

<template>
    <div class="w-full">
        <!-- Gunakan container dengan max-width tertentu dan mx-auto untuk membuat konten terpusat -->
        <div class="mx-auto max-w-screen-xl px-4 py-8 sm:py-12 md:py-16 sm:px-6 lg:px-8">
            <!-- Hero Content -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- Left Content -->
                <div>
                    <!-- Responsive title that displays differently on mobile -->
                    <div class="mb-6">
                        <!-- Mobile version (stacked lines) -->
                        <h2 class="block md:hidden text-4xl font-bold leading-tight">
                            <span class="block mb-2">EXPO</span>
                            <span class="block mb-2">Technopreneurship</span>
                            <span class="block">2024: Inovasi Digital untuk UMKM Indonesia</span>
                        </h2>

                        <!-- Desktop version (original) -->
                        <h2 class="hidden md:block text-5xl font-bold leading-tight">
                            {{ tahunExpo.deskripsi || 'SELAMAT DATANG DI WEBSITE EXPO TECHNOPRENEURSHIP ' + tahunExpo.tahun + '.' }}
                        </h2>
                    </div>

                    <p class="mb-8 text-gray-500">
                        Event ini diselenggarakan oleh Program Studi Sistem Informasi sebagai hasil dari pendampingan yang dilakukan kepada UMKM.
                    </p>
                    <a
                        href="#tenant-section"
                        @click="scrollToTenants"
                        class="inline-block rounded-full bg-black px-6 py-3 font-medium text-white transition-transform hover:scale-105 active:scale-95"
                    >
                        Check Our Tenant!
                    </a>

                    <!-- Stats -->
                    <div class="mt-12 flex space-x-16">
                        <div>
                            <h3 class="text-4xl font-bold">{{ stats.totalTenants }}</h3>
                            <p class="text-gray-500">Jumlah Tenant</p>
                        </div>
                        <div>
                            <h3 class="text-4xl font-bold">{{ stats.totalOrders }}</h3>
                            <p class="text-gray-500">Total Orders</p>
                        </div>
                    </div>

                    <!-- Trusted By -->
                    <!-- <div class="mt-12">
                        <p class="mb-4 text-sm text-gray-500">TRUSTED BY THE BEST</p>
                        <div class="flex space-x-6">
                            <span class="text-gray-700">Microsoft</span>
                            <span class="text-gray-700">Apple</span>
                            <span class="text-gray-700">The Boring Company</span>
                        </div>
                    </div> -->
                </div>

                <!-- Right Content - Cards -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Mobile view cards -->
                    <div class="md:hidden space-y-6 w-full">
                        <!-- Card 1 - Main card for mobile -->
                        <div class="w-full h-64 overflow-hidden rounded-xl bg-cover bg-center" style="background-image: url('/assets/expotenant2.jpg')">
                            <div class="flex h-full flex-col justify-end bg-gradient-to-t from-black/70 to-transparent p-6">
                                <span class="text-sm text-white/80">Mendukung ekonomi lokal</span>
                                <h3 class="text-2xl font-semibold text-white">Puluhan UMKM berpartisipasi</h3>
                            </div>
                        </div>

                        <!-- Card 2 for mobile -->
                        <div class="w-full h-64 overflow-hidden rounded-xl bg-cover bg-center" style="background-image: url('/assets/expotenant3.jpg')">
                            <div class="flex h-full flex-col justify-end bg-gradient-to-t from-black/70 to-transparent p-6">
                                <span class="text-sm text-white/80">Pemberdayaan pelaku usaha</span>
                                <h3 class="text-2xl font-semibold text-white">Pertumbuhan UMKM nasional</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop view cards -->
                    <div class="hidden md:block col-span-2 overflow-hidden rounded-3xl bg-cover bg-center" style="background-image: url('/assets/expotenant2.jpg')">
                        <div class="flex h-full flex-col justify-end bg-gradient-to-t from-black/70 to-transparent p-6">
                            <span class="text-sm text-white/80">Mendukung ekonomi lokal</span>
                            <h3 class="text-3xl font-semibold text-white">Puluhan UMKM berpartisipasi</h3>
                        </div>
                    </div>

                    <div class="hidden md:block overflow-hidden rounded-3xl bg-cover bg-center" style="background-image: url('/assets/expotenant1.jpg')">
                        <div class="flex h-full flex-col justify-end bg-gradient-to-t from-black/70 to-transparent p-6">
                            <span class="text-sm text-white/80">Produk berkualitas tinggi</span>
                            <h3 class="text-2xl font-semibold text-white">Karya terbaik lokal</h3>
                        </div>
                    </div>

                    <div class="hidden md:block overflow-hidden rounded-3xl bg-cover bg-center" style="background-image: url('/assets/expotenant3.jpg')">
                        <div class="flex h-full flex-col justify-end bg-gradient-to-t from-black/70 to-transparent p-6">
                            <span class="text-sm text-white/80">Pemberdayaan pelaku usaha</span>
                            <h3 class="text-2xl font-semibold text-white">Pertumbuhan UMKM nasional</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
