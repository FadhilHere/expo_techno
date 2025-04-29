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
                    <Input id="username" v-model="form.username" placeholder="Enter your username" />
                    <p v-if="errors.username" class="text-sm text-red-500">{{ errors.username }}</p>
                </div>

                <div class="space-y-2">
                    <Label for="password">Password</Label>
                    <Input id="password" v-model="form.password" type="password" placeholder="••••••••" />
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

<script setup lang="ts">
import { ref, reactive } from 'vue';
import axios from 'axios';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

// Form data
const form = reactive({
    username: '',
    password: ''
});

// Form state
const errors = reactive({
    username: '',
    password: ''
});
const isSubmitting = ref(false);
const loginError = ref('');

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
            password: form.password
        });

        // Handle successful login (adjust based on your response structure)
        if (response.data.success) {
            // For Inertia, you can use:
            window.location.href = response.data.redirect || '/admin/dashboard';
        }
    } catch (error) {
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
