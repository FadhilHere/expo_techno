<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import HeroSectionView from '@/pages/user/HeroSectionView.vue';
import TenantList from '@/pages/user/TenantListView.vue';
import { usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

// Props dari controller
const props = defineProps({
    tenants: Array,
    kategoriTenants: Array,
    stats: Object,
    flash: Object, // Tambahkan prop flash
});

// Success modal state
const showSuccessModal = ref(false);
const orderSuccessData = ref(null);

// Cek flash message untuk order_success dengan cara yang lebih aman
onMounted(() => {
    const page = usePage();

    // Cek jika flash dan order_success tersedia
    if (page.props.flash && page.props.flash.order_success) {
        orderSuccessData.value = page.props.flash.order_success;
        showSuccessModal.value = true;
    } else if (props.flash && props.flash.order_success) {
        // Alternatif: cek dari props
        orderSuccessData.value = props.flash.order_success;
        showSuccessModal.value = true;
    }

    console.log('Page props:', page.props); // Untuk debugging
});

// Format items untuk ditampilkan di modal
const formatOrderItems = (items) => {
    if (!items || !items.length) return '';

    return items.map((item) => `${item.nama_produk} (${item.quantity})`).join(', ');
};

// Format harga ke format rupiah
const formatPrice = (price) => {
    if (!price) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};
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

        <!-- Stats Section dengan Card Order -->
        <div class="relative mt-16 overflow-hidden rounded-xl bg-gray-100 p-8">
            <!-- Elemen geometris dekoratif dengan warna brand -->
            <div class="absolute -top-10 -left-10 h-32 w-32 rounded-full bg-yellow-400 opacity-20"></div>
            <div class="absolute top-10 right-10 h-16 w-20 rotate-12 transform bg-red-500 opacity-15"></div>
            <div class="absolute bottom-10 left-20 h-16 w-24 rounded-full bg-purple-600 opacity-15"></div>
            <div class="absolute right-20 -bottom-10 h-28 w-28 rounded-full bg-orange-400 opacity-20"></div>

            <div class="relative z-10 mb-8 text-center">
                <h2 class="mb-2 text-2xl font-bold">EXPO UMKM dalam Angka</h2>
                <p class="text-muted-foreground">Bersama kita mendukung pertumbuhan UMKM lokal</p>
            </div>
            <div class="relative z-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- Card Total UMKM - Warna Kuning -->
                <div
                    class="relative overflow-hidden rounded-lg bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 text-center shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                >
                    <!-- Elemen geometris dalam card -->
                    <div class="absolute -top-4 -right-4 rounded-full bg-yellow-400 p-6 opacity-20"></div>
                    <div class="absolute -bottom-10 -left-10 h-24 w-24 rounded-full bg-yellow-400 opacity-10"></div>

                    <div class="mb-4 flex justify-center">
                        <div class="relative z-10 rounded-full bg-yellow-100 p-3">
                            <!-- Icon untuk UMKM -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-yellow-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                />
                            </svg>
                        </div>
                    </div>
                    <div class="relative z-10 mb-2 text-4xl font-bold text-gray-900">{{ stats.totalTenants }}</div>
                    <div class="relative z-10 text-gray-600">Total UMKM</div>
                </div>

                <!-- Card Kategori Produk - Warna Oranye/Merah -->
                <div
                    class="relative overflow-hidden rounded-lg bg-gradient-to-br from-red-50 to-orange-100 p-6 text-center shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                >
                    <!-- Elemen geometris dalam card -->
                    <div class="absolute -top-4 -right-4 h-16 w-16 rotate-45 transform bg-red-500 opacity-10"></div>
                    <div class="absolute right-0 bottom-0 h-16 w-20 skew-x-12 transform bg-orange-400 opacity-10"></div>

                    <div class="mb-4 flex justify-center">
                        <div class="relative z-10 rounded-full bg-orange-100 p-3">
                            <!-- Icon untuk Kategori -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-red-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.585l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                />
                            </svg>
                        </div>
                    </div>
                    <div class="relative z-10 mb-2 text-4xl font-bold text-gray-900">{{ kategoriTenants.length }}</div>
                    <div class="relative z-10 text-gray-600">Kategori Produk</div>
                </div>

                <!-- Card Jumlah Order - Warna Ungu (Ganti dari Pengunjung) -->
                <div
                    class="relative overflow-hidden rounded-lg bg-gradient-to-br from-purple-50 to-purple-100 p-6 text-center shadow-lg transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
                >
                    <!-- Elemen geometris dalam card -->
                    <div class="absolute -top-10 -right-10 h-24 w-24 rounded-l-full bg-purple-600 opacity-10"></div>
                    <div class="absolute -bottom-5 -left-5 h-20 w-20 rounded-tr-full bg-purple-500 opacity-10"></div>

                    <div class="mb-4 flex justify-center">
                        <div class="relative z-10 rounded-full bg-purple-100 p-3">
                            <!-- Icon untuk Order (Mengganti icon pengunjung) -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-8 w-8 text-purple-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
                                />
                            </svg>
                        </div>
                    </div>
                    <div class="relative z-10 mb-2 text-4xl font-bold text-gray-900">{{ stats.totalOrders }}</div>
                    <div class="relative z-10 text-gray-600">Total Order</div>
                </div>
            </div>
        </div>

        <!-- Order Success Modal -->
        <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                <div class="mb-4 flex justify-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-green-100 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>

                <h2 class="mb-4 text-center text-xl font-bold">Pesanan Berhasil!</h2>

                <p class="mb-3 text-center text-gray-700">
                    Terima kasih sudah berbelanja di <span class="font-semibold">{{ orderSuccessData?.tenant_name }}</span>
                </p>

                <!-- Daftar produk sebagai list -->
                <div class="mb-4 rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <h3 class="mb-2 font-medium">Detail Pesanan:</h3>
                    <ul class="space-y-2">
                        <li v-for="(item, index) in orderSuccessData?.items" :key="index" class="flex justify-between text-sm">
                            <span>{{ item.nama_produk }} x{{ item.quantity }}</span>
                            <span class="font-medium">{{ formatPrice(item.subtotal) }}</span>
                        </li>
                    </ul>

                    <!-- Garis pemisah -->
                    <div class="my-2 border-t border-gray-200"></div>

                    <!-- Total harga -->
                    <div class="flex justify-between font-medium">
                        <span>Total:</span>
                        <span>{{ formatPrice(orderSuccessData?.total) }}</span>
                    </div>
                </div>

                <!-- Catatan tambahan jika ada -->
                <div v-if="orderSuccessData?.catatan_tambahan" class="mb-5 rounded-lg bg-gray-50 p-3">
                    <p class="text-sm font-medium text-gray-700">Catatan Tambahan:</p>
                    <p class="text-sm text-gray-600">{{ orderSuccessData.catatan_tambahan }}</p>
                </div>

                <p class="mb-5 text-center text-sm text-gray-700">Selanjutnya Anda akan dihubungi via WhatsApp untuk konfirmasi pesanan.</p>

                <div class="text-center">
                    <button @click="showSuccessModal = false" class="rounded-lg bg-black px-4 py-2 text-white transition-colors hover:bg-gray-800">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
