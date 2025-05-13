<script setup lang="ts">
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
import AuthLayout from '@/layouts/AuthLayout.vue';
import { router } from '@inertiajs/vue3';
import { Pencil, Search, Trash2, UserPlus, UserCheck, UserX } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
import axios from 'axios';

interface Account {
    id: number;
    username: string;
    role: string;
    is_active: boolean;
    tenant_id: number | null;
}

interface Tenant {
    id: number;
    name: string;
}

const props = defineProps<{
    account: Account[];
}>();

// State management
const showDialog = ref(false);
const dialogMode = ref<'add' | 'edit'>('add');
const formData = ref({
    id: '',
    username: '',
    password: '',
    role: 'mahasiswa',
    is_active: true,
    tenant_id: null as number | null,
});

// Direct alert state - not using the Alert component
const alertState = ref({
    show: false,
    type: 'success' as 'success' | 'error',
    message: '',
});

const deleteId = ref<number | null>(null);
const showDeleteDialog = ref(false);
const validationErrors = ref<Record<string, string>>({});
const isSubmitting = ref(false);
const tenants = ref<Tenant[]>([]);
const isLoadingTenants = ref(true);

// Use a direct object map instead of an array for better tracking
const selectedAccountsMap = ref<Record<number, boolean>>({});
const showStatusDialog = ref(false);
const newStatus = ref(true);

// Datatable state
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Computed to get array of selected IDs
const selectedAccountIds = computed(() => {
    return Object.entries(selectedAccountsMap.value)
        .filter(([, isChecked]) => isChecked)
        .map(([id]) => parseInt(id, 10));
});

// Check if all accounts on current page are selected
const areAllSelected = computed(() => {
    if (paginatedAccounts.value.length === 0) return false;
    return paginatedAccounts.value.every(account => selectedAccountsMap.value[account.id] === true);
});

// Toggle account selection
const toggleAccountSelection = (id: number) => {
    // console.log('Toggling account:', id);
    selectedAccountsMap.value[id] = !selectedAccountsMap.value[id];
    // console.log('Selection state after toggle:', selectedAccountsMap.value);
};

// Toggle select all
const toggleSelectAll = () => {
    const newValue = !areAllSelected.value;
    // console.log('Toggle all accounts to:', newValue);

    paginatedAccounts.value.forEach(account => {
        selectedAccountsMap.value[account.id] = newValue;
    });

    // console.log('Selection state after toggle all:', selectedAccountsMap.value);
};

// Load tenants for dropdown
onMounted(async () => {
    isLoadingTenants.value = true;
    try {
        const response = await axios.get('/super-admin/tenants');
        tenants.value = response.data;
    } catch (error) {
        console.error('Failed to load tenants:', error);
    } finally {
        isLoadingTenants.value = false;
    }
});

// Filtered accounts based on search
const filteredAccounts = computed(() => {
    let filtered = [...props.account];

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
            (item) =>
                item.username.toLowerCase().includes(query) ||
                item.role.toLowerCase().includes(query)
        );
    }

    return filtered;
});

// Reset page when filter changes
watch([searchQuery], () => {
    currentPage.value = 1;
});

// Paginated accounts
const paginatedAccounts = computed(() => {
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    const endIndex = startIndex + itemsPerPage.value;
    return filteredAccounts.value.slice(startIndex, endIndex);
});

// Reset selections when paging
watch([currentPage, filteredAccounts], () => {
    // Clear selections when changing pages
    selectedAccountsMap.value = {};
});

// Total pages
const totalPages = computed(() => Math.ceil(filteredAccounts.value.length / itemsPerPage.value));

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

// Open dialog for updating multiple account status
const openStatusDialog = (status: boolean) => {
    const selectedIds = selectedAccountIds.value;
    // console.log('Opening status dialog with selected accounts:', selectedIds);

    if (selectedIds.length === 0) {
        showCustomAlert('error', 'Pilih minimal satu akun terlebih dahulu');
        return;
    }

    newStatus.value = status;
    showStatusDialog.value = true;
};

// Update status for multiple accounts
const updateMultipleStatus = () => {
    const selectedIds = selectedAccountIds.value;
    // console.log('Updating status for accounts:', selectedIds, 'to:', newStatus.value);

    if (selectedIds.length === 0) {
        showCustomAlert('error', 'Pilih minimal satu akun terlebih dahulu');
        return;
    }

    // Set this to true while submitting to prevent double-clicks
    isSubmitting.value = true;

    router.post('/super-admin/account/multiple-status', {
        account_ids: selectedIds,
        is_active: newStatus.value
    }, {
        onSuccess: () => {
            // console.log('Status update successful');
            showStatusDialog.value = false;
            selectedAccountsMap.value = {}; // Clear selections
            showCustomAlert('success', `Akun berhasil ${newStatus.value ? 'diaktifkan' : 'dinonaktifkan'}`);
            isSubmitting.value = false;
        },
        onError: (errors) => {
            console.error('Status update error:', errors);
            showStatusDialog.value = false;
            showCustomAlert('error', 'Gagal mengubah status akun');
            isSubmitting.value = false;
        },
        preserveState: true // Preserve form state so we stay on the page
    });
};

// Form handlers
const openAddDialog = () => {
    dialogMode.value = 'add';
    formData.value = {
        id: '',
        username: '',
        password: '',
        role: 'mahasiswa',
        is_active: true,
        tenant_id: null,
    };
    validationErrors.value = {};
    showDialog.value = true;
};

const openEditDialog = (data: Account) => {
    dialogMode.value = 'edit';

    formData.value = {
        id: String(data.id),
        username: data.username || '',
        password: '', // Reset password field for security
        role: data.role || 'mahasiswa',
        is_active: data.is_active,
        tenant_id: data.tenant_id,
    };

    validationErrors.value = {};
    showDialog.value = true;
};

const confirmDelete = (id: number) => {
    deleteId.value = id;
    showDeleteDialog.value = true;
};

// Watch for role changes to reset tenant_id when switching away from mahasiswa
watch(() => formData.value.role, (newRole) => {
    if (newRole !== 'mahasiswa') {
        formData.value.tenant_id = null;
    }
});

const validateForm = () => {
    const errors: Record<string, string> = {};

    // Username validation
    if (!formData.value.username || formData.value.username.trim() === '') {
        errors.username = 'Username tidak boleh kosong.';
    }

    // Password validation (only required for new accounts)
    if (dialogMode.value === 'add' && (!formData.value.password || formData.value.password.trim() === '')) {
        errors.password = 'Password tidak boleh kosong.';
    } else if (formData.value.password && formData.value.password.length < 8) {
        errors.password = 'Password minimal 8 karakter.';
    }

    validationErrors.value = errors;
    return Object.keys(errors).length === 0;
};

const handleSubmit = () => {
    if (isSubmitting.value) return;

    if (!validateForm()) {
        return;
    }

    isSubmitting.value = true;

    // Ensure tenant_id is null if empty or not provided
    const tenant_id = formData.value.tenant_id || null;

    if (dialogMode.value === 'add') {
        router.post('/super-admin/account', {
            username: formData.value.username.trim(),
            password: formData.value.password,
            role: formData.value.role,
            is_active: formData.value.is_active,
            tenant_id: tenant_id,
        }, {
            onSuccess: () => {
                showDialog.value = false;
                showCustomAlert('success', 'Akun berhasil ditambahkan');
                isSubmitting.value = false;
            },
            onError: (errors) => {
                validationErrors.value = errors;
                showCustomAlert('error', 'Terjadi kesalahan saat menambah akun');
                isSubmitting.value = false;
            },
        });
    } else {
        const id = formData.value.id;
        const data: any = {};

        // Only include password if it's provided
        if (formData.value.password) {
            data.password = formData.value.password;
        }

        data.role = formData.value.role;
        data.is_active = formData.value.is_active;
        data.tenant_id = tenant_id;

        router.put(`/super-admin/account/${id}`, data, {
            onSuccess: () => {
                showDialog.value = false;
                showCustomAlert('success', 'Akun berhasil diupdate');
                isSubmitting.value = false;
            },
            onError: (errors) => {
                validationErrors.value = errors;
                showCustomAlert('error', 'Terjadi kesalahan saat update akun');
                isSubmitting.value = false;
            },
        });
    }
};

const handleDelete = () => {
    if (deleteId.value === null) return;

    // Set this to true while submitting to prevent double-clicks
    isSubmitting.value = true;

    router.delete(`/super-admin/account/${deleteId.value}`, {
        onSuccess: () => {
            showDeleteDialog.value = false;
            showCustomAlert('success', 'Akun berhasil dihapus');
            isSubmitting.value = false;
        },
        onError: () => {
            showDeleteDialog.value = false;
            showCustomAlert('error', 'Gagal menghapus akun');
            isSubmitting.value = false;
        },
        preserveState: true // Preserve form state so we stay on the page
    });
};

// Custom function to show alert without relying on the Alert component
const showCustomAlert = (type: 'success' | 'error', message: string) => {
    // console.log('Showing custom alert:', type, message);
    alertState.value = {
        show: true,
        type,
        message
    };

    // Auto-hide after 3 seconds
    setTimeout(() => {
        alertState.value.show = false;
    }, 3000);
};
</script>

<template>
    <AuthLayout title="Manajemen Akun" description="Kelola data akun pengguna">
        <div class="p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-bold">Manajemen Akun</h1>
                <div class="flex space-x-2">
                    <Button @click="openStatusDialog(true)" variant="outline" class="text-green-600">
                        <UserCheck class="mr-2 h-4 w-4" />
                        Aktifkan
                    </Button>
                    <Button @click="openStatusDialog(false)" variant="outline" class="text-red-600">
                        <UserX class="mr-2 h-4 w-4" />
                        Nonaktifkan
                    </Button>
                    <Button @click="openAddDialog">
                        <UserPlus class="mr-2 h-4 w-4" />
                        Tambah Akun
                    </Button>
                </div>
            </div>

            <!-- Search filter -->
            <div class="mb-4 flex">
                <div class="relative w-full max-w-md">
                    <Search class="absolute top-3 left-3 h-4 w-4 text-gray-400" />
                    <Input v-model="searchQuery" class="pl-10" placeholder="Cari username atau role..." />
                </div>
            </div>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-12">
                            <input
                                type="checkbox"
                                :checked="areAllSelected"
                                @change="toggleSelectAll"
                                class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                            />
                        </TableHead>
                        <TableHead>No</TableHead>
                        <TableHead>Username</TableHead>
                        <TableHead>Role</TableHead>
                        <TableHead>Status</TableHead>
                        <TableHead>Aksi</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(item, index) in paginatedAccounts" :key="item.id">
                        <TableCell>
                            <input
                                type="checkbox"
                                :checked="selectedAccountsMap[item.id] === true"
                                @change="toggleAccountSelection(item.id)"
                                class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary"
                            />
                        </TableCell>
                        <TableCell>{{ (currentPage - 1) * itemsPerPage + index + 1 }}</TableCell>
                        <TableCell>{{ item.username }}</TableCell>
                        <TableCell>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="{
                                    'bg-purple-100 text-purple-800': item.role === 'super_admin',
                                    'bg-blue-100 text-blue-800': item.role === 'admin',
                                    'bg-green-100 text-green-800': item.role === 'mahasiswa'
                                }">
                                {{ item.role }}
                            </span>
                        </TableCell>
                        <TableCell>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="item.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                {{ item.is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </TableCell>
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
                    <TableRow v-if="paginatedAccounts.length === 0">
                        <TableCell colspan="6" class="py-8 text-center">
                            {{ searchQuery ? 'Tidak ada akun yang sesuai dengan pencarian Anda.' : 'Belum ada data akun.' }}
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Selected accounts debug info -->
            <div v-if="selectedAccountIds.length > 0" class="mt-4 text-sm text-gray-500">
                {{ selectedAccountIds.length }} akun terpilih: {{ selectedAccountIds.join(', ') }}
            </div>

            <!-- Pagination -->
            <div v-if="filteredAccounts.length > 0" class="mt-4 flex items-center justify-between">
                <div class="text-sm text-gray-500">Menampilkan {{ paginatedAccounts.length }} dari {{ filteredAccounts.length }} data</div>
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
                            {{ dialogMode === 'add' ? 'Tambah Akun' : 'Edit Akun' }}
                        </DialogTitle>
                        <DialogDescription>
                            Silakan isi formulir berikut untuk {{ dialogMode === 'add' ? 'menambahkan' : 'mengubah' }} data akun.
                        </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="handleSubmit" class="space-y-4">
                        <div>
                            <Label for="username" class="text-sm font-medium">Username</Label>
                            <Input
                                id="username"
                                v-model="formData.username"
                                type="text"
                                :disabled="dialogMode === 'edit'"
                                required
                                :class="{ 'border-red-500 focus:ring-red-500': validationErrors.username }"
                            />
                            <p v-if="validationErrors.username" class="mt-1 text-xs text-red-500">
                                {{ validationErrors.username }}
                            </p>
                        </div>

                        <div>
                            <Label for="password" class="text-sm font-medium">
                                Password {{ dialogMode === 'edit' ? '(Kosongkan jika tidak ingin mengubah)' : '' }}
                            </Label>
                            <Input
                                id="password"
                                v-model="formData.password"
                                type="password"
                                :required="dialogMode === 'add'"
                                :class="{ 'border-red-500 focus:ring-red-500': validationErrors.password }"
                            />
                            <p v-if="validationErrors.password" class="mt-1 text-xs text-red-500">
                                {{ validationErrors.password }}
                            </p>
                        </div>

                        <div>
                            <Label for="role" class="text-sm font-medium">Role</Label>
                            <Select v-model="formData.role">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Pilih role" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="admin">Admin</SelectItem>
                                    <SelectItem value="mahasiswa">Mahasiswa</SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="validationErrors.role" class="mt-1 text-xs text-red-500">
                                {{ validationErrors.role }}
                            </p>
                        </div>

    <div>
                            <Label for="status" class="text-sm font-medium">Status</Label>
                            <Select v-model="formData.is_active">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Pilih status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem :value="true">Aktif</SelectItem>
                                    <SelectItem :value="false">Nonaktif</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div v-if="formData.role === 'mahasiswa'">
                            <Label for="tenant" class="text-sm font-medium">Tenant</Label>
                            <Select v-model="formData.tenant_id">
                                <SelectTrigger class="w-full" :disabled="isLoadingTenants">
                                    <SelectValue :placeholder="isLoadingTenants ? 'Loading tenants...' : 'Pilih tenant'">
                                        {{ formData.tenant_id === null ? 'Tidak ada' :
                                           tenants.find(t => t.id === formData.tenant_id)?.name || 'Pilih tenant' }}
                                    </SelectValue>
                                </SelectTrigger>
                                <SelectContent>
                                    <div v-if="isLoadingTenants" class="px-2 py-4 text-center text-sm text-gray-500">
                                        Loading tenants...
                                    </div>
                                    <template v-else>
                                        <SelectItem :value="null">Tidak ada</SelectItem>
                                        <SelectItem v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                                            {{ tenant.name }}
                                        </SelectItem>
                                        <div v-if="tenants.length === 0" class="px-2 py-1 text-sm text-gray-500">
                                            Tidak ada tenant tersedia
                                        </div>
                                    </template>
                                </SelectContent>
                            </Select>
                            <p class="mt-1 text-xs text-gray-500">
                                Tenant dapat dikosongkan jika pengguna tidak terkait dengan tenant manapun
                            </p>
                            <p v-if="validationErrors.tenant_id" class="mt-1 text-xs text-red-500">
                                {{ validationErrors.tenant_id }}
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
                            Apakah Anda yakin ingin menghapus akun ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus akun secara permanen.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel @click="showDeleteDialog = false" :disabled="isSubmitting">Batal</AlertDialogCancel>
                        <AlertDialogAction
                            @click="handleDelete"
                            :disabled="isSubmitting"
                            :class="[
                                'bg-destructive text-destructive-foreground hover:bg-destructive/90',
                                isSubmitting ? 'opacity-75 cursor-not-allowed' : ''
                            ]"
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
                                Hapus
                            </template>
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>

            <!-- Status Update Confirmation Dialog -->
            <AlertDialog v-model:open="showStatusDialog">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Konfirmasi Perubahan Status</AlertDialogTitle>
                        <AlertDialogDescription>
                            Apakah Anda yakin ingin {{ newStatus ? 'mengaktifkan' : 'menonaktifkan' }} {{ selectedAccountIds.length }} akun yang dipilih?
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel @click="showStatusDialog = false" :disabled="isSubmitting">Batal</AlertDialogCancel>
                        <AlertDialogAction
                            @click="updateMultipleStatus"
                            :disabled="isSubmitting"
                            :class="[
                                newStatus ? 'bg-green-600 hover:bg-green-700' : 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
                                isSubmitting ? 'opacity-75 cursor-not-allowed' : ''
                            ]"
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
                                {{ newStatus ? 'Aktifkan' : 'Nonaktifkan' }}
                            </template>
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>

            <!-- Custom alert notification that doesn't rely on the Alert component -->
            <div
                v-if="alertState.show"
                class="fixed top-6 right-6 z-[9999] rounded-lg py-4 px-6 shadow-2xl"
                :class="alertState.type === 'success' ? 'bg-green-100 text-green-800 border-l-4 border-green-500' : 'bg-red-100 text-red-800 border-l-4 border-red-500'"
            >
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center">
                        <span v-if="alertState.type === 'success'" class="mr-2 text-lg font-bold text-green-600">✅</span>
                        <span v-else class="mr-2 text-lg font-bold text-red-600">❌</span>
                        <span class="font-medium text-base">{{ alertState.message }}</span>
                    </div>
                    <button
                        class="ml-4 text-gray-500 hover:text-gray-800 text-xl font-bold"
                        @click="alertState.show = false"
                    >
                        &times;
                    </button>
                </div>
            </div>
    </div>
    </AuthLayout>
</template>

