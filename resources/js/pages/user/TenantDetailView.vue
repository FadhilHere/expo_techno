<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import { useForm } from '@inertiajs/vue3';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import { computed, onMounted, ref } from 'vue';

// Define interfaces for type safety
interface Product {
    id: number;
    nama_produk: string;
    harga: number;
    deskripsi?: string;
    foto_url?: string;
}

interface OrderItem extends Product {
    quantity: number;
}

interface Tenant {
    id: number;
    nama_tenant: string;
    deskripsi?: string;
    whatsapp_tenant?: string;
    logo_url?: string;
    kategori?: string;
    tahun_expo?: string;
    products?: Product[];
}

const props = defineProps<{
    tenant: Tenant;
}>();

// Order state
const orderItems = ref<OrderItem[]>([]);
const showOrderModal = ref(false);
const orderInProgress = ref(false);
const orderSuccess = ref(false);
const orderError = ref<string | null>(null);

// Order form using Inertia form
const orderForm = useForm({
    tenant_id: props.tenant.id,
    nama_pemesan: '',
    nomor_wa: '',
    catatan_tambahan: '',
    items: [] as { id: number; quantity: number }[],
});

// Format harga ke format rupiah
const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};

// Membuat URL WhatsApp untuk menghubungi tenant
// const whatsappLink = computed(() => {
//     if (!props.tenant.whatsapp_tenant) return null;

//     let number = props.tenant.whatsapp_tenant;
//     if (number.startsWith('0')) {
//         number = '62' + number.substring(1);
//     }
//     if (!number.startsWith('62')) {
//         number = '62' + number;
//     }

//     return `https://wa.me/${number}`;
// });

// Order methods
const addToOrder = (product: Product) => {
    const existingItem = orderItems.value.find((item) => item.id === product.id);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        orderItems.value.push({
            ...product,
            quantity: 1,
        });
    }
};

const removeFromOrder = (product: Product) => {
    const existingItem = orderItems.value.find((item) => item.id === product.id);

    if (existingItem) {
        if (existingItem.quantity > 1) {
            existingItem.quantity -= 1;
        } else {
            orderItems.value = orderItems.value.filter((item) => item.id !== product.id);
        }
    }
};

const getProductQuantity = (productId: number) => {
    const item = orderItems.value.find((item) => item.id === productId);
    return item ? item.quantity : 0;
};

const totalPrice = computed(() => {
    return orderItems.value.reduce((total, item) => {
        return total + item.harga * item.quantity;
    }, 0);
});

const submitOrder = () => {
    // Prepare form data
    orderForm.items = orderItems.value.map((item) => ({
        id: item.id,
        quantity: item.quantity,
    }));

    orderInProgress.value = true;

    // Submit using Inertia
    orderForm.post('/pre-order', {
        onSuccess: () => {
            orderItems.value = [];
            orderForm.reset();
            showOrderModal.value = false;
            orderSuccess.value = true; // Set success state
        },
        onError: (errors) => {
            orderError.value = errors.message || 'Terjadi kesalahan saat memproses pesanan'; // Set error message
        },
        onFinish: () => {
            orderInProgress.value = false;
        },
    });
};

// Function untuk membatasi teks deskripsi
const limitText = (text: string, limit = 100) => {
    if (!text) return '';
    return text.length > limit ? text.substring(0, limit) + '...' : text;
};

// Setup Tippy.js untuk tooltip
onMounted(() => {
    // Setup tooltips untuk semua deskripsi produk
    setTimeout(() => {
        const descriptionRefs = document.querySelectorAll('.product-description');
        descriptionRefs.forEach((ref, index) => {
            const product = props.tenant.products?.[index];
            if (product?.deskripsi) {
                tippy(ref, {
                    content: product.deskripsi,
                    placement: 'top',
                    theme: 'light',
                    maxWidth: 350,
                    delay: [0, 200],
                    arrow: true,
                    allowHTML: true,
                    interactive: true,
                    onShow() {
                        const truncatedText = limitText(product.deskripsi || '', 100);
                        if (truncatedText === product.deskripsi) {
                            return false;
                        }
                    },
                });
            }
        });
    }, 100);
});

// Kategori badge style dengan warna yang lebih tegas untuk tampilan detail
const getCategoryStyle = computed(() => {
    if (!props.tenant.kategori) return 'bg-gray-100 text-gray-800';

    // Mapping khusus untuk beberapa kategori umum
    const specificStyles: Record<string, string> = {
        'Food & Beverage': 'bg-red-500 text-white',
        'Digital & Technology': 'bg-blue-500 text-white',
        'Beauty & Wellness': 'bg-pink-500 text-white',
        'Fashion & Accessories': 'bg-purple-500 text-white',
        'Bakery & Dessert': 'bg-yellow-500 text-white',
        Handicraft: 'bg-amber-500 text-white',
        'Home Decor': 'bg-emerald-500 text-white',
        Services: 'bg-gray-700 text-white',
    };

    // Cek apakah kategori memiliki style khusus
    const kategori = props.tenant.kategori || '';
    if (specificStyles[kategori]) {
        return specificStyles[kategori];
    }

    // Untuk kategori lain, gunakan sistem warna yang lebih beragam
    const colorStyles = [
        'bg-violet-600 text-white',
        'bg-fuchsia-600 text-white',
        'bg-rose-600 text-white',
        'bg-orange-600 text-white',
        'bg-amber-600 text-white',
        'bg-lime-600 text-white',
        'bg-emerald-600 text-white',
        'bg-teal-600 text-white',
        'bg-cyan-600 text-white',
        'bg-indigo-600 text-white',
        'bg-blue-600 text-white',
        'bg-sky-600 text-white',
        'bg-green-600 text-white',
        'bg-pink-600 text-white',
        'bg-purple-600 text-white',
    ];

    // Membuat hash dari nama kategori
    const hash = (props.tenant.kategori || '').split('').reduce((acc: number, char: string) => acc + char.charCodeAt(0), 0);

    // Memilih warna berdasarkan hash
    return colorStyles[hash % colorStyles.length];
});

// Warna aksen untuk tenant (digunakan di bagian bawah)
const accentColor = computed(() => {
    if (!props.tenant.nama_tenant) return 'bg-gray-200';

    // Array warna sesuai dengan logo
    const colors = [
        'bg-amber-400', // kuning
        'bg-red-500', // merah
        'bg-orange-400', // oranye
        'bg-violet-500', // ungu
    ];

    // Menggunakan string hash sederhana untuk memilih warna
    const hash = props.tenant.nama_tenant.split('').reduce((acc: number, char: string) => acc + char.charCodeAt(0), 0);

    return colors[hash % colors.length];
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

            <!-- Success Alert -->
            <div v-if="orderSuccess" class="rounded-lg bg-green-50 p-4 text-green-800">
                <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-semibold">Pesanan Berhasil Dibuat!</h3>
                        <div class="mt-1 text-sm">Terima kasih telah memesan. Detail pesanan telah dikirim ke WhatsApp penjual.</div>
                        <div class="mt-3">
                            <button
                                @click="orderSuccess = false"
                                class="rounded-lg bg-green-100 px-3 py-1.5 text-sm font-medium text-green-800 hover:bg-green-200"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Alert -->
            <div v-if="orderError" class="rounded-lg bg-red-50 p-4 text-red-800">
                <div class="flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-semibold">Terjadi Kesalahan</h3>
                        <div class="mt-1 text-sm">
                            {{ orderError }}
                        </div>
                        <div class="mt-3">
                            <button
                                @click="orderError = null"
                                class="rounded-lg bg-red-100 px-3 py-1.5 text-sm font-medium text-red-800 hover:bg-red-200"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tenant Profile Section -->
            <div class="relative overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <!-- Accent color bar at top -->
                <div :class="[accentColor, 'h-1 w-full']"></div>

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
                                <span v-if="tenant.kategori" :class="['rounded-full px-2.5 py-1 text-xs font-medium', getCategoryStyle]">
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
                            <!-- <div v-if="whatsappLink" class="mt-4">
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
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Section -->
            <div v-if="orderItems.length > 0" class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="p-6 md:p-8">
                    <h2 class="mb-4 text-xl font-bold">Pesanan Anda</h2>

                    <!-- Order Items -->
                    <div class="space-y-4">
                        <div
                            v-for="item in orderItems"
                            :key="item.id"
                            class="flex items-center justify-between border-b border-gray-100 pb-4 last:border-0 last:pb-0"
                        >
                            <div class="flex items-center space-x-4">
                                <img
                                    :src="item.foto_url"
                                    :alt="item.nama_produk"
                                    class="h-16 w-16 rounded-lg object-cover"
                                    onerror="this.src='/assets/no_image.png'"
                                />
                                <div>
                                    <h3 class="font-semibold">{{ item.nama_produk }}</h3>
                                    <p class="text-sm text-gray-600">{{ formatPrice(item.harga) }} × {{ item.quantity }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold">{{ formatPrice(item.harga * item.quantity) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
                        <p class="text-lg font-bold">Total</p>
                        <p class="text-xl font-bold">{{ formatPrice(totalPrice) }}</p>
                    </div>

                    <!-- Order Button -->
                    <div class="mt-6">
                        <button
                            @click="showOrderModal = true"
                            class="w-full rounded-lg bg-black px-4 py-3 text-white transition-colors hover:bg-gray-800"
                        >
                            Lanjut ke Pemesanan
                        </button>
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
                        class="flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md"
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
                        <div class="flex flex-1 flex-col p-4">
                            <h3 class="mb-1 text-lg font-semibold">{{ product.nama_produk }}</h3>
                            <p class="mb-2 font-bold text-gray-700">{{ formatPrice(product.harga) }}</p>

                            <!-- Description with Tippy Tooltip -->
                            <p class="product-description mb-4 h-12 cursor-help overflow-hidden text-sm text-gray-600">
                                {{ limitText(product.deskripsi ? product.deskripsi : 'Tidak ada deskripsi produk.') }}
                            </p>

                            <!-- Quantity Controls - Positioned at bottom -->
                            <div class="mt-auto flex items-center justify-between gap-4">
                                <div class="flex items-center">
                                    <button
                                        @click="removeFromOrder(product)"
                                        :disabled="getProductQuantity(product.id) === 0"
                                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div class="w-12 text-center font-medium">
                                        {{ getProductQuantity(product.id) }}
                                    </div>
                                    <button
                                        @click="addToOrder(product)"
                                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-700 transition-colors hover:bg-gray-50"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path
                                                fill-rule="evenodd"
                                                d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </div>
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

            <!-- Order Modal -->
            <div v-if="showOrderModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
                <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                    <h2 class="mb-4 text-xl font-bold">Form Pemesanan</h2>

                    <form @submit.prevent="submitOrder" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Pemesan</label>
                            <input
                                v-model="orderForm.nama_pemesan"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-black focus:ring-1 focus:ring-black focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                            <input
                                v-model="orderForm.nomor_wa"
                                type="tel"
                                required
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-black focus:ring-1 focus:ring-black focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                            <textarea
                                v-model="orderForm.catatan_tambahan"
                                rows="3"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-black focus:ring-1 focus:ring-black focus:outline-none"
                            ></textarea>
                        </div>

                        <div class="mt-6 flex space-x-3">
                            <button
                                type="submit"
                                class="flex-1 rounded-lg bg-black px-4 py-2 text-white transition-colors hover:bg-gray-800 disabled:opacity-50"
                                :disabled="orderInProgress"
                            >
                                <span v-if="orderInProgress">
                                    <svg
                                        class="mr-2 h-5 w-5 animate-spin text-white"
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        ></path>
                                    </svg>
                                    Memproses...
                                </span>
                                <span v-else>Pesan Sekarang</span>
                            </button>
                            <button
                                type="button"
                                @click="showOrderModal = false"
                                class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-gray-700 transition-colors hover:bg-gray-50"
                                :disabled="orderInProgress"
                            >
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
