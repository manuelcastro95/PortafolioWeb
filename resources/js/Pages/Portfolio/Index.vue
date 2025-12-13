<template>
    <Head title="Portfolio - Manuel Castro" />
    
    <div class="min-h-screen bg-gradient-to-br from-soft via-warm to-white">
        <!-- Navbar -->
        <Navbar :user="user" />
        
        <!-- Hero Section - Asymmetric Bento -->
        <section id="inicio" class="container mx-auto px-4 py-12 sm:py-16 lg:py-20 mt-16">
            <div class="grid grid-cols-1 md:grid-cols-6 lg:grid-cols-8 gap-4 lg:gap-6 auto-rows-fr">
                <!-- Main Content - Large Card -->
                <div class="
                    md:col-span-4 lg:col-span-5
                    bg-gradient-to-br from-primary/5 via-white to-accent/5
                    rounded-3xl p-8 sm:p-10 lg:p-12
                    shadow-xl border border-gray-100
                    flex flex-col justify-center
                ">
                    <div class="mb-6">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl mb-4 sm:mb-6 font-bold text-dark leading-tight">
                            ¡Hola, soy <span class="text-primary">{{ user?.name || 'Manuel Castro' }}</span>!
                        </h1>
                        <div class="text-2xl sm:text-3xl lg:text-4xl font-semibold text-dark">
                            Desarrollador <TypeWriter :words="['web', 'front-end', 'back-end']" :delay="2000" />
                        </div>
                    </div>
                    <p class="
                        text-base sm:text-lg lg:text-xl 
                        text-gray-700 leading-relaxed
                    ">
                        {{ user?.bio || 'Desarrollador FullStack con más de 3 años de experiencia especializado en el diseño e implementación de soluciones web empresariales. Destaco por mi capacidad para crear aplicaciones robustas y escalables, con un fuerte enfoque en la arquitectura limpia y las mejores prácticas de desarrollo.' }}
                    </p>
                </div>

                <!-- Profile Image - Medium Card -->
                <div class="
                    md:col-span-2 lg:col-span-3
                    bg-white rounded-3xl p-6 sm:p-8
                    shadow-xl border border-gray-100
                    flex items-center justify-center
                ">
                    <div class="
                        relative
                        w-full max-w-xs aspect-square
                        rounded-3xl overflow-hidden
                        shadow-2xl ring-4 ring-white
                    ">
                        <img 
                            v-if="user?.profile_image_url || user?.profile_image" 
                            :src="user?.profile_image_url || getImageUrl(user?.profile_image)" 
                            class="object-cover w-full h-full" 
                            alt="Manuel Castro" 
                        />
                        <img 
                            v-else 
                            src="/imgs/perfil.png" 
                            class="object-cover w-full h-full" 
                            alt="Manuel Castro" 
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- Skills Section - Varied Bento Grid -->
        <section id="habilidades" class="container mx-auto px-4 py-12 sm:py-16 lg:py-20">
            <div class="mb-8 sm:mb-12 text-center">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-dark mb-4">
                    Habilidades
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                    Tecnologías y herramientas que domino
                </p>
            </div>

            <div class="
                grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6
                gap-4 sm:gap-6
            ">
                <div
                    v-for="(skill, index) in skills"
                    :key="index"
                    :class="[
                        'group relative rounded-2xl p-4 sm:p-6',
                        'shadow-lg hover:shadow-2xl transform transition-all duration-300',
                        'hover:-translate-y-2 border border-gray-100',
                        'flex flex-col items-center justify-center aspect-square',
                        index % 6 === 0 ? 'bg-gradient-to-br from-primary/10 to-white' :
                        index % 6 === 1 ? 'bg-gradient-to-br from-accent/10 to-white' :
                        index % 6 === 2 ? 'bg-gradient-to-br from-secondary/10 to-white' :
                        index % 6 === 3 ? 'bg-white' :
                        index % 6 === 4 ? 'bg-gradient-to-br from-primary/5 to-accent/5' :
                        'bg-white'
                    ]"
                >
                    <div class="w-12 h-12 sm:w-16 sm:h-16 mb-3 sm:mb-4 flex items-center justify-center">
                        <img
                            v-if="skill.icon_url || skill.icon"
                            :src="skill.icon_url || getImageUrl(skill.icon)"
                            :alt="`${skill.name} logo`"
                            class="
                                w-full h-full object-contain
                                transition-transform duration-300
                                group-hover:scale-110
                            "
                        />
                        <div v-else class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                        </div>
                    </div>
                    <p class="
                        text-xs sm:text-sm font-semibold text-gray-700
                        group-hover:text-primary transition-colors duration-300
                        text-center
                    ">
                        {{ skill.name }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Experience Section - Bento Cards -->
        <section id="experiencia" class="container mx-auto px-4 py-12 sm:py-16 lg:py-20">
            <div class="mb-8 sm:mb-12 text-center">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-dark mb-4">
                    Experiencia
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                    Mi trayectoria profesional en desarrollo de software
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8 max-w-7xl mx-auto">
                <div
                    v-for="(experience, index) in experiences"
                    :key="index"
                    :class="[
                        'rounded-2xl p-6 sm:p-8 shadow-lg hover:shadow-2xl',
                        'transform transition-all duration-300 hover:-translate-y-2',
                        'border border-gray-100',
                        index % 2 === 0 
                            ? 'bg-gradient-to-br from-white to-primary/5' 
                            : 'bg-gradient-to-br from-white to-accent/5'
                    ]"
                >
                    <div class="flex items-start gap-4 mb-4">
                        <div class="
                            w-12 h-12 rounded-xl
                            flex items-center justify-center
                            flex-shrink-0
                            bg-primary/10
                        ">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-dark mb-1">{{ experience.position }}</h3>
                            <p class="text-xl font-semibold text-primary mb-2">{{ experience.company }}</p>
                        </div>
                    </div>
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
                    <div v-if="experience.responsibilities && experience.responsibilities.length > 0">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Responsabilidades:</h4>
                        <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                            <li v-for="(resp, idx) in (Array.isArray(experience.responsibilities) ? experience.responsibilities : [])" :key="idx">
                                {{ resp }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Projects Section - Mixed Bento Grid -->
        <section id="proyectos" class="container mx-auto px-4 py-12 sm:py-16 lg:py-20">
            <div class="mb-8 sm:mb-12 text-center">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-dark mb-4">
                    Proyectos
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                    Explora algunos de mis proyectos más destacados
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <div 
                    v-for="(proyecto, index) in projects"
                    :key="index" 
                    :class="[
                        'group relative rounded-2xl overflow-hidden',
                        'shadow-lg hover:shadow-2xl transform transition-all duration-300',
                        'hover:-translate-y-2 border border-gray-100',
                        index % 3 === 0 ? 'bg-gradient-to-br from-white to-primary/5' :
                        index % 3 === 1 ? 'bg-white' :
                        'bg-gradient-to-br from-white to-accent/5'
                    ]"
                >
                    <!-- Image Section -->
                    <div class="relative h-48 sm:h-56 lg:h-64 overflow-hidden bg-gray-100">
                        <ProjectCarousel :images="proyecto.images" />
                        <div class="
                            absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent
                            opacity-0 group-hover:opacity-100 transition-opacity duration-300
                            flex items-end p-6
                        ">
                            <p class="text-white text-sm leading-relaxed">{{ proyecto.description }}</p>
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="p-5 sm:p-6">
                        <h3 class="text-xl sm:text-2xl font-bold text-dark mb-3">{{ proyecto.title }}</h3>
                        
                        <!-- Skills Tags -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span 
                                v-for="skill in proyecto.skills" 
                                :key="skill.id"
                                class="
                                    px-3 py-1 text-xs font-medium
                                    bg-primary/10 text-primary
                                    rounded-full
                                "
                            >
                                {{ skill.name }}
                            </span>
                        </div>

                        <!-- Links -->
                        <div class="flex justify-end gap-4 pt-4 border-t border-gray-100">
                            <a 
                                v-if="proyecto.link"
                                :href="proyecto.link"
                                target="_blank"
                                class="
                                    flex items-center gap-2
                                    text-accent hover:text-primary
                                    transition-colors duration-300
                                    font-medium text-sm
                                "
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Demo
                            </a>
                            <a 
                                v-if="proyecto.github"
                                :href="proyecto.github"
                                target="_blank"
                                class="
                                    flex items-center gap-2
                                    text-accent hover:text-primary
                                    transition-colors duration-300
                                    font-medium text-sm
                                "
                            >
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                                </svg>
                                Código
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section - Colorful Bento Cards -->
        <section id="contacto" class="container mx-auto px-4 py-12 sm:py-16 lg:py-20">
            <div class="mb-8 sm:mb-12 text-center">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-dark mb-4">
                    Contacto
                </h2>
                <p class="text-base sm:text-lg text-gray-600 max-w-2xl mx-auto">
                    ¿Interesado en trabajar juntos? ¡Contáctame!
                </p>
            </div>

            <div class="
                grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 
                gap-4 sm:gap-6
                max-w-6xl mx-auto
            ">
                <!-- Email Card -->
                <a 
                    v-if="user?.email"
                    :href="`mailto:${user.email}`" 
                    class="
                        group relative
                        bg-gradient-to-br from-primary/10 to-primary/5
                        rounded-2xl p-6
                        shadow-lg hover:shadow-2xl
                        transform transition-all duration-300
                        hover:-translate-y-2 hover:scale-105
                        border border-primary/20
                        flex flex-col items-center text-center
                    "
                >
                    <div class="w-16 h-16 rounded-xl bg-primary/20 flex items-center justify-center mb-4 group-hover:bg-primary/30 transition-colors">
                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Email</p>
                    <p class="text-dark font-semibold text-sm truncate w-full" :title="user.email">{{ user.email }}</p>
                </a>

                <!-- LinkedIn Card -->
                <a 
                    v-if="user?.linkedin_url"
                    :href="user.linkedin_url" 
                    target="_blank"
                    class="
                        group relative
                        bg-gradient-to-br from-accent/10 to-accent/5
                        rounded-2xl p-6
                        shadow-lg hover:shadow-2xl
                        transform transition-all duration-300
                        hover:-translate-y-2 hover:scale-105
                        border border-accent/20
                        flex flex-col items-center text-center
                    "
                >
                    <div class="w-16 h-16 rounded-xl bg-accent/20 flex items-center justify-center mb-4 group-hover:bg-accent/30 transition-colors">
                        <svg class="w-8 h-8 text-accent" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">LinkedIn</p>
                    <p class="text-dark font-semibold text-sm">Ver perfil</p>
                </a>

                <!-- GitHub Card -->
                <a 
                    v-if="user?.github_url"
                    :href="user.github_url" 
                    target="_blank"
                    class="
                        group relative
                        bg-gradient-to-br from-secondary/10 to-secondary/5
                        rounded-2xl p-6
                        shadow-lg hover:shadow-2xl
                        transform transition-all duration-300
                        hover:-translate-y-2 hover:scale-105
                        border border-secondary/20
                        flex flex-col items-center text-center
                    "
                >
                    <div class="w-16 h-16 rounded-xl bg-secondary/20 flex items-center justify-center mb-4 group-hover:bg-secondary/30 transition-colors">
                        <svg class="w-8 h-8 text-secondary" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">GitHub</p>
                    <p class="text-dark font-semibold text-sm">Ver repositorios</p>
                </a>

                <!-- WhatsApp Card -->
                <a 
                    v-if="user?.whatsapp"
                    :href="`https://wa.me/${user.whatsapp.replace(/[^0-9]/g, '')}`" 
                    target="_blank"
                    class="
                        group relative
                        bg-gradient-to-br from-green-100 to-green-50
                        rounded-2xl p-6
                        shadow-lg hover:shadow-2xl
                        transform transition-all duration-300
                        hover:-translate-y-2 hover:scale-105
                        border border-green-200
                        flex flex-col items-center text-center
                    "
                >
                    <div class="w-16 h-16 rounded-xl bg-green-200 flex items-center justify-center mb-4 group-hover:bg-green-300 transition-colors">
                        <svg class="w-8 h-8 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">WhatsApp</p>
                    <p class="text-dark font-semibold text-sm">Enviar mensaje</p>
                </a>
            </div>

            <!-- Footer -->
            <div class="text-center mt-12 sm:mt-16 text-gray-600">
                <p class="text-sm sm:text-base">© {{ new Date().getFullYear() }} {{ user?.name || 'Manuel Castro' }}. Todos los derechos reservados.</p>
            </div>
        </section>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import Navbar from '@/Components/Portfolio/Navbar.vue';
import TypeWriter from '@/Components/Portfolio/TypeWriter.vue';
import ProjectCarousel from '@/Components/Portfolio/ProjectCarousel.vue';
import ExperienceItem from '@/Components/Portfolio/ExperienceItem.vue';

defineProps({
    user: Object,
    projects: Array,
    skills: Array,
    experiences: Array,
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
</script>
