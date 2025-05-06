<script setup lang="ts">
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Plus, Package, ShoppingCart } from 'lucide-vue-next';

// Get current user data - we'll use this in future implementation
const user = computed(() => usePage().props.auth?.user);

// You would typically fetch tenant data from props passed by the controller
const props = defineProps({
    tenant: {
        type: Object,
        default: () => ({
            id: null,
            nama_tenant: '',
            deskripsi: '',
            whatsapp_tenant: '',
            logo: null,
            products: []
        })
    }
});
</script>

<template>
    <AuthLayout title="Tenant Mahasiswa" description="Kelola informasi tenant dan produk Anda">
        <div class="p-6 space-y-6">
            <!-- Conditional welcome message using the user variable -->
            <div v-if="user" class="text-sm text-muted-foreground mb-4">
                Logged in as: {{ user.username }}
            </div>

            <Tabs defaultValue="info" class="w-full">
                <TabsList class="grid w-full grid-cols-3">
                    <TabsTrigger value="info">Informasi Tenant</TabsTrigger>
                    <TabsTrigger value="products">Produk</TabsTrigger>
                    <TabsTrigger value="orders">Pre-Order</TabsTrigger>
                </TabsList>

                <!-- Tenant Information Tab -->
                <TabsContent value="info">
                    <Card>
                        <CardHeader>
                            <CardTitle>Informasi Tenant</CardTitle>
                            <CardDescription>
                                Kelola informasi tenant Anda seperti nama, deskripsi, dan kontak
                            </CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="space-y-2">
                                <Label for="tenant-name">Nama Tenant</Label>
                                <Input id="tenant-name" :value="props.tenant.nama_tenant" placeholder="Masukkan nama tenant" />
                            </div>

                            <div class="space-y-2">
                                <Label for="tenant-description">Deskripsi</Label>
                                <Input id="tenant-description" :value="props.tenant.deskripsi" placeholder="Deskripsi singkat tenant" />
                            </div>

                            <div class="space-y-2">
                                <Label for="tenant-whatsapp">Whatsapp</Label>
                                <Input id="tenant-whatsapp" :value="props.tenant.whatsapp_tenant" placeholder="Nomor whatsapp" />
                            </div>

                            <div class="space-y-2">
                                <Label for="tenant-logo">Logo</Label>
                                <div class="mt-1 flex items-center">
                                    <div class="flex-shrink-0 h-16 w-16 border rounded-md overflow-hidden bg-gray-100">
                                        <img v-if="props.tenant.logo" :src="props.tenant.logo" class="h-full w-full object-cover" />
                                        <div v-else class="h-full w-full flex items-center justify-center text-gray-400">
                                            No Image
                                        </div>
                                    </div>
                                    <Button variant="outline" class="ml-5">
                                        Upload Logo
                                    </Button>
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter className="flex justify-end space-x-2">
                            <Button>Simpan Perubahan</Button>
                        </CardFooter>
                    </Card>
                </TabsContent>

                <!-- Products Tab -->
                <TabsContent value="products">
                    <Card>
                        <CardHeader className="flex flex-row items-center justify-between">
                            <div>
                                <CardTitle>Produk</CardTitle>
                                <CardDescription>
                                    Kelola produk yang Anda tawarkan
                                </CardDescription>
                            </div>
                            <Button>
                                <Plus className="mr-2 h-4 w-4" />
                                Tambah Produk
                            </Button>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div v-if="props.tenant.products && props.tenant.products.length > 0">
                                    <!-- Product list would go here -->
                                    <div class="rounded-md border p-4 mb-3" v-for="i in 3" :key="i">
                                        <div class="flex items-start justify-between">
                                            <div class="flex space-x-4">
                                                <div class="h-12 w-12 rounded-md bg-gray-100 flex items-center justify-center">
                                                    <Package />
                                                </div>
                                                <div>
                                                    <h4 class="font-semibold">Produk {{ i }}</h4>
                                                    <p class="text-sm text-muted-foreground">Rp 50.000</p>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2">
                                                <Button variant="outline" size="sm">Edit</Button>
                                                <Button variant="destructive" size="sm">Hapus</Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center py-8 text-muted-foreground">
                                    Belum ada produk. Klik 'Tambah Produk' untuk menambahkan produk baru.
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>

                <!-- Orders Tab -->
                <TabsContent value="orders">
                    <Card>
                        <CardHeader>
                            <CardTitle>Pre-Order</CardTitle>
                            <CardDescription>
                                Lihat dan kelola pre-order yang masuk
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <div v-for="i in 3" :key="i" class="rounded-md border p-4 mb-3">
                                    <div class="flex items-start justify-between">
                                        <div class="flex space-x-4">
                                            <div class="h-12 w-12 rounded-md bg-gray-100 flex items-center justify-center">
                                                <ShoppingCart />
                                            </div>
                                            <div>
                                                <h4 class="font-semibold">Order #{{ 1000 + i }}</h4>
                                                <p class="text-sm text-muted-foreground">Pembeli: User{{ i }}</p>
                                                <p class="text-sm text-muted-foreground">Tanggal: {{ new Date().toLocaleDateString() }}</p>
                                                <div class="mt-1 inline-flex px-2 py-1 rounded-full text-xs"
                                                     :class="i === 1 ? 'bg-green-100 text-green-800' : i === 2 ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800'">
                                                    {{ i === 1 ? 'Selesai' : i === 2 ? 'Menunggu' : 'Diproses' }}
                                                </div>
                                            </div>
                                        </div>
                                        <Button variant="outline" size="sm">Detail</Button>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>
        </div>
    </AuthLayout>
</template>
