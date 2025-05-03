<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import { computed } from 'vue';

// Props dari controller
const props = defineProps({
    tenant: Object,
});

// Format harga ke format rupiah
const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};

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
</script>

<template>
    <UserLayout :title="tenant.nama_tenant" :description="tenant.deskripsi">
        <div class="space-y-10">
            <!-- Back Button -->
            <div>
                <a href="/" class="inline-flex items-center text-gray-700 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Kembali ke Home
                </a>
            </div>

            <!-- Tenant Profile Section -->
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="p-6 md:p-8">
                    <div class="flex flex-col items-start gap-6 md:flex-row">
                        <!-- Logo -->
                        <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-gray-100 md:h-32 md:w-32">
                            <img
                                :src="tenant.logo_url"
                                :alt="tenant.nama_tenant"
                                class="h-full w-full object-cover"
                                onerror="this.src='/assets/no_image.png'"
                            />
                        </div>

                        <!-- Info -->
                        <div class="flex-1">
                            <div class="mb-2 flex flex-wrap gap-2">
                                <span v-if="tenant.kategori" class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-800">
                                    {{ tenant.kategori }}
                                </span>
                                <span v-if="tenant.tahun_expo" class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-800">
                                    Expo {{ tenant.tahun_expo }}
                                </span>
                            </div>

                            <h1 class="mb-3 text-2xl font-bold md:text-3xl">{{ tenant.nama_tenant }}</h1>

                            <div class="mb-6 text-gray-600">
                                <p>{{ tenant.deskripsi }}</p>
                            </div>

                            <!-- Contact Info -->
                            <div v-if="whatsappLink" class="mt-4">
                                <a
                                    :href="whatsappLink"
                                    target="_blank"
                                    class="inline-flex items-center rounded-lg bg-black px-4 py-2 text-white transition-colors hover:bg-gray-800"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"
                                        />
                                    </svg>
                                    Hubungi via WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Section -->
            <div>
                <h2 class="mb-4 text-2xl font-bold">Produk {{ tenant.nama_tenant }}</h2>

                <div v-if="tenant.products && tenant.products.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Product Card -->
                    <div
                        v-for="product in tenant.products"
                        :key="product.id"
                        class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md"
                    >
                        <!-- Product Image -->
                        <div class="aspect-video w-full overflow-hidden bg-gray-100">
                            <img
                                :src="product.foto_url"
                                :alt="product.nama_produk"
                                class="h-full w-full object-cover"
                                onerror="this.src='/assets/no_image.png'"
                            />
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="mb-1 text-lg font-semibold">{{ product.nama_produk }}</h3>
                            <p class="mb-2 font-bold text-gray-700">{{ formatPrice(product.harga) }}</p>
                            <p class="text-sm text-gray-600">
                                {{ product.deskripsi ? product.deskripsi : 'Tidak ada deskripsi produk.' }}
                            </p>

                            <!-- CTA Button -->
                            <div v-if="whatsappLink" class="mt-4">
                                <a
                                    :href="`${whatsappLink}&text=Halo, saya tertarik dengan produk ${product.nama_produk} dari ${tenant.nama_tenant}.`"
                                    target="_blank"
                                    class="block w-full rounded-lg bg-black py-2 text-center text-sm text-white transition-colors hover:bg-gray-800"
                                >
                                    Pesan Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Products -->
                <div v-else class="rounded-xl bg-gray-50 p-8 text-center">
                    <div class="mb-4 text-5xl">📦</div>
                    <h3 class="mb-2 text-xl font-semibold">Belum ada produk</h3>
                    <p class="text-gray-600">UMKM ini belum menambahkan produk yang dijual.</p>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
