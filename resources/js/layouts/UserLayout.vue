<script setup lang="ts">
import AppNavbar from '@/components/AppNavbar.vue';

// Props untuk title dan description
// const props = defineProps({
//   title: {
//     type: String,
//     default: 'Welcome'
//   },
//   description: {
//     type: String,
//     default: 'Welcome to our website'
//   }
// })

// Current year for footer
const currentYear = new Date().getFullYear();
</script>

<template>
    <div class="flex min-h-screen flex-col">
        <!-- Navbar -->
        <AppNavbar />

        <!-- Main content area with improved spacing -->
        <main class="flex-1">
            <div class="container mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
                <!-- <div class="mb-10">
                    <h1 class="mb-2 text-3xl font-bold tracking-tight">{{ title }}</h1>
                    <p class="text-muted-foreground text-lg">{{ description }}</p>
                </div> -->

                <!-- Slot untuk konten dari page yang menggunakan layout ini -->
                <div class="space-y-8">
                    <slot></slot>
                </div>
            </div>
        </main>

        <!-- Footer with improved spacing -->
        <footer class="text-muted-foreground mt-12 border-t py-6 text-center text-sm">
            <p>© {{ currentYear }} Fadhil Parmata. All rights reserved.</p>
        </footer>
    </div>
</template>
