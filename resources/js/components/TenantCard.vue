<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css'; // Import CSS Tippy.js
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    tenant: {
        type: Object,
        required: true,
    },
});

const descriptionRef = ref(null);

// Function untuk membatasi teks deskripsi
const limitText = (text, limit = 100) => {
    if (!text) return '';
    return text.length > limit ? text.substring(0, limit) + '...' : text;
};

// Setup Tippy.js untuk tooltip
onMounted(() => {
    if (descriptionRef.value) {
        tippy(descriptionRef.value, {
            content: props.tenant.deskripsi || 'Tidak ada deskripsi tersedia untuk UMKM ini.',
            placement: 'top',
            theme: 'light',
            maxWidth: 350,
            delay: [0, 200],
            arrow: true,
            allowHTML: true,
            interactive: true,
            // Custom styling untuk tooltip
            onShow(instance) {
                // Hanya tampilkan tooltip jika teks sudah dipotong
                const truncatedText = limitText(props.tenant.deskripsi || '', 100);
                if (truncatedText === props.tenant.deskripsi) {
                    return false; // Jangan tampilkan tooltip jika teks tidak dipotong
                }
            },
        });
    }
});

// Function untuk navigasi ke halaman detail tenant
const navigateToDetail = () => {
    window.location.href = `/tenant/${props.tenant.id}`;
};

// Mendapatkan inisial dari nama tenant untuk fallback image
const tenantInitials = computed(() => {
    if (!props.tenant.nama_tenant) return '';
    return props.tenant.nama_tenant
        .split(' ')
        .map((word) => word[0])
        .join('')
        .toUpperCase()
        .substring(0, 2);
});

// Menghasilkan warna background untuk inisial berdasarkan nama tenant
const initialsBgColor = computed(() => {
    if (!props.tenant.nama_tenant) return 'bg-gray-200';

    // Array warna sesuai dengan logo
    const colors = [
        'bg-amber-400', // kuning
        'bg-red-500', // merah
        'bg-orange-400', // oranye
        'bg-violet-500', // ungu
    ];

    // Menggunakan string hash sederhana untuk memilih warna
    const hash = props.tenant.nama_tenant.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);

    return colors[hash % colors.length];
});

// Kategori badge style dengan warna yang sangat berbeda untuk setiap kategori
const kategoriBadgeStyle = computed(() => {
    if (!props.tenant.kategori) return 'bg-gray-100 text-gray-900 border-gray-200';

    // Mapping khusus untuk beberapa kategori umum
    const specificStyles = {
        'Food & Beverage': 'bg-red-500 text-white border-red-600',
        'Digital & Technology': 'bg-blue-500 text-white border-blue-600',
        'Beauty & Wellness': 'bg-pink-500 text-white border-pink-600',
        'Fashion & Accessories': 'bg-purple-500 text-white border-purple-600',
        'Bakery & Dessert': 'bg-yellow-500 text-white border-yellow-600',
        Handicraft: 'bg-amber-500 text-white border-amber-600',
        'Home Decor': 'bg-emerald-500 text-white border-emerald-600',
        Services: 'bg-gray-700 text-white border-gray-800',
    };

    // Cek apakah kategori memiliki style khusus
    if (specificStyles[props.tenant.kategori]) {
        return specificStyles[props.tenant.kategori];
    }

    // Untuk kategori lain, gunakan sistem warna yang lebih beragam
    // Format: [bg-color, text-color, border-color]
    const colorStyles = [
        ['bg-violet-600', 'text-white', 'border-violet-700'],
        ['bg-fuchsia-600', 'text-white', 'border-fuchsia-700'],
        ['bg-rose-600', 'text-white', 'border-rose-700'],
        ['bg-orange-600', 'text-white', 'border-orange-700'],
        ['bg-amber-600', 'text-white', 'border-amber-700'],
        ['bg-lime-600', 'text-white', 'border-lime-700'],
        ['bg-emerald-600', 'text-white', 'border-emerald-700'],
        ['bg-teal-600', 'text-white', 'border-teal-700'],
        ['bg-cyan-600', 'text-white', 'border-cyan-700'],
        ['bg-indigo-600', 'text-white', 'border-indigo-700'],
        ['bg-blue-600', 'text-white', 'border-blue-700'],
        ['bg-sky-600', 'text-white', 'border-sky-700'],
        ['bg-green-600', 'text-white', 'border-green-700'],
        ['bg-pink-600', 'text-white', 'border-pink-700'],
        ['bg-purple-600', 'text-white', 'border-purple-700'],
    ];

    // Membuat hash dari nama kategori
    const hash = props.tenant.kategori.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);

    // Memilih warna berdasarkan hash
    const selectedColor = colorStyles[hash % colorStyles.length];

    // Menggabungkan class warna
    return `${selectedColor[0]} ${selectedColor[1]} ${selectedColor[2]}`;
});
</script>

<template>
    <!-- Wrapper dengan cursor-pointer untuk menunjukkan card dapat diklik -->
    <div
        class="group relative cursor-pointer overflow-hidden rounded-lg border border-gray-200 bg-white transition-all duration-200 hover:border-gray-900/10 hover:shadow-md"
        @click="navigateToDetail"
    >
        <!-- Header with Logo and Category Badge -->
        <div class="relative p-5">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div
                        class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-full border border-gray-200 bg-gray-50 shadow-sm transition-all duration-200 group-hover:border-gray-300"
                    >
                        <img
                            v-if="tenant.logo_url && tenant.logo_url !== '/assets/no_image.png'"
                            :src="tenant.logo_url"
                            :alt="tenant.nama_tenant"
                            class="h-full w-full object-cover"
                            onerror="this.style.display='none'"
                        />
                        <div v-else :class="['flex h-full w-full items-center justify-center text-xl font-bold text-white', initialsBgColor]">
                            {{ tenantInitials }}
                        </div>
                    </div>
                </div>

                <div class="flex-1">
                    <h3 class="text-base leading-tight font-semibold text-gray-900 md:text-lg">
                        {{ tenant.nama_tenant }}
                    </h3>

                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <div v-if="tenant.kategori" :class="['inline-block rounded-md border px-2 py-0.5 text-xs font-medium', kategoriBadgeStyle]">
                            {{ tenant.kategori }}
                        </div>
                        <div
                            v-if="tenant.tahun_expo"
                            class="inline-block rounded-md border border-gray-200 bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-800"
                        >
                            {{ tenant.tahun_expo }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="px-5">
            <div class="h-px w-full bg-gray-100"></div>
        </div>

        <!-- Content Area -->
        <div class="p-5">
            <!-- Deskripsi dengan Tippy.js tooltip -->
            <p ref="descriptionRef" class="mb-4 h-12 cursor-help overflow-hidden text-xs leading-relaxed text-gray-600 md:text-sm" @click.stop>
                {{ limitText(tenant.deskripsi || 'Tidak ada deskripsi tersedia untuk UMKM ini.') }}
            </p>

            <div class="flex items-center justify-between">
                <div class="flex items-center rounded-md border border-gray-200 bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1.5 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    {{ tenant.product_count || 0 }} Produk
                </div>

                <Link
                    :href="`/tenant/${tenant.id}`"
                    class="rounded-md bg-black px-4 py-1.5 text-xs font-medium text-white transition-colors hover:bg-gray-800"
                    @click.stop
                >
                    Lihat Produk
                </Link>
            </div>
        </div>

        <!-- Accent line at the bottom - mengambil warna dari logo -->
        <div :class="[initialsBgColor, 'h-1 w-full']"></div>
    </div>
</template>
