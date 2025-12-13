<template>
    <AdminLayout>
        <Head title="Habilidades" />
        
        <div class="min-h-screen bg-gradient-to-br from-soft via-warm to-white py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-4xl font-bold text-dark mb-2">Habilidades</h1>
                        <p class="text-gray-600">Gestiona tus habilidades técnicas</p>
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
                        {{ showForm ? 'Cancelar' : 'Nueva Habilidad' }}
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
                        {{ editingSkill ? 'Editar Habilidad' : 'Nueva Habilidad' }}
                    </h2>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre</label>
                                <input
                                    v-model="form.name"
                                    type="text"
                                    class="
                                        w-full px-4 py-3 rounded-xl
                                        border border-gray-300
                                        focus:border-primary focus:ring-2 focus:ring-primary/20
                                        transition-all duration-200
                                    "
                                    placeholder="Ej: React, Laravel..."
                                    required
                                />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Icono</label>
                                <input
                                    @change="handleIconChange"
                                    type="file"
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
                            </div>
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
                        </div>
                        <div class="flex items-center gap-3">
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
                            <label class="text-sm font-medium text-gray-700 cursor-pointer">Habilidad activa</label>
                        </div>
                        <div class="flex justify-end gap-4 pt-4 border-t border-gray-100">
                            <button
                                type="button"
                                @click="showForm = false; editingSkill = null; form.reset()"
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

                <!-- Skills Grid -->
                <div v-if="skills.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <div
                        v-for="skill in skills"
                        :key="skill.id"
                        class="
                            group relative
                            bg-white rounded-2xl
                            p-6 shadow-lg hover:shadow-2xl
                            transition-all duration-300
                            hover:-translate-y-1
                            border border-gray-100
                            flex flex-col items-center text-center
                        "
                    >
                        <!-- Icon -->
                        <div class="mb-4 w-20 h-20 rounded-xl bg-gray-50 flex items-center justify-center group-hover:bg-primary/5 transition-colors">
                            <img
                                v-if="skill.icon_url || skill.icon"
                                :src="skill.icon_url || getImageUrl(skill.icon)"
                                :alt="skill.name"
                                class="h-12 w-12 object-contain"
                            />
                            <svg v-else class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>

                        <!-- Name -->
                        <h3 class="text-lg font-bold text-dark mb-2">{{ skill.name }}</h3>

                        <!-- Status & Order -->
                        <div class="flex items-center gap-3 mb-4">
                            <span
                                :class="[
                                    skill.is_active
                                        ? 'bg-green-100 text-green-800 border-green-200'
                                        : 'bg-red-100 text-red-800 border-red-200',
                                    'px-3 py-1 text-xs font-semibold rounded-full border'
                                ]"
                            >
                                {{ skill.is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                            <span class="text-xs text-gray-500">Orden: {{ skill.order }}</span>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-center gap-4 pt-4 border-t border-gray-100 w-full">
                            <button
                                @click="editSkill(skill)"
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
                                @click="deleteSkill(skill.id)"
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay habilidades</h3>
                    <p class="text-gray-600 mb-6">Comienza agregando tu primera habilidad</p>
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
                        Agregar Habilidad
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
    skills: Array,
});

const showForm = ref(false);
const editingSkill = ref(null);

const form = useForm({
    name: '',
    icon: null,
    order: 0,
    is_active: true,
});

const processing = computed(() => form.processing);

const handleIconChange = (event) => {
    form.icon = event.target.files[0];
};

const editSkill = (skill) => {
    editingSkill.value = skill;
    form.name = skill.name;
    form.order = skill.order;
    form.is_active = skill.is_active;
    showForm.value = true;
};

const submit = () => {
    if (editingSkill.value) {
        // Si hay archivo, usar POST con _method, sino usar PATCH directamente
        if (form.icon) {
            form._method = 'PATCH';
            form.post(route('admin.skills.update', editingSkill.value.id), {
                forceFormData: true,
                onSuccess: () => {
                    form.reset();
                    showForm.value = false;
                    editingSkill.value = null;
                },
            });
        } else {
            form.patch(route('admin.skills.update', editingSkill.value.id), {
                onSuccess: () => {
                    form.reset();
                    showForm.value = false;
                    editingSkill.value = null;
                },
            });
        }
    } else {
        form.post(route('admin.skills.store'), {
            forceFormData: true,
            onSuccess: () => {
                form.reset();
                showForm.value = false;
            },
        });
    }
};

const deleteSkill = (id) => {
    if (confirm('¿Estás seguro de eliminar esta habilidad?')) {
        router.delete(route('admin.skills.destroy', id));
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
</script>
