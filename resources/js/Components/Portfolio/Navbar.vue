<template>
    <nav class="bg-white shadow-lg fixed top-0 left-0 right-0 z-50 font-nunito">
        <div class="container mx-auto flex items-center justify-between py-4 px-6">
            <div class="flex items-center">
                <a href="/" class="text-dark font-bold text-2xl">
                    <img 
                        class="w-12 h-auto rounded-full hover:scale-110 transition-transform duration-300" 
                        src="/imgs/favicon.ico" 
                        alt="Logo" 
                    />
                </a>
            </div>

            <div class="hidden md:flex items-center space-x-8">
                <a
                    v-for="item in menuItems"
                    :key="item"
                    :href="`#${item}`"
                    @click="handleMenuClick(item)"
                    class="
                        relative px-2 py-1
                        text-dark capitalize font-medium
                        transition-all duration-300
                        hover:text-primary
                        after:content-['']
                        after:absolute after:left-0 after:bottom-0
                        after:w-full after:h-0.5 after:bg-primary
                        after:transform after:scale-x-0
                        after:transition-transform after:duration-300
                        hover:after:scale-x-100
                    "
                >
                    {{ item }}
                </a>
                <button
                    v-if="user?.cv_file || user?.cv_file_url"
                    @click="showCvModal = true"
                    class="
                        bg-primary text-white px-4 py-2 rounded-full
                        hover:bg-primary/80 transition-colors duration-300
                        font-medium
                    "
                >
                    Ver CV
                </button>
            </div>

            <div class="md:hidden">
                <button
                    @click="toggleMenu"
                    class="text-dark hover:text-primary transition-colors duration-300 focus:outline-none"
                    aria-controls="mobile-menu"
                    :aria-expanded="isOpen"
                >
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
            </div>
        </div>

        <div 
            id="mobile-menu" 
            :class="[
                isOpen ? 'block' : 'hidden',
                'md:hidden bg-white shadow-lg transform transition-all duration-300'
            ]"
        >
            <ul class="flex flex-col items-center space-y-4 py-4">
                <li v-for="item in menuItems" :key="item">
                    <a 
                        :href="`#${item}`" 
                        @click="handleMenuClick(item)"
                        class="
                            text-dark capitalize font-medium
                            hover:text-primary transition-colors duration-300
                            block px-4 py-2
                        "
                    >
                        {{ item }}
                    </a>
                </li>
                <li v-if="user?.cv_file || user?.cv_file_url">
                    <button
                        @click="showCvModal = true"
                        class="
                            bg-primary text-white px-4 py-2 rounded-full
                            hover:bg-primary/80 transition-colors duration-300
                            font-medium
                            w-full
                        "
                    >
                        Ver CV
                    </button>
                </li>
            </ul>
        </div>

        <!-- CV Modal -->
        <CvModal
            v-if="user?.cv_file || user?.cv_file_url"
            :is-open="showCvModal"
            :cv-url="getCvUrl()"
            @close="showCvModal = false"
        />
    </nav>
</template>

<script setup>
import { ref, watch } from 'vue';
import CvModal from './CvModal.vue';
import { useAnalytics } from '@/composables/useAnalytics';

const props = defineProps({
    user: Object,
});

const { trackMenuClick, trackCvView } = useAnalytics();
const menuItems = ['inicio', 'habilidades', 'experiencia', 'proyectos', 'contacto'];
const isOpen = ref(false);
const showCvModal = ref(false);

const toggleMenu = () => {
    isOpen.value = !isOpen.value;
};

// Trackear cuando se abre el modal de CV
watch(showCvModal, (newVal) => {
    if (newVal) {
        trackCvView();
    }
});

const getCvUrl = () => {
    if (!props.user?.cv_file && !props.user?.cv_file_url) return '#';
    // Si viene del backend con URL completa, usar esa
    if (props.user?.cv_file_url) {
        return props.user.cv_file_url;
    }
    // Si ya es una URL completa, retornarla
    if (props.user.cv_file.startsWith('http')) return props.user.cv_file;
    // Fallback: construir URL manualmente
    const baseUrl = import.meta.env.VITE_APP_URL || '';
    if (import.meta.env.VITE_AWS_URL) {
        return `${import.meta.env.VITE_AWS_URL}/${props.user.cv_file}`;
    }
    return `${baseUrl}/storage/${props.user.cv_file}`;
};

const handleMenuClick = (item) => {
    trackMenuClick(item);
};
</script>

