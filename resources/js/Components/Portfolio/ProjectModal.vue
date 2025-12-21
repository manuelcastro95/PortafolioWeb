<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="isOpen"
            class="fixed inset-0 z-50 overflow-y-auto"
            @click.self="close"
        >
            <!-- Overlay -->
            <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

            <!-- Modal -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="relative transform overflow-hidden rounded-2xl bg-white shadow-2xl transition-all w-full max-w-6xl h-[70vh] flex flex-col"
                    @click.stop
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-primary/5 to-accent/5 flex-shrink-0">
                        <div class="flex-1">
                            <h2 class="text-2xl sm:text-3xl font-bold text-dark">{{ project?.title }}</h2>
                        </div>
                        <button
                            @click="close"
                            class="
                                ml-4
                                p-2
                                text-gray-400 hover:text-gray-600
                                rounded-full hover:bg-gray-100
                                transition-colors
                            "
                        >
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content - Two Column Layout -->
                    <div class="flex-1 overflow-hidden flex flex-col lg:flex-row">
                        <!-- Left Column - Image Carousel -->
                        <div v-if="project?.images && project.images.length > 0" class="w-full lg:w-1/2 flex-shrink-0 bg-gray-50 p-6 flex items-center justify-center">
                            <div class="relative w-full h-[600px] rounded-xl overflow-hidden bg-gray-100">
                                <div class="relative w-full h-full">
                                    <img
                                        :src="currentImageUrl"
                                        :alt="project.title"
                                        class="w-full h-full object-contain transition-opacity duration-300"
                                    />
                                    
                                    <!-- Navigation Arrows -->
                                    <button
                                        v-if="project.images.length > 1"
                                        @click="previousImage"
                                        class="
                                            absolute left-4 top-1/2 -translate-y-1/2
                                            p-2 rounded-full
                                            bg-black/50 hover:bg-black/70
                                            text-white
                                            transition-all duration-200
                                            z-10
                                        "
                                    >
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button
                                        v-if="project.images.length > 1"
                                        @click="nextImage"
                                        class="
                                            absolute right-4 top-1/2 -translate-y-1/2
                                            p-2 rounded-full
                                            bg-black/50 hover:bg-black/70
                                            text-white
                                            transition-all duration-200
                                            z-10
                                        "
                                    >
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>

                                    <!-- Image Indicators -->
                                    <div
                                        v-if="project.images.length > 1"
                                        class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2 z-10"
                                    >
                                        <button
                                            v-for="(image, index) in project.images"
                                            :key="index"
                                            @click="currentImageIndex = index"
                                            :class="[
                                                'w-2 h-2 rounded-full transition-all duration-200',
                                                currentImageIndex === index
                                                    ? 'bg-white w-8'
                                                    : 'bg-white/50 hover:bg-white/75'
                                            ]"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Description -->
                        <div class="w-full lg:w-1/2 flex flex-col overflow-y-auto px-6 py-6">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-dark mb-4">Descripción</h3>
                                <p class="text-gray-700 leading-relaxed whitespace-pre-line text-base mb-6">{{ project?.description }}</p>
                                
                                <!-- Skills -->
                                <div v-if="project?.skills && project.skills.length > 0">
                                    <h3 class="text-xl font-semibold text-dark mb-4">Tecnologías</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <span 
                                            v-for="skill in project.skills" 
                                            :key="skill.id"
                                            class="
                                                px-4 py-2 text-sm font-medium
                                                bg-primary/10 text-primary
                                                rounded-full
                                                border border-primary/20
                                            "
                                        >
                                            {{ skill.name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-end gap-4 border-t border-gray-200 px-6 py-4 bg-gray-50 flex-shrink-0">
                        <a 
                            v-if="project?.link"
                            :href="project.link"
                            target="_blank"
                            @click="handleLinkClick('demo')"
                            class="
                                inline-flex items-center gap-2
                                px-4 py-2
                                bg-primary text-white
                                rounded-lg
                                hover:bg-primary/90
                                transition-colors
                                font-medium
                            "
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Ver Demo
                        </a>
                        <a 
                            v-if="project?.github"
                            :href="project.github"
                            target="_blank"
                            @click="handleLinkClick('github')"
                            class="
                                inline-flex items-center gap-2
                                px-4 py-2
                                bg-gray-800 text-white
                                rounded-lg
                                hover:bg-gray-900
                                transition-colors
                                font-medium
                            "
                        >
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                            </svg>
                            Ver Código
                        </a>
                        <button
                            @click="close"
                            class="
                                px-4 py-2
                                border border-gray-300
                                rounded-lg
                                text-gray-700
                                hover:bg-gray-50
                                transition-colors
                                font-medium
                            "
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useAnalytics } from '@/composables/useAnalytics';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    project: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close']);

const analytics = useAnalytics();
const { trackProjectClick } = analytics;

const currentImageIndex = ref(0);

const currentImageUrl = computed(() => {
    if (!props.project?.images || props.project.images.length === 0) {
        return null;
    }
    const image = props.project.images[currentImageIndex.value];
    if (!image) return null;
    
    if (image.image_url) return image.image_url;
    if (image.image_path) return getImageUrl(image.image_path);
    return null;
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

const nextImage = () => {
    if (props.project?.images && props.project.images.length > 0) {
        currentImageIndex.value = (currentImageIndex.value + 1) % props.project.images.length;
    }
};

const previousImage = () => {
    if (props.project?.images && props.project.images.length > 0) {
        currentImageIndex.value = currentImageIndex.value === 0 
            ? props.project.images.length - 1 
            : currentImageIndex.value - 1;
    }
};

const close = () => {
    emit('close');
};

const handleLinkClick = (linkType) => {
    if (props.project) {
        const url = linkType === 'demo' ? props.project.link : props.project.github;
        trackProjectClick(props.project.title, linkType, url);
    }
};

// Reset image index when project changes
watch(() => props.project, () => {
    currentImageIndex.value = 0;
});

// Handle keyboard navigation
const handleKeydown = (e) => {
    if (!props.isOpen) return;
    
    if (e.key === 'Escape') {
        close();
    } else if (e.key === 'ArrowLeft') {
        previousImage();
    } else if (e.key === 'ArrowRight') {
        nextImage();
    }
};

watch(() => props.isOpen, (isOpen) => {
    if (isOpen) {
        document.addEventListener('keydown', handleKeydown);
        document.body.style.overflow = 'hidden';
    } else {
        document.removeEventListener('keydown', handleKeydown);
        document.body.style.overflow = null;
    }
});
</script>

