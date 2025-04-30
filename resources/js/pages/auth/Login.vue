<template>
    <div class="flex items-center justify-center min-h-screen bg-background">
      <div class="w-full max-w-md p-8 space-y-6 bg-card rounded-lg shadow-lg">
        <div class="space-y-2 text-center">
          <h1 class="text-2xl font-bold tracking-tight">Login</h1>
          <p class="text-sm text-muted-foreground">
            Enter your credentials to access the system
          </p>
        </div>

        <form @submit.prevent="onSubmit" class="space-y-4">
          <div class="space-y-2">
            <Label for="username">Username</Label>
            <Input
              id="username"
              v-model="form.username"
              placeholder="Enter your username"
            />
            <p v-if="errors.username" class="text-sm text-red-500">{{ errors.username }}</p>
          </div>

          <div class="space-y-2">
            <Label for="password">Password</Label>
            <Input
              id="password"
              v-model="form.password"
              type="password"
              placeholder="••••••••"
            />
            <p v-if="errors.password" class="text-sm text-red-500">{{ errors.password }}</p>
          </div>

          <div v-if="loginError" class="text-red-500 text-sm p-2 rounded bg-red-50">
            {{ loginError }}
          </div>

          <Button type="submit" class="w-full" :disabled="isSubmitting">
            {{ isSubmitting ? 'Logging in...' : 'Login' }}
          </Button>
        </form>
      </div>
    </div>
  </template>

<!-- <script setup lang="ts">
import { ref, reactive } from 'vue';
import axios from 'axios';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label'; -->

<script setup lang="ts">
import { ref, reactive } from 'vue'
import type { HTMLAttributes } from 'vue'
import axios, { AxiosError } from 'axios';
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'

const form = reactive({
  username: '',
  password: '',
})
const errors = reactive({
  username: '',
  password: '',
})
const isSubmitting = ref(false)
const loginError = ref('')

// Simulasi login
const onSubmit = async () => {
  isSubmitting.value = true
  loginError.value = ''
  errors.username = ''
  errors.password = ''

  // Validasi dummy
  if (!form.username) errors.username = 'Username is required'
  if (!form.password) errors.password = 'Password is required'

  if (errors.username || errors.password) {
    isSubmitting.value = false
    return
  }

  try {
    isSubmitting.value = true;
    loginError.value = '';

    const response = await axios.post('/login', {
        username: form.username,
        password: form.password
    });

    if (response.data.success) {
        window.location.href = response.data.redirect || '/admin/dashboard';
    }
} catch (error) {
    const err = error as AxiosError;

    if (err.response && err.response.data) {
        // Jika backend mengirim message di dalam response.data.message
        loginError.value = (err.response.data as any).message || 'Login failed. Please check your credentials.';
    } else {
        loginError.value = 'Network error. Please try again.';
    }
} finally {
    isSubmitting.value = false;
}
}
</script>
