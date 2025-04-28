<template>
    <AuthLayout title="Dashboard">
        <div class="space-y-4">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <!-- Dashboard Cards -->
                <div class="rounded-lg border bg-card p-4 text-card-foreground shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium">Total Tenants</h3>
                        <Store class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <div class="mt-2">
                        <p class="text-2xl font-bold">14</p>
                        <p class="text-xs text-muted-foreground">+2 from last EXPO</p>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-4 text-card-foreground shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium">Total Products</h3>
                        <Package class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <div class="mt-2">
                        <p class="text-2xl font-bold">87</p>
                        <p class="text-xs text-muted-foreground">+15 from last EXPO</p>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-4 text-card-foreground shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium">Pre-Orders</h3>
                        <ShoppingCart class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <div class="mt-2">
                        <p class="text-2xl font-bold">32</p>
                        <p class="text-xs text-muted-foreground">+7 this week</p>
                    </div>
                </div>

                <div class="rounded-lg border bg-card p-4 text-card-foreground shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium">Categories</h3>
                        <FolderTree class="h-4 w-4 text-muted-foreground" />
                    </div>
                    <div class="mt-2">
                        <p class="text-2xl font-bold">8</p>
                        <p class="text-xs text-muted-foreground">Same as last EXPO</p>
                    </div>
                </div>
            </div>

            <!-- Recent Pre-Orders -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="flex flex-col space-y-1.5 p-6">
                    <h3 class="font-semibold leading-none tracking-tight">Recent Pre-Orders</h3>
                    <p class="text-sm text-muted-foreground">
                        Overview of the latest pre-orders from customers.
                    </p>
                </div>
                <div class="p-6 pt-0">
                    <div class="rounded-md border">
                        <div class="relative w-full overflow-auto">
                            <table class="w-full caption-bottom text-sm">
                                <thead class="[&_tr]:border-b">
                                <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Customer</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Tenant</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Status</th>
                                    <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Date</th>
                                </tr>
                                </thead>
                                <tbody class="[&_tr:last-child]:border-0">
                                <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <td class="p-4 align-middle">Budi Santoso</td>
                                    <td class="p-4 align-middle">Coffee Tech</td>
                                    <td class="p-4 align-middle"><span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-primary text-primary-foreground hover:bg-primary/80">Confirmed</span></td>
                                    <td class="p-4 align-middle">2025-04-28</td>
                                </tr>
                                <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <td class="p-4 align-middle">Siti Rahayu</td>
                                    <td class="p-4 align-middle">Sweet Bytes</td>
                                    <td class="p-4 align-middle"><span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 text-foreground border-border">Pending</span></td>
                                    <td class="p-4 align-middle">2025-04-27</td>
                                </tr>
                                <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                    <td class="p-4 align-middle">Ahmad Hidayat</td>
                                    <td class="p-4 align-middle">EcoStyle</td>
                                    <td class="p-4 align-middle"><span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-primary text-primary-foreground hover:bg-primary/80">Confirmed</span></td>
                                    <td class="p-4 align-middle">2025-04-26</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup lang="ts">
import { Store, Package, ShoppingCart, FolderTree } from 'lucide-vue-next';
import AuthLayout from '@/layouts/AuthLayout.vue';

// Define component options
defineOptions({
    name: 'Dashboard'
});
</script>
