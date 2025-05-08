<script setup lang="ts">
import { router, useForm, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import tippy from 'tippy.js';
import 'tippy.js/dist/tippy.css';
import 'tippy.js/themes/light.css';

// Import required components
import Alert from '@/components/Alert.vue';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import AuthLayout from '@/layouts/AuthLayout.vue';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Mail, Package, Phone } from 'lucide-vue-next';

// Get current user data
const currentUser = computed(() => usePage().props.auth?.user);
const hasTenant = computed(() => currentUser.value?.tenant_id);

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
    subtotal: number;
}

interface Tenant {
    id: number;
    nama_tenant: string;
    deskripsi?: string;
    whatsapp_tenant?: string;
    logo_url?: string;
    products?: Product[];
}

interface OnSiteOrder {
    id: number;
    tenant_id: number;
    nama_pembeli: string;
    total_amount: number;
    tanggal_pembelian: string;
    catatan?: string;
    created_at: string;
    items: {
        id: number;
        produk_id: number;
        nama_produk: string;
        harga_satuan: number;
        qty: number;
        subtotal: number;
    }[];
}

// Extend Inertia PageProps to include flash messages
interface CustomPageProps {
    flash: {
        success?: string;
        error?: string;
    };
    [key: string]: any;
}

// Props
const props = defineProps<{
    tenants: Tenant[];
    orders: OnSiteOrder[];
}>();

// Breadcrumb definition
const breadcrumbItems = computed(() => {
    return [
        { label: 'Dashboard', href: '/mahasiswa/dashboard' },
        { label: 'Pemesanan Onsite', href: null },
    ];
});

// Select the tenant automatically if available
const selectedTenant = ref<Tenant | null>(props.tenants.length > 0 ? props.tenants[0] : null);
const orderItems = ref<OrderItem[]>([]);
const orderForm = useForm({
    tenant_id: selectedTenant.value ? selectedTenant.value.id.toString() : '',
    nama_pembeli: '',
    catatan: '',
    items: [] as { produk_id: number; nama_produk: string; harga_satuan: number; qty: number }[],
});
const isSubmitting = ref(false);
const showOrderDetails = ref(false);
const selectedOrder = ref<OnSiteOrder | null>(null);
const isDeleting = ref(false);
const showDeleteOrderDialog = ref(false);
const deleteOrderId = ref<number | null>(null);

// For datatable
const currentPage = ref(1);
const itemsPerPage = ref(10);
const searchQuery = ref('');

// Alert state
const alert = ref({
    show: false,
    type: 'success' as 'success' | 'error',
    message: '',
});

// Check initial flash messages
const page = usePage<CustomPageProps>();
if (page.props.flash?.success) {
    alert.value = {
        show: true,
        type: 'success',
        message: page.props.flash.success,
    };
} else if (page.props.flash?.error) {
    alert.value = {
        show: true,
        type: 'error',
        message: page.props.flash.error,
    };
}

// Set up event listener for success events
const successHandler = () => {
    // This is called after a successful form submission and page reload
    if (page.props.flash?.success) {
        alert.value = {
            show: true,
            type: 'success',
            message: page.props.flash.success,
        };
    }
};

onMounted(() => {
    router.on('success', successHandler);
    // Auto-select the tenant if there's only one
    if (props.tenants.length === 1) {
        selectedTenant.value = props.tenants[0];
        orderForm.tenant_id = props.tenants[0].id.toString();
    }
    initializeTippy();
});

onBeforeUnmount(() => {
    // Use type assertion to avoid TypeScript error
    (router as any).off?.('success', successHandler);
});

// Format price to IDR
const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};

// Format date
const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    }).format(date);
};

// When tenant changes, reset order items
watch(selectedTenant, () => {
    orderItems.value = [];
    if (selectedTenant.value) {
        orderForm.tenant_id = selectedTenant.value.id.toString();
    }
});

// Filter orders
const filteredOrders = computed(() => {
    let filtered = [...props.orders];

    // Filter by search term
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter((order) => {
            // Check if order's items contain the search query in product names
            const hasMatchingProduct = order.items.some((item) => item.nama_produk.toLowerCase().includes(query));

            return order.nama_pembeli.toLowerCase().includes(query) || hasMatchingProduct;
        });
    }

    return filtered.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
});

// Paginated orders
const paginatedOrders = computed(() => {
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    const endIndex = startIndex + itemsPerPage.value;
    return filteredOrders.value.slice(startIndex, endIndex);
});

// Total pages
const totalPages = computed(() => Math.ceil(filteredOrders.value.length / itemsPerPage.value));

// Reset page when filters change
watch([searchQuery], () => {
    currentPage.value = 1;
});

// Navigation functions
const previousPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const goToPage = (page: number) => {
    currentPage.value = page;
};

// Order management
const addToOrder = (product: Product) => {
    const existingItem = orderItems.value.find((item) => item.id === product.id);

    if (existingItem) {
        existingItem.quantity += 1;
        existingItem.subtotal = existingItem.quantity * existingItem.harga;
    } else {
        orderItems.value.push({
            ...product,
            quantity: 1,
            subtotal: product.harga,
        });
    }
};

const removeFromOrder = (product: Product) => {
    const existingItem = orderItems.value.find((item) => item.id === product.id);

    if (existingItem) {
        if (existingItem.quantity > 1) {
            existingItem.quantity -= 1;
            existingItem.subtotal = existingItem.quantity * existingItem.harga;
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

// Show alert
const showAlert = (type: 'success' | 'error', message: string) => {
    alert.value = {
        show: true,
        type,
        message,
    };
};

// Submit order
const submitOrder = () => {
    if (isSubmitting.value) return;
    if (!selectedTenant.value) {
        showAlert('error', 'Silahkan pilih tenant terlebih dahulu');
        return;
    }

    if (!orderForm.nama_pembeli) {
        showAlert('error', 'Nama pembeli tidak boleh kosong');
        return;
    }

    if (orderItems.value.length === 0) {
        showAlert('error', 'Pesanan tidak boleh kosong');
        return;
    }

    isSubmitting.value = true;

    // Prepare form data
    orderForm.items = orderItems.value.map((item) => ({
        produk_id: item.id,
        nama_produk: item.nama_produk,
        harga_satuan: item.harga,
        qty: item.quantity,
    }));

    // Submit using Inertia
    orderForm.post('/mahasiswa/on-site-orders', {
        onSuccess: () => {
            orderItems.value = [];
            orderForm.reset();
            // Reset tenant_id to the current tenant
            if (selectedTenant.value) {
                orderForm.tenant_id = selectedTenant.value.id.toString();
            }
            showAlert('success', 'Pesanan berhasil dibuat!');
        },
        onError: (errors) => {
            showAlert('error', errors.message || 'Terjadi kesalahan saat memproses pesanan');
        },
        onFinish: () => {
            isSubmitting.value = false;
        },
    });
};

// View order details
const viewOrderDetails = (order: OnSiteOrder) => {
    selectedOrder.value = order;
    showOrderDetails.value = true;
};

// Confirm order deletion
const confirmDeleteOrder = (id: number) => {
    deleteOrderId.value = id;
    showDeleteOrderDialog.value = true;
};

// Delete order
const deleteOrder = () => {
    if (!deleteOrderId.value) return;

    isDeleting.value = true;

    // Use Inertia to delete the order
    router.delete(`/mahasiswa/on-site-orders/${deleteOrderId.value}`, {
        onSuccess: () => {
            showDeleteOrderDialog.value = false;
            showAlert('success', 'Pesanan berhasil dihapus');
            isDeleting.value = false;
        },
        onError: (errors) => {
            showAlert('error', errors.message || 'Gagal menghapus pesanan');
            isDeleting.value = false;
        },
    });
};

// Function untuk inisialisasi Tippy
const initializeTippy = () => {
    if (!selectedTenant.value?.products) return;

    // Destroy existing tooltips first
    const existingInstances = tippy.instances;
    if (existingInstances) {
        existingInstances.forEach(instance => instance.destroy());
    }

    // Wait for DOM to update
    setTimeout(() => {
        const descriptionRefs = document.querySelectorAll('.product-description');
        descriptionRefs.forEach((ref, index) => {
            const product = selectedTenant.value?.products?.[index];
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
                });
            }
        });
    }, 100);
};

// Watch for changes in selectedTenant
watch(() => selectedTenant.value, (newValue) => {
    if (newValue) {
        initializeTippy();
    }
}, { deep: true });
</script>
<template>
    <AuthLayout title="Pemesanan Onsite" description="Kelola pemesanan onsite untuk tenant" :breadcrumbs="breadcrumbItems">
        <div class="p-6">
            <!-- No Tenant State -->
            <div v-if="!hasTenant" class="flex flex-col items-center justify-center py-12 text-center">
                <div class="mb-4 rounded-full bg-orange-100 p-4">
                    <Package class="h-10 w-10 text-orange-600" />
                </div>
                <h2 class="mb-2 text-2xl font-bold">Belum Bisa Mengelola Pesanan Onsite</h2>
                <p class="text-muted-foreground mb-6 max-w-md">
                    Kamu belum memiliki tenant yang terdaftar. Tenant akan diassign oleh admin Technologia. Silakan hubungi admin untuk informasi lebih lanjut.
                </p>
                <div class="flex flex-col items-center gap-3 sm:flex-row">
                    <Button variant="outline" class="gap-2">
                        <Mail class="h-4 w-4" />
                        <span>techno@example.com</span>
                    </Button>
                    <Button variant="outline" class="gap-2">
                        <Phone class="h-4 w-4" />
                        <span>+62 812-3456-7890</span>
                    </Button>
                </div>
            </div>

            <!-- Onsite Order Management (when tenant exists) -->
            <div v-else>
                <div class="mb-6 flex items-center justify-between">
                    <h1 class="text-2xl font-bold">Pemesanan Onsite</h1>
                </div>

                <!-- Alert Notification -->
                <Alert v-model:show="alert.show" :type="alert.type" :message="alert.message" />

                <!-- Main Content Grid - 3 Columns on larger screens -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <!-- Order Input Section - Left Column (2/3 width on large screens) -->
                    <div class="space-y-6 lg:col-span-2">
                        <!-- Display Tenant Info -->
                        <div v-if="selectedTenant" class="rounded-xl border bg-white p-6 shadow-sm">
                            <div class="flex flex-col items-start gap-6 md:flex-row">
                                <!-- Logo -->
                                <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-gray-100 md:h-32 md:w-32">
                                    <img
                                        :src="selectedTenant.logo_url"
                                        :alt="selectedTenant.nama_tenant"
                                        class="h-full w-full object-cover"
                                        onerror="this.src='/assets/no_image.png'"
                                    />
                                </div>

                                <!-- Info -->
                                <div class="flex-1">
                                    <h2 class="mb-3 text-2xl font-bold md:text-3xl">{{ selectedTenant.nama_tenant }}</h2>

                                    <div v-if="selectedTenant.deskripsi" class="mb-6 text-gray-600">
                                        <p>{{ selectedTenant.deskripsi }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- No Tenant Selected Message -->
                        <div v-if="!selectedTenant" class="rounded-xl border bg-white p-8 text-center shadow-sm">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="mx-auto mb-4 h-12 w-12 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                            <h2 class="mb-1 text-xl font-semibold">Tidak Ada Tenant</h2>
                            <p class="text-gray-600">Anda tidak terhubung dengan tenant manapun. Silakan hubungi administrator.</p>
                        </div>

                        <!-- Products Section -->
                        <div v-if="selectedTenant" class="rounded-xl border bg-white p-6 shadow-sm">
                            <h2 class="mb-4 text-lg font-semibold">Produk {{ selectedTenant.nama_tenant }}</h2>

                            <div
                                v-if="selectedTenant.products && selectedTenant.products.length > 0"
                                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4"
                            >
                                <!-- Product Card -->
                                <div
                                    v-for="product in selectedTenant.products"
                                    :key="product.id"
                                    class="group flex h-full flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition-all hover:shadow-md"
                                >
                                    <!-- Product Image -->
                                    <div class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100">
                                        <img
                                            :src="product.foto_url"
                                            :alt="product.nama_produk"
                                            class="h-full w-full object-contain"
                                            onerror="this.src='/assets/no_image.png'"
                                        />
                                    </div>

                                    <!-- Product Info -->
                                    <div class="flex flex-1 flex-col p-4">
                                        <h3 class="mb-2 text-lg font-semibold line-clamp-1">
                                            {{ product.nama_produk }}
                                        </h3>
                                        <p class="mb-3 font-bold text-gray-700">{{ formatPrice(product.harga) }}</p>

                                        <p v-if="product.deskripsi"
                                           class="product-description mb-6 text-sm text-gray-600 line-clamp-2 cursor-help"
                                           :data-tippy-content="product.deskripsi">
                                            {{ product.deskripsi }}
                                        </p>

                                        <!-- Quantity Controls -->
                                        <div class="mt-auto flex items-center justify-center space-x-6">
                                            <button
                                                @click="removeFromOrder(product)"
                                                :disabled="getProductQuantity(product.id) === 0"
                                                class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-700 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-50"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                            <div class="w-10 text-center font-medium">
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

                            <!-- No Products -->
                            <div v-else class="rounded-xl bg-gray-50 p-8 text-center">
                                <div class="mb-4 text-5xl">📦</div>
                                <h3 class="mb-2 text-xl font-semibold">Belum ada produk</h3>
                                <p class="text-gray-600">Tenant ini belum menambahkan produk yang dijual.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary and Form - Right Column (1/3 width on large screens) -->
                    <div class="space-y-6">
                        <!-- Order Form Card -->
                        <div class="sticky top-6 rounded-xl border bg-white p-6 shadow-sm">
                            <h2 class="mb-4 flex items-center text-lg font-semibold">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                    />
                                </svg>
                                Pesanan
                            </h2>

                            <!-- Order Items -->
                            <div v-if="orderItems.length > 0" class="mb-4 space-y-4">
                                <div
                                    v-for="item in orderItems"
                                    :key="item.id"
                                    class="flex items-center justify-between border-b border-gray-100 pb-4 last:border-0"
                                >
                                    <div class="flex-1">
                                        <h3 class="font-semibold">{{ item.nama_produk }}</h3>
                                        <p class="text-sm text-gray-600">{{ formatPrice(item.harga) }} × {{ item.quantity }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold">{{ formatPrice(item.harga * item.quantity) }}</p>
                                    </div>
                                </div>

                                <!-- Total -->
                                <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                    <p class="text-lg font-bold">Total</p>
                                    <p class="text-xl font-bold">{{ formatPrice(totalPrice) }}</p>
                                </div>
                            </div>

                            <!-- Empty order state -->
                            <div v-else class="mb-4 rounded-lg bg-gray-50 p-4 text-center">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="mx-auto mb-2 h-12 w-12 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                    />
                                </svg>
                                <p class="text-gray-500">Belum ada item dalam pesanan</p>
                            </div>

                            <!-- Order Form -->
                            <form @submit.prevent="submitOrder" class="mt-6 space-y-4">
                                <div>
                                    <Label for="buyer-name" class="text-sm font-medium">Nama Pembeli</Label>
                                    <Input id="buyer-name" v-model="orderForm.nama_pembeli" type="text" required placeholder="Masukkan nama pembeli" />
                                </div>

                                <div>
                                    <Label for="order-notes" class="text-sm font-medium">Catatan (Opsional)</Label>
                                    <Textarea id="order-notes" v-model="orderForm.catatan" placeholder="Tambahkan catatan untuk pesanan ini" rows="3" />
                                </div>

                                <Button
                                    type="submit"
                                    class="w-full"
                                    :disabled="orderItems.length === 0 || !selectedTenant || !orderForm.nama_pembeli || isSubmitting"
                                >
                                    <template v-if="isSubmitting">
                                        <svg class="mr-2 h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            ></path>
                                        </svg>
                                        Memproses...
                                    </template>
                                    <template v-else> Proses Pesanan </template>
                                </Button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Order History Section -->
                <div class="mt-10 rounded-xl border bg-white p-6 shadow-sm">
                    <div class="mb-6 flex flex-wrap items-center justify-between">
                        <h2 class="text-lg font-semibold">Riwayat Pemesanan Onsite</h2>
                        <div class="flex space-x-2">
                            <div class="relative">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="absolute top-3 left-3 h-4 w-4 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>
                                <Input v-model="searchQuery" class="w-64 pl-10" placeholder="Cari nama produk atau pembeli..." />
                            </div>
                        </div>
                    </div>

                    <!-- Orders Table -->
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>No</TableHead>
                                <TableHead>Tanggal</TableHead>
                                <TableHead>Nama Pembeli</TableHead>
                                <TableHead>Total</TableHead>
                                <TableHead>Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="(order, index) in paginatedOrders" :key="order.id">
                                <TableCell>{{ (currentPage - 1) * itemsPerPage + index + 1 }}</TableCell>
                                <TableCell>{{ formatDate(order.tanggal_pembelian) }}</TableCell>
                                <TableCell>{{ order.nama_pembeli }}</TableCell>
                                <TableCell>{{ formatPrice(order.total_amount) }}</TableCell>
                                <TableCell>
                                    <div class="flex space-x-2">
                                        <Button variant="outline" size="sm" @click="viewOrderDetails(order)"> Detail </Button>
                                        <Button variant="destructive" size="sm" @click="confirmDeleteOrder(order.id)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                />
                                            </svg>
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="paginatedOrders.length === 0">
                                <TableCell colspan="5" class="h-40 text-center text-gray-500"> Belum ada data pesanan onsite </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                    <!-- Pagination -->
                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-500">Menampilkan {{ paginatedOrders.length }} dari {{ filteredOrders.length }} data</div>
                        <div class="flex items-center space-x-2">
                            <Button variant="outline" size="sm" :disabled="currentPage === 1" @click="previousPage"> Sebelumnya </Button>

                            <span v-for="page in totalPages" :key="page">
                                <Button
                                    size="sm"
                                    :variant="page === currentPage ? 'default' : 'outline'"
                                    @click="goToPage(page)"
                                    class="mx-1 hidden sm:inline-flex"
                                    v-if="page <= 5 || page === totalPages || Math.abs(page - currentPage) <= 1"
                                >
                                    {{ page }}
                                </Button>
                                <span
                                    v-else-if="(page === 6 && currentPage <= 4) || (page === totalPages - 1 && currentPage >= totalPages - 3)"
                                    class="mx-1"
                                    >...</span
                                >
                            </span>

                            <Button variant="outline" size="sm" :disabled="currentPage === totalPages || totalPages === 0" @click="nextPage">
                                Selanjutnya
                            </Button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Details Dialog -->
            <Dialog v-model:open="showOrderDetails">
                <DialogContent class="max-w-3xl">
                    <DialogHeader>
                        <DialogTitle>Detail Pesanan</DialogTitle>
                        <DialogDescription> Informasi lengkap untuk pesanan ini. </DialogDescription>
                    </DialogHeader>

                    <div v-if="selectedOrder" class="space-y-6">
                        <!-- Order Info -->
                        <div class="grid gap-4 md:grid-cols-2">
                            <div class="space-y-1">
                                <Label class="text-xs text-gray-500">Nomor Pesanan</Label>
                                <p class="font-medium">#{{ selectedOrder.id }}</p>
                            </div>
                            <div class="space-y-1">
                                <Label class="text-xs text-gray-500">Tanggal</Label>
                                <p class="font-medium">{{ formatDate(selectedOrder.tanggal_pembelian) }}</p>
                            </div>
                            <div class="space-y-1">
                                <Label class="text-xs text-gray-500">Nama Pembeli</Label>
                                <p class="font-medium">{{ selectedOrder.nama_pembeli }}</p>
                            </div>
                            <div v-if="selectedOrder.catatan" class="space-y-1 md:col-span-2">
                                <Label class="text-xs text-gray-500">Catatan</Label>
                                <p class="text-sm">{{ selectedOrder.catatan }}</p>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div>
                            <h3 class="mb-2 font-medium">Detail Item</h3>
                            <div class="overflow-hidden rounded-lg border">
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Produk</TableHead>
                                            <TableHead>Harga</TableHead>
                                            <TableHead>Qty</TableHead>
                                            <TableHead>Subtotal</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="item in selectedOrder.items" :key="item.id">
                                            <TableCell>{{ item.nama_produk }}</TableCell>
                                            <TableCell>{{ formatPrice(item.harga_satuan) }}</TableCell>
                                            <TableCell>{{ item.qty }}</TableCell>
                                            <TableCell>{{ formatPrice(item.subtotal) }}</TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="flex items-center justify-between border-t pt-4">
                            <span class="font-semibold">Total Pembayaran</span>
                            <span class="text-xl font-bold">{{ formatPrice(selectedOrder.total_amount) }}</span>
                        </div>
                    </div>

                    <DialogFooter>
                        <Button variant="outline" @click="showOrderDetails = false">Tutup</Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AuthLayout>

    <!-- Delete Order Confirmation Dialog -->
    <AlertDialog v-model:open="showDeleteOrderDialog">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Konfirmasi Hapus</AlertDialogTitle>
                <AlertDialogDescription>
                    Apakah Anda yakin ingin menghapus pesanan ini? Tindakan ini tidak dapat dibatalkan.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="showDeleteOrderDialog = false">Batal</AlertDialogCancel>
                <AlertDialogAction @click="deleteOrder" class="bg-destructive text-destructive-foreground hover:bg-destructive/90">
                    Hapus
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
