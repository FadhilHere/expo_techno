<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthLayout from '@/layouts/AuthLayout.vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Switch } from '@/components/ui/switch'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import Alert from '@/components/Alert.vue'

defineProps({
  feedbacks: {
    type: Array as () => {
      id: number
      tenant: {
        id: number
        name: string
        logo: string
      }
      tahun_expo: {
        id: number
        tahun: string
      }
      link_type: string
      link_url: string
      description: string
      is_active: boolean
    }[],
    required: true
  },
  tenants: {
    type: Array as () => {
      id: number
      name: string
      logo: string
    }[],
    required: true
  },
  tahunExpoOptions: {
    type: Array as () => {
      id: number
      tahun: string
    }[],
    required: true
  },
  errors: {
    type: Object,
    default: () => ({})
  }
})

const isEditing = ref(false)
const showDeleteModal = ref(false)
const selectedFeedback = ref<any>(null)

const form = useForm({
  tenant_id: '',
  tahun_expo_id: '',
  link_type: '',
  link_url: '',
  description: '',
  is_active: true
})

const showAlert = ref(false)
const alertMessage = ref('')
const alertType = ref<'success' | 'error'>('success')

const handleSubmit = () => {
  if (isEditing.value) {
    form.put(route('tenant-feedback.update', selectedFeedback.value.id), {
      onSuccess: () => {
        resetForm()
        showSuccessAlert('Feedback berhasil diperbarui')
      },
      onError: () => {
        showErrorAlert('Gagal memperbarui feedback')
      }
    })
  } else {
    form.post(route('tenant-feedback.store'), {
      onSuccess: () => {
        resetForm()
        showSuccessAlert('Feedback berhasil ditambahkan')
      },
      onError: () => {
        showErrorAlert('Gagal menambahkan feedback')
      }
    })
  }
}

const editFeedback = (feedback: any) => {
  isEditing.value = true
  selectedFeedback.value = feedback
  form.tenant_id = feedback.tenant.id
  form.tahun_expo_id = feedback.tahun_expo.id
  form.link_type = feedback.link_type
  form.link_url = feedback.link_url
  form.description = feedback.description
  form.is_active = feedback.is_active
}

const resetForm = () => {
  isEditing.value = false
  selectedFeedback.value = null
  form.reset()
}

const confirmDelete = (feedback: any) => {
  selectedFeedback.value = feedback
  showDeleteModal.value = true
}

const deleteFeedback = () => {
  form.delete(route('tenant-feedback.destroy', selectedFeedback.value.id), {
    onSuccess: () => {
      showDeleteModal.value = false
      selectedFeedback.value = null
      showSuccessAlert('Feedback berhasil dihapus')
    },
    onError: () => {
      showErrorAlert('Gagal menghapus feedback')
    }
  })
}

const getLinkTypeColor = (type: string) => {
  const colors = {
    youtube: 'destructive',
    instagram: 'purple',
    tiktok: 'blue'
  }
  return colors[type as keyof typeof colors] || 'secondary'
}

const showSuccessAlert = (message: string) => {
  alertType.value = 'success'
  alertMessage.value = message
  showAlert.value = true
}

const showErrorAlert = (message: string) => {
  alertType.value = 'error'
  alertMessage.value = message
  showAlert.value = true
}
</script>

<template>
    <AuthLayout
      title="Kelola Tenant Feedback"
      description="Kelola feedback dari tenant berupa link video atau sosial media"
    >
      <div class="container">
        <Alert
          v-model:show="showAlert"
          :type="alertType"
          :message="alertMessage"
        />
        <!-- Form Section -->
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm p-6 mb-6">
          <h2 class="text-lg font-semibold mb-4">{{ isEditing ? 'Edit' : 'Tambah' }} Feedback</h2>
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <Label>Tenant</Label>
                <Select v-model="form.tenant_id">
                  <SelectTrigger>
                    <SelectValue placeholder="Pilih Tenant" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem v-for="tenant in tenants" :key="tenant.id" :value="tenant.id">
                      {{ tenant.name }}
                    </SelectItem>
                  </SelectContent>
                </Select>
                <p v-if="errors.tenant_id" class="text-sm text-destructive">
                  {{ errors.tenant_id }}
                </p>
              </div>

              <div class="space-y-2">
                <Label>Tahun Expo</Label>
                <Select v-model="form.tahun_expo_id">
                  <SelectTrigger>
                    <SelectValue placeholder="Pilih Tahun" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem v-for="tahun in tahunExpoOptions" :key="tahun.id" :value="tahun.id">
                      {{ tahun.tahun }}
                    </SelectItem>
                  </SelectContent>
                </Select>
                <p v-if="errors.tahun_expo_id" class="text-sm text-destructive">
                  {{ errors.tahun_expo_id }}
                </p>
              </div>

              <div class="space-y-2">
                <Label>Jenis Link</Label>
                <Select v-model="form.link_type">
                  <SelectTrigger>
                    <SelectValue placeholder="Pilih Jenis" />
                  </SelectTrigger>
                  <SelectContent>
                    <SelectItem value="youtube">YouTube</SelectItem>
                    <SelectItem value="instagram">Instagram</SelectItem>
                    <SelectItem value="tiktok">TikTok</SelectItem>
                  </SelectContent>
                </Select>
                <p v-if="errors.link_type" class="text-sm text-destructive">
                  {{ errors.link_type }}
                </p>
              </div>

              <div class="space-y-2">
                <Label>URL</Label>
                <Input
                  type="url"
                  v-model="form.link_url"
                  placeholder="https://"
                />
                <p v-if="errors.link_url" class="text-sm text-destructive">
                  {{ errors.link_url }}
                </p>
              </div>
            </div>

            <div class="space-y-2">
              <Label>Deskripsi</Label>
              <Textarea
                v-model="form.description"
                placeholder="Deskripsi feedback"
                :rows="3"
              />
              <p v-if="errors.description" class="text-sm text-destructive">
                {{ errors.description }}
              </p>
            </div>

            <div class="flex items-center space-x-2">
              <Switch v-model="form.is_active" />
              <Label>Aktif</Label>
            </div>

            <div class="flex justify-end space-x-2">
              <Button
                v-if="isEditing"
                type="button"
                variant="outline"
                @click="resetForm"
              >
                Batal
              </Button>
              <Button type="submit">
                {{ isEditing ? 'Simpan Perubahan' : 'Tambah Feedback' }}
              </Button>
            </div>
          </form>
        </div>

        <!-- Table Section -->
        <div class="rounded-lg border bg-card text-card-foreground">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Tenant</TableHead>
                <TableHead>Tahun</TableHead>
                <TableHead>Jenis</TableHead>
                <TableHead>Status</TableHead>
                <TableHead>Aksi</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="feedback in feedbacks" :key="feedback.id">
                <TableCell>{{ feedback.tenant.name }}</TableCell>
                <TableCell>{{ feedback.tahun_expo.tahun }}</TableCell>
                <TableCell>
                  <Badge :variant="getLinkTypeColor(feedback.link_type)">
                    {{ feedback.link_type }}
                  </Badge>
                </TableCell>
                <TableCell>
                  <Badge :variant="feedback.is_active ? 'default' : 'secondary'">
                    {{ feedback.is_active ? 'Aktif' : 'Nonaktif' }}
                  </Badge>
                </TableCell>
                <TableCell>
                  <div class="flex space-x-2">
                    <Button
                      variant="outline"
                      size="sm"
                      @click="editFeedback(feedback)"
                    >
                      Edit
                    </Button>
                    <Button
                      variant="destructive"
                      size="sm"
                      @click="confirmDelete(feedback)"
                    >
                      Hapus
                    </Button>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </div>

      <!-- Delete Confirmation Dialog -->
      <Dialog :open="showDeleteModal" @update:open="showDeleteModal = $event">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Konfirmasi Hapus</DialogTitle>
            <DialogDescription>
              Apakah Anda yakin ingin menghapus feedback ini? Tindakan ini tidak dapat dibatalkan.
            </DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <Button
              variant="outline"
              @click="showDeleteModal = false"
            >
              Batal
            </Button>
            <Button
              variant="destructive"
              @click="deleteFeedback"
            >
              Hapus
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </AuthLayout>
  </template>
