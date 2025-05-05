<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Calendar, DollarSign, ShoppingCart, Store } from 'lucide-vue-next';
import { ref } from 'vue';
import type { LucideIcon } from 'lucide-vue-next';

// Define interfaces for type safety
interface Tenant {
    id: number;
    nama_tenant: string;
}

interface Order {
    id: number;
    created_at: string;
    tenant: Tenant;
    nama_pemesan: string;
    nomor_wa: string;
    total: number;
    status_pesanan: string;
}

interface DashboardStats {
    totalRevenue: number;
    revenueChange: number;
    activeTenants: number;
    tenantChange: number;
    totalOrders: number;
    orderChange: number;
    todayOrders: number;
    todayOrderChange: number;
}

interface StatCard {
    title: string;
    value: string | number;
    icon: LucideIcon;
    change: string;
    trend: 'up' | 'down';
}

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

// Props dari controller - dengan tambahan data statistik
const props = defineProps<{
    recentOrders?: Order[];
    dashboardStats?: DashboardStats;
}>();

// Data untuk stat cards dengan data dinamis dari controller
const stats = ref<StatCard[]>([
    {
        title: 'Total Pendapatan',
        value: formatCurrency(props.dashboardStats?.totalRevenue || 0),
        icon: DollarSign,
        change: (props.dashboardStats?.revenueChange || 0) + '%',
        trend: (props.dashboardStats?.revenueChange || 0) >= 0 ? 'up' : 'down',
    },
    {
        title: 'Total Tenant Aktif',
        value: props.dashboardStats?.activeTenants || 0,
        icon: Store,
        change: (props.dashboardStats?.tenantChange || 0) + '%',
        trend: (props.dashboardStats?.tenantChange || 0) >= 0 ? 'up' : 'down',
    },
    {
        title: 'Total Pesanan',
        value: props.dashboardStats?.totalOrders || 0,
        icon: ShoppingCart,
        change: (props.dashboardStats?.orderChange || 0) + '%',
        trend: (props.dashboardStats?.orderChange || 0) >= 0 ? 'up' : 'down',
    },
    {
        title: 'Pesanan Hari Ini',
        value: props.dashboardStats?.todayOrders || 0,
        icon: Calendar,
        change: (props.dashboardStats?.todayOrderChange || 0) + '%',
        trend: (props.dashboardStats?.todayOrderChange || 0) >= 0 ? 'up' : 'down',
    },
]);

const recentOrders = ref<Order[]>(props.recentOrders || []);

// Function untuk mendapatkan class berdasarkan status
const getStatusColor = (status: string) => {
    switch (status) {
        case 'pending':
            return 'bg-yellow-100 text-yellow-800';
        case 'confirmed':
            return 'bg-green-100 text-green-800';
        case 'canceled':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-gray-100 text-gray-800';
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
</script>

<template>
    <AuthLayout title="Dashboard" description="Selamat datang di dashboard">
        <!-- Stats grid -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card v-for="(stat, index) in stats" :key="index" class="bg-card">
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ stat.title }}</CardTitle>
                    <component :is="stat.icon" class="text-muted-foreground h-4 w-4" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stat.value }}</div>
                    <!-- <p class="text-muted-foreground mt-1 flex items-center text-xs">
                        <span :class="stat.trend === 'up' ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
                            {{ stat.change }}
                        </span>
                        <span class="ml-1">dari bulan lalu</span>
                    </p> -->
                </CardContent>
            </Card>
        </div>

        <!-- Recent Orders Table -->
        <Card class="mt-6">
            <CardHeader>
                <CardTitle>5 Order Terbaru</CardTitle>
            </CardHeader>
            <CardContent>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>No</TableHead>
                            <TableHead>Tanggal</TableHead>
                            <TableHead>Tenant</TableHead>
                            <TableHead>Pemesan</TableHead>
                            <TableHead>Whatsapp</TableHead>
                            <TableHead>Total</TableHead>
                            <TableHead>Status</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(order, index) in recentOrders" :key="order.id">
                            <TableCell>{{ index + 1 }}</TableCell>
                            <TableCell>{{ formatDate(order.created_at) }}</TableCell>
                            <TableCell>{{ order.tenant.nama_tenant }}</TableCell>
                            <TableCell>{{ order.nama_pemesan }}</TableCell>
                            <TableCell>{{ order.nomor_wa }}</TableCell>
                            <TableCell>{{ formatCurrency(order.total) }}</TableCell>
                            <TableCell>
                                <span :class="`rounded-full px-2 py-1 text-xs font-medium ${getStatusColor(order.status_pesanan)}`">
                                    {{ getStatusText(order.status_pesanan) }}
                                </span>
                            </TableCell>
                        </TableRow>
                        <!-- Tampilkan pesan jika tidak ada order -->
                        <TableRow v-if="!recentOrders || recentOrders.length === 0">
                            <TableCell colspan="7" class="py-6 text-center text-gray-500"> Belum ada order masuk </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </CardContent>
        </Card>
    </AuthLayout>
</template>
