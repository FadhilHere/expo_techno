<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';
import Alert from '@/components/Alert.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Card } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious
} from '@/components/ui/carousel';

// Props and interfaces
interface Image {
    id: number;
    image_path: string;
    caption: string;
    image_url: string;
}

interface History {
    id: number;
    tahun_expo_id: number;
    title: string;
    content: string;
    cover_image: string;
    cover_image_url: string;
    published_at: string;
    images: Image[];
    tahun_expo: {
        id: number;
        tahun: string;
    };
}

const { histories, tahunExpoOptions } = defineProps<{
    histories: History[];
    tahunExpoOptions?: { id: number; tahun: string }[];
}>();

// State management
const alert = ref({
    show: false,
    type: 'success' as 'success' | 'error',
    message: '',
});

const showAddDialog = ref(false);
const showEditDialog = ref(false);
const showDeleteDialog = ref(false);
const isSubmitting = ref(false);
const selectedHistory = ref<History | null>(null);

const formData = ref({
    tahun_expo_id: '',
    title: '',
    content: '',
    cover_image: null as File | null,
    published_at: '',
    additional_images: [] as File[],
    captions: [] as string[],
});

const coverImagePreview = ref<string | null>(null);
const additionalImagePreviews = ref<string[]>([]);
const additionalImagesInput = ref<HTMLInputElement | null>(null);

// Add new state for tracking images to delete
const imagesToDelete = ref<number[]>([]);

const showDialog = computed({
    get: () => showAddDialog.value || showEditDialog.value,
    set: (value: boolean) => {
        if (!value) {
            showAddDialog.value = false;
            showEditDialog.value = false;
        }
    }
});

// Add view state
const viewMode = ref<'grid' | 'table'>('grid');

// Add search and pagination
const searchQuery = ref('');
const currentPage = ref(1);
const itemsPerPage = ref(10);

// Computed properties for table
const filteredHistories = computed(() => {
    return histories.filter(history =>
        history.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        history.content.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        history.tahun_expo.tahun.toString().includes(searchQuery.value)
    );
});

const paginatedHistories = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredHistories.value.slice(start, end);
});

const totalPages = computed(() =>
    Math.ceil(filteredHistories.value.length / itemsPerPage.value)
);

// Pagination methods
const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
};

const prevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

// Format date
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Methods
const handleCoverImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files.length > 0) {
        formData.value.cover_image = target.files[0];
        const reader = new FileReader();
        reader.onload = (e) => {
            coverImagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(target.files[0]);
    }
};

const handleAdditionalImagesChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files) {
        const newFiles = Array.from(target.files);

        // Append new files to existing ones
        formData.value.additional_images = [
            ...formData.value.additional_images,
            ...newFiles
        ];

        // Add new captions
        formData.value.captions = [
            ...formData.value.captions,
            ...new Array(newFiles.length).fill('')
        ];

        // Generate previews for new images
        newFiles.forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                additionalImagePreviews.value.push(e.target?.result as string);
            };
            reader.readAsDataURL(file);
        });

        // Reset input so the same file can be selected again
        target.value = '';
    }
};

// Reset form function
const resetForm = () => {
    formData.value = {
        tahun_expo_id: '',
        title: '',
        content: '',
        cover_image: null,
        published_at: '',
        additional_images: [],
        captions: [],
    };
    coverImagePreview.value = null;
    additionalImagePreviews.value = [];
    selectedHistory.value = null;
    imagesToDelete.value = [];
};

const openAddDialog = () => {
    resetForm();
    showAddDialog.value = true;
};

const openEditDialog = (history: History) => {
    resetForm(); // Reset first before setting new data
    selectedHistory.value = history;
    formData.value = {
        tahun_expo_id: history.tahun_expo_id.toString(),
        title: history.title,
        content: history.content,
        cover_image: null,
        published_at: new Date(history.published_at).toISOString().split('T')[0],
        additional_images: [],
        captions: [],
    };
    coverImagePreview.value = history.cover_image_url;
    showEditDialog.value = true;
};

const confirmDelete = (history: History) => {
    selectedHistory.value = history;
    showDeleteDialog.value = true;
};

const submitForm = async () => {
    if (isSubmitting.value) return;
    isSubmitting.value = true;

    try {
        const form = new FormData();
        form.append('tahun_expo_id', formData.value.tahun_expo_id);
        form.append('title', formData.value.title);
        form.append('content', formData.value.content);
        form.append('published_at', formData.value.published_at);

        if (formData.value.cover_image) {
            form.append('cover_image', formData.value.cover_image);
        }

        // Append images to delete
        imagesToDelete.value.forEach(imageId => {
            form.append('images_to_delete[]', imageId.toString());
        });

        formData.value.additional_images.forEach((image, index) => {
            form.append(`additional_images[]`, image);
            form.append(`captions[]`, formData.value.captions[index] || '');
        });

        if (selectedHistory.value) {
            await router.post(`/admin/expo-history/${selectedHistory.value.id}`, form);
        } else {
            await router.post('/admin/expo-history', form);
        }

        showAddDialog.value = false;
        showEditDialog.value = false;
        alert.value = {
            show: true,
            type: 'success',
            message: selectedHistory.value ? 'History berhasil diperbarui' : 'History berhasil ditambahkan',
        };
    } catch (err) {
        console.error('Error submitting form:', err);
        alert.value = {
            show: true,
            type: 'error',
            message: 'Terjadi kesalahan saat menyimpan data',
        };
    } finally {
        isSubmitting.value = false;
    }
};

const deleteHistory = async () => {
    if (!selectedHistory.value || isSubmitting.value) return;
    isSubmitting.value = true;

    try {
        await router.delete(`/admin/expo-history/${selectedHistory.value.id}`);
        showDeleteDialog.value = false;
        alert.value = {
            show: true,
            type: 'success',
            message: 'History berhasil dihapus',
        };
    } catch (err) {
        console.error('Error deleting history:', err);
        alert.value = {
            show: true,
            type: 'error',
            message: 'Terjadi kesalahan saat menghapus data',
        };
    } finally {
        isSubmitting.value = false;
    }
};

const addMoreImages = () => {
    if (additionalImagesInput.value) {
        additionalImagesInput.value.click();
    }
};

const removeAdditionalImage = (index: number) => {
    formData.value.additional_images.splice(index, 1);
    formData.value.captions.splice(index, 1);
    additionalImagePreviews.value.splice(index, 1);
};

const removeExistingImage = (imageId: number) => {
    if (!selectedHistory.value) return;

    // Add image ID to the list of images to delete
    imagesToDelete.value.push(imageId);

    // Remove image from UI only
    selectedHistory.value.images = selectedHistory.value.images.filter(img => img.id !== imageId);
};

// Watch for dialog close
watch(showDialog, (newValue) => {
    if (!newValue) {
        resetForm();
    }
});
</script>

<template>
    <AuthLayout title="Expo History" description="Kelola history expo technologia">
        <div class="p-6">
            <!-- Alert -->
            <Alert v-model:show="alert.show" :type="alert.type" :message="alert.message" />

            <!-- Header with View Toggle -->
            <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="text-2xl font-bold">Expo History</h1>
                <div class="flex items-center gap-4">
                    <div class="flex items-center space-x-2">
                        <Button
                            size="sm"
                            variant="outline"
                            :class="{ 'bg-primary text-primary-foreground': viewMode === 'grid' }"
                            @click="viewMode = 'grid'"
                        >
                            Grid
                        </Button>
                        <Button
                            size="sm"
                            variant="outline"
                            :class="{ 'bg-primary text-primary-foreground': viewMode === 'table' }"
                            @click="viewMode = 'table'"
                        >
                            Table
                        </Button>
                    </div>
                    <Button @click="openAddDialog">Tambah History</Button>
                </div>
            </div>

            <!-- Search Input -->
            <div class="mb-6">
                <Input
                    v-model="searchQuery"
                    placeholder="Cari history..."
                    class="max-w-sm"
                />
            </div>

            <!-- Grid View -->
            <div v-if="viewMode === 'grid'" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="history in paginatedHistories" :key="history.id" class="flex flex-col overflow-hidden">
                    <!-- Cover Image -->
                    <div class="relative aspect-video w-full">
                        <img
                            :src="history.cover_image_url"
                            :alt="history.title"
                            class="h-full w-full object-cover"
                            @error="e => e.target.src = '/assets/no_image.png'"
                        />
                    </div>

                    <div class="flex flex-col flex-grow p-6">
                        <div class="mb-4">
                            <h3 class="font-semibold">{{ history.title }}</h3>
                            <p class="text-sm text-gray-500">
                                Tahun Expo: {{ history.tahun_expo.tahun }}
                            </p>
                        </div>

                        <p class="mb-4 line-clamp-2 text-sm text-gray-600">{{ history.content }}</p>

                        <!-- Gallery Images Carousel -->
                        <div v-if="history.images.length > 0" class="mb-4">
                            <p class="mb-2 text-sm font-medium">Gallery ({{ history.images.length }} gambar)</p>
                            <Carousel class="w-full">
                                <CarouselContent>
                                    <CarouselItem v-for="image in history.images" :key="image.id" class="basis-1/2">
                                        <div class="relative aspect-square">
                                            <img
                                                :src="image.image_url"
                                                :alt="image.caption || 'Gallery image'"
                                                class="h-full w-full rounded-md object-cover"
                                                @error="e => e.target.src = '/assets/no_image.png'"
                                            />
                                            <div
                                                v-if="image.caption"
                                                class="absolute bottom-0 left-0 right-0 bg-black/50 p-1 text-xs text-white"
                                            >
                                                {{ image.caption }}
                                            </div>
                                        </div>
                                    </CarouselItem>
                                </CarouselContent>
                                <div class="flex justify-end gap-2 mt-2">
                                    <CarouselPrevious className="relative h-7 w-7" />
                                    <CarouselNext className="relative h-7 w-7" />
                                </div>
                            </Carousel>
                        </div>

                        <div class="mt-auto flex justify-end gap-2">
                            <Button variant="outline" size="sm" @click="openEditDialog(history)">
                                Edit
                            </Button>
                            <Button
                                variant="destructive"
                                size="sm"
                                @click="confirmDelete(history)"
                            >
                                Hapus
                            </Button>
                        </div>
                    </div>
                </Card>
            </div>

            <!-- Table View -->
            <div v-else class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Cover</TableHead>
                            <TableHead>Gallery</TableHead>
                            <TableHead>Informasi</TableHead>
                            <TableHead class="text-right">Aksi</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="history in paginatedHistories" :key="history.id">
                            <!-- Cover Image -->
                            <TableCell>
                                <div class="relative h-24 w-24">
                                    <img
                                        :src="history.cover_image_url"
                                        :alt="history.title"
                                        class="h-full w-full rounded object-cover"
                                        @error="e => e.target.src = '/assets/no_image.png'"
                                    />
                                </div>
                            </TableCell>

                            <!-- Gallery Images -->
                            <TableCell>
                                <div v-if="history.images.length > 0" class="w-48">
                                    <Carousel>
                                        <CarouselContent>
                                            <CarouselItem v-for="image in history.images" :key="image.id">
                                                <div class="relative aspect-square p-1">
                                                    <img
                                                        :src="image.image_url"
                                                        :alt="image.caption || 'Gallery image'"
                                                        class="h-full w-full rounded object-cover"
                                                        @error="e => e.target.src = '/assets/no_image.png'"
                                                    />
                                                    <div
                                                        v-if="image.caption"
                                                        class="absolute bottom-0 left-0 right-0 bg-black/50 p-1 text-xs text-white"
                                                    >
                                                        {{ image.caption }}
                                                    </div>
                                                </div>
                                            </CarouselItem>
                                        </CarouselContent>
                                        <div class="flex justify-end gap-1 mt-1">
                                            <CarouselPrevious className="h-6 w-6 relative" />
                                            <CarouselNext className="h-6 w-6 relative" />
                                        </div>
                                    </Carousel>
                                </div>
                                <p v-else class="text-sm text-gray-500">Tidak ada gambar tambahan</p>
                            </TableCell>

                            <!-- Information -->
                            <TableCell>
                                <div class="space-y-1">
                                    <p class="font-medium">{{ history.title }}</p>
                                    <p class="text-sm text-gray-500">Tahun: {{ history.tahun_expo.tahun }}</p>
                                    <p class="text-sm text-gray-500">Publikasi: {{ formatDate(history.published_at) }}</p>
                                    <p class="text-sm text-gray-500">{{ history.images.length }} gambar tambahan</p>
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ history.content }}</p>
                                </div>
                            </TableCell>

                            <!-- Actions -->
                            <TableCell class="text-right">
                                <div class="flex justify-end space-x-2">
                                    <Button variant="outline" size="sm" @click="openEditDialog(history)">
                                        Edit
                                    </Button>
                                    <Button
                                        variant="destructive"
                                        size="sm"
                                        @click="confirmDelete(history)"
                                    >
                                        Hapus
                                    </Button>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    Menampilkan {{ paginatedHistories.length }} dari {{ filteredHistories.length }} data
                </p>
                <div class="flex space-x-2">
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="currentPage === 1"
                        @click="prevPage"
                    >
                        Previous
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        :disabled="currentPage === totalPages"
                        @click="nextPage"
                    >
                        Next
                    </Button>
                </div>
            </div>

            <!-- Add/Edit Dialog -->
            <Dialog v-model:open="showDialog">
                <DialogContent class="sm:max-w-[600px] max-h-[80vh] overflow-y-auto">
                    <DialogHeader>
                        <DialogTitle>
                            {{ showEditDialog ? 'Edit History' : 'Tambah History' }}
                        </DialogTitle>
                        <DialogDescription>
                            {{ showEditDialog ? 'Edit informasi history yang sudah ada' : 'Tambahkan history expo baru' }}
                        </DialogDescription>
                    </DialogHeader>

                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div class="space-y-2">
                            <Label for="title">Judul</Label>
                            <Input
                                id="title"
                                v-model="formData.title"
                                required
                                placeholder="Masukkan judul history"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="tahun_expo">Tahun Expo</Label>
                            <select
                                id="tahun_expo"
                                v-model="formData.tahun_expo_id"
                                required
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <option value="" disabled>Pilih tahun expo</option>
                                <option
                                    v-for="option in tahunExpoOptions"
                                    :key="option.id"
                                    :value="option.id"
                                >
                                    {{ option.tahun }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <Label for="content">Konten</Label>
                            <Textarea
                                id="content"
                                v-model="formData.content"
                                required
                                placeholder="Tulis konten history"
                                rows="4"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="published_at">Tanggal Publikasi</Label>
                            <Input
                                id="published_at"
                                v-model="formData.published_at"
                                type="date"
                                required
                            />
                        </div>

                        <div class="space-y-2">
                            <Label for="cover_image">Cover Image</Label>
                            <Input
                                id="cover_image"
                                type="file"
                                accept="image/*"
                                @change="handleCoverImageChange"
                                :required="!selectedHistory"
                            />
                            <div v-if="coverImagePreview" class="mt-2">
                                <img
                                    :src="coverImagePreview"
                                    alt="Cover Preview"
                                    class="h-40 w-full rounded object-cover"
                                />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <Label for="additional_images">Gambar Tambahan</Label>
                            <div class="space-y-4">
                                <!-- Hidden file input - Changed to native input -->
                                <input
                                    ref="additionalImagesInput"
                                    id="additional_images"
                                    type="file"
                                    accept="image/*"
                                    multiple
                                    class="hidden"
                                    @change="handleAdditionalImagesChange"
                                />

                                <!-- Add images button -->
                                <Button
                                    type="button"
                                    variant="outline"
                                    class="w-full"
                                    @click="addMoreImages"
                                >
                                    <span v-if="!additionalImagePreviews.length">Tambah Gambar</span>
                                    <span v-else>Tambah Gambar Lainnya</span>
                                </Button>

                                <!-- Preview existing images when editing -->
                                <div v-if="selectedHistory?.images?.length" class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                                    <div
                                        v-for="image in selectedHistory.images"
                                        :key="image.id"
                                        class="relative group"
                                    >
                                        <img
                                            :src="image.image_url"
                                            :alt="image.caption"
                                            class="h-32 w-full rounded object-cover"
                                        />
                                        <Input
                                            v-model="image.caption"
                                            placeholder="Caption"
                                            class="mt-1"
                                        />
                                        <Button
                                            type="button"
                                            variant="destructive"
                                            size="sm"
                                            class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity"
                                            @click="removeExistingImage(image.id)"
                                        >
                                            ×
                                        </Button>
                                    </div>
                                </div>

                                <!-- Preview new images -->
                                <div v-if="additionalImagePreviews.length" class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                                    <div
                                        v-for="(preview, index) in additionalImagePreviews"
                                        :key="index"
                                        class="relative group"
                                    >
                                        <img
                                            :src="preview"
                                            alt="Preview"
                                            class="h-32 w-full rounded object-cover"
                                        />
                                        <Input
                                            v-model="formData.captions[index]"
                                            placeholder="Caption"
                                            class="mt-1"
                                        />
                                        <Button
                                            type="button"
                                            variant="destructive"
                                            size="sm"
                                            class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-opacity"
                                            @click="removeAdditionalImage(index)"
                                        >
                                            ×
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <DialogFooter>
                            <Button
                                type="button"
                                variant="outline"
                                @click="resetForm(); showDialog = false"
                            >
                                Batal
                            </Button>
                            <Button type="submit" :disabled="isSubmitting">
                                {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>

            <!-- Delete Confirmation Dialog -->
            <AlertDialog v-model:open="showDeleteDialog">
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Konfirmasi Hapus</AlertDialogTitle>
                        <AlertDialogDescription>
                            Apakah Anda yakin ingin menghapus history ini? Tindakan ini tidak dapat dibatalkan.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel @click="showDeleteDialog = false">
                            Batal
                        </AlertDialogCancel>
                        <AlertDialogAction
                            @click="deleteHistory"
                            class="bg-destructive text-destructive-foreground hover:bg-destructive/90"
                        >
                            {{ isSubmitting ? 'Menghapus...' : 'Hapus' }}
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>
    </AuthLayout>
</template>
