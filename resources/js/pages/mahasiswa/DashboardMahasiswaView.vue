<script setup lang="ts">
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { usePage } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';
import { Clock, DollarSign, FileText, ShoppingCart, Package, Mail, Phone } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';

// Interfaces for type safety
interface TenantInfo {
    id: number;
    nama_tenant: string;
}

interface User {
    id: number;
    username: string;
    tenant_id: number;
    tenant?: TenantInfo;
}

interface Order {
    id: number;
    created_at: string;
    nama_pemesan: string;
    nomor_wa: string;
    total: number;
    status_pesanan: string;
    type: string;
}

interface DashboardStats {
    totalRevenue: number;
    confirmedOrders: number;
    pendingOrders: number;
    preOrderCount: number;
    onSiteOrderCount: number;
}

interface StatCard {
    title: string;
    value: string | number;
    icon: LucideIcon;
    description: string;
    color: string;
}

// Props untuk data dari controller
const props = defineProps<{
    tenantStats?: DashboardStats;
    recentOrders?: Order[];
}>();

// Get current user data
const currentUser = computed(() => usePage().props.auth?.user as User);
const hasTenant = computed(() => currentUser.value?.tenant_id);
// const tenantName = computed(() => currentUser.value?.tenant?.nama_tenant || 'Tenant');

// Function untuk memformat currency
const formatCurrency = (value: number) => {
    // Pastikan nilai adalah angka
    const number = parseFloat(value.toString());

    // Cek apakah angka valid
    if (isNaN(number)) {
        return value;
    }

    // Format sebagai mata uang Indonesia (Rupiah)
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(number);
};

// Data untuk stat cards
const stats = computed<StatCard[]>(() => [
    {
        title: 'Total Pendapatan',
        value: formatCurrency(props.tenantStats?.totalRevenue || 0),
        icon: DollarSign,
        description: 'Total pendapatan dari semua pesanan',
        color: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    },
    {
        title: 'Pesanan Dikonfirmasi',
        value: props.tenantStats?.confirmedOrders || 0,
        icon: ShoppingCart,
        description: 'Total pesanan yang sudah dikonfirmasi',
        color: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    },
    {
        title: 'Pesanan Pending',
        value: props.tenantStats?.pendingOrders || 0,
        icon: Clock,
        description: 'Pesanan yang belum dikonfirmasi',
        color: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    },
    {
        title: 'Breakdown Pesanan',
        value: '', // Akan dirender secara terpisah
        icon: FileText,
        description: 'Breakdown pesanan berdasarkan tipe',
        color: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
    },
]);

// Data pesanan terbaru
const recentOrders = computed(() => props.recentOrders || []);

// Function untuk mendapatkan class berdasarkan status
const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'confirmed':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'canceled':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
    }
};

// Function untuk memformat status text
const getStatusText = (status: string) => {
    switch (status) {
        case 'pending':
            return 'Pending';
        case 'confirmed':
            return 'Confirmed';
        case 'canceled':
            return 'Canceled';
        default:
            return status;
    }
};

// Function untuk memformat tanggal
const formatDate = (dateString: string) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

// Function untuk mendapatkan label tipe order
const getOrderTypeLabel = (type: string) => {
    return type === 'pre-order' ? 'Pre-Order' : 'On-Site';
};

// Function untuk mendapatkan warna tipe order
const getOrderTypeColor = (type: string) => {
    return type === 'pre-order'
        ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300'
        : 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-300';
};
</script>

<template>
    <AuthLayout title="Dashboard Tim Mahasiswa" description="Kelola pesanan tenant anda">
        <div class="space-y-6 p-6">
            <!-- No Tenant State -->
            <div v-if="!hasTenant" class="flex flex-col items-center justify-center py-12 text-center">
                <div class="mb-4 rounded-full bg-orange-100 p-4">
                    <Package class="h-10 w-10 text-orange-600" />
                </div>
                <h2 class="mb-2 text-2xl font-bold">Selamat Datang di Dashboard Mahasiswa</h2>
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

            <!-- Dashboard Content (when tenant exists) -->
            <div v-else class="space-y-6">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Tiga card pertama -->
                    <Card v-for="(stat, index) in stats.slice(0, 3)" :key="index" class="overflow-hidden">
                        <CardHeader class="pb-2">
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-sm font-medium">{{ stat.title }}</CardTitle>
                                <div :class="`rounded-full p-2 ${stat.color.split(' ')[0]} ${stat.color.split(' ')[1]}`">
                                    <component :is="stat.icon" class="h-4 w-4" />
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="text-2xl font-bold">{{ stat.value }}</div>
                            <p class="text-muted-foreground mt-1 text-xs">{{ stat.description }}</p>
                        </CardContent>
                    </Card>

                    <!-- Card keempat (Breakdown Pesanan) -->
                    <Card class="overflow-hidden">
                        <CardHeader class="pb-2">
                            <div class="flex items-center justify-between">
                                <CardTitle class="text-sm font-medium">{{ stats[3].title }}</CardTitle>
                                <div :class="`rounded-full p-2 ${stats[3].color.split(' ')[0]} ${stats[3].color.split(' ')[1]}`">
                                    <component :is="stats[3].icon" class="h-4 w-4" />
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="flex flex-col space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">Pre-Order:</span>
                                    <span
                                        class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300"
                                    >
                                        {{ props.tenantStats?.preOrderCount || 0 }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm">On-Site:</span>
                                    <span
                                        class="rounded-full bg-teal-100 px-2 py-0.5 text-xs font-medium text-teal-800 dark:bg-teal-900 dark:text-teal-300"
                                    >
                                        {{ props.tenantStats?.onSiteOrderCount || 0 }}
                                    </span>
                                </div>
                            </div>
                            <p class="text-muted-foreground mt-2 text-xs">{{ stats[3].description }}</p>
                        </CardContent>
                    </Card>
                </div>

                <!-- Recent Orders Table -->
                <Card>
                    <CardHeader>
                        <CardTitle>5 Pesanan Terbaru</CardTitle>
                        <CardDescription>Pesanan yang baru masuk untuk tenant anda</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>No</TableHead>
                                    <TableHead>Tanggal</TableHead>
                                    <TableHead>Pemesan</TableHead>
                                    <TableHead>WhatsApp</TableHead>
                                    <TableHead>Total</TableHead>
                                    <TableHead>Tipe</TableHead>
                                    <TableHead>Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(order, index) in recentOrders" :key="order.id">
                                    <TableCell>{{ index + 1 }}</TableCell>
                                    <TableCell>{{ formatDate(order.created_at) }}</TableCell>
                                    <TableCell>{{ order.nama_pemesan }}</TableCell>
                                    <TableCell>{{ order.nomor_wa }}</TableCell>
                                    <TableCell>{{ formatCurrency(order.total) }}</TableCell>
                                    <TableCell>
                                        <span :class="`rounded-full px-2 py-1 text-xs font-medium ${getOrderTypeColor(order.type)}`">
                                            {{ getOrderTypeLabel(order.type) }}
                                        </span>
                                    </TableCell>
                                    <TableCell>
                                        <span :class="`rounded-full px-2 py-1 text-xs font-medium ${getStatusColor(order.status_pesanan)}`">
                                            {{ getStatusText(order.status_pesanan) }}
                                        </span>
                                    </TableCell>
                                </TableRow>
                                <!-- Tampilkan pesan jika tidak ada order -->
                                <TableRow v-if="!recentOrders.length">
                                    <TableCell colspan="7" class="py-6 text-center text-gray-500"> Belum ada pesanan masuk </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthLayout>
</template>
