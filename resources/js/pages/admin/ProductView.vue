<script setup lang="ts">
import { ref, nextTick, computed } from 'vue'
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
import { Textarea } from '@/components/ui/textarea'
import { Plus, Pencil, Trash2, X, ArrowLeft } from 'lucide-vue-next'
import Alert from '@/components/Alert.vue'

interface Product {
    id: number
    nama_produk: string
    harga: number
    deskripsi: string
    foto_produk: string | null
    foto_produk_url: string
    tenant_id: number
}

interface Tenant {
    id: number
    nama_tenant: string
    deskripsi: string
    tahun_expo: string
    kategori: string
}

// Props definition
const props = defineProps<{
    products: Product[]
    tenant: Tenant
}>()

// Compute breadcrumb items for dynamic breadcrumb in AuthLayout
const breadcrumbItems = computed(() => {
    return [
        { label: 'Menu', href: '/' },
        { label: 'Tenant', href: '/admin/tenant' },
        { label: props.tenant.nama_tenant, href: null }
    ]
})

// State management
const showDialog = ref(false)
const dialogMode = ref<'add' | 'edit'>('add')
const formData = ref({
    id: '',
    nama_produk: '',
    harga: '',
    deskripsi: '',
    foto_produk: null as File | null,
    remove_foto: false
})
const fotoPreview = ref<string | null>(null)
const alert = ref({
    show: false,
    type: 'success' as 'success' | 'error',
    message: ''
})
const deleteId = ref<number | null>(null)
const showDeleteDialog = ref(false)
const validationErrors = ref<Record<string, string>>({})
const isSubmitting = ref(false)

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(price)
}

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
        nama_produk: '',
        harga: '',
        deskripsi: '',
        foto_produk: null,
        remove_foto: false
    }
    fotoPreview.value = null
    validationErrors.value = {}
    showDialog.value = true
}

const openEditDialog = (data: Product) => {
    dialogMode.value = 'edit'

    // Reset form data first
    formData.value = {
        id: String(data.id),
        nama_produk: data.nama_produk || '',
        harga: String(data.harga) || '',
        deskripsi: data.deskripsi || '',
        foto_produk: null,
        remove_foto: false
    }

    fotoPreview.value = data.foto_produk_url
    validationErrors.value = {}
    showDialog.value = true
}

const confirmDelete = (id: number) => {
    deleteId.value = id
    showDeleteDialog.value = true
}

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement
    if (target.files && target.files.length > 0) {
        const file = target.files[0]
        formData.value.foto_produk = file
        formData.value.remove_foto = false

        // Create preview
        const reader = new FileReader()
        reader.onload = (e) => {
            fotoPreview.value = e.target?.result as string
        }
        reader.readAsDataURL(file)
    }
}

const removeFoto = () => {
    formData.value.foto_produk = null
    formData.value.remove_foto = true
    fotoPreview.value = null
}

const validateForm = () => {
    const errors: Record<string, string> = {}

    // Validasi field required
    if (!formData.value.nama_produk || formData.value.nama_produk.trim() === '') {
        errors.nama_produk = 'Nama produk harus diisi.'
    }

    if (!formData.value.harga) {
        errors.harga = 'Harga produk harus diisi.'
    } else if (isNaN(Number(formData.value.harga)) || Number(formData.value.harga) < 0) {
        errors.harga = 'Harga produk harus berupa angka dan tidak boleh negatif.'
    }

    validationErrors.value = errors
    return Object.keys(errors).length === 0
}

// Handle form submission
const handleSubmit = () => {
    if (isSubmitting.value) return

    // Validasi form sebelum mengirim
    if (!validateForm()) {
        return
    }

    isSubmitting.value = true

    // Penting: apply trim to text fields
    const namaProdukValue = formData.value.nama_produk.trim()
    const hargaValue = formData.value.harga
    const deskripsiValue = formData.value.deskripsi.trim()

    if (dialogMode.value === 'add') {
        // Untuk ADD, gunakan FormData normal
        const submitData = new FormData()
        submitData.append('nama_produk', namaProdukValue)
        submitData.append('harga', hargaValue)
        submitData.append('deskripsi', deskripsiValue)

        if (formData.value.foto_produk) {
            submitData.append('foto_produk', formData.value.foto_produk)
        }

        router.post(`/admin/tenant/${props.tenant.id}/products`, submitData, {
            onSuccess: () => {
                showDialog.value = false
                showAlert('success', 'Produk berhasil ditambahkan')
                isSubmitting.value = false
            },
            onError: (errors) => {
                validationErrors.value = errors
                showAlert('error', 'Terjadi kesalahan saat menambah produk')
                isSubmitting.value = false
                console.error('Validation errors:', errors)
            }
        })
    } else {
        // Untuk UPDATE
        const id = formData.value.id

        // Selalu gunakan FormData untuk update agar bisa menangani file
        const submitData = new FormData()

        // Tambahkan data form
        submitData.append('nama_produk', namaProdukValue)
        submitData.append('harga', hargaValue)
        submitData.append('deskripsi', deskripsiValue)
        submitData.append('_method', 'PUT') // Tambahkan _method untuk spoofing PUT

        // Handle file dan remove_foto
        if (formData.value.foto_produk) {
            submitData.append('foto_produk', formData.value.foto_produk)
        }

        // PENTING: Hanya append remove_foto jika nilainya true
        if (formData.value.remove_foto === true) {
            submitData.append('remove_foto', '1')
        }

        // Gunakan router.post dengan _method: 'PUT' untuk file upload
        router.post(`/admin/tenant/${props.tenant.id}/products/${id}`, submitData, {
            onSuccess: () => {
                showDialog.value = false
                showAlert('success', 'Produk berhasil diupdate')
                isSubmitting.value = false
            },
            onError: (errors) => {
                validationErrors.value = errors
                showAlert('error', 'Terjadi kesalahan saat update produk')
                isSubmitting.value = false
                console.error('Validation errors on update:', errors)
            }
        })
    }
}

const handleDelete = () => {
    if (deleteId.value === null) return

    router.delete(`/admin/tenant/${props.tenant.id}/products/${deleteId.value}`, {
        onSuccess: () => {
            showDeleteDialog.value = false
            showAlert('success', 'Produk berhasil dihapus')
        },
        onError: () => {
            showDeleteDialog.value = false
            showAlert('error', 'Gagal menghapus produk')
        }
    })
}

const backToTenants = () => {
    router.visit('/admin/tenant')
}
</script>

<template>
    <AuthLayout
        :title="tenant.nama_tenant + ' - Produk'"
        :description="'Kelola produk untuk tenant ' + tenant.nama_tenant"
        :breadcrumbs="breadcrumbItems"
    >
        <div class="space-y-6">
            <!-- Back button and title -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center gap-2">
                    <Button variant="outline" size="icon" @click="backToTenants">
                        <ArrowLeft class="h-4 w-4" />
                    </Button>
                    <h1 class="text-2xl font-bold">Produk {{ tenant.nama_tenant }}</h1>
                </div>
                <Button @click="openAddDialog">
                    <Plus class="w-4 h-4 mr-2" />
                    Tambah Produk
                </Button>
            </div>

            <!-- Tenant info card -->
            <div class="bg-card rounded-lg p-4 shadow">
                <h2 class="font-semibold text-lg mb-2">Informasi Tenant</h2>

                <!-- Grid untuk informasi dasar (3 kolom) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                    <div>
                        <p class="text-sm text-muted-foreground">Nama Tenant</p>
                        <p>{{ tenant.nama_tenant }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">Tahun Expo</p>
                        <p>{{ tenant.tahun_expo }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-muted-foreground">Kategori</p>
                        <p>{{ tenant.kategori }}</p>
                    </div>
                </div>

                <!-- Deskripsi tenant (1 baris penuh) -->
                <div v-if="tenant.deskripsi" class="mt-2">
                    <p class="text-sm text-muted-foreground">Deskripsi</p>
                    <p class="mt-1">{{ tenant.deskripsi }}</p>
                </div>
            </div>

            <!-- Products table -->
            <Table v-if="products.length > 0">
                <TableHeader>
                    <TableRow>
                        <TableHead>No</TableHead>
                        <TableHead>Foto</TableHead>
                        <TableHead>Nama Produk</TableHead>
                        <TableHead>Harga</TableHead>
                        <TableHead>Deskripsi</TableHead>
                        <TableHead>Aksi</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(item, index) in products" :key="item.id">
                        <TableCell>{{ index + 1 }}</TableCell>
                        <TableCell>
                            <img
                                :src="item.foto_produk_url"
                                alt="Foto Produk"
                                class="w-16 h-16 object-cover rounded"
                            />
                        </TableCell>
                        <TableCell>{{ item.nama_produk }}</TableCell>
                        <TableCell>{{ formatPrice(item.harga) }}</TableCell>
                        <TableCell class="max-w-xs truncate">{{ item.deskripsi }}</TableCell>
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

            <div v-else class="text-center py-8">
                <p class="text-muted-foreground">Belum ada produk untuk tenant ini. Klik 'Tambah Produk' untuk mulai menambahkan produk.</p>
            </div>

            <!-- Form Dialog -->
            <Dialog v-model:open="showDialog">
                <DialogContent class="max-w-2xl">
                    <DialogHeader>
                        <DialogTitle>
                            {{ dialogMode === 'add' ? 'Tambah Produk' : 'Edit Produk' }}
                        </DialogTitle>
                        <DialogDescription>
                            Silakan isi formulir berikut untuk {{ dialogMode === 'add' ? 'menambahkan' : 'mengubah' }} produk.
                        </DialogDescription>
                    </DialogHeader>
                    <form @submit.prevent="handleSubmit" class="space-y-4">
                        <div>
                            <Label for="nama-produk-input" class="text-sm font-medium">Nama Produk</Label>
                            <Input
                                id="nama-produk-input"
                                v-model="formData.nama_produk"
                                type="text"
                                required
                                :class="{'border-red-500 focus:ring-red-500': validationErrors.nama_produk}"
                            />
                            <p v-if="validationErrors.nama_produk" class="text-red-500 text-xs mt-1">
                                {{ validationErrors.nama_produk }}
                            </p>
                        </div>

                        <div>
                            <Label for="harga-input" class="text-sm font-medium">Harga</Label>
                            <Input
                                id="harga-input"
                                v-model="formData.harga"
                                type="number"
                                min="0"
                                required
                                :class="{'border-red-500 focus:ring-red-500': validationErrors.harga}"
                            />
                            <p v-if="validationErrors.harga" class="text-red-500 text-xs mt-1">
                                {{ validationErrors.harga }}
                            </p>
                        </div>

                        <div>
                            <Label for="deskripsi" class="text-sm font-medium">Deskripsi</Label>
                            <Textarea
                                id="deskripsi"
                                v-model="formData.deskripsi"
                                rows="3"
                                :class="{'border-red-500 focus:ring-red-500': validationErrors.deskripsi}"
                            />
                            <p v-if="validationErrors.deskripsi" class="text-red-500 text-xs mt-1">
                                {{ validationErrors.deskripsi }}
                            </p>
                        </div>

                        <div>
                            <Label for="foto" class="text-sm font-medium">Foto Produk</Label>

                            <!-- Foto Preview -->
                            <div v-if="fotoPreview" class="mt-2 mb-3">
                                <div class="relative inline-block">
                                    <img
                                        :src="fotoPreview"
                                        alt="Foto Preview"
                                        class="w-32 h-32 object-cover rounded border"
                                    />
                                    <button
                                        type="button"
                                        @click="removeFoto"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none"
                                    >
                                        <X class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>

                            <!-- File Upload -->
                            <Input
                                id="foto"
                                type="file"
                                accept="image/*"
                                @change="handleFileChange"
                                class="mt-1"
                                :class="{'border-red-500 focus:ring-red-500': validationErrors.foto_produk}"
                            />
                            <p v-if="validationErrors.foto_produk" class="text-red-500 text-xs mt-1">
                                {{ validationErrors.foto_produk }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Ukuran maksimal 2MB. Format: JPG, PNG, GIF</p>
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
                            Apakah Anda yakin ingin menghapus produk ini?
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
