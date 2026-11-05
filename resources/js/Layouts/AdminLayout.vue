<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <Link :href="route('dashboard')" class="text-xl font-bold text-primary">
                                Panel Admin
                            </Link>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <Link
                                :href="route('admin.projects.index')"
                                :class="[
                                    $page.url.startsWith('/admin/projects')
                                        ? 'border-primary text-gray-900'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                                ]"
                            >
                                Proyectos
                            </Link>
                            <Link
                                :href="route('admin.skills.index')"
                                :class="[
                                    $page.url.startsWith('/admin/skills')
                                        ? 'border-primary text-gray-900'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                                ]"
                            >
                                Habilidades
                            </Link>
                            <Link
                                :href="route('admin.experiences.index')"
                                :class="[
                                    $page.url.startsWith('/admin/experiences')
                                        ? 'border-primary text-gray-900'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                                ]"
                            >
                                Experiencias
                            </Link>
                            <Link
                                :href="route('admin.proposals.index')"
                                :class="[
                                    $page.url.startsWith('/admin/proposals')
                                        ? 'border-primary text-gray-900'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                                ]"
                            >
                                Propuestas
                            </Link>
                            <Link
                                :href="route('admin.profile.edit')"
                                :class="[
                                    $page.url.startsWith('/admin/profile')
                                        ? 'border-primary text-gray-900'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                    'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
                                ]"
                            >
                                Perfil
                            </Link>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <Link
                            :href="route('portfolio.index')"
                            class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm font-medium"
                            target="_blank"
                        >
                            Ver Portfolio
                        </Link>
                        <Link
                            :href="route('profile.edit')"
                            class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm font-medium"
                        >
                            Mi Cuenta
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>

