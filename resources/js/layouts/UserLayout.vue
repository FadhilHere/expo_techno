<script setup lang="ts">
import AppNavbar from '@/components/AppNavbar.vue';
import { computed, onMounted, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Moon, Sun, Menu } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { useMediaQuery } from '@vueuse/core';

// Props untuk title dan description
const props = defineProps({
    title: {
        type: String,
        default: 'Welcome',
    },
    description: {
        type: String,
        default: 'Welcome to our website',
    },
});

// Mobile detection
const isMobile = useMediaQuery('(max-width: 768px)');
const mobileSidebarOpen = ref(false);

// Theme state
const theme = ref('light');

// Set initial theme based on local storage or system preference
onMounted(() => {
    // Check if theme is stored in localStorage
    const storedTheme = localStorage.getItem('theme');

    if (storedTheme) {
        theme.value = storedTheme;
        applyTheme(theme.value);
    } else {
        // Check system preference
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        theme.value = prefersDark ? 'dark' : 'light';
        applyTheme(theme.value);
    }
});

// Toggle theme function
const toggleTheme = () => {
    theme.value = theme.value === 'light' ? 'dark' : 'light';
    applyTheme(theme.value);
    localStorage.setItem('theme', theme.value);
};

// Apply theme to document
const applyTheme = (newTheme) => {
    if (newTheme === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};

// Toggle sidebar for mobile
const toggleMobileSidebar = () => {
    mobileSidebarOpen.value = !mobileSidebarOpen.value;
    // console.log('Mobile sidebar toggled:', mobileSidebarOpen.value);
};

// Current year for footer
const currentYear = new Date().getFullYear();

// Navigation items for mobile sidebar
const navItems = [
    { title: 'Home', url: '/' },
    { title: 'About Us', url: '/about' },
];
</script>

<template>
    <Head :title="title" />

    <!-- Desktop Layout -->
    <div v-if="!isMobile" class="flex min-h-screen flex-col">
        <!-- Navbar -->
        <AppNavbar />

        <!-- Main content area -->
        <main class="flex-1">
            <!-- Slot khusus untuk Hero Section yang memerlukan width 100% -->
            <slot name="hero"></slot>

            <!-- Container untuk konten lainnya -->
            <div class="container mx-auto max-w-screen-xl px-4 py-8 sm:px-6 lg:px-8">
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

    <!-- Mobile Layout (with Sidebar) -->
    <div v-else class="min-h-screen">
        <!-- Mobile sidebar overlay backdrop - now using backdrop-blur instead of bg-black -->
        <div
            v-if="mobileSidebarOpen"
            class="fixed inset-0 backdrop-blur-sm bg-black/30 z-40 transition-all duration-300"
            @click="toggleMobileSidebar"
        ></div>

        <!-- Mobile sidebar -->
        <div
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-900 transform transition-transform duration-300 shadow-lg"
            :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Header -->
            <div class="flex items-center justify-center border-b h-16 px-4">
                <div class="flex items-center justify-center">
                    <img src="/assets/Logo_polos.png" alt="Logo" class="h-8" />
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4">
                <ul class="space-y-2">
                    <li v-for="(item, index) in navItems" :key="index">
                        <a
                            :href="item.url"
                            class="flex items-center px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"
                        >
                            <span>{{ item.title }}</span>
                        </a>
                    </li>
                </ul>

                <!-- Theme Toggle (Commented out as requested) -->
                <!--
                <div class="mt-6">
                    <button
                        @click="toggleTheme"
                        class="flex items-center w-full px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"
                    >
                        <Sun v-if="theme === 'dark'" class="mr-2 h-5 w-5" />
                        <Moon v-else class="mr-2 h-5 w-5" />
                        <span>{{ theme === 'light' ? 'Dark Mode' : 'Light Mode' }}</span>
                    </button>
                </div>
                -->

                <!-- Dark Mode button instead of toggle -->
                <!-- <div class="mt-6">
                    <button
                        class="flex items-center w-full px-4 py-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800"
                    >
                        <Moon class="mr-2 h-5 w-5" />
                        <span>Dark Mode</span>
                    </button>
                </div> -->
            </nav>
        </div>

        <!-- Main content -->
        <div class="flex flex-col min-h-screen">
            <!-- Header with hamburger menu -->
            <header class="bg-white dark:bg-gray-900 border-b h-16 flex items-center px-4">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center space-x-3">
                        <!-- Hamburger button -->
                        <button
                            @click="toggleMobileSidebar"
                            class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none"
                        >
                            <Menu class="h-6 w-6" />
                        </button>

                        <h1 class="font-semibold">{{ title }}</h1>
                    </div>
                </div>
            </header>

            <!-- Main content -->
            <main class="flex-1">
                <!-- Hero section -->
                <slot name="hero"></slot>

                <!-- Main content -->
                <div class="px-4 py-6">
                    <slot></slot>
                </div>
            </main>

            <!-- Footer -->
            <footer class="py-4 border-t text-center text-sm text-gray-500">
                <p>© {{ currentYear }} Fadhil Parmata. All rights reserved.</p>
            </footer>
        </div>
    </div>
</template>