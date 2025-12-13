<template>
    <AdminLayout>
        <Head title="Experiencias" />
        
        <div class="min-h-screen bg-gradient-to-br from-soft via-warm to-white py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-4xl font-bold text-dark mb-2">Experiencias</h1>
                        <p class="text-gray-600">Gestiona tu experiencia laboral</p>
                    </div>
                    <button
                        @click="showForm = !showForm"
                        class="
                            inline-flex items-center gap-2
                            bg-primary text-white px-6 py-3 rounded-xl
                            hover:bg-primary/90 shadow-lg hover:shadow-xl
                            transition-all duration-300
                            font-semibold
                        "
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="showForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4'" />
                        </svg>
                        {{ showForm ? 'Cancelar' : 'Nueva Experiencia' }}
                    </button>
                </div>

                <!-- Success Message -->
                <div 
                    v-if="$page.props.flash?.success" 
                    class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-md"
                >
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        {{ $page.props.flash.success }}
                    </div>
                </div>

                <!-- Form -->
                <div 
                    v-if="showForm" 
                    class="mb-8 bg-white rounded-2xl p-6 lg:p-8 shadow-xl border border-gray-100"
                >
                    <h2 class="text-2xl font-bold text-dark mb-6">
                        {{ editingExperience ? 'Editar Experiencia' : 'Nueva Experiencia' }}
                    </h2>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Cargo</label>
                                <input
                                    v-model="form.position"
                                    type="text"
                                    class="
                                        w-full px-4 py-3 rounded-xl
                                        border border-gray-300
                                        focus:border-primary focus:ring-2 focus:ring-primary/20
                                        transition-all duration-200
                                    "
                                    placeholder="Ej: Desarrollador Full Stack"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Empresa</label>
                                <input
                                    v-model="form.company"
                                    type="text"
                                    class="
                                        w-full px-4 py-3 rounded-xl
                                        border border-gray-300
                                        focus:border-primary focus:ring-2 focus:ring-primary/20
                                        transition-all duration-200
                                    "
                                    placeholder="Nombre de la empresa"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Duración</label>
                                <input
                                    v-model="form.duration"
                                    type="text"
                                    class="
                                        w-full px-4 py-3 rounded-xl
                                        border border-gray-300
                                        focus:border-primary focus:ring-2 focus:ring-primary/20
                                        transition-all duration-200
                                    "
                                    placeholder="Ej: Enero 2020 - Presente"
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Ubicación</label>
                                <input
                                    v-model="form.location"
                                    type="text"
                                    class="
                                        w-full px-4 py-3 rounded-xl
                                        border border-gray-300
                                        focus:border-primary focus:ring-2 focus:ring-primary/20
                                        transition-all duration-200
                                    "
                                    placeholder="Ej: Ciudad, País"
                                />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Responsabilidades (una por línea)
                            </label>
                            <textarea
                                v-model="responsibilitiesText"
                                rows="6"
                                class="
                                    w-full px-4 py-3 rounded-xl
                                    border border-gray-300
                                    focus:border-primary focus:ring-2 focus:ring-primary/20
                                    transition-all duration-200
                                    resize-none
                                "
                                placeholder="Responsabilidad 1&#10;Responsabilidad 2&#10;..."
                            ></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-6">
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
                                <label class="text-sm font-medium text-gray-700 cursor-pointer">Experiencia activa</label>
                            </div>
                        </div>
                        <div class="flex justify-end gap-4 pt-4 border-t border-gray-100">
                            <button
                                type="button"
                                @click="showForm = false; editingExperience = null; form.reset(); responsibilitiesText = ''"
                                class="
                                    px-6 py-3 rounded-xl
                                    border border-gray-300 text-gray-700
                                    hover:bg-gray-50
                                    transition-all duration-200
                                    font-semibold
                                "
                            >
                                Cancelar
                            </button>
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
                                {{ processing ? 'Guardando...' : 'Guardar' }}
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Experiences Grid -->
                <div v-if="experiences.length > 0" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div
                        v-for="experience in experiences"
                        :key="experience.id"
                        class="
                            group relative
                            bg-white rounded-2xl
                            p-6 shadow-lg hover:shadow-2xl
                            transition-all duration-300
                            hover:-translate-y-1
                            border border-gray-100
                        "
                    >
                        <!-- Status Badge -->
                        <div class="absolute top-4 right-4">
                            <span
                                :class="[
                                    experience.is_active
                                        ? 'bg-green-100 text-green-800 border-green-200'
                                        : 'bg-red-100 text-red-800 border-red-200',
                                    'px-3 py-1 text-xs font-semibold rounded-full border'
                                ]"
                            >
                                {{ experience.is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="mb-4 pr-16">
                            <h3 class="text-xl font-bold text-dark mb-1">{{ experience.position }}</h3>
                            <p class="text-lg font-semibold text-primary mb-2">{{ experience.company }}</p>
                            <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ experience.duration }}
                                </span>
                                <span v-if="experience.location" class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ experience.location }}
                                </span>
                            </div>
                            <div v-if="experience.responsibilities && experience.responsibilities.length > 0" class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Responsabilidades:</h4>
                                <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                                    <li v-for="(resp, index) in (Array.isArray(experience.responsibilities) ? experience.responsibilities : [])" :key="index">
                                        {{ resp }}
                                    </li>
                                </ul>
                            </div>
                            <div class="text-xs text-gray-500">
                                Orden: {{ experience.order }}
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <button
                                @click="editExperience(experience)"
                                class="
                                    flex items-center gap-2
                                    text-primary hover:text-primary/80
                                    font-medium text-sm
                                    transition-colors
                                "
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </button>
                            <button
                                @click="deleteExperience(experience.id)"
                                class="
                                    flex items-center gap-2
                                    text-red-600 hover:text-red-700
                                    font-medium text-sm
                                    transition-colors
                                "
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-white rounded-2xl p-12 text-center shadow-lg border border-gray-100">
                    <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay experiencias</h3>
                    <p class="text-gray-600 mb-6">Comienza agregando tu primera experiencia laboral</p>
                    <button
                        @click="showForm = true"
                        class="
                            inline-flex items-center gap-2
                            bg-primary text-white px-6 py-3 rounded-xl
                            hover:bg-primary/90 shadow-lg hover:shadow-xl
                            transition-all duration-300
                            font-semibold
                        "
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Agregar Experiencia
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    experiences: Array,
});

const showForm = ref(false);
const editingExperience = ref(null);
const responsibilitiesText = ref('');

const form = useForm({
    position: '',
    company: '',
    duration: '',
    location: '',
    responsibilities: [],
    order: 0,
    is_active: true,
});

const processing = computed(() => form.processing);

const editExperience = (experience) => {
    editingExperience.value = experience;
    form.position = experience.position;
    form.company = experience.company;
    form.duration = experience.duration;
    form.location = experience.location || '';
    form.order = experience.order;
    form.is_active = experience.is_active;
    responsibilitiesText.value = Array.isArray(experience.responsibilities)
        ? experience.responsibilities.join('\n')
        : '';
    showForm.value = true;
};

const submit = () => {
    form.responsibilities = responsibilitiesText.value
        .split('\n')
        .map(r => r.trim())
        .filter(r => r.length > 0);

    if (editingExperience.value) {
        form.patch(route('admin.experiences.update', editingExperience.value.id), {
            onSuccess: () => {
                form.reset();
                responsibilitiesText.value = '';
                showForm.value = false;
                editingExperience.value = null;
            },
        });
    } else {
        form.post(route('admin.experiences.store'), {
            onSuccess: () => {
                form.reset();
                responsibilitiesText.value = '';
                showForm.value = false;
            },
        });
    }
};

const deleteExperience = (id) => {
    if (confirm('¿Estás seguro de eliminar esta experiencia?')) {
        router.delete(route('admin.experiences.destroy', id));
    }
};
</script>
