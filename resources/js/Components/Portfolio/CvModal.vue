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
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

            <!-- Modal -->
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white shadow-xl transition-all w-full max-w-6xl max-h-[90vh] flex flex-col"
                    @click.stop
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900">Curriculum Vitae</h3>
                        <button
                            @click="close"
                            class="text-gray-400 hover:text-gray-500 transition-colors"
                        >
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- PDF Viewer -->
                    <div class="flex-1 overflow-hidden">
                        <iframe
                            :src="cvUrl"
                            class="w-full h-full min-h-[600px]"
                            frameborder="0"
                        ></iframe>
                    </div>

                    <!-- Footer -->
                    <div class="flex items-center justify-between border-t border-gray-200 px-6 py-4">
                        <button
                            @click="downloadCv"
                            class="
                                inline-flex items-center gap-2
                                px-4 py-2
                                bg-primary text-white
                                rounded-lg
                                hover:bg-primary/80
                                transition-colors
                                font-medium
                            "
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Descargar CV
                        </button>
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
import { useAnalytics } from '@/composables/useAnalytics';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    cvUrl: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['close']);
const { trackCvDownload } = useAnalytics();

const close = () => {
    emit('close');
};

const downloadCv = () => {
    // Trackear descarga
    trackCvDownload(props.cvUrl);
    
    // Crear un enlace temporal para descargar el archivo sin abrir nueva página
    const link = document.createElement('a');
    link.href = props.cvUrl;
    link.download = 'CV.pdf'; // Nombre del archivo a descargar
    // No usar target="_blank" para que no abra nueva página
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};
</script>

