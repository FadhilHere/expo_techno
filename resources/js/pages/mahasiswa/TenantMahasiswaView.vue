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
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { Mail, Package, Pencil, Phone, Plus, Trash2, X } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

// Define types for our data
interface Tenant {
    id: number;
    nama_tenant: string;
    deskripsi: string;
    whatsapp_tenant: string;
    logo: string | null;
    logo_url: string;
    tahun_expo_id: number;
    tahun_expo: string;
    kategori_id: number;
    kategori: string;
}

interface Product {
    id: number;
    nama_produk: string;
    harga: number;
    deskripsi: string;
    foto_produk: string | null;
    foto_produk_url: string;
}

// Extend Inertia PageProps to include flash messages
interface CustomPageProps {
    flash: {
        success?: string;
        error?: string;
    };
    [key: string]: any;
}

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
});

onBeforeUnmount(() => {
    // Use type assertion to avoid TypeScript error
    // This is safe because Inertia router does have an 'off' method
    (router as any).off?.('success', successHandler);
});

// Receive props from controller
const props = defineProps<{
    tenants: Tenant[];
    products: Product[];
}>();

// Check if student has a tenant assigned
const hasTenant = computed(() => props.tenants && props.tenants.length > 0);

// Get the tenant data - we expect only one tenant per student
const tenant = computed(() => (hasTenant.value ? props.tenants[0] : null));

// Product management
const showProductDialog = ref(false);
const productDialogMode = ref<'add' | 'edit'>('add');
const productFormData = ref({
    id: null as number | null,
    nama_produk: '',
    harga: 0,
    deskripsi: '',
    foto_produk: null as File | null,
    remove_foto: false,
});
const productImagePreview = ref<string | null>(null);
const showDeleteProductDialog = ref(false);
const deleteProductId = ref<number | null>(null);

// Open product dialogs
const openAddProductDialog = () => {
    productDialogMode.value = 'add';
    productFormData.value = {
        id: null,
        nama_produk: '',
        harga: 0,
        deskripsi: '',
        foto_produk: null,
        remove_foto: false,
    };
    productImagePreview.value = null;
    showProductDialog.value = true;
};

const openEditProductDialog = (product: Product) => {
    productDialogMode.value = 'edit';
    productFormData.value = {
        id: product.id,
        nama_produk: product.nama_produk || '',
        harga: product.harga || 0,
        deskripsi: product.deskripsi || '',
        foto_produk: null,
        remove_foto: false,
    };
    productImagePreview.value = product.foto_produk_url;
    showProductDialog.value = true;
};

const confirmDeleteProduct = (id: number) => {
    deleteProductId.value = id;
    showDeleteProductDialog.value = true;
};

// Handle product image
const handleProductImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        productFormData.value.foto_produk = file;
        productFormData.value.remove_foto = false;

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            productImagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const removeProductImage = () => {
    productFormData.value.foto_produk = null;
    productFormData.value.remove_foto = true;
    productImagePreview.value = null;
};

// Save product
const saveProduct = () => {
    if (tenant.value?.id) {
        const formData = new FormData();

        if (productDialogMode.value === 'edit' && productFormData.value.id) {
            // Add form fields
            formData.append('nama_produk', productFormData.value.nama_produk);
            formData.append('harga', productFormData.value.harga.toString());
            formData.append('deskripsi', productFormData.value.deskripsi);

            // Don't use method spoofing, just use POST directly
            // formData.append('_method', 'PUT');

            if (productFormData.value.foto_produk) {
                formData.append('foto_produk', productFormData.value.foto_produk);
            }

            if (productFormData.value.remove_foto) {
                formData.append('remove_foto', '1');
            }

            const url = `/mahasiswa/tenant/${tenant.value.id}/products/${productFormData.value.id}`;

            // Use POST directly since our route is now POST
            router.post(url, formData, {
                onSuccess: function () {
                    showProductDialog.value = false;

                    // Show success alert
                    alert.value = {
                        show: true,
                        type: 'success',
                        message: 'Produk berhasil diperbarui',
                    };
                },
                onError: function (errors) {
                    console.error('Update error:', errors);
                    alert.value = {
                        show: true,
                        type: 'error',
                        message: 'Terjadi kesalahan saat menyimpan produk',
                    };
                },
            });
        } else {
            // Add form fields for new product
            formData.append('nama_produk', productFormData.value.nama_produk);
            formData.append('harga', productFormData.value.harga.toString());
            formData.append('deskripsi', productFormData.value.deskripsi);

            if (productFormData.value.foto_produk) {
                formData.append('foto_produk', productFormData.value.foto_produk);
            }

            router.post(`/mahasiswa/tenant/${tenant.value.id}/products`, formData, {
                onSuccess: function () {
                    showProductDialog.value = false;

                    // Show success alert
                    alert.value = {
                        show: true,
                        type: 'success',
                        message: 'Produk berhasil ditambahkan',
                    };
                },
                onError: function () {
                    alert.value = {
                        show: true,
                        type: 'error',
                        message: 'Terjadi kesalahan saat menambah produk',
                    };
                },
            });
        }
    }
};

// Delete product
const deleteProduct = () => {
    if (tenant.value?.id && deleteProductId.value) {
        router.delete(`/mahasiswa/tenant/${tenant.value.id}/products/${deleteProductId.value}`, {
            preserveScroll: true,
            onSuccess: function () {
                showDeleteProductDialog.value = false;
                deleteProductId.value = null;

                // Show success alert
                alert.value = {
                    show: true,
                    type: 'success',
                    message: 'Produk berhasil dihapus',
                };
            },
            onError: function (errors) {
                console.error('Delete error:', errors);
                alert.value = {
                    show: true,
                    type: 'error',
                    message: 'Terjadi kesalahan saat menghapus produk',
                };
            },
        });
    }
};
</script>

<template>
    <AuthLayout title="Tenant Mahasiswa" description="Kelola informasi tenant dan produk Anda">
        <!-- Container 1: Main wrapper -->
        <div class="space-y-6 p-6">
            <!-- No Tenant Message -->
            <div v-if="!hasTenant" class="flex flex-col items-center justify-center py-12 text-center">
                <div class="mb-4 rounded-full bg-orange-100 p-4">
                    <Package class="h-10 w-10 text-orange-600" />
                </div>
                <h2 class="mb-2 text-2xl font-bold">Belum Ada Tenant</h2>
                <p class="text-muted-foreground mb-6 max-w-md">
                    Kamu belum memiliki tenant yang terdaftar. Tenant akan diassign oleh admin Technologia.
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

            <!-- Tenant Information and Products (if tenant exists) -->
            <div v-else class="space-y-6">
                <!-- Container 2: Tenant Information Card (Read-only) -->
                <Card>
                    <CardHeader>
                        <CardTitle>Informasi Tenant</CardTitle>
                        <CardDescription> Informasi tenant yang dikelola oleh admin Technologia </CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-6 md:grid-cols-4">
                        <!-- Logo -->
                        <div class="flex flex-col items-center justify-center">
                            <div class="h-32 w-32 overflow-hidden rounded-md border bg-gray-100">
                                <img v-if="tenant?.logo_url" :src="tenant.logo_url" class="h-full w-full object-cover" alt="Logo Tenant" />
                                <div v-else class="flex h-full w-full items-center justify-center text-gray-400">No Logo</div>
                            </div>
                        </div>

                        <!-- Tenant Details -->
                        <div class="space-y-4 md:col-span-3">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Nama Tenant</h3>
                                <p class="mt-1 text-lg font-medium">{{ tenant?.nama_tenant || 'Belum diatur' }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Kategori</h3>
                                <p class="mt-1">{{ tenant?.kategori || 'Belum dikategorikan' }}</p>
                            </div>

                            <div>
                                <h3 class="text-sm font-medium text-gray-500">Whatsapp</h3>
                                <p class="mt-1">{{ tenant?.whatsapp_tenant || 'Belum diatur' }}</p>
                            </div>

                            <!-- <div>
                                <h3 class="text-sm font-medium text-gray-500">Deskripsi</h3>
                                <p class="mt-1 line-clamp-2 overflow-hidden text-ellipsis">{{ tenant?.deskripsi || 'Belum ada deskripsi' }}</p>
                            </div> -->
                        </div>
                    </CardContent>
                </Card>

                <!-- Container 3: Products Management -->
                <Card>
                    <CardHeader class="flex flex-row items-center justify-between">
                        <div>
                            <CardTitle>Produk</CardTitle>
                            <CardDescription> Kelola produk yang Anda tawarkan </CardDescription>
                        </div>
                        <Button @click="openAddProductDialog">
                            <Plus class="mr-2 h-4 w-4" />
                            Tambah Produk
                        </Button>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div v-if="products && products.length > 0">
                                <!-- Product list -->
                                <div class="mb-3 rounded-md border p-4" v-for="product in products" :key="product.id">
                                    <div class="flex items-start justify-between">
                                        <div class="flex space-x-4">
                                            <div class="h-16 w-16 overflow-hidden rounded-md bg-gray-100">
                                                <img
                                                    v-if="product.foto_produk_url"
                                                    :src="product.foto_produk_url"
                                                    class="h-full w-full object-cover"
                                                    alt="Product image"
                                                />
                                                <div v-else class="flex h-full w-full items-center justify-center">
                                                    <Package class="h-6 w-6 text-gray-400" />
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold">{{ product.nama_produk }}</h4>
                                                <p class="text-muted-foreground text-sm">Rp {{ product.harga.toLocaleString('id-ID') }}</p>
                                                <p v-if="product.deskripsi" class="text-muted-foreground line-clamp-1 text-sm">
                                                    {{ product.deskripsi }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <Button variant="outline" size="sm" @click="openEditProductDialog(product)">
                                                <Pencil class="mr-1 h-4 w-4" />
                                                Edit
                                            </Button>
                                            <Button variant="destructive" size="sm" @click="confirmDeleteProduct(product.id)">
                                                <Trash2 class="mr-1 h-4 w-4" />
                                                Hapus
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="mb-4 rounded-full bg-blue-100 p-4">
                                    <Package class="h-8 w-8 text-blue-600" />
                                </div>
                                <h3 class="mb-2 text-lg font-semibold">Belum Ada Produk</h3>
                                <p class="text-muted-foreground mb-6 max-w-md">
                                    Produk kamu belum ada nih. Yuk tambahkan produk andalan kamu ke tenant ini!
                                </p>
                                <Button @click="openAddProductDialog" class="gap-2">
                                    <Plus class="h-4 w-4" />
                                    Tambah Produk Sekarang
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AuthLayout>

    <!-- Alert Component -->
    <Alert v-model:show="alert.show" :type="alert.type" :message="alert.message" />

    <!-- Product Dialog -->
    <Dialog v-model:open="showProductDialog">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ productDialogMode === 'add' ? 'Tambah Produk' : 'Edit Produk' }}</DialogTitle>
                <DialogDescription>
                    {{ productDialogMode === 'add' ? 'Tambahkan produk baru ke tenant Anda' : 'Edit informasi produk' }}
                </DialogDescription>
            </DialogHeader>
            <div class="space-y-4 py-4">
                <div class="space-y-2">
                    <Label for="product-name">Nama Produk</Label>
                    <Input id="product-name" v-model="productFormData.nama_produk" placeholder="Masukkan nama produk" />
                </div>
                <div class="space-y-2">
                    <Label for="product-price">Harga (Rp)</Label>
                    <Input id="product-price" v-model="productFormData.harga" type="number" min="0" placeholder="Masukkan harga produk" />
                </div>
                <div class="space-y-2">
                    <Label for="product-description">Deskripsi</Label>
                    <Textarea id="product-description" v-model="productFormData.deskripsi" placeholder="Deskripsi produk" />
                </div>
                <div class="space-y-2">
                    <Label for="product-image">Foto Produk</Label>

                    <!-- Image Preview -->
                    <div v-if="productImagePreview" class="mt-2 mb-3">
                        <div class="relative inline-block">
                            <img :src="productImagePreview" alt="Product Preview" class="h-32 w-32 rounded border object-cover" />
                            <button
                                type="button"
                                @click="removeProductImage"
                                class="absolute -top-2 -right-2 rounded-full bg-red-500 p-1 text-white hover:bg-red-600 focus:outline-none"
                            >
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <Input id="product-image" type="file" accept="image/*" @change="handleProductImageChange" />
                    <!-- <p class="text-xs text-gray-500">Format: JPG, PNG, GIF. Ukuran maksimal: 5MB</p> -->
                </div>
            </div>
            <div class="flex justify-end space-x-2">
                <Button variant="outline" @click="showProductDialog = false">Batal</Button>
                <Button @click="saveProduct">
                    {{ productDialogMode === 'add' ? 'Tambah' : 'Simpan' }}
                </Button>
            </div>
        </DialogContent>
    </Dialog>

    <!-- Delete Product Confirmation Dialog -->
    <AlertDialog v-model:open="showDeleteProductDialog">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Konfirmasi Hapus</AlertDialogTitle>
                <AlertDialogDescription> Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan. </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="showDeleteProductDialog = false">Batal</AlertDialogCancel>
                <AlertDialogAction @click="deleteProduct" class="bg-destructive text-destructive-foreground hover:bg-destructive/90">
                    Hapus
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
