<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import UserLayout from '@/layouts/UserLayout.vue';

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

const props = defineProps<{
    histories: History[];
}>();

// Format date
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Group histories by year
const historiesByYear = computed(() => {
    const grouped = new Map<string, History[]>();
    props.histories.forEach(history => {
        if (!grouped.has(history.tahun_expo)) {
            grouped.set(history.tahun_expo, []);
        }
        grouped.get(history.tahun_expo)?.push(history);
    });
    return new Map([...grouped.entries()].sort((a, b) => b[0].localeCompare(a[0])));
});

// Fungsi untuk smooth scroll ke timeline section
const scrollToTimeline = (event: Event) => {
    event.preventDefault();
    const timelineSection = document.getElementById('timeline-section');

    if (timelineSection) {
        timelineSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start',
        });
    }
};
</script>

<template>
    <Head>
        <title>Sejarah Expo Technopreneurship</title>
        <meta name="description" content="Melihat perjalanan kolaborasi mahasiswa Sistem Informasi dengan UMKM dalam mengembangkan solusi digital yang inovatif. Event yang diselenggarakan melalui mata kuliah IS Technopreneurship sebagai wadah pengembangan UMKM lokal." />
    </Head>

    <UserLayout>
        <!-- Hero Section -->
        <template #hero>
            <div class="relative overflow-hidden bg-gray-900 py-24 sm:py-32">
                <div class="absolute inset-0">
                    <img
                        src="/assets/expotenant1.jpg"
                        alt="Expo Technologia"
                        class="h-full w-full object-cover opacity-30"
                    />
                </div>
                <div class="relative mx-auto max-w-7xl px-6 lg:px-8">
                    <div class="mx-auto max-w-3xl text-center">
                        <h1 class="text-4xl font-bold tracking-tight text-white sm:text-6xl">
                            Sejarah Expo Technopreneurship
                        </h1>
                        <p class="mt-6 text-lg leading-8 text-gray-300">
                            Melihat perjalanan kolaborasi mahasiswa Sistem Informasi dengan UMKM dalam mengembangkan solusi digital yang inovatif. Event yang diselenggarakan melalui mata kuliah IS Technopreneurship sebagai wadah pengembangan UMKM lokal.
                        </p>
                        <div class="mt-8 flex items-center justify-center gap-x-6">
                            <a
                                href="#timeline-section"
                                @click="scrollToTimeline"
                                class="rounded-full bg-white px-6 py-3 text-sm font-semibold text-gray-900 shadow-sm transition-transform hover:scale-105 active:scale-95"
                            >
                                Lihat Timeline
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Timeline Section -->
        <div id="timeline-section" class="space-y-20">
            <div v-for="[year, yearHistories] in historiesByYear" :key="year" class="relative">
                <!-- Year marker -->
                <div class="sticky top-0 z-10 bg-gradient-to-b from-gray-50 to-transparent pb-4 pt-2">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                        Expo Technopreneurship {{ year }}
                    </h2>
                </div>

                <!-- History cards -->
                <div class="mt-8 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <Link
                        v-for="history in yearHistories"
                        :key="history.id"
                        :href="route('expo-history.show', history.id)"
                        class="no-underline"
                    >
                        <article
                            class="group relative isolate flex flex-col justify-end overflow-hidden rounded-2xl bg-gray-900 px-8 pb-8 pt-80 sm:pt-48 lg:pt-80"
                        >
                            <!-- Background image -->
                            <img
                                :src="history.cover_image_url"
                                :alt="history.title"
                                class="absolute inset-0 -z-10 h-full w-full object-cover transition duration-300 group-hover:scale-105"
                            />
                            <div class="absolute inset-0 -z-10 bg-gradient-to-t from-gray-900 via-gray-900/40"></div>

                            <!-- Content -->
                            <div class="flex flex-col items-start gap-y-4">
                                <div class="flex items-center gap-x-4 text-sm">
                                    <time :datetime="history.published_at" class="text-gray-300">
                                        {{ formatDate(history.published_at) }}
                                    </time>
                                </div>
                                <h3 class="text-xl font-semibold leading-6 text-white">
                                    <span class="absolute inset-0"></span>
                                    {{ history.title }}
                                </h3>
                                <p class="line-clamp-3 text-sm leading-6 text-gray-300">
                                    {{ history.content }}
                                </p>

                                <!-- Gallery preview -->
                                <div v-if="history.images.length > 0" class="w-full">
                                    <span class="mt-2 inline-flex items-center text-sm font-semibold text-white">
                                        Lihat {{ history.images.length }} foto
                                        <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </article>
                    </Link>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
