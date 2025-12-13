<template>
    <div class="w-full h-full flex justify-center items-center bg-gray-100">
        <img
            v-if="firstImage"
            :src="firstImage.image_url || getImageUrl(firstImage.image_path)"
            alt="Imagen de proyecto"
            class="w-full h-full object-cover"
        />
        <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center">
            <span class="text-gray-400">Sin imagen</span>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    images: {
        type: Array,
        default: () => [],
    },
});

const firstImage = computed(() => {
    return props.images && props.images.length > 0 ? props.images[0] : null;
});

const getImageUrl = (path) => {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    const baseUrl = import.meta.env.VITE_APP_URL || '';
    if (import.meta.env.VITE_AWS_URL) {
        return `${import.meta.env.VITE_AWS_URL}/${path}`;
    }
    return `${baseUrl}/storage/${path}`;
};
</script>

