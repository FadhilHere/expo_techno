<template>
    <div class="w-full">
        <!-- Gunakan container dengan max-width tertentu dan mx-auto untuk membuat konten terpusat -->
        <div class="mx-auto max-w-screen-xl px-4 py-16 sm:px-6 lg:px-8">
            <!-- Hero Content -->
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- Left Content -->
                <div>
                    <h2 class="mb-6 text-5xl leading-tight font-bold">
                        SELAMAT DATANG <br />
                        DI WEBSITE EXPO TECHNOPRENEURSHIP 2025.
                    </h2>
                    <p class="mb-8 text-gray-500">
                        Event ini diselenggarakan oleh Program Studi Sistem Informasi sebagai hasil dari pendampingan yang dilakukan kepada UMKM.
                    </p>
                    <button class="rounded-full bg-black px-6 py-3 font-medium text-white">Check Our Tenant!</button>

                    <!-- Stats -->
                    <div class="mt-12 flex space-x-16">
                        <div>
                            <h3 class="text-4xl font-bold">24</h3>
                            <p class="text-gray-500">Jumlah Tenant</p>
                        </div>
                        <div>
                            <h3 class="text-4xl font-bold">123</h3>
                            <p class="text-gray-500">Belum tau nih</p>
                        </div>
                    </div>

                    <!-- Trusted By -->
                    <!-- <div class="mt-12">
                        <p class="mb-4 text-sm text-gray-500">TRUSTED BY THE BEST</p>
                        <div class="flex space-x-6">
                            <span class="text-gray-700">Microsoft</span>
                            <span class="text-gray-700">Apple</span>
                            <span class="text-gray-700">The Boring Company</span>
                        </div>
                    </div> -->
                </div>

                <!-- Right Content - Cards -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <!-- Card 1 - Main -->
                    <div class="col-span-2 overflow-hidden rounded-3xl bg-cover bg-center" style="background-image: url('/assets/expotenant1.jpg')">
                        <div class="flex h-full flex-col justify-end bg-gradient-to-t from-black/70 to-transparent p-6">
                            <span class="text-sm text-white/80">Mendukung ekonomi lokal</span>
                            <h3 class="text-3xl font-semibold text-white">Puluhan UMKM berpartisipasi</h3>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="overflow-hidden rounded-3xl bg-cover bg-center" style="background-image: url('/assets/expotenant2.jpg')">
                        <div class="flex h-full flex-col justify-end bg-gradient-to-t from-black/70 to-transparent p-6">
                            <span class="text-sm text-white/80">Produk berkualitas tinggi</span>
                            <h3 class="text-2xl font-semibold text-white">Karya terbaik lokal</h3>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="overflow-hidden rounded-3xl bg-cover bg-center" style="background-image: url('/assets/expotenant3.jpg')">
                        <div class="flex h-full flex-col justify-end bg-gradient-to-t from-black/70 to-transparent p-6">
                            <span class="text-sm text-white/80">Pemberdayaan pelaku usaha</span>
                            <h3 class="text-2xl font-semibold text-white">Pertumbuhan UMKM nasional</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
