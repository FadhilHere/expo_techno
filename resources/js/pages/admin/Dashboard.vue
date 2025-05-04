<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AuthLayout from '@/layouts/AuthLayout.vue';
// import { formatCurrency } from '@/lib/utils'; // Asumsi Anda memiliki utility function ini
import { Activity, DollarSign, ShoppingCart, Users } from 'lucide-vue-next';

// Props dari controller
const props = defineProps({
    recentOrders: Array,
});

// Data untuk stat cards
const stats = [
    {
        title: 'Total Revenue',
        value: '$45,231.89',
        icon: DollarSign,
        change: '+20.1%',
        trend: 'up',
    },
    {
        title: 'New Users',
        value: '2,350',
        icon: Users,
        change: '+10.5%',
        trend: 'up',
    },
    {
        title: 'Orders',
        value: '1,247',
        icon: ShoppingCart,
        change: '+12.2%',
        trend: 'up',
    },
    {
        title: 'Active Sessions',
        value: '573',
        icon: Activity,
        change: '-2.5%',
        trend: 'down',
    },
];

// Function untuk memformat status pesanan
const getStatusBadge = (status) => {
    switch (status) {
        case 'pending':
            return { variant: 'warning', label: 'Pending' };
        case 'processing':
            return { variant: 'secondary', label: 'Diproses' };
        case 'completed':
            return { variant: 'success', label: 'Selesai' };
        case 'cancelled':
            return { variant: 'destructive', label: 'Dibatalkan' };
        default:
            return { variant: 'outline', label: status };
    }
};

// Function untuk memformat tanggal
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

// Function untuk memformat currency
const formatCurrency = (value) => {
    // Pastikan nilai adalah angka
    const number = parseFloat(value);

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
                    <p class="text-muted-foreground mt-1 flex items-center text-xs">
                        <span :class="stat.trend === 'up' ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
                            {{ stat.change }}
                        </span>
                        <span class="ml-1">from last month</span>
                    </p>
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
                                <Badge :variant="getStatusBadge(order.status_pesanan).variant">
                                    {{ getStatusBadge(order.status_pesanan).label }}
                                </Badge>
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
