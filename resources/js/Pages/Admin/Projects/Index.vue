<template>
    <AdminLayout>
        <Head title="Proyectos" />
        
        <div class="min-h-screen bg-gradient-to-br from-soft via-warm to-white py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-4xl font-bold text-dark mb-2">Proyectos</h1>
                        <p class="text-gray-600">Gestiona todos tus proyectos</p>
                    </div>
                    <Link
                        :href="route('admin.projects.create')"
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
                        Nuevo Proyecto
                    </Link>
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

                <!-- Projects Grid -->
                <div v-if="projects.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="project in projects"
                        :key="project.id"
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
                                    project.is_active
                                        ? 'bg-green-100 text-green-800 border-green-200'
                                        : 'bg-red-100 text-red-800 border-red-200',
                                    'px-3 py-1 text-xs font-semibold rounded-full border'
                                ]"
                            >
                                {{ project.is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-dark mb-2 pr-16">{{ project.title }}</h3>
                            <p class="text-gray-600 text-sm line-clamp-2 mb-4">
                                {{ project.description }}
                            </p>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Orden: {{ project.order }}
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <Link
                                :href="route('admin.projects.edit', project.id)"
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
                            </Link>
                            <button
                                @click="deleteProject(project.id)"
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay proyectos</h3>
                    <p class="text-gray-600 mb-6">Comienza creando tu primer proyecto</p>
                    <Link
                        :href="route('admin.projects.create')"
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
                        Crear Proyecto
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

defineProps({
    projects: Array,
});

const deleteProject = (id) => {
    if (confirm('¿Estás seguro de eliminar este proyecto?')) {
        router.delete(route('admin.projects.destroy', id));
    }
};
</script>

