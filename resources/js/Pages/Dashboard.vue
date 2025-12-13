<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            projects: { total: 0, active: 0 },
            skills: { total: 0, active: 0 },
            experiences: { total: 0, active: 0 },
        }),
    },
});
</script>

<template>
    <AdminLayout>
        <Head title="Dashboard" />

        <div class="min-h-screen bg-gradient-to-br from-soft via-warm to-white py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-4xl font-bold text-dark mb-2">Panel de Administración</h1>
                    <p class="text-gray-600">Gestiona tu portfolio desde aquí</p>
                </div>

                <!-- Bento Grid Layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 auto-rows-fr">
                    <!-- Proyectos - Large Card -->
                    <Link
                        :href="route('admin.projects.index')"
                        class="
                            group relative
                            lg:col-span-2 lg:row-span-2
                            bg-white rounded-3xl
                            p-6 lg:p-8
                            shadow-lg hover:shadow-2xl
                            transition-all duration-300
                            hover:-translate-y-1
                            overflow-hidden
                            border border-gray-100
                        "
                    >
                        <!-- Background Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-accent/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <!-- Content -->
                        <div class="relative z-10 h-full flex flex-col">
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-primary bg-primary/10 px-3 py-1 rounded-full">
                                    Gestionar
                                </span>
                            </div>
                            
                            <h2 class="text-3xl font-bold text-dark mb-2">Proyectos</h2>
                            <p class="text-gray-600 mb-6 flex-grow">Administra y organiza todos tus proyectos</p>
                            
                            <div class="flex items-end justify-between">
                                <div>
                                    <div class="text-4xl font-bold text-primary mb-1">{{ stats.projects.active }}</div>
                                    <div class="text-sm text-gray-500">activos de {{ stats.projects.total }} total</div>
                                </div>
                                <div class="text-primary opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </Link>

                    <!-- Habilidades - Medium Card -->
                    <Link
                        :href="route('admin.skills.index')"
                        class="
                            group relative
                            lg:col-span-2
                            bg-white rounded-3xl
                            p-6
                            shadow-lg hover:shadow-2xl
                            transition-all duration-300
                            hover:-translate-y-1
                            overflow-hidden
                            border border-gray-100
                        "
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-accent/5 via-transparent to-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center group-hover:bg-accent/20 transition-colors">
                                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                </div>
                            </div>
                            
                            <h2 class="text-2xl font-bold text-dark mb-2">Habilidades</h2>
                            <p class="text-gray-600 text-sm mb-4">Gestiona tus habilidades técnicas</p>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-3xl font-bold text-accent">{{ stats.skills.active }}</div>
                                    <div class="text-xs text-gray-500">activas</div>
                                </div>
                                <div class="text-accent opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </Link>

                    <!-- Experiencias - Medium Card -->
                    <Link
                        :href="route('admin.experiences.index')"
                        class="
                            group relative
                            lg:col-span-2
                            bg-white rounded-3xl
                            p-6
                            shadow-lg hover:shadow-2xl
                            transition-all duration-300
                            hover:-translate-y-1
                            overflow-hidden
                            border border-gray-100
                        "
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-secondary/5 via-transparent to-primary/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center group-hover:bg-secondary/20 transition-colors">
                                    <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            
                            <h2 class="text-2xl font-bold text-dark mb-2">Experiencias</h2>
                            <p class="text-gray-600 text-sm mb-4">Administra tu experiencia laboral</p>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-3xl font-bold text-secondary">{{ stats.experiences.active }}</div>
                                    <div class="text-xs text-gray-500">activas</div>
                                </div>
                                <div class="text-secondary opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </Link>

                    <!-- Perfil - Small Card -->
                    <Link
                        :href="route('admin.profile.edit')"
                        class="
                            group relative
                            bg-white rounded-3xl
                            p-6
                            shadow-lg hover:shadow-2xl
                            transition-all duration-300
                            hover:-translate-y-1
                            overflow-hidden
                            border border-gray-100
                        "
                    >
                        <div class="absolute inset-0 bg-gradient-to-br from-primary/5 via-transparent to-accent/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <div class="relative z-10">
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-4 group-hover:bg-primary/20 transition-colors">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            
                            <h2 class="text-xl font-bold text-dark mb-1">Perfil</h2>
                            <p class="text-gray-600 text-sm mb-4">Edita tu información personal</p>
                            
                            <div class="text-primary opacity-0 group-hover:opacity-100 transition-opacity flex items-center text-sm font-medium">
                                <span>Editar</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </div>
                    </Link>

                    <!-- Ver Portfolio - Small Card -->
                    <Link
                        :href="route('portfolio.index')"
                        target="_blank"
                        class="
                            group relative
                            bg-gradient-to-br from-primary to-accent
                            rounded-3xl
                            p-6
                            shadow-lg hover:shadow-2xl
                            transition-all duration-300
                            hover:-translate-y-1
                            overflow-hidden
                        "
                    >
                        <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        
                        <div class="relative z-10 text-white">
                            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center mb-4 group-hover:bg-white/30 transition-colors backdrop-blur-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            
                            <h2 class="text-xl font-bold mb-1">Ver Portfolio</h2>
                            <p class="text-white/90 text-sm">Vista previa pública</p>
                            
                            <div class="mt-4 flex items-center text-sm font-medium">
                                <span>Abrir</span>
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Quick Stats -->
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Proyectos</p>
                                <p class="text-2xl font-bold text-dark">{{ stats.projects.total }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Habilidades</p>
                                <p class="text-2xl font-bold text-dark">{{ stats.skills.total }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 shadow-md border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total Experiencias</p>
                                <p class="text-2xl font-bold text-dark">{{ stats.experiences.total }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
