<template>
    <div class="
        bg-white rounded-xl p-6 shadow-lg
        transform transition-all duration-300
        hover:-translate-y-2 hover:shadow-xl
        border border-gray-100
    ">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-dark mb-2">{{ position }}</h3>
            <div class="space-y-1">
                <p class="text-accent font-medium">{{ company }}</p>
                <p class="text-gray-600 text-sm">{{ duration }}</p>
                <p v-if="location" class="text-gray-600 text-sm">{{ location }}</p>
            </div>
        </div>

        <div v-if="responsibilities && responsibilities.length > 0">
            <h4 class="text-lg font-semibold text-dark mb-3">
                Responsabilidades y logros
            </h4>
            <ul class="space-y-2">
                <li 
                    v-for="(responsibility, index) in responsibilities"
                    :key="index"
                    class="
                        flex items-start gap-2 text-gray-700
                        before:content-['•'] before:text-primary before:font-bold
                    "
                >
                    <span>{{ responsibility }}</span>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
defineProps({
    position: String,
    company: String,
    duration: String,
    location: String,
    responsibilities: {
        type: Array,
        default: () => [],
    },
});
</script>

