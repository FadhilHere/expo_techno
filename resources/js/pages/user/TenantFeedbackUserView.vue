<script setup lang="ts">
import { ref, computed } from 'vue'
import UserLayout from '@/layouts/UserLayout.vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import Alert from '@/components/Alert.vue'

interface TahunExpo {
  id: number
  tahun: string
}

interface Feedback {
  id: number
  tenant_name: string
  tenant_logo: string
  tahun_expo: {
    id: number
    tahun: string
  }
  link_type: string
  link_url: string
  description: string
}

const props = defineProps<{
  feedbacks: Feedback[]
  tahunExpoOptions: TahunExpo[]
}>()

const showAlert = ref(false)
const alertMessage = ref('')
const alertType = ref<'success' | 'error'>('success')

const selectedYear = ref('')
const selectedType = ref('')

const linkTypes = [
  { value: '', label: 'Semua' },
  { value: 'youtube', label: 'YouTube' },
  { value: 'instagram', label: 'Instagram' },
  { value: 'tiktok', label: 'TikTok' }
]

const filteredFeedbacks = computed(() => {
  return props.feedbacks.filter(feedback => {
    const yearMatch = !selectedYear.value || feedback.tahun_expo.id.toString() === selectedYear.value
    const typeMatch = !selectedType.value || feedback.link_type === selectedType.value
    return yearMatch && typeMatch
  })
})

const filterByYear = async () => {
  try {
    if (selectedYear.value) {
      const response = await fetch(`/api/tenant-feedback/year/${selectedYear.value}`)
      const data = await response.json()

      if (data.status === 'success') {
        // Handle success - will be managed by Inertia refresh
      } else {
        showErrorAlert('Gagal memuat data berdasarkan tahun')
      }
    }
  } catch {
    showErrorAlert('Terjadi kesalahan saat memfilter data')
  }
}

const filterByType = async (type: string) => {
  try {
    selectedType.value = type === selectedType.value ? '' : type

    if (selectedType.value) {
      const response = await fetch(`/api/tenant-feedback/type/${selectedType.value}`)
      const data = await response.json()

      if (data.status === 'success') {
        // Handle success - will be managed by Inertia refresh
      } else {
        showErrorAlert('Gagal memuat data berdasarkan tipe')
      }
    }
  } catch {
    showErrorAlert('Terjadi kesalahan saat memfilter data')
  }
}

const showErrorAlert = (message: string) => {
  alertType.value = 'error'
  alertMessage.value = message
  showAlert.value = true
}

const getYouTubeEmbedUrl = (url: string) => {
  const videoId = url.match(/(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/user\/\S+|\/ytscreeningroom\?v=|\/sandalsResorts#\w\/\w\/.*\/))([^\/&\?]*)/)?.[1]
  return videoId ? `https://www.youtube.com/embed/${videoId}` : ''
}

const getInstagramEmbedUrl = (url: string) => {
  const postId = url.split('/').slice(-2)[0]
  return `https://www.instagram.com/p/${postId}/embed`
}

const getTikTokEmbedUrl = (url: string) => {
  const videoId = url.split('/').pop()
  return `https://www.tiktok.com/embed/v2/${videoId}`
}

const getLinkTypeColor = (type: string) => {
  const colors = {
    youtube: 'destructive',
    instagram: 'purple',
    tiktok: 'blue'
  }
  return colors[type as keyof typeof colors] || 'secondary'
}
</script>

<template>
  <UserLayout>
    <div class="container mx-auto px-4 py-8">
      <Alert
        v-model:show="showAlert"
        :type="alertType"
        :message="alertMessage"
      />

      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Galeri Tenant</h1>
        <p class="mt-2 text-gray-600">
          Lihat berbagai konten menarik dari tenant-tenant kami
        </p>
      </div>

      <!-- Filter Section -->
      <div class="mb-8 bg-white p-4 rounded-lg shadow">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
          <div class="w-full sm:w-48">
            <select
              v-model="selectedYear"
              class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"
              @change="filterByYear"
            >
              <option value="">Semua Tahun</option>
              <option
                v-for="tahun in tahunExpoOptions"
                :key="tahun.id"
                :value="tahun.id"
              >
                {{ tahun.tahun }}
              </option>
            </select>
          </div>

          <div class="flex flex-wrap gap-2">
            <Button
              v-for="type in linkTypes"
              :key="type.value"
              :variant="selectedType === type.value ? type.value || 'default' : 'outline'"
              @click="filterByType(type.value)"
            >
              {{ type.label }}
            </Button>
          </div>
        </div>
      </div>

      <!-- Content Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="feedback in filteredFeedbacks"
          :key="feedback.id"
          class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300"
        >
          <!-- Content Header -->
          <div class="p-4 border-b">
            <div class="flex items-center space-x-3">
              <img
                :src="feedback.tenant_logo"
                :alt="feedback.tenant_name"
                class="w-12 h-12 rounded-full object-cover border-2 border-gray-200"
              />
              <div>
                <h3 class="font-semibold text-gray-900">
                  {{ feedback.tenant_name }}
                </h3>
                <p class="text-sm text-gray-500">
                  Expo {{ feedback.tahun_expo.tahun }}
                </p>
              </div>
              <Badge
                :variant="getLinkTypeColor(feedback.link_type)"
                class="ml-auto"
              >
                {{ feedback.link_type }}
              </Badge>
            </div>
          </div>

          <!-- Embedded Content -->
          <div class="relative pt-[56.25%] bg-gray-100">
            <div
              v-if="feedback.link_type === 'youtube'"
              class="absolute inset-0"
            >
              <iframe
                :src="getYouTubeEmbedUrl(feedback.link_url)"
                class="w-full h-full"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            </div>
            <div
              v-else-if="feedback.link_type === 'instagram'"
              class="absolute inset-0"
            >
              <iframe
                :src="getInstagramEmbedUrl(feedback.link_url)"
                class="w-full h-full"
                frameborder="0"
                scrolling="no"
                allowtransparency="true"
              ></iframe>
            </div>
            <div
              v-else-if="feedback.link_type === 'tiktok'"
              class="absolute inset-0"
            >
              <iframe
                :src="getTikTokEmbedUrl(feedback.link_url)"
                class="w-full h-full"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
              ></iframe>
            </div>
          </div>

          <!-- Description -->
          <div class="p-4">
            <p class="text-gray-600 text-sm">
              {{ feedback.description }}
            </p>
          </div>

          <!-- Action Button -->
          <div class="px-4 pb-4">
            <a
              :href="feedback.link_url"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center text-primary-600 hover:text-primary-700 text-sm font-medium"
            >
              Lihat di {{ feedback.link_type }}
              <svg
                class="ml-1 w-4 h-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                />
              </svg>
            </a>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-if="filteredFeedbacks.length === 0"
        class="text-center py-12 bg-white rounded-lg shadow-sm"
      >
        <div class="text-gray-400">
          <svg
            class="mx-auto h-12 w-12"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
            />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">
            Tidak ada konten
          </h3>
          <p class="mt-1 text-sm text-gray-500">
            Belum ada feedback yang ditambahkan untuk filter yang dipilih.
          </p>
        </div>
      </div>
    </div>
  </UserLayout>
</template>