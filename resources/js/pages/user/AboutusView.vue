<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue'

// Function to smoothly scroll to a section
const scrollToSection = (event: Event, targetId: string) => {
  event.preventDefault();
  const targetElement = document.getElementById(targetId);

  if (targetElement) {
    targetElement.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    });
  }
};
</script>

<template>
  <UserLayout
    title="About Us"
    description="Tentang EXPO Technopreneurship 2025"
  >
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gray-50 py-12">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-12 lg:gap-8">
          <div class="lg:col-span-6">
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl md:text-6xl">
              EXPO Technopreneurship 2025
            </h1>
            <p class="mt-6 text-xl text-gray-500">
              Inovasi Digital untuk UMKM Indonesia: Mendorong pertumbuhan ekonomi lokal melalui teknologi dan kolaborasi.
            </p>
            <div class="mt-8 flex">
              <div class="rounded-md shadow">
                <a
                  href="#our-mission"
                  @click="(event) => scrollToSection(event, 'our-mission')"
                  class="flex items-center justify-center rounded-md border border-transparent bg-black px-5 py-3 text-base font-medium text-white hover:opacity-90 transition-all duration-300"
                >
                  Pelajari Lebih Lanjut
                </a>
              </div>
            </div>
          </div>
          <div class="mt-12 lg:col-span-6 lg:mt-0">
            <div class="overflow-hidden rounded-lg shadow-lg">
              <img src="/assets/expotenant2.jpg" alt="EXPO Technopreneurship" class="w-full h-auto object-cover">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- About Section -->
    <div class="bg-white py-16">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="lg:grid lg:grid-cols-2 lg:gap-16">
          <!-- Left column -->
          <div>
            <h2 id="our-mission" class="text-3xl font-bold tracking-tight text-gray-900">Tentang EXPO Technopreneurship</h2>
            <p class="mt-4 text-lg text-gray-500">
              EXPO Technopreneurship 2025 adalah event tahunan yang diselenggarakan oleh Program Studi Sistem Informasi Politeknik Caltex Riau sebagai hasil dari pendampingan yang dilakukan kepada Usaha Mikro, Kecil, dan Menengah (UMKM) di Indonesia.
            </p>
            <p class="mt-4 text-lg text-gray-500">
              Event ini bertujuan untuk memperkenalkan inovasi digital dan teknologi kepada UMKM serta memberikan platform bagi mereka untuk menampilkan produk dan layanan mereka kepada masyarakat luas.
            </p>

            <div class="mt-10">
              <h3 class="text-xl font-bold text-gray-900">Visi Kami</h3>
              <p class="mt-2 text-gray-500">
                Menjadi katalisator pertumbuhan ekonomi lokal melalui pemberdayaan UMKM dengan teknologi digital yang inovatif dan berkelanjutan.
              </p>
            </div>

            <div class="mt-8">
              <h3 class="text-xl font-bold text-gray-900">Misi Kami</h3>
              <ul class="mt-2 list-disc list-inside text-gray-500 space-y-2">
                <li>Memfasilitasi adopsi teknologi digital di kalangan UMKM</li>
                <li>Menciptakan ekosistem kolaboratif antara akademisi, industri, dan pelaku usaha</li>
                <li>Meningkatkan daya saing UMKM melalui inovasi dan digitalisasi</li>
                <li>Mendukung pertumbuhan ekonomi lokal dan nasional</li>
              </ul>
            </div>
          </div>

          <!-- Right column with persuasive content instead of stats -->
          <div class="mt-12 lg:mt-0">
            <div class="overflow-hidden rounded-lg bg-gray-50 shadow p-8">
              <h3 class="text-2xl font-bold text-gray-900 mb-6">Dampak Kami</h3>

              <div class="space-y-6">
                <div class="border-l-4 border-black pl-4">
                  <h4 class="font-semibold text-lg">Meningkatkan Daya Saing</h4>
                  <p class="text-gray-500 mt-1">Membantu UMKM tumbuh lebih kuat dengan adopsi teknologi yang tepat guna.</p>
                </div>

                <div class="border-l-4 border-black pl-4">
                  <h4 class="font-semibold text-lg">Memperluas Jangkauan</h4>
                  <p class="text-gray-500 mt-1">Membuka pasar baru dan kesempatan bagi UMKM untuk menjangkau pelanggan potensial.</p>
                </div>

                <div class="border-l-4 border-black pl-4">
                  <h4 class="font-semibold text-lg">Membangun Komunitas</h4>
                  <p class="text-gray-500 mt-1">Menciptakan jaringan UMKM yang saling mendukung dan berkolaborasi.</p>
                </div>

                <div class="border-l-4 border-black pl-4">
                  <h4 class="font-semibold text-lg">Mendorong Inovasi</h4>
                  <p class="text-gray-500 mt-1">Menginspirasi para pelaku UMKM untuk terus berinovasi dan beradaptasi dengan perkembangan teknologi.</p>
                </div>
              </div>

              <div class="mt-8">
                <img src="/assets/expotenant3.jpg" alt="Tenant UMKM" class="w-full h-auto rounded-lg object-cover">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Programs Section -->
    <div class="bg-gray-50 py-16">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h2 class="text-3xl font-bold tracking-tight text-gray-900">Program Kami</h2>
          <p class="mx-auto mt-4 max-w-3xl text-lg text-gray-500">
            Beberapa program unggulan kami dalam mendukung pertumbuhan UMKM melalui inovasi digital.
          </p>
        </div>

        <div class="mt-12 grid gap-8 lg:grid-cols-3">
          <!-- Program 1 -->
          <div class="bg-white overflow-hidden rounded-lg shadow">
            <div class="h-48 overflow-hidden">
              <img src="/assets/expotenant1.jpg" alt="Pemberdayaan UMKM" class="w-full h-full object-cover">
            </div>
            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900">Pendampingan Digital</h3>
              <p class="mt-2 text-gray-500">
                Program pendampingan intensif untuk membantu UMKM mengadopsi teknologi digital dalam operasional bisnis mereka.
              </p>
            </div>
          </div>

          <!-- Program 2 -->
          <div class="bg-white overflow-hidden rounded-lg shadow">
            <div class="h-48 overflow-hidden">
              <img src="/assets/expotenant2.jpg" alt="Showcase UMKM" class="w-full h-full object-cover">
            </div>
            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900">Showcase Produk UMKM</h3>
              <p class="mt-2 text-gray-500">
                Platform untuk memamerkan produk inovatif dari UMKM lokal kepada masyarakat dan potensial investor.
              </p>
            </div>
          </div>

          <!-- Program 3 -->
          <div class="bg-white overflow-hidden rounded-lg shadow">
            <div class="h-48 overflow-hidden">
              <img src="/assets/expotenant3.jpg" alt="Workshop Digital" class="w-full h-full object-cover">
            </div>
            <div class="p-6">
              <h3 class="text-xl font-bold text-gray-900">Workshop Keterampilan Digital</h3>
              <p class="mt-2 text-gray-500">
                Pelatihan praktis tentang pemasaran digital, e-commerce, dan keterampilan teknologi untuk pelaku UMKM.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Inspiring Message Section (replacing Testimonial) -->
    <div class="bg-white py-16">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h2 class="text-3xl font-bold tracking-tight text-gray-900">Mari Bergerak Bersama</h2>
          <p class="mx-auto mt-4 max-w-3xl text-lg text-gray-500">
            Bersama kita dapat menciptakan ekosistem digital yang inklusif dan mendukung pertumbuhan ekonomi lokal.
          </p>
        </div>

        <div class="mt-12 flex flex-col items-center">
          <div class="max-w-3xl text-center">
            <p class="text-lg italic text-gray-500">
              "Teknologi digital bukan lagi masa depan, melainkan realitas hari ini. Dengan EXPO Technopreneurship, kami mengajak seluruh pelaku UMKM untuk bersama-sama menghadapi tantangan digitalisasi dan mengubahnya menjadi peluang. Bersama, kita bangun ekonomi digital yang inklusif dan berkelanjutan."
            </p>
            <div class="mt-6">
              <p class="font-semibold text-gray-900">Ketua Program Studi Sistem Informasi</p>
              <p class="text-gray-500">Politeknik Caltex Riau</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Call to Action Section (replacing Contact) -->
    <div class="bg-gray-50 py-16">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
          <h2 class="text-3xl font-bold tracking-tight text-gray-900">Jadilah Bagian dari Perubahan</h2>
          <p class="mx-auto mt-4 max-w-3xl text-lg text-gray-500">
            Ayo bergabung dalam gerakan transformasi digital untuk UMKM Indonesia. Bersama kita wujudkan ekonomi digital yang inklusif.
          </p>
        </div>

        <div class="mt-12 grid gap-8 lg:grid-cols-3">
          <div class="bg-white p-6 rounded-lg shadow text-center">
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-md bg-black text-white">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
              </svg>
            </div>
            <h3 class="mt-4 text-xl font-semibold text-gray-900">Daftar sebagai Tenant</h3>
            <p class="mt-2 text-gray-500">Pamerkan produk dan inovasi Anda di platform EXPO Technopreneurship 2025.</p>
          </div>

          <div class="bg-white p-6 rounded-lg shadow text-center">
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-md bg-black text-white">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
              </svg>
            </div>
            <h3 class="mt-4 text-xl font-semibold text-gray-900">Ikuti Workshop</h3>
            <p class="mt-2 text-gray-500">Pelajari keterampilan digital terbaru untuk mengembangkan bisnis Anda.</p>
          </div>

          <div class="bg-white p-6 rounded-lg shadow text-center">
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-md bg-black text-white">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
              </svg>
            </div>
            <h3 class="mt-4 text-xl font-semibold text-gray-900">Jalin Kolaborasi</h3>
            <p class="mt-2 text-gray-500">Bangun jaringan dengan sesama pelaku usaha, investor, dan akademisi.</p>
          </div>
        </div>

        <!-- Contact Info -->
        <div class="mt-16 text-center">
          <h3 class="text-xl font-bold text-gray-900 mb-4">Hubungi Kami</h3>
          <p class="text-gray-500">Email: expo@pcr.ac.id | Telepon: +62 761 53939</p>
          <p class="text-gray-500 mt-1">Politeknik Caltex Riau, Jl. Umban Sari No.1, Rumbai, Pekanbaru, Riau 28265</p>
        </div>
      </div>
    </div>
  </UserLayout>
</template>
