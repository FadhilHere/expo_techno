<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import UserLayout from '@/layouts/UserLayout.vue';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from '@/components/ui/carousel';

interface Image {
    id: number;
    image_url: string;
    caption: string;
}

interface History {
    id: number;
    title: string;
    content: string;
    cover_image_url: string;
    published_at: string;
    tahun_expo: string;
    images: Image[];
}

defineProps<{
    history: History;
}>();

// Format date
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};
</script>

<template>
    <Head :title="history.title" />

    <UserLayout>
        <div class="space-y-10">
            <!-- Back Button -->
            <div>
                <Link :href="route('expo-history')" class="inline-flex items-center text-gray-700 hover:text-gray-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Kembali ke Sejarah Expo
                </Link>
            </div>

            <!-- Hero image -->
            <div class="relative h-[60vh] w-full overflow-hidden rounded-xl">
                <img
                    :src="history.cover_image_url"
                    :alt="history.title"
                    class="h-full w-full object-cover"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-black/60 to-black/30"></div>
                <div class="absolute bottom-0 left-0 right-0 p-8 text-white">
                    <h1 class="text-3xl font-bold sm:text-4xl">{{ history.title }}</h1>
                    <p class="mt-2 text-lg">Expo Technologia {{ history.tahun_expo }}</p>
                    <p class="mt-1 text-sm opacity-80">{{ formatDate(history.published_at) }}</p>
                </div>
            </div>

            <!-- Content -->
            <div class="mx-auto max-w-4xl">
                <!-- Description -->
                <div class="prose prose-lg dark:prose-invert">
                    <p>{{ history.content }}</p>
                </div>

                <!-- Gallery -->
                <div v-if="history.images.length > 0" class="mt-16">
                    <h2 class="mb-8 text-2xl font-bold">Galeri Foto</h2>
                    <Carousel class="w-full">
                        <CarouselContent>
                            <CarouselItem v-for="image in history.images" :key="image.id">
                                <div class="relative aspect-video">
                                    <img
                                        :src="image.image_url"
                                        :alt="image.caption"
                                        class="h-full w-full rounded-lg object-cover"
                                    />
                                    <div
                                        v-if="image.caption"
                                        class="absolute bottom-0 left-0 right-0 bg-black/50 p-4 text-white"
                                    >
                                        {{ image.caption }}
                                    </div>
                                </div>
                            </CarouselItem>
                        </CarouselContent>
                        <CarouselPrevious class="left-4" />
                        <CarouselNext class="right-4" />
                    </Carousel>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
