<template>
    <div class="description">
        <div v-for="(line, index) in contentLines" :key="index" class="text-line">
            {{ line }}
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    content: {
        type: String,
        required: true,
        default: '',
    },
});

// Split content into individual lines
const contentLines = computed(() => {
    if (!props.content) return [];

    // Split by newlines and filter out empty lines
    return props.content.split('\n').filter((line) => line.trim() !== '');
});
</script>

<style scoped>
.description {
    text-align: left;
}

.text-line {
    margin-bottom: 0.5rem; /* Consistent spacing between all lines */
    line-height: 1.5;
}
</style>
