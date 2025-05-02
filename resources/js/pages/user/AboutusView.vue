<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue'
</script>

<template>
  <UserLayout
    title="About Us"
    description="Learn more about our company and mission"
  >
    <div class="grid gap-6">
      <div class="rounded-lg border p-6">
        <h2 class="text-xl font-semibold mb-4">Our Story</h2>
        <p class="text-muted-foreground">
          Founded with a vision to transform the technology landscape, we've been at the forefront of innovation for years.
        </p>
      </div>

      <div class="rounded-lg border p-6">
        <h2 class="text-xl font-semibold mb-4">Our Mission</h2>
        <p class="text-muted-foreground">
          To empower businesses with cutting-edge technology solutions that drive growth and success.
        </p>
      </div>

      <div class="rounded-lg border p-6">
        <h2 class="text-xl font-semibold mb-4">Our Team</h2>
        <p class="text-muted-foreground">
          Our team consists of passionate professionals dedicated to delivering excellence in every project.
        </p>
      </div>
    </div>
  </UserLayout>
</template>
