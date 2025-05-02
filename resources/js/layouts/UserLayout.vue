<script setup lang="ts">
import AppNavbar from '@/components/AppNavbar.vue'

// Props untuk title dan description
const props = defineProps({
  title: {
    type: String,
    default: 'Welcome'
  },
  description: {
    type: String,
    default: 'Welcome to our website'
  }
})

// Current year for footer
const currentYear = new Date().getFullYear()
</script>

<template>
  <div class="min-h-screen flex flex-col">
    <!-- Navbar -->
    <AppNavbar />

    <!-- Main content area with improved spacing -->
    <main class="flex-1">
      <div class="container max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="mb-10">
          <h1 class="text-3xl font-bold tracking-tight mb-2">{{ title }}</h1>
          <p class="text-lg text-muted-foreground">{{ description }}</p>
        </div>

        <!-- Slot untuk konten dari page yang menggunakan layout ini -->
        <div class="space-y-8">
          <slot></slot>
        </div>
      </div>
    </main>

    <!-- Footer with improved spacing -->
    <footer class="py-6 border-t text-center text-sm text-muted-foreground mt-12">
      <p>© {{ currentYear }} Fadhil Parmata. All rights reserved.</p>
    </footer>
  </div>
</template>