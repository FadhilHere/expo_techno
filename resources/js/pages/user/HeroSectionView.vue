<script setup lang="ts">
import { onMounted, ref } from 'vue';

// Interface for tahun data
// interface TahunExpo {
//     id: number;
//     tahun: string;
//     deskripsi: string;
// }

// Definisikan prop untuk menerima data dari komponen induk
const props = defineProps({
    tahunExpo: {
        type: Object,
        required: false,
        default: null,
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

// Data untuk tahun expo
const tahunExpoData = ref({
    tahun: new Date().getFullYear().toString(),
    deskripsi: 'SELAMAT DATANG DI WEBSITE EXPO TECHNOPRENEURSHIP.',
});

// Fungsi untuk mengatur tahun expo dari props atau default
const initTahunExpo = () => {
    // Jika props.tahunExpo ada, gunakan itu
    if (props.tahunExpo && props.tahunExpo.tahun) {
        tahunExpoData.value = {
            tahun: props.tahunExpo.tahun,
            deskripsi: props.tahunExpo.deskripsi,
        };
        // console.log('Using tahunExpo from props:', tahunExpoData.value);
    } else {
        // Fallback ke tahun sekarang
        const currentYear = new Date().getFullYear().toString();
        tahunExpoData.value = {
            tahun: currentYear,
            deskripsi: `SELAMAT DATANG DI WEBSITE EXPO TECHNOPRENEURSHIP ${currentYear}.`,
        };
        // console.log('Using default tahunExpo:', tahunExpoData.value);
    }
};

// Panggil fungsi initialization saat component dimount
onMounted(() => {
    initTahunExpo();
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
        <div class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 md:py-16 lg:px-8">
            <!-- Hero Content -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- Left Content -->
                <div>
                    <!-- Tambahkan logo SI dan HIMA di sini, di bagian atas konten kiri -->
                    <!-- <div class="mb-2 flex items-center space-x-2">
                        <img src="assets/Logo_SI.png" alt="Logo SI" class="h-25 w-auto" />
                        <img src="assets/LOGO_HIMA.png" alt="Logo HIMA" class="h-22 w-auto" />
                    </div> -->

                    <!-- Responsive title that displays differently on mobile -->
                    <div class="mb-6">
                        <!-- Mobile version -->
                        <h2 class="block text-4xl leading-tight font-bold md:hidden">
                            {{ tahunExpoData.deskripsi || 'SELAMAT DATANG DI WEBSITE EXPO TECHNOPRENEURSHIP ' + tahunExpoData.tahun + '.' }}
                        </h2>

                        <!-- Desktop version -->
                        <h2 class="hidden text-5xl leading-tight font-bold md:block">
                            {{ tahunExpoData.deskripsi || 'SELAMAT DATANG DI WEBSITE EXPO TECHNOPRENEURSHIP ' + tahunExpoData.tahun + '.' }}
                        </h2>
                    </div>

                    <p class="mb-8 text-gray-500">
                        TI FEST merupakan kegiatan yang ditaja oleh Jurusan Teknologi Informasi Politeknik Caltex Riau, dengan tujuan untuk menjadi
                        wadah ekspresi, kreativitas, dan bakat mahasiswa dalam bidang non-akademik. Kegiatan ini juga diharapkan dapat mempererat
                        hubungan antar mahasiswa dari tiga program studi di bawah naungan Jurusan Teknologi Informasi, yaitu Teknik Informatika,
                        Sistem Informasi, dan Teknik Rekayasa Komputer.
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
                </div>

                <!-- Right Content - Cards -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Mobile view cards -->
                    <div class="w-full space-y-6 md:hidden">
                        <!-- Card 1 - Main card for mobile -->
                        <div
                            class="h-64 w-full overflow-hidden rounded-xl bg-cover bg-center"
                            style="background-image: url('/assets/expotenant2.jpg')"
                        >
                            <div class="flex h-full flex-col justify-end bg-gradient-to-t from-black/70 to-transparent p-6">
                                <span class="text-sm text-white/80">Mendukung ekonomi lokal</span>
                                <h3 class="text-2xl font-semibold text-white">Puluhan UMKM berpartisipasi</h3>
                            </div>
                        </div>

                        <!-- Card 2 for mobile -->
                        <div
                            class="h-64 w-full overflow-hidden rounded-xl bg-cover bg-center"
                            style="background-image: url('/assets/expotenant3.jpg')"
                        >
                            <div class="flex h-full flex-col justify-end bg-gradient-to-t from-black/70 to-transparent p-6">
                                <span class="text-sm text-white/80">Pemberdayaan pelaku usaha</span>
                                <h3 class="text-2xl font-semibold text-white">Pertumbuhan UMKM nasional</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop view cards -->
                    <div
                        class="col-span-2 hidden overflow-hidden rounded-3xl bg-cover bg-center md:block"
                        style="background-image: url('/assets/expotenant2.jpg')"
                    >
                        <div class="flex h-full flex-col justify-end bg-gradient-to-t from-black/70 to-transparent p-6">
                            <span class="text-sm text-white/80">Mendukung ekonomi lokal</span>
                            <h3 class="text-3xl font-semibold text-white">Puluhan UMKM berpartisipasi</h3>
                        </div>
                    </div>

                    <div
                        class="hidden overflow-hidden rounded-3xl bg-cover bg-center md:block"
                        style="background-image: url('/assets/expotenant1.jpg')"
                    >
                        <div class="flex h-full flex-col justify-end bg-gradient-to-t from-black/70 to-transparent p-6">
                            <span class="text-sm text-white/80">Produk berkualitas tinggi</span>
                            <h3 class="text-2xl font-semibold text-white">Karya terbaik lokal</h3>
                        </div>
                    </div>

                    <div
                        class="hidden overflow-hidden rounded-3xl bg-cover bg-center md:block"
                        style="background-image: url('/assets/expotenant3.jpg')"
                    >
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
