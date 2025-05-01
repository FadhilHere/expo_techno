<script setup lang="ts">
import { ref, nextTick } from 'vue'
import { router } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog'
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { Button } from '@/components/ui/button'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Plus, Pencil, Trash2 } from 'lucide-vue-next'
import Alert from '@/components/Alert.vue'

interface KategoriTenant {
    id: number
    nama_kategori: string
}

defineProps<{
    kategoriTenant: KategoriTenant[]
}>()

// State management
const showDialog = ref(false)
const dialogMode = ref<'add' | 'edit'>('add')
const formData = ref({
    id: '',
    nama_kategori: ''
})
const alert = ref({
    show: false,
    type: 'success' as 'success' | 'error',
    message: ''
})
const deleteId = ref<number | null>(null)
const showDeleteDialog = ref(false)
const validationErrors = ref<Record<string, string>>({})
const isSubmitting = ref(false)

const showAlert = (type: 'success' | 'error', message: string) => {
    alert.value = {
        show: true,
        type,
        message
    }
    setTimeout(() => {
        alert.value.show = false
    }, 5000) // Otomatis hilang setelah 5 detik
}

// Form handlers
const openAddDialog = () => {
    dialogMode.value = 'add'
    formData.value = {
        id: '',
        nama_kategori: ''
    }
    validationErrors.value = {}
    showDialog.value = true
}

const openEditDialog = (data: KategoriTenant) => {
    dialogMode.value = 'edit'
    formData.value = {
        id: String(data.id),
        nama_kategori: data.nama_kategori || ''
    }
    validationErrors.value = {}
    showDialog.value = true

    // Force refresh of input values
    nextTick(() => {
        const namaInput = document.getElementById('nama-kategori-input') as HTMLInputElement
        if (namaInput) {
            namaInput.value = formData.value.nama_kategori
        }
    })
}

const confirmDelete = (id: number) => {
    deleteId.value = id
    showDeleteDialog.value = true
}

const validateForm = () => {
    const errors: Record<string, string> = {}

    // Validate nama_kategori is not empty
    if (!formData.value.nama_kategori || formData.value.nama_kategori.trim() === '') {
        errors.nama_kategori = 'The nama kategori field is required.'
    }

    validationErrors.value = errors
    return Object.keys(errors).length === 0
}

const handleSubmit = () => {
    if (isSubmitting.value) return

    // Validate form before submitting
    if (!validateForm()) {
        return
    }

    isSubmitting.value = true

    // Apply trim to nama_kategori field
    const namaKategoriValue = formData.value.nama_kategori.trim()

    if (dialogMode.value === 'add') {
        router.post('/admin/kategori-tenant', {
            nama_kategori: namaKategoriValue
        }, {
            onSuccess: () => {
                showDialog.value = false
                showAlert('success', 'Data berhasil ditambahkan')
                isSubmitting.value = false
            },
            onError: (errors) => {
                validationErrors.value = errors
                showAlert('error', 'Terjadi kesalahan saat menambah data')
                isSubmitting.value = false
                console.error('Validation errors:', errors)
            }
        })
    } else {
        // For UPDATE
        const id = formData.value.id

        router.put(`/admin/kategori-tenant/${id}`, {
            nama_kategori: namaKategoriValue
        }, {
            onSuccess: () => {
                showDialog.value = false
                showAlert('success', 'Data berhasil diupdate')
                isSubmitting.value = false
            },
            onError: (errors) => {
                validationErrors.value = errors
                showAlert('error', 'Terjadi kesalahan saat update data')
                isSubmitting.value = false
                console.error('Validation errors on update:', errors)
            }
        })
    }
}

const handleDelete = () => {
    if (deleteId.value === null) return

    router.delete(`/admin/kategori-tenant/${deleteId.value}`, {
        onSuccess: () => {
            showDeleteDialog.value = false
            showAlert('success', 'Data berhasil dihapus')
        },
        onError: () => {
            showDeleteDialog.value = false
            showAlert('error', 'Gagal menghapus data')
        }
    })
}
</script>

<template>
    <AuthLayout
        title="Kategori Tenant"
        description="Kelola data kategori tenant"
    >
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Kategori Tenant</h1>
                <Button @click="openAddDialog">
                    <Plus class="w-4 h-4 mr-2" />
                    Tambah Kategori
                </Button>
            </div>

            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>No</TableHead>
                        <TableHead>Nama Kategori</TableHead>
                        <TableHead>Aksi</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(item, index) in kategoriTenant" :key="item.id">
                        <TableCell>{{ index + 1 }}</TableCell>
                        <TableCell>{{ item.nama_kategori }}</TableCell>
                        <TableCell>
                            <div class="flex space-x-2">
                                <Button variant="outline" size="icon" @click="openEditDialog(item)">
                                    <Pencil class="w-4 h-4" />
                                </Button>
                                <Button variant="destructive" size="icon" @click="confirmDelete(item.id)">
                                    <Trash2 class="w-4 h-4" />
                                </Button>
                            </div>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>

            <!-- Form Dialog -->
            <Dialog v-model:open="showDialog">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>
                            {{ dialogMode === 'add' ? 'Tambah Kategori Tenant' : 'Edit Kategori Tenant' }}
                        </DialogTitle>
                        <DialogDescription>
                            Silakan isi formulir berikut untuk {{ dialogMode === 'add' ? 'menambahkan' : 'mengubah' }} data kategori tenant.
                        </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="handleSubmit" class="space-y-4">
                        <div>
                            <Label for="nama-kategori-input" class="text-sm font-medium">Nama Kategori</Label>
                            <Input
                                id="nama-kategori-input"
                                v-model="formData.nama_kategori"
                                type="text"
                                required
                                :class="{'border-red-500 focus:ring-red-500': validationErrors.nama_kategori}"
                                :value="formData.nama_kategori"
                            />
                            <p v-if="validationErrors.nama_kategori" class="text-red-500 text-xs mt-1">
                                {{ validationErrors.nama_kategori }}
                            </p>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <Button type="button" variant="outline" @click="showDialog = false">
                                Batal
                            </Button>
                            <Button
                                type="submit"
                                :disabled="isSubmitting"
                                :class="{'opacity-75 cursor-not-allowed': isSubmitting}"
                            >
                                <template v-if="isSubmitting">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
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
                            Apakah Anda yakin ingin menghapus data ini?
                            Tindakan ini tidak dapat dibatalkan dan akan menghapus data secara permanen.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel @click="showDeleteDialog = false">Batal</AlertDialogCancel>
                        <AlertDialogAction @click="handleDelete" class="bg-destructive text-destructive-foreground hover:bg-destructive/90">Hapus</AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>

            <!-- Alert Notification -->
            <Alert
                v-model:show="alert.show"
                :type="alert.type"
                :message="alert.message"
            />
        </div>
    </AuthLayout>
</template>
