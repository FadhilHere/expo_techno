<script setup lang="ts">
import { NavigationMenu, NavigationMenuItem, NavigationMenuLink, NavigationMenuList } from '@/components/ui/navigation-menu';
import { onMounted, ref } from 'vue';

// Theme state
const theme = ref<'light' | 'dark'>('light');

// Set initial theme based on local storage or system preference
onMounted(() => {
    const storedTheme = localStorage.getItem('theme');
    if (storedTheme) {
        theme.value = storedTheme as 'light' | 'dark';
        applyTheme(theme.value);
    } else {
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
const applyTheme = (newTheme: string) => {
    if (newTheme === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
};
</script>

<template>
    <header class="bg-background/100 supports-[backdrop-filter]:bg-background/60 sticky top-0 z-50 w-full border-b backdrop-blur">
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
                        <NavigationMenuList class="flex gap-6">
                            <NavigationMenuItem>
                                <NavigationMenuLink href="/"> Home </NavigationMenuLink>
                            </NavigationMenuItem>

                            <NavigationMenuItem>
                                <NavigationMenuLink href="/about"> About Us </NavigationMenuLink>
                            </NavigationMenuItem>
                        </NavigationMenuList>
                    </NavigationMenu>
                </div>

                <!-- Right section with buttons -->
                <div class="flex items-center gap-4">
                    <!-- <Button variant="default" as="a" href="/login"> Login </Button> -->
                    <!-- <Button variant="outline" size="icon" @click="toggleTheme" aria-label="Toggle theme">
                        <Sun v-if="theme === 'dark'" class="h-[1.2rem] w-[1.2rem] scale-100 rotate-0 transition-all" />
                        <Moon v-else class="h-[1.2rem] w-[1.2rem] scale-100 rotate-0 transition-all" />
                    </Button> -->
                </div>
            </div>
        </div>
    </header>
</template>
