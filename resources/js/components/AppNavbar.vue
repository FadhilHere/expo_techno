<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { NavigationMenu, NavigationMenuItem, NavigationMenuLink, NavigationMenuList } from '@/components/ui/navigation-menu';
import { Moon, Sun } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

// Theme state - force light theme on initial load
const theme = ref<'light' | 'dark'>('light');
const currentPath = ref('');

// Force light theme - ignore localStorage and system preferences
onMounted(() => {
    // Set current path
    currentPath.value = window.location.pathname;

    // Add event listener for path changes (optional)
    window.addEventListener('popstate', () => {
        currentPath.value = window.location.pathname;
    });

    // Force light theme - no checks, just apply light mode
    theme.value = 'light';
    localStorage.setItem('theme', 'light'); // Save to localStorage too
    applyTheme('light');
});

// Toggle theme function
const toggleTheme = () => {
    theme.value = theme.value === 'light' ? 'dark' : 'light';
    applyTheme(theme.value);
    localStorage.setItem('theme', theme.value);
};

// Apply theme to document
const applyTheme = (newTheme: string) => {
    if (newTheme === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};

// Check if route is active
const isActive = (path: string) => {
    return currentPath.value === path;
};

// Navigation items
const navItems = [
    { label: 'Home', path: '/' },
    { label: 'Expo History', path: '/expo-history' },
    { label: 'Tenant Feedback', path: '/tenant-feedback' },
    { label: 'About Us', path: '/about' },
];
</script>

<template>
    <header class="bg-background/95 supports-[backdrop-filter]:bg-background/80 sticky top-0 z-50 w-full border-b backdrop-blur">
        <div class="mx-auto max-w-screen-xl px-4 py-4 sm:px-6 lg:px-8">
            <div class="flex h-full items-center justify-between">
                <!-- Left section with brand -->
                <div class="flex items-center">
                    <a href="/" class="font-bold">
                        <img :src="'/assets/Logo_polos.png'" alt="Techno Logo" class="h-8" />
                    </a>
                </div>

                <!-- Center section with navigation links -->
                <div class="flex items-center justify-center">
                    <NavigationMenu>
                        <NavigationMenuList class="flex gap-8">
                            <NavigationMenuItem v-for="(item, index) in navItems" :key="index">
                                <NavigationMenuLink
                                    :href="item.path"
                                    :class="[
                                        'group relative px-1 py-2 text-base font-medium transition-all duration-300',
                                        isActive(item.path)
                                            ? 'bg-gradient-to-r from-yellow-500 to-red-500 bg-clip-text text-transparent'
                                            : 'text-gray-700 hover:bg-gradient-to-r hover:from-yellow-500 hover:to-red-500 hover:bg-clip-text hover:text-transparent dark:text-gray-300',
                                    ]"
                                    @click="
                                        (e) => {
                                            // If you want to handle navigation manually (optional)
                                            // e.preventDefault();
                                            // window.location.href = item.path;
                                            // currentPath.value = item.path;
                                        }
                                    "
                                >
                                    {{ item.label }}
                                    <span
                                        :class="[
                                            'absolute bottom-0 left-0 h-0.5 bg-gradient-to-r from-yellow-500 to-red-500 transition-all duration-300 group-hover:w-full',
                                            isActive(item.path) ? 'w-full' : 'w-0',
                                        ]"
                                    ></span>
                                </NavigationMenuLink>
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <!-- Right section with buttons -->
                <div class="flex items-center gap-4">
                    <Button
                        class="border-none bg-gradient-to-r from-yellow-500 to-red-500 font-medium text-white hover:from-yellow-600 hover:to-red-600"
                        as="a"
                        href="/login"
                    >
                        Login
                    </Button>
                    <Button
                        variant="outline"
                        size="icon"
                        @click="toggleTheme"
                        aria-label="Toggle theme"
                        class="rounded-full border-gray-300 hover:bg-gray-100 dark:border-gray-700 dark:hover:bg-gray-800"
                    >
                        <Sun v-if="theme === 'dark'" class="h-5 w-5 text-yellow-500" />
                        <Moon v-else class="h-5 w-5 text-indigo-600" />
                    </Button>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
/* Tambahan untuk animasi yang lebih halus */
.group:hover span {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}

/* Efek hover tambahan untuk tombol login */
:deep(.bg-gradient-to-r) {
    background-size: 200% auto;
    transition: background-position 0.5s ease-in-out;
}

:deep(.bg-gradient-to-r:hover) {
    background-position: right center;
}
</style>
