<script setup lang="ts">
import UserLayout from '@/layouts/UserLayout.vue';
import axios from 'axios';
import { reactive, ref } from 'vue';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

// Form data
const form = reactive({
    username: '',
    password: '',
});

// Form state
const errors = reactive({
    username: '',
    password: '',
});
const isSubmitting = ref(false);
const loginError = ref('');
const showPassword = ref(false);

// Toggle password visibility
const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

// Validate the form
const validateForm = () => {
    let isValid = true;

    // Reset errors
    errors.username = '';
    errors.password = '';

    if (!form.username) {
        errors.username = 'Username is required';
        isValid = false;
    }

    if (!form.password) {
        errors.password = 'Password is required';
        isValid = false;
    }

    return isValid;
};

// Form submission handler
const onSubmit = async () => {
    if (!validateForm()) {
        return;
    }

    try {
        isSubmitting.value = true;
        loginError.value = '';

        // Submit credentials to your Laravel backend
        const response = await axios.post('/login', {
            username: form.username,
            password: form.password,
        });

        // Handle successful login (adjust based on your response structure)
        if (response.data.success) {
            // For Inertia, you can use:
            window.location.href = response.data.redirect || '/dashboard';
        }
    } catch (error: any) {
        // Handle login errors
        if (error.response && error.response.data) {
            loginError.value = error.response.data.message || 'Login failed. Please check your credentials.';
        } else {
            loginError.value = 'Network error. Please try again.';
        }
    } finally {
        isSubmitting.value = false;
    }
};
</script>

<template>
    <UserLayout>
        <div class="flex h-screen">
            <!-- Left Side - Background Image (hidden on mobile) -->
            <div class="hidden md:block md:w-3/5 bg-gray-100">
                <img src="/assets/foto_untuk_login.jpg" alt="Background" class="h-full w-full object-contain" />
            </div>

            <!-- Right Side - Login Form -->
            <div class="w-full md:w-2/5 flex items-center justify-center">
                <div class="w-full max-w-md px-6 py-8">
                    <div class="flex justify-center mb-8">
                        <img src="/assets/Logo_polos.png" alt="Expo Techno" class="h-16 w-auto" />
                    </div>

                    <div class="text-center mb-10">
                        <h1 class="text-2xl font-semibold">Selamat Datang Kembali</h1>
                    </div>

                    <form @submit.prevent="onSubmit" class="space-y-6">
                        <div>
                            <Label for="username" class="block text-sm font-medium mb-1">Username</Label>
                            <Input
                                id="username"
                                v-model="form.username"
                                class="w-full rounded-md"
                                placeholder="Username"
                            />
                            <p v-if="errors.username" class="text-sm text-red-500 mt-1">{{ errors.username }}</p>
                        </div>

                        <div>
                            <Label for="password" class="block text-sm font-medium mb-1">Password</Label>
                            <div class="relative">
                                <Input
                                    id="password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="w-full pr-10 rounded-md"
                                    placeholder="Masukkan password"
                                />
                                <button
                                    type="button"
                                    @click="togglePasswordVisibility"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        class="h-5 w-5 text-gray-500"
                                    >
                                        <path
                                            v-if="showPassword"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                        <path
                                            v-if="showPassword"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />
                                        <path
                                            v-if="!showPassword"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                                        />
                                    </svg>
                                </button>
                            </div>
                            <p v-if="errors.password" class="text-sm text-red-500 mt-1">{{ errors.password }}</p>
                        </div>

                        <div v-if="loginError" class="rounded bg-red-50 p-3 text-sm text-red-500">
                            {{ loginError }}
                        </div>

                        <Button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-md" :disabled="isSubmitting">
                            {{ isSubmitting ? 'Sedang Masuk...' : 'Masuk' }}
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    </UserLayout>
</template>
