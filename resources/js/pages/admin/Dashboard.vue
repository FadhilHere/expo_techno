<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    DollarSign,
    Users,
    ShoppingCart,
    Activity
} from 'lucide-vue-next';

// Data untuk stat cards
const stats = [
    {
        title: "Total Revenue",
        value: "$45,231.89",
        icon: DollarSign,
        change: "+20.1%",
        trend: "up"
    },
    {
        title: "New Users",
        value: "2,350",
        icon: Users,
        change: "+10.5%",
        trend: "up"
    },
    {
        title: "Orders",
        value: "1,247",
        icon: ShoppingCart,
        change: "+12.2%",
        trend: "up"
    },
    {
        title: "Active Sessions",
        value: "573",
        icon: Activity,
        change: "-2.5%",
        trend: "down"
    }
];
</script>

<template>
    <AuthLayout
        title="Dashboard"
        description="Selamat datang di dashboard"
    >
        <!-- Stats grid -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card v-for="(stat, index) in stats" :key="index" class="bg-card">
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium">{{ stat.title }}</CardTitle>
                    <component :is="stat.icon" class="h-4 w-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-2xl font-bold">{{ stat.value }}</div>
                    <p class="text-xs text-muted-foreground flex items-center mt-1">
            <span :class="stat.trend === 'up' ? 'text-green-500 dark:text-green-400' : 'text-red-500 dark:text-red-400'">
              {{ stat.change }}
            </span>
                        <span class="ml-1">from last month</span>
                    </p>
                </CardContent>
            </Card>
        </div>

        <!-- Simple welcome message -->
        <Card class="mt-6">
            <CardHeader>
                <CardTitle>Selamat Datang</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="flex h-64 items-center justify-center">
                    <h2 class="text-3xl font-bold">Hai!</h2>
                </div>
            </CardContent>
        </Card>
    </AuthLayout>
</template>
