<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Get current user data
const currentUser = computed(() => usePage().props.auth?.user);
</script>

<template>
    <AuthLayout title="Dashboard Mahasiswa" description="Selamat datang di dashboard mahasiswa">
        <div class="p-6 space-y-6">
            <!-- Welcome Card -->
            <Card>
                <CardHeader>
                    <CardTitle>Selamat Datang, {{ currentUser?.username }}</CardTitle>
                    <CardDescription>Dashboard Mahasiswa Technologia</CardDescription>
                </CardHeader>
                <CardContent>
                    <p>Gunakan dashboard ini untuk mengelola tenant dan melihat informasi penting.</p>
                </CardContent>
            </Card>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Tenant Info Card -->
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-lg">Tenant Anda</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">Informasi tenant yang terdaftar</p>
                    </CardContent>
                    <CardFooter>
                        <Button size="sm" variant="outline" class="w-full">Lihat Detail</Button>
                    </CardFooter>
                </Card>

                <!-- Pre-Order Card -->
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-lg">Pre-Order</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">Informasi pre-order produk</p>
                    </CardContent>
                    <CardFooter>
                        <Button size="sm" variant="outline" class="w-full">Lihat Pre-Order</Button>
                    </CardFooter>
                </Card>

                <!-- Status Card -->
                <Card>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-lg">Status</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-sm text-muted-foreground">Status tenant dan pesanan</p>
                    </CardContent>
                    <CardFooter>
                        <Button size="sm" variant="outline" class="w-full">Lihat Status</Button>
                    </CardFooter>
                </Card>
            </div>

            <!-- Recent Activity -->
            <Card>
                <CardHeader>
                    <CardTitle>Aktivitas Terbaru</CardTitle>
                    <CardDescription>Aktivitas dan notifikasi terbaru</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div class="border-b pb-3">
                            <h4 class="font-medium">Informasi Tenant Diperbarui</h4>
                            <p class="text-sm text-muted-foreground">Selamat datang di dashboard mahasiswa</p>
                        </div>

                        <div class="border-b pb-3">
                            <h4 class="font-medium">Expo Technopreneurship 2024</h4>
                            <p class="text-sm text-muted-foreground">Informasi jadwal dan ketentuan expo</p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AuthLayout>
</template>
