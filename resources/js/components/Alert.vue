<script setup lang="ts">
import { watch } from 'vue'

const props = defineProps<{
    type: 'success' | 'error'
    message: string
    show: boolean
}>()

const emit = defineEmits<{
    (e: 'update:show', value: boolean): void
}>()

watch(() => props.show, (newVal) => {
    if (newVal) {
        setTimeout(() => {
            emit('update:show', false)
        }, 3000)
    }
})
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform translate-y-2 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform translate-y-2 opacity-0"
    >
        <div
            v-if="show"
            :class="[
        'fixed top-4 right-4 p-4 rounded-lg shadow-lg',
        type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'
      ]"
        >
            {{ message }}
        </div>
    </Transition>
</template>
