<script setup lang="ts">
import Alert from '@/components/Alert.vue';
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
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios'; // Import axios untuk request AJAX
import { FileText, Search, Trash2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

// Definisi breadcrumb
const breadcrumbItems = computed(() => {
    return [
        { label: 'Menu', href: '/' },
        { label: 'Pre-Order', href: null },
    ];
});

interface PreOrderItem {
    id: number;
    produk_id: number;
    nama_produk: string;
    harga_satuan: number;
    qty: number;
    subtotal: number;
}

interface PreOrder {
    id: number;
    tenant_id: number;
    tenant_name: string;
    nama_pemesan: string;
    nomor_wa: string;
    status_pesanan: string;
    catatan_tambahan: string | null;
    total: number;
    item_count: number;
    created_at: string;
    items?: PreOrderItem[]; // Optional for list view, will be loaded for detail view
}

const props = defineProps<{
    preOrders: PreOrder[];
}>();

// State for pagination and search
const currentPage = ref(1);
const itemsPerPage = ref(10);
const searchQuery = ref('');
const selectedStatus = ref<string>('');
const deleteId = ref<number | null>(null);
const showDeleteDialog = ref(false);
const isDeleting = ref(false);
const alert = ref({
    show: false,
    type: 'success' as 'success' | 'error',
    message: '',
});

// State for detail modal
const selectedOrder = ref<PreOrder | null>(null);
const showDetailModal = ref(false);
const selectedDetailStatus = ref<string>('');
const isUpdating = ref(false);

// Status options for filter and update
const statusOptions = [
    { value: 'pending', label: 'Pending' },
    { value: 'confirmed', label: 'Confirmed' }, // nilai diganti menjadi 'confirmed', label tetap
    { value: 'canceled', label: 'Canceled' }, // nilai diganti menjadi 'canceled', label tetap
];

// Get color class based on status
const getStatusColor = (status: string): string => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'confirmed': // diganti dari 'processing' menjadi 'confirmed'
            return 'bg-green-100 text-green-800'; // Warna hijau untuk Confirmed
        case 'canceled': // diganti dari 'cancelled' menjadi 'canceled'
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

// Get status text
const getStatusText = (status: string): string => {
    switch (status) {
        case 'pending':
            return 'Pending';
        case 'confirmed': // diganti dari 'processing' menjadi 'confirmed'
            return 'Confirmed'; // Text tetap 'Confirmed'
        case 'canceled': // diganti dari 'cancelled' menjadi 'canceled'
            return 'Canceled'; // Text tetap 'Canceled'
        default:
            return status;
    }
};

// Format currency
const formatCurrency = (value: number): string => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

// Filtered pre-orders based on search and filters
const filteredPreOrders = computed(() => {
    let filtered = [...props.preOrders];

    // Filter by search term
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (order) =>
                order.nama_pemesan.toLowerCase().includes(query) ||
                order.nomor_wa.toLowerCase().includes(query) ||
                order.tenant_name.toLowerCase().includes(query),
        );
    }

    // Filter by status - perbaikan untuk menggunakan "all" sebagai nilai untuk semua status
    if (selectedStatus.value && selectedStatus.value !== 'all') {
        filtered = filtered.filter((order) => order.status_pesanan === selectedStatus.value);
    }

    return filtered;
});

// Reset page when filters change
watch([searchQuery, selectedStatus], () => {
    currentPage.value = 1;
});

// Paginated pre-orders
const paginatedPreOrders = computed(() => {
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    const endIndex = startIndex + itemsPerPage.value;
    return filteredPreOrders.value.slice(startIndex, endIndex);
});

// Total pages
const totalPages = computed(() => Math.ceil(filteredPreOrders.value.length / itemsPerPage.value));

// Previous page
const previousPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

// Next page
const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

// Go to page
const goToPage = (page: number) => {
    currentPage.value = page;
};

// View pre-order details in modal
const viewPreOrderDetail = (order: PreOrder) => {
    selectedOrder.value = order;
    selectedDetailStatus.value = order.status_pesanan;
    showDetailModal.value = true;
};

// Show alert
const showAlert = (type: 'success' | 'error', message: string) => {
    alert.value = {
        show: true,
        type,
        message,
    };
};

// Confirmation for delete
const confirmDelete = (id: number) => {
    deleteId.value = id;
    showDeleteDialog.value = true;
};

// Delete pre-order
const handleDelete = () => {
    if (deleteId.value === null) return;

    isDeleting.value = true;

    router.delete(`/admin/pre-orders/${deleteId.value}`, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            showAlert('success', 'Pesanan berhasil dihapus');
            isDeleting.value = false;
        },
        onError: (errors) => {
            showDeleteDialog.value = false;
            showAlert('error', errors.message || 'Gagal menghapus pesanan');
            isDeleting.value = false;
            console.error('Delete errors:', errors);
        },
    });
};

// Update pre-order status - Kode perbaikan
const updateOrderStatus = () => {
    if (!selectedOrder.value || selectedDetailStatus.value === selectedOrder.value.status_pesanan) {
        return;
    }

    isUpdating.value = true;
    const orderId = selectedOrder.value.id;

    // Menambahkan headers untuk CSRF protection dan content type
    axios
        .put(
            `/admin/pre-orders/${orderId}/status`,
            {
                status_pesanan: selectedDetailStatus.value,
            },
            {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
            },
        )
        .then((response) => {
            console.log('Status update response:', response.data);

            // Update the status in the current list without refreshing
            if (selectedOrder.value) {
                const index = props.preOrders.findIndex((order) => order.id === selectedOrder.value.id);
                if (index !== -1) {
                    props.preOrders[index].status_pesanan = selectedDetailStatus.value;
                }
                selectedOrder.value.status_pesanan = selectedDetailStatus.value;
            }

            showAlert('success', 'Status pesanan berhasil diubah');

            // Reload halaman untuk memastikan data refresh dari database
            // Menambahkan delay agar alert dapat terlihat
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        })
        .catch((error) => {
            // Menambahkan logging yang lebih detail untuk debugging
            console.error('Status update error:', error);
            console.error('Response data:', error.response ? error.response.data : 'No response data');
            console.error('Response status:', error.response ? error.response.status : 'No response status');

            showAlert(
                'error',
                'Gagal mengubah status pesanan: ' +
                    (error.response && error.response.data.message ? error.response.data.message : error.message || 'Unknown error'),
            );
        })
        .finally(() => {
            isUpdating.value = false;
        });
};

// Open WhatsApp
const openWhatsApp = (phone: string, name: string, tenant: string) => {
    const formattedPhone = phone.startsWith('0') ? `62${phone.substring(1)}` : phone;

    const message = `Halo ${name}, saya dari ${tenant} mengenai pesanan Anda...`;
    const url = `https://wa.me/${formattedPhone}?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');
};
</script>

<template>
    <AuthLayout title="Pre-Order" description="Kelola data pesanan" :breadcrumbs="breadcrumbItems">
        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Pre-Order</h1>
            </div>

            <!-- Filters -->
            <div class="mb-6 grid gap-4 md:grid-cols-3">
                <div>
                    <div class="relative">
                        <Search class="absolute top-3 left-3 h-4 w-4 text-gray-400" />
                        <Input v-model="searchQuery" class="pl-10" placeholder="Cari nama pemesan, nomor, tenant..." />
                    </div>
                </div>

                <!-- Hapus filter tenant, hanya gunakan filter status -->
                <div>
                    <Select v-model="selectedStatus">
                        <SelectTrigger>
                            <SelectValue placeholder="Filter Status" />
                        </SelectTrigger>
                        <SelectContent class="z-50 overflow-auto">
                            <!-- Gunakan nilai "all" sebagai ganti nilai kosong "" -->
                            <SelectItem value="all">Semua Status</SelectItem>
                            <SelectItem v-for="status in statusOptions" :key="status.value" :value="status.value">
                                {{ status.label }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>

            <!-- Table -->
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>No</TableHead>
                        <TableHead>Tanggal</TableHead>
                        <TableHead>Tenant</TableHead>
                        <TableHead>Nama Pemesan</TableHead>
                        <TableHead>Nomor WA</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Total</TableHead>
                        <TableHead>Jumlah Item</TableHead>
                        <TableHead>Aksi</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(order, index) in paginatedPreOrders" :key="order.id">
                        <TableCell>{{ (currentPage - 1) * itemsPerPage + index + 1 }}</TableCell>
                        <TableCell>{{ order.created_at }}</TableCell>
                        <TableCell>{{ order.tenant_name }}</TableCell>
                        <TableCell>{{ order.nama_pemesan }}</TableCell>
                        <TableCell>{{ order.nomor_wa }}</TableCell>
                        <TableCell>
                            <span :class="`rounded-full px-2 py-1 text-xs font-medium ${getStatusColor(order.status_pesanan)}`">
                                {{ getStatusText(order.status_pesanan) }}
                            </span>
                        </TableCell>
                        <TableCell>{{ formatCurrency(order.total) }}</TableCell>
                        <TableCell>{{ order.item_count }}</TableCell>
                        <TableCell>
                            <div class="flex space-x-2">
                                <Button variant="outline" size="icon" title="Lihat Detail" @click="viewPreOrderDetail(order)">
                                    <FileText class="h-4 w-4" />
                                </Button>
                                <Button variant="destructive" size="icon" title="Hapus Pesanan" @click="confirmDelete(order.id)">
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="paginatedPreOrders.length === 0">
                        <TableCell colspan="9" class="py-8 text-center"> Tidak ada data pesanan yang ditemukan. </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Pagination -->
            <div class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-500">Menampilkan {{ paginatedPreOrders.length }} dari {{ filteredPreOrders.length }} data</div>
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
                        <span v-else-if="(page === 6 && currentPage <= 4) || (page === totalPages - 1 && currentPage >= totalPages - 3)" class="mx-1"
                            >...</span
                        >
                    </span>

                    <Button variant="outline" size="sm" :disabled="currentPage === totalPages || totalPages === 0" @click="nextPage">
                        Selanjutnya
                    </Button>
                </div>
            </div>

            <!-- Pre-Order Detail Modal - Ukuran diperbesar dan position-nya fixed -->
            <Dialog v-model:open="showDetailModal">
                <DialogContent class="flex max-h-[90vh] flex-col overflow-hidden sm:max-w-[900px] lg:max-w-[1100px]">
                    <DialogHeader class="flex-shrink-0">
                        <DialogTitle v-if="selectedOrder"> Detail Pesanan #{{ selectedOrder.id }} </DialogTitle>
                        <DialogDescription> Informasi detail pesanan </DialogDescription>
                    </DialogHeader>

                    <!-- Modal content with scrollable area -->
                    <div v-if="selectedOrder" class="-mr-1 overflow-y-auto py-2 pr-1">
                        <div class="grid gap-8 md:grid-cols-12">
                            <!-- Customer & Order Info -->
                            <div class="space-y-4 md:col-span-5">
                                <h3 class="text-base font-semibold">Informasi Pesanan</h3>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">ID Pesanan</h4>
                                        <p>{{ selectedOrder.id }}</p>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500">Tanggal</h4>
                                        <p>{{ selectedOrder.created_at }}</p>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Tenant</h4>
                                    <p>{{ selectedOrder.tenant_name }}</p>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Status</h4>
                                    <div class="mt-1 flex items-center">
                                        <span
                                            :class="`mr-4 rounded-full px-2 py-1 text-xs font-medium ${getStatusColor(selectedOrder.status_pesanan)}`"
                                        >
                                            {{ getStatusText(selectedOrder.status_pesanan) }}
                                        </span>

                                        <div class="flex-1">
                                            <Select v-model="selectedDetailStatus">
                                                <SelectTrigger class="w-full">
                                                    <SelectValue placeholder="Ubah Status" />
                                                </SelectTrigger>
                                                <SelectContent class="z-[100] overflow-auto">
                                                    <SelectItem v-for="status in statusOptions" :key="status.value" :value="status.value">
                                                        {{ status.label }}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </div>

                                        <Button
                                            class="ml-4 px-6"
                                            :disabled="selectedDetailStatus === selectedOrder.status_pesanan || isUpdating"
                                            @click="updateOrderStatus"
                                        >
                                            <template v-if="isUpdating">
                                                <svg
                                                    class="mr-2 -ml-1 h-4 w-4 animate-spin text-white"
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
                                                Processing...
                                            </template>
                                            <template v-else>Simpan</template>
                                        </Button>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Nama Pemesan</h4>
                                    <p>{{ selectedOrder.nama_pemesan }}</p>
                                </div>

                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Nomor WhatsApp</h4>
                                    <p class="flex items-center">
                                        {{ selectedOrder.nomor_wa }}
                                        <Button
                                            variant="outline"
                                            size="sm"
                                            class="ml-2"
                                            @click="openWhatsApp(selectedOrder.nomor_wa, selectedOrder.nama_pemesan, selectedOrder.tenant_name)"
                                        >
                                            Hubungi
                                        </Button>
                                    </p>
                                </div>

                                <div v-if="selectedOrder.catatan_tambahan">
                                    <h4 class="text-sm font-medium text-gray-500">Catatan</h4>
                                    <p class="mt-1 rounded border bg-gray-50 p-2 text-sm">{{ selectedOrder.catatan_tambahan }}</p>
                                </div>
                            </div>

                            <!-- Items & Summary -->
                            <div class="md:col-span-7">
                                <h3 class="mb-3 text-base font-semibold">Detail Item</h3>

                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>No</TableHead>
                                            <TableHead>Nama Produk</TableHead>
                                            <TableHead class="text-right">Harga</TableHead>
                                            <TableHead class="text-center">Qty</TableHead>
                                            <TableHead class="text-right">Subtotal</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        <TableRow v-for="(item, index) in selectedOrder.items" :key="item.id">
                                            <TableCell>{{ index + 1 }}</TableCell>
                                            <TableCell>{{ item.nama_produk }}</TableCell>
                                            <TableCell class="text-right">{{ formatCurrency(item.harga_satuan) }}</TableCell>
                                            <TableCell class="text-center">{{ item.qty }}</TableCell>
                                            <TableCell class="text-right">{{ formatCurrency(item.subtotal) }}</TableCell>
                                        </TableRow>
                                        <TableRow v-if="!selectedOrder.items || selectedOrder.items.length === 0">
                                            <TableCell colspan="5" class="py-4 text-center"> Tidak ada item dalam pesanan ini. </TableCell>
                                        </TableRow>
                                    </TableBody>
                                </Table>

                                <div class="mt-4 flex justify-between border-t pt-4">
                                    <div class="text-sm text-gray-500">Total Item: {{ selectedOrder.items ? selectedOrder.items.length : 0 }}</div>
                                    <div class="text-xl font-bold">Total: {{ formatCurrency(selectedOrder.total) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer buttons -->
                    <div class="mt-4 flex flex-shrink-0 justify-end space-x-2 border-t border-gray-200 pt-4">
                        <Button type="button" variant="outline" @click="showDetailModal = false"> Tutup </Button>
                    </div>
                </DialogContent>
            </Dialog>

            <!-- Delete Confirmation Dialog -->
            <AlertDialog v-model:open="showDeleteDialog">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Konfirmasi Hapus</AlertDialogTitle>
                        <AlertDialogDescription>
                            Apakah Anda yakin ingin menghapus pesanan ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus data secara
                            permanen.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel @click="showDeleteDialog = false" :disabled="isDeleting">Batal</AlertDialogCancel>
                        <AlertDialogAction
                            @click="handleDelete"
                            class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                            :disabled="isDeleting"
                        >
                            <template v-if="isDeleting">
                                <svg
                                    class="mr-2 -ml-1 h-4 w-4 animate-spin text-white"
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
                                Menghapus...
                            </template>
                            <template v-else>Hapus</template>
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>

            <!-- Alert Notification -->
            <Alert v-model:show="alert.show" :type="alert.type" :message="alert.message" />
        </div>
    </AuthLayout>
</template>
