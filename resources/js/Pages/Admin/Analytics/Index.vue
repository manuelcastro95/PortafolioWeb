<template>
    <AdminLayout>
        <Head title="Analytics" />

        <div class="min-h-screen bg-gradient-to-br from-soft via-warm to-white py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-dark mb-2">Analytics</h1>
                        <p class="text-gray-600">Seguimiento de visitantes y eventos</p>
                    </div>
                    <div class="flex gap-2">
                        <select
                            v-model="selectedDays"
                            @change="updateDays"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                        >
                            <option :value="7">Últimos 7 días</option>
                            <option :value="30">Últimos 30 días</option>
                            <option :value="90">Últimos 90 días</option>
                            <option :value="365">Último año</option>
                        </select>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
                    <!-- Page Views -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-600">Vistas de Página</h3>
                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-dark">{{ stats.total_page_views }}</p>
                    </div>

                    <!-- Total Clicks -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-600">Clics Totales</h3>
                            <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-dark">{{ stats.total_clicks }}</p>
                    </div>

                    <!-- CV Downloads -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-600">Descargas CV</h3>
                            <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center">
                                <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-dark">{{ stats.cv_downloads }}</p>
                    </div>

                    <!-- CV Views -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-medium text-gray-600">Visualizaciones CV</h3>
                            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-dark">{{ stats.cv_views }}</p>
                    </div>
                </div>

                <!-- Charts Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Events by Day -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                        <h3 class="text-lg font-semibold text-dark mb-4">Eventos por Día (Últimos 7 días)</h3>
                        <div class="h-64 flex items-end justify-between gap-2">
                            <div
                                v-for="(day, index) in eventsByDay"
                                :key="index"
                                class="flex-1 flex flex-col items-center"
                            >
                                <div
                                    class="w-full bg-primary rounded-t-lg transition-all hover:bg-primary/80"
                                    :style="{ height: `${(day.count / maxDayEvents) * 100}%` }"
                                    :title="`${day.date}: ${day.count} eventos`"
                                ></div>
                                <span class="text-xs text-gray-600 mt-2">{{ formatDate(day.date) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Events by Section -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                        <h3 class="text-lg font-semibold text-dark mb-4">Eventos por Sección</h3>
                        <div class="space-y-4">
                            <div
                                v-for="(section, index) in eventsBySection"
                                :key="index"
                                class="flex items-center justify-between"
                            >
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full" :class="getSectionColor(section.section)"></div>
                                    <span class="text-sm font-medium text-gray-700 capitalize">{{ section.section || 'Sin sección' }}</span>
                                </div>
                                <span class="text-lg font-bold text-dark">{{ section.count }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Stats -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Top Events -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                        <h3 class="text-lg font-semibold text-dark mb-4">Eventos Más Populares</h3>
                        <div class="space-y-3">
                            <div
                                v-for="(event, index) in topEvents"
                                :key="index"
                                class="flex items-center justify-between"
                            >
                                <span class="text-sm text-gray-700">{{ formatEventName(event.event_name) }}</span>
                                <span class="text-sm font-bold text-dark">{{ event.count }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Devices -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                        <h3 class="text-lg font-semibold text-dark mb-4">Dispositivos</h3>
                        <div class="space-y-3">
                            <div
                                v-for="(device, index) in devices"
                                :key="index"
                                class="flex items-center justify-between"
                            >
                                <span class="text-sm text-gray-700 capitalize">{{ device.device_type || 'Desconocido' }}</span>
                                <span class="text-sm font-bold text-dark">{{ device.count }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Browsers -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                        <h3 class="text-lg font-semibold text-dark mb-4">Navegadores</h3>
                        <div class="space-y-3">
                            <div
                                v-for="(browser, index) in browsers"
                                :key="index"
                                class="flex items-center justify-between"
                            >
                                <span class="text-sm text-gray-700 capitalize">{{ browser.browser || 'Desconocido' }}</span>
                                <span class="text-sm font-bold text-dark">{{ browser.count }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Project Clicks -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                        <h3 class="text-lg font-semibold text-dark mb-2">Clics en Proyectos</h3>
                        <p class="text-3xl font-bold text-primary">{{ stats.project_clicks }}</p>
                    </div>

                    <!-- Contact Clicks -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                        <h3 class="text-lg font-semibold text-dark mb-2">Clics en Contacto</h3>
                        <p class="text-3xl font-bold text-accent">{{ stats.contact_clicks }}</p>
                    </div>

                    <!-- Menu Clicks -->
                    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                        <h3 class="text-lg font-semibold text-dark mb-2">Clics en Menú</h3>
                        <p class="text-3xl font-bold text-secondary">{{ stats.menu_clicks }}</p>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    stats: Object,
    topEvents: Array,
    eventsBySection: Array,
    eventsByDay: Array,
    devices: Array,
    browsers: Array,
    days: Number,
});

const selectedDays = ref(props.days || 30);

const maxDayEvents = computed(() => {
    if (!props.eventsByDay || props.eventsByDay.length === 0) return 1;
    return Math.max(...props.eventsByDay.map(d => d.count), 1);
});

const updateDays = () => {
    router.get(route('admin.analytics.index'), { days: selectedDays.value }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', { day: 'numeric', month: 'short' });
};

const formatEventName = (eventName) => {
    return eventName
        .replace(/_/g, ' ')
        .replace(/\b\w/g, l => l.toUpperCase());
};

const getSectionColor = (section) => {
    const colors = {
        'navbar': 'bg-primary',
        'projects': 'bg-accent',
        'contact': 'bg-secondary',
        'cv_modal': 'bg-green-500',
        'page': 'bg-blue-500',
    };
    return colors[section] || 'bg-gray-400';
};
</script>

