<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    tenant: {
        type: Object,
        required: true,
    },
});

// Membuat URL WhatsApp untuk menghubungi tenant
const whatsappLink = computed(() => {
    if (!props.tenant.whatsapp_tenant) return null;

    let number = props.tenant.whatsapp_tenant;
    // Memastikan format nomor benar
    if (number.startsWith('0')) {
        number = '62' + number.substring(1);
    }
    if (!number.startsWith('62')) {
        number = '62' + number;
    }

    return `https://wa.me/${number}`;
});

// Function untuk membatasi teks deskripsi
const limitText = (text, limit = 100) => {
    if (!text) return '';
    return text.length > limit ? text.substring(0, limit) + '...' : text;
};
</script>

<template>
    <div class="group overflow-hidden rounded-xl border border-gray-200 bg-white transition-all duration-300 hover:border-gray-300 hover:shadow-md">
        <!-- Header with Logo and Category Badge -->
        <div class="relative border-b border-gray-100 p-5">
            <div class="flex items-center space-x-3">
                <div class="flex h-14 w-14 items-center justify-center overflow-hidden rounded-full border border-gray-200 bg-gray-100 shadow-sm">
                    <img
                        :src="tenant.logo_url"
                        :alt="tenant.nama_tenant"
                        class="h-full w-full object-cover"
                        onerror="this.src='/assets/no_image.png'"
                    />
                </div>
                <div>
                    <h3 class="text-base leading-tight font-bold transition-colors group-hover:text-gray-800 md:text-lg">
                        {{ tenant.nama_tenant }}
                    </h3>
                    <div class="mt-1 flex items-center gap-2">
                        <div v-if="tenant.kategori" class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">
                            {{ tenant.kategori }}
                        </div>
                        <div v-if="tenant.tahun_expo" class="inline-block rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">
                            {{ tenant.tahun_expo }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="p-5">
            <p class="mb-4 h-12 overflow-hidden text-xs text-gray-600 md:text-sm">
                {{ limitText(tenant.deskripsi || 'Tidak ada deskripsi tersedia untuk UMKM ini.') }}
            </p>

            <div class="flex items-center justify-between">
                <div class="flex items-center text-xs text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    {{ tenant.product_count || 0 }} Produk
                </div>

                <div class="flex space-x-2">
                    <!-- <a
                        v-if="whatsappLink"
                        :href="whatsappLink"
                        target="_blank"
                        class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100 text-gray-700 transition-colors hover:bg-gray-200"
                        title="Hubungi via WhatsApp"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"
                            />
                        </svg>
                    </a> -->

                    <Link
                        :href="`/tenant/${tenant.id}`"
                        class="rounded-lg bg-black px-3 py-1.5 text-xs font-medium text-white transition-colors hover:bg-gray-800"
                    >
                        Lihat Detail
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
