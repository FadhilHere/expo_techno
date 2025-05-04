<script setup lang="ts">
import Alert from '@/components/Alert.vue'; // Import the separate Alert component
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
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Textarea } from '@/components/ui/textarea';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { router } from '@inertiajs/vue3';
import { Package, Pencil, Plus, Trash2, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

// Definisi breadcrumb
const breadcrumbItems = computed(() => {
    return [
        { label: 'Menu', href: '/' },
        { label: 'Tenant', href: null },
    ];
});

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

interface TahunExpo {
    id: number;
    tahun: string;
}

interface KategoriTenant {
    id: number;
    nama_kategori: string;
}

defineProps<{
    tenants: Tenant[];
    tahunExpos: TahunExpo[];
    kategoriTenants: KategoriTenant[];
}>();

// State management
const showDialog = ref(false);
const dialogMode = ref<'add' | 'edit'>('add');
const formData = ref({
    id: '',
    tahun_expo_id: '',
    kategori_id: '',
    nama_tenant: '',
    deskripsi: '',
    whatsapp_tenant: '',
    logo: null as File | null,
    remove_logo: false,
});
const logoPreview = ref<string | null>(null);
const alert = ref({
    show: false,
    type: 'success' as 'success' | 'error',
    message: '',
});
const deleteId = ref<number | null>(null);
const showDeleteDialog = ref(false);
const validationErrors = ref<Record<string, string>>({});
const isSubmitting = ref(false);
const isDeleting = ref(false); // Add state for tracking delete process

const showAlert = (type: 'success' | 'error', message: string) => {
    alert.value = {
        show: true,
        type,
        message,
    };
    setTimeout(() => {
        alert.value.show = false;
    }, 5000); // Otomatis hilang setelah 5 detik
};

// Form handlers
const openAddDialog = () => {
    dialogMode.value = 'add';
    formData.value = {
        id: '',
        tahun_expo_id: '',
        kategori_id: '',
        nama_tenant: '',
        deskripsi: '',
        whatsapp_tenant: '',
        logo: null,
        remove_logo: false,
    };
    logoPreview.value = null;
    validationErrors.value = {};
    showDialog.value = true;
};

const openEditDialog = (data: Tenant) => {
    dialogMode.value = 'edit';

    // Reset form data first
    formData.value = {
        id: String(data.id),
        tahun_expo_id: String(data.tahun_expo_id),
        kategori_id: String(data.kategori_id),
        nama_tenant: data.nama_tenant || '',
        deskripsi: data.deskripsi || '',
        whatsapp_tenant: data.whatsapp_tenant || '',
        logo: null,
        remove_logo: false,
    };

    logoPreview.value = data.logo_url;
    validationErrors.value = {};
    showDialog.value = true;
};

const confirmDelete = (id: number) => {
    deleteId.value = id;
    showDeleteDialog.value = true;
};

const goToProducts = (tenantId: number) => {
    router.visit(`/admin/tenant/${tenantId}/products`);
};

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        const file = target.files[0];
        formData.value.logo = file;
        formData.value.remove_logo = false;

        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            logoPreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const removeLogo = () => {
    formData.value.logo = null;
    formData.value.remove_logo = true;
    logoPreview.value = null;
};

const validateForm = () => {
    const errors: Record<string, string> = {};

    // Validasi field required
    if (!formData.value.tahun_expo_id) {
        errors.tahun_expo_id = 'The tahun expo field is required.';
    }

    if (!formData.value.kategori_id) {
        errors.kategori_id = 'The kategori field is required.';
    }

    if (!formData.value.nama_tenant || formData.value.nama_tenant.trim() === '') {
        errors.nama_tenant = 'The nama tenant field is required.';
    }

    validationErrors.value = errors;
    return Object.keys(errors).length === 0;
};

// Handle form submission
const handleSubmit = () => {
    if (isSubmitting.value) return;

    // Validasi form sebelum mengirim
    if (!validateForm()) {
        return;
    }

    isSubmitting.value = true;

    // Penting: apply trim to text fields
    const namaTenantValue = formData.value.nama_tenant.trim();
    const deskripsiValue = formData.value.deskripsi.trim();
    const whatsappValue = formData.value.whatsapp_tenant.trim();

    if (dialogMode.value === 'add') {
        // Untuk ADD, gunakan FormData normal
        const submitData = new FormData();
        submitData.append('tahun_expo_id', formData.value.tahun_expo_id);
        submitData.append('kategori_id', formData.value.kategori_id);
        submitData.append('nama_tenant', namaTenantValue);
        submitData.append('deskripsi', deskripsiValue);
        submitData.append('whatsapp_tenant', whatsappValue);

        if (formData.value.logo) {
            submitData.append('logo', formData.value.logo);
        }

        router.post('/admin/tenant', submitData, {
            onSuccess: () => {
                showDialog.value = false;
                showAlert('success', 'Data berhasil ditambahkan');
                isSubmitting.value = false;
            },
            onError: (errors) => {
                validationErrors.value = errors;
                showAlert('error', 'Terjadi kesalahan saat menambah data');
                isSubmitting.value = false;
                console.error('Validation errors:', errors);
            },
        });
    } else {
        // Untuk UPDATE
        const id = formData.value.id;

        // Selalu gunakan FormData untuk update agar bisa menangani file
        const submitData = new FormData();

        // Tambahkan data form
        submitData.append('tahun_expo_id', formData.value.tahun_expo_id);
        submitData.append('kategori_id', formData.value.kategori_id);
        submitData.append('nama_tenant', namaTenantValue);
        submitData.append('deskripsi', deskripsiValue);
        submitData.append('whatsapp_tenant', whatsappValue);
        submitData.append('_method', 'PUT'); // Tambahkan _method untuk spoofing PUT

        // Handle file dan remove_logo
        if (formData.value.logo) {
            submitData.append('logo', formData.value.logo);
        }

        // PENTING: Hanya append remove_logo jika nilainya true
        if (formData.value.remove_logo === true) {
            submitData.append('remove_logo', '1');
        }

        // Gunakan router.post dengan _method: 'PUT' untuk file upload
        router.post(`/admin/tenant/${id}`, submitData, {
            onSuccess: () => {
                showDialog.value = false;
                showAlert('success', 'Data berhasil diupdate');
                isSubmitting.value = false;
            },
            onError: (errors) => {
                validationErrors.value = errors;
                showAlert('error', 'Terjadi kesalahan saat update data');
                isSubmitting.value = false;
                console.error('Validation errors on update:', errors);
            },
        });
    }
};

const handleDelete = () => {
    if (deleteId.value === null) return;

    // Set loading state untuk delete
    isDeleting.value = true;

    // Debug log
    console.log('Deleting tenant with ID:', deleteId.value);

    router.delete(`/admin/tenant/${deleteId.value}`, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            showAlert('success', 'Data berhasil dihapus');
            isDeleting.value = false;
        },
        onError: (errors) => {
            showDeleteDialog.value = false;
            showAlert('error', errors.message || 'Gagal menghapus data');
            isDeleting.value = false;
            console.error('Delete errors:', errors);
        },
    });
};
</script>

<template>
    <AuthLayout title="Tenant" description="Kelola data tenant" :breadcrumbs="breadcrumbItems">
        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Tenant</h1>
                <Button @click="openAddDialog">
                    <Plus class="mr-2 h-4 w-4" />
                    Tambah Tenant
                </Button>
            </div>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>No</TableHead>
                        <TableHead>Logo</TableHead>
                        <TableHead>Nama Tenant</TableHead>
                        <TableHead>Tahun Expo</TableHead>
                        <TableHead>Kategori</TableHead>
                        <TableHead>Whatsapp</TableHead>
                        <TableHead>Aksi</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(item, index) in tenants" :key="item.id">
                        <TableCell>{{ index + 1 }}</TableCell>
                        <TableCell>
                            <img :src="item.logo_url" alt="Logo Tenant" class="h-16 w-16 rounded object-cover" />
                        </TableCell>
                        <TableCell>{{ item.nama_tenant }}</TableCell>
                        <TableCell>{{ item.tahun_expo }}</TableCell>
                        <TableCell>{{ item.kategori }}</TableCell>
                        <TableCell>{{ item.whatsapp_tenant }}</TableCell>
                        <TableCell>
                            <div class="flex space-x-2">
                                <Button variant="outline" size="icon" title="Lihat Produk" @click="goToProducts(item.id)">
                                    <Package class="h-4 w-4" />
                                </Button>
                                <Button variant="outline" size="icon" title="Edit Tenant" @click="openEditDialog(item)">
                                    <Pencil class="h-4 w-4" />
                                </Button>
                                <Button variant="destructive" size="icon" title="Hapus Tenant" @click="confirmDelete(item.id)">
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Form Dialog -->
            <!-- Form Dialog dengan scrolling yang dioptimalkan -->
            <Dialog v-model:open="showDialog">
                <DialogContent class="flex max-h-[90vh] max-w-2xl flex-col overflow-hidden">
                    <DialogHeader class="flex-shrink-0">
                        <DialogTitle>
                            {{ dialogMode === 'add' ? 'Tambah Tenant' : 'Edit Tenant' }}
                        </DialogTitle>
                        <DialogDescription>
                            Silakan isi formulir berikut untuk {{ dialogMode === 'add' ? 'menambahkan' : 'mengubah' }} data tenant.
                        </DialogDescription>
                    </DialogHeader>

                    <!-- Gunakan div dengan overflow-y-auto untuk membuat area form dapat di-scroll -->
                    <div class="-mr-1 overflow-y-auto py-2 pr-1">
                        <form @submit.prevent="handleSubmit" class="space-y-4">
                            <div>
                                <Label for="tahun-expo-select" class="text-sm font-medium">Tahun Expo</Label>
                                <Select v-model="formData.tahun_expo_id" required>
                                    <SelectTrigger
                                        id="tahun-expo-select"
                                        class="w-full"
                                        :class="{ 'border-red-500 focus:ring-red-500': validationErrors.tahun_expo_id }"
                                    >
                                        <SelectValue placeholder="Pilih Tahun Expo" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="tahun in tahunExpos" :key="tahun.id" :value="String(tahun.id)">
                                            {{ tahun.tahun }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="validationErrors.tahun_expo_id" class="mt-1 text-xs text-red-500">
                                    {{ validationErrors.tahun_expo_id }}
                                </p>
                            </div>

                            <div>
                                <Label for="kategori-select" class="text-sm font-medium">Kategori</Label>
                                <Select v-model="formData.kategori_id" required>
                                    <SelectTrigger
                                        id="kategori-select"
                                        class="w-full"
                                        :class="{ 'border-red-500 focus:ring-red-500': validationErrors.kategori_id }"
                                    >
                                        <SelectValue placeholder="Pilih Kategori" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="kategori in kategoriTenants" :key="kategori.id" :value="String(kategori.id)">
                                            {{ kategori.nama_kategori }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <p v-if="validationErrors.kategori_id" class="mt-1 text-xs text-red-500">
                                    {{ validationErrors.kategori_id }}
                                </p>
                            </div>

                            <div>
                                <Label for="nama-tenant-input" class="text-sm font-medium">Nama Tenant</Label>
                                <Input
                                    id="nama-tenant-input"
                                    v-model="formData.nama_tenant"
                                    type="text"
                                    required
                                    :class="{ 'border-red-500 focus:ring-red-500': validationErrors.nama_tenant }"
                                />
                                <p v-if="validationErrors.nama_tenant" class="mt-1 text-xs text-red-500">
                                    {{ validationErrors.nama_tenant }}
                                </p>
                            </div>

                            <div>
                                <Label for="deskripsi" class="text-sm font-medium">Deskripsi</Label>
                                <Textarea
                                    id="deskripsi"
                                    v-model="formData.deskripsi"
                                    rows="3"
                                    :class="{ 'border-red-500 focus:ring-red-500': validationErrors.deskripsi }"
                                />
                                <p v-if="validationErrors.deskripsi" class="mt-1 text-xs text-red-500">
                                    {{ validationErrors.deskripsi }}
                                </p>
                            </div>

                            <div>
                                <Label for="whatsapp-tenant" class="text-sm font-medium">Whatsapp</Label>
                                <Input
                                    id="whatsapp-tenant"
                                    v-model="formData.whatsapp_tenant"
                                    type="text"
                                    placeholder="Contoh: 628123456789"
                                    :class="{ 'border-red-500 focus:ring-red-500': validationErrors.whatsapp_tenant }"
                                />
                                <p v-if="validationErrors.whatsapp_tenant" class="mt-1 text-xs text-red-500">
                                    {{ validationErrors.whatsapp_tenant }}
                                </p>
                            </div>

                            <div>
                                <Label for="logo" class="text-sm font-medium">Logo</Label>

                                <!-- Logo Preview -->
                                <div v-if="logoPreview" class="mt-2 mb-3">
                                    <div class="relative inline-block">
                                        <img :src="logoPreview" alt="Logo Preview" class="h-32 w-32 rounded border object-cover" />
                                        <button
                                            type="button"
                                            @click="removeLogo"
                                            class="absolute -top-2 -right-2 rounded-full bg-red-500 p-1 text-white hover:bg-red-600 focus:outline-none"
                                        >
                                            <X class="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>

                                <!-- File Upload -->
                                <Input
                                    id="logo"
                                    type="file"
                                    accept="image/*"
                                    @change="handleFileChange"
                                    class="mt-1"
                                    :class="{ 'border-red-500 focus:ring-red-500': validationErrors.logo }"
                                />
                                <p v-if="validationErrors.logo" class="mt-1 text-xs text-red-500">
                                    {{ validationErrors.logo }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">Ukuran maksimal 5MB. Format: JPG, PNG, GIF</p>
                            </div>
                        </form>
                    </div>

                    <!-- Footer buttons tetap di bagian bawah, tidak ikut scroll -->
                    <div class="mt-4 flex flex-shrink-0 justify-end space-x-2 border-t border-gray-200 pt-4">
                        <Button type="button" variant="outline" @click="showDialog = false"> Batal </Button>
                        <Button
                            type="button"
                            @click="handleSubmit"
                            :disabled="isSubmitting"
                            :class="{ 'cursor-not-allowed opacity-75': isSubmitting }"
                        >
                            <template v-if="isSubmitting">
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
                            <template v-else>
                                {{ dialogMode === 'add' ? 'Tambah' : 'Simpan' }}
                            </template>
                        </Button>
                    </div>
                </DialogContent>
            </Dialog>

            <!-- Delete Confirmation Dialog -->
            <AlertDialog v-model:open="showDeleteDialog">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Konfirmasi Hapus</AlertDialogTitle>
                        <AlertDialogDescription>
                            Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus data secara permanen.
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
                            <template v-else> Hapus </template>
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>

            <!-- Alert Notification -->
            <Alert v-model:show="alert.show" :type="alert.type" :message="alert.message" />
        </div>
    </AuthLayout>
</template>
