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
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { router } from '@inertiajs/vue3';
import { Pencil, Plus, Search, Trash2 } from 'lucide-vue-next';
import { computed, nextTick, ref, watch } from 'vue';

interface TahunExpo {
    id: number;
    tahun: string;
    deskripsi: string;
    photo: string | null;
    photo_url: string;
}

const props = defineProps<{
    tahunExpo: TahunExpo[];
}>();

// State management
const showDialog = ref(false);
const dialogMode = ref<'add' | 'edit'>('add');
const formData = ref({
    id: '',
    tahun: '',
    deskripsi: '',
    photo: null as File | null,
    remove_photo: false,
});
const photoPreview = ref<string | null>(null);
const alert = ref({
    show: false,
    type: 'success' as 'success' | 'error',
    message: '',
});
const deleteId = ref<number | null>(null);
const showDeleteDialog = ref(false);
const validationErrors = ref<Record<string, string>>({});
const isSubmitting = ref(false);

// Datatable state
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Filtered tahun expo based on search
const filteredTahunExpo = computed(() => {
    let filtered = [...props.tahunExpo];

    // Filter by search term
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (item) => item.tahun.toLowerCase().includes(query) || (item.deskripsi && item.deskripsi.toLowerCase().includes(query)),
        );
    }

    return filtered;
});

// Reset page when filter changes
watch([searchQuery], () => {
    currentPage.value = 1;
});

// Paginated tahun expo
const paginatedTahunExpo = computed(() => {
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    const endIndex = startIndex + itemsPerPage.value;
    return filteredTahunExpo.value.slice(startIndex, endIndex);
});

// Total pages
const totalPages = computed(() => Math.ceil(filteredTahunExpo.value.length / itemsPerPage.value));

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

const showAlert = (type: 'success' | 'error', message: string) => {
    alert.value = {
        show: true,
        type,
        message,
    };
};

// Form handlers
const openAddDialog = () => {
    dialogMode.value = 'add';
    formData.value = {
        id: '',
        tahun: '',
        deskripsi: '',
        photo: null,
        remove_photo: false,
    };
    photoPreview.value = null;
    validationErrors.value = {};
    showDialog.value = true;
};

const openEditDialog = (data: TahunExpo) => {
    dialogMode.value = 'edit';

    // Reset form data first
    formData.value = {
        id: String(data.id),
        tahun: String(data.tahun || '').trim(),
        deskripsi: data.deskripsi || '',
        photo: null,
        remove_photo: false,
    };

    photoPreview.value = data.photo_url;
    validationErrors.value = {};
    showDialog.value = true;

    // Force refresh of input values
    nextTick(() => {
        const tahunInput = document.getElementById('tahun-input') as HTMLInputElement;
        if (tahunInput) {
            tahunInput.value = formData.value.tahun;
        }
    });
};

const confirmDelete = (id: number) => {
    deleteId.value = id;
    showDeleteDialog.value = true;
};

// const handleFileChange = (event: Event) => {
//     const target = event.target as HTMLInputElement;
//     if (target.files && target.files.length > 0) {
//         const file = target.files[0];
//         formData.value.photo = file;
//         formData.value.remove_photo = false;

//         // Create preview
//         const reader = new FileReader();
//         reader.onload = (e) => {
//             photoPreview.value = e.target?.result as string;
//         };
//         reader.readAsDataURL(file);
//     }
// };

// const removePhoto = () => {
//     formData.value.photo = null;
//     formData.value.remove_photo = true;
//     photoPreview.value = null;
// };

const validateForm = () => {
    const errors: Record<string, string> = {};

    // Pastikan tahun tidak kosong
    if (!formData.value.tahun || formData.value.tahun.trim() === '') {
        errors.tahun = 'The tahun field is required.';
    }

    validationErrors.value = errors;
    return Object.keys(errors).length === 0;
};

// METODE SUBMIT YANG DIPERBAIKI - UNTUK MENANGANI FORM DENGAN FILE UPLOAD DENGAN BENAR
const handleSubmit = () => {
    if (isSubmitting.value) return;

    // Validasi form sebelum mengirim
    if (!validateForm()) {
        return;
    }

    isSubmitting.value = true;

    // Penting: terapkan trim pada field tahun
    const tahunValue = formData.value.tahun.trim();

    if (dialogMode.value === 'add') {
        // Untuk ADD, gunakan FormData normal
        const submitData = new FormData();
        submitData.append('tahun', tahunValue);
        submitData.append('deskripsi', formData.value.deskripsi || '');

        if (formData.value.photo) {
            submitData.append('photo', formData.value.photo);
        }

        router.post('/admin/tahun-expo', submitData, {
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
        submitData.append('tahun', tahunValue);
        submitData.append('deskripsi', formData.value.deskripsi || '');
        submitData.append('_method', 'PUT'); // Tambahkan _method untuk spoofing PUT

        // Handle file dan remove_photo
        if (formData.value.photo) {
            submitData.append('photo', formData.value.photo);
        }

        if (formData.value.remove_photo) {
            submitData.append('remove_photo', '1');
        }

        // Gunakan router.post dengan _method: 'PUT' untuk file upload
        router.post(`/admin/tahun-expo/${id}`, submitData, {
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

    router.delete(`/admin/tahun-expo/${deleteId.value}`, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            showAlert('success', 'Data berhasil dihapus');
        },
        onError: () => {
            showDeleteDialog.value = false;
            showAlert('error', 'Gagal menghapus data');
        },
    });
};
</script>

<template>
    <AuthLayout title="Tahun Expo" description="Kelola data tahun expo">
        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Tahun Expo</h1>
                <Button @click="openAddDialog">
                    <Plus class="mr-2 h-4 w-4" />
                    Tambah Tahun
                </Button>
            </div>

            <!-- Search filter -->
            <div class="mb-4 flex">
                <div class="relative w-full max-w-md">
                    <Search class="absolute top-3 left-3 h-4 w-4 text-gray-400" />
                    <Input v-model="searchQuery" class="pl-10" placeholder="Cari tahun atau deskripsi..." />
                </div>
            </div>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>No</TableHead>
                        <TableHead>Tahun</TableHead>
                        <TableHead>Deskripsi</TableHead>
                        <TableHead>Aksi</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(item, index) in paginatedTahunExpo" :key="item.id">
                        <TableCell>{{ (currentPage - 1) * itemsPerPage + index + 1 }}</TableCell>
                        <TableCell>{{ item.tahun }}</TableCell>
                        <TableCell>{{ item.deskripsi }}</TableCell>
                        <TableCell>
                            <div class="flex space-x-2">
                                <Button variant="outline" size="icon" @click="openEditDialog(item)">
                                    <Pencil class="h-4 w-4" />
                                </Button>
                                <Button variant="destructive" size="icon" @click="confirmDelete(item.id)">
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="paginatedTahunExpo.length === 0">
                        <TableCell colspan="4" class="py-8 text-center">
                            {{ searchQuery ? 'Tidak ada tahun expo yang sesuai dengan pencarian Anda.' : 'Belum ada data tahun expo.' }}
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Pagination -->
            <div v-if="filteredTahunExpo.length > 0" class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-500">Menampilkan {{ paginatedTahunExpo.length }} dari {{ filteredTahunExpo.length }} data</div>
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

            <!-- Form Dialog -->
            <Dialog v-model:open="showDialog">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            {{ dialogMode === 'add' ? 'Tambah Tahun Expo' : 'Edit Tahun Expo' }}
                        </DialogTitle>
                        <DialogDescription>
                            Silakan isi formulir berikut untuk {{ dialogMode === 'add' ? 'menambahkan' : 'mengubah' }} data tahun expo.
                        </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="handleSubmit" class="space-y-4">
                        <div>
                            <Label for="tahun-input" class="text-sm font-medium">Tahun</Label>
                            <Input
                                id="tahun-input"
                                v-model="formData.tahun"
                                type="text"
                                maxlength="4"
                                required
                                :class="{ 'border-red-500 focus:ring-red-500': validationErrors.tahun }"
                                :value="formData.tahun"
                            />
                            <p v-if="validationErrors.tahun" class="mt-1 text-xs text-red-500">
                                {{ validationErrors.tahun }}
                            </p>
                        </div>

                        <div>
                            <Label for="deskripsi" class="text-sm font-medium">Deskripsi</Label>
                            <Input
                                id="deskripsi"
                                v-model="formData.deskripsi"
                                type="text"
                                :class="{ 'border-red-500 focus:ring-red-500': validationErrors.deskripsi }"
                            />
                            <p v-if="validationErrors.deskripsi" class="mt-1 text-xs text-red-500">
                                {{ validationErrors.deskripsi }}
                            </p>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <Button type="button" variant="outline" @click="showDialog = false"> Batal </Button>
                            <Button type="submit" :disabled="isSubmitting" :class="{ 'cursor-not-allowed opacity-75': isSubmitting }">
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
                    </form>
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
                        <AlertDialogCancel @click="showDeleteDialog = false">Batal</AlertDialogCancel>
                        <AlertDialogAction @click="handleDelete" class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                            >Hapus</AlertDialogAction
                        >
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>

            <!-- Alert Notification -->
            <Alert v-model:show="alert.show" :type="alert.type" :message="alert.message" />
        </div>
    </AuthLayout>
</template>
