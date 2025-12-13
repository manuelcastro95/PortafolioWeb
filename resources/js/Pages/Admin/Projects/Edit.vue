<template>
    <AdminLayout>
        <Head title="Editar Proyecto" />
        
        <div class="min-h-screen bg-gradient-to-br from-soft via-warm to-white py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-6 lg:p-8">
                        <div class="mb-8">
                            <h2 class="text-3xl font-bold text-dark mb-2">Editar Proyecto</h2>
                            <p class="text-gray-600">Actualiza la información de tu proyecto</p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Título</label>
                                <input
                                    v-model="form.title"
                                    type="text"
                                    class="
                                        w-full px-4 py-3 rounded-xl
                                        border border-gray-300
                                        focus:border-primary focus:ring-2 focus:ring-primary/20
                                        transition-all duration-200
                                    "
                                    required
                                />
                                <div v-if="errors.title" class="text-red-600 text-sm mt-1">{{ errors.title }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Descripción</label>
                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    class="
                                        w-full px-4 py-3 rounded-xl
                                        border border-gray-300
                                        focus:border-primary focus:ring-2 focus:ring-primary/20
                                        transition-all duration-200
                                        resize-none
                                    "
                                    required
                                ></textarea>
                                <div v-if="errors.description" class="text-red-600 text-sm mt-1">{{ errors.description }}</div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Link (URL)</label>
                                    <input
                                        v-model="form.link"
                                        type="url"
                                        class="
                                            w-full px-4 py-3 rounded-xl
                                            border border-gray-300
                                            focus:border-primary focus:ring-2 focus:ring-primary/20
                                            transition-all duration-200
                                        "
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">GitHub</label>
                                    <input
                                        v-model="form.github"
                                        type="url"
                                        class="
                                            w-full px-4 py-3 rounded-xl
                                            border border-gray-300
                                            focus:border-primary focus:ring-2 focus:ring-primary/20
                                            transition-all duration-200
                                        "
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Orden</label>
                                    <input
                                        v-model.number="form.order"
                                        type="number"
                                        class="
                                            w-full px-4 py-3 rounded-xl
                                            border border-gray-300
                                            focus:border-primary focus:ring-2 focus:ring-primary/20
                                            transition-all duration-200
                                        "
                                    />
                                </div>

                                <div class="flex items-center gap-3 pt-8">
                                    <input
                                        v-model="form.is_active"
                                        type="checkbox"
                                        class="
                                            w-5 h-5 rounded
                                            border-gray-300 text-primary
                                            focus:ring-2 focus:ring-primary/20
                                            cursor-pointer
                                        "
                                    />
                                    <label class="text-sm font-medium text-gray-700 cursor-pointer">Proyecto activo</label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Habilidades</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                    <label
                                        v-for="skill in skills"
                                        :key="skill.id"
                                        class="
                                            flex items-center gap-2 p-3 rounded-xl
                                            border border-gray-200 hover:border-primary/50
                                            cursor-pointer transition-all duration-200
                                            hover:bg-primary/5
                                        "
                                    >
                                        <input
                                            v-model="form.skills"
                                            type="checkbox"
                                            :value="skill.id"
                                            class="
                                                rounded border-gray-300 text-primary
                                                focus:ring-2 focus:ring-primary/20
                                                cursor-pointer
                                            "
                                        />
                                        <span class="text-sm text-gray-700">{{ skill.name }}</span>
                                    </label>
                                </div>
                            </div>

                            <div v-if="project.images && project.images.length > 0">
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Imágenes Actuales</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    <div
                                        v-for="image in project.images"
                                        :key="image.id"
                                        class="relative group"
                                    >
                                        <img
                                            :src="image.image_url || getImageUrl(image.image_path)"
                                            :alt="project.title"
                                            class="w-full h-32 object-cover rounded-xl"
                                        />
                                        <button
                                            type="button"
                                            @click="deleteImage(image.id)"
                                            class="
                                                absolute top-2 right-2
                                                bg-red-500 text-white rounded-full p-2
                                                hover:bg-red-600 shadow-lg
                                                transition-all duration-200
                                                opacity-0 group-hover:opacity-100
                                            "
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Agregar Nuevas Imágenes</label>
                                <input
                                    @change="handleImageChange"
                                    type="file"
                                    multiple
                                    accept="image/*"
                                    class="
                                        w-full px-4 py-3 rounded-xl
                                        border border-gray-300
                                        focus:border-primary focus:ring-2 focus:ring-primary/20
                                        transition-all duration-200
                                        file:mr-4 file:py-2 file:px-4 file:rounded-lg
                                        file:border-0 file:text-sm file:font-semibold
                                        file:bg-primary file:text-white
                                        hover:file:bg-primary/90
                                        cursor-pointer
                                    "
                                />
                                <div v-if="errors.images" class="text-red-600 text-sm mt-1">{{ errors.images }}</div>
                            </div>

                            <div class="flex justify-end gap-4 pt-6 border-t border-gray-100">
                                <Link
                                    :href="route('admin.projects.index')"
                                    class="
                                        px-6 py-3 rounded-xl
                                        border border-gray-300 text-gray-700
                                        hover:bg-gray-50
                                        transition-all duration-200
                                        font-semibold
                                    "
                                >
                                    Cancelar
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="processing"
                                    class="
                                        px-6 py-3 rounded-xl
                                        bg-primary text-white
                                        hover:bg-primary/90 shadow-lg hover:shadow-xl
                                        disabled:opacity-50 disabled:cursor-not-allowed
                                        transition-all duration-200
                                        font-semibold
                                    "
                                >
                                    {{ processing ? 'Guardando...' : 'Actualizar Proyecto' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    project: Object,
    skills: Array,
    errors: Object,
});

const form = useForm({
    title: props.project.title,
    description: props.project.description,
    link: props.project.link || '',
    github: props.project.github || '',
    order: props.project.order,
    is_active: props.project.is_active,
    skills: props.project.skills?.map(s => s.id) || [],
    images: [],
    delete_images: [],
});

const processing = computed(() => form.processing);

const handleImageChange = (event) => {
    form.images = Array.from(event.target.files);
};

const deleteImage = (imageId) => {
    if (!form.delete_images.includes(imageId)) {
        form.delete_images.push(imageId);
    }
};

const getImageUrl = (path) => {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    const baseUrl = import.meta.env.VITE_APP_URL || '';
    if (import.meta.env.VITE_AWS_URL) {
        return `${import.meta.env.VITE_AWS_URL}/${path}`;
    }
    return `${baseUrl}/storage/${path}`;
};

const submit = () => {
    form.post(route('admin.projects.update', props.project.id), {
        forceFormData: true,
    });
};
</script>

