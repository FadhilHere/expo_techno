<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue'
</script>

<template>
  <UserLayout
    title="Welcome to Techno"
    description="Your trusted partner in technology solutions"
  >
    <div class="grid gap-6">
      <div class="rounded-lg border p-6">
        <h2 class="text-xl font-semibold mb-4">Featured Services</h2>
        <p class="text-muted-foreground">
          Discover our comprehensive range of technology solutions designed to help your business grow.
        </p>
      </div>

      <div class="rounded-lg border p-6">
        <h2 class="text-xl font-semibold mb-4">Why Choose Us?</h2>
        <p class="text-muted-foreground">
          We combine expertise with innovation to deliver exceptional results for our clients.
        </p>
      </div>
    </div>
  </UserLayout>
</template>
