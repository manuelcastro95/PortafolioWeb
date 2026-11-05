<template>
    <AdminLayout>
        <Head title="Propuestas" />
        <div class="min-h-screen bg-gradient-to-br from-soft via-warm to-white py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <!-- Header -->
                <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h1 class="text-4xl font-bold text-dark mb-2">Propuestas</h1>
                        <p class="text-gray-600">Arma y hace seguimiento a tus propuestas de Workana</p>
                    </div>
                    <Link :href="route('admin.proposals.create')" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl hover:bg-primary/90 shadow-lg font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Nueva propuesta
                    </Link>
                </div>

                <div v-if="$page.props.flash?.success" class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Métricas -->
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
                    <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Enviadas</p>
                        <p class="text-2xl font-bold text-dark">{{ metrics.sent }}</p>
                        <p class="text-xs text-gray-400">{{ metrics.total }} en total</p>
                    </div>
                    <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Conversión</p>
                        <p class="text-2xl font-bold text-dark">{{ metrics.conversion_rate }}%</p>
                        <p class="text-xs text-gray-400">{{ metrics.won }} ganadas / {{ metrics.lost }} perdidas</p>
                    </div>
                    <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Valor promedio</p>
                        <p class="text-2xl font-bold text-dark">{{ fmtCopShort(metrics.avg_value_cop) }}</p>
                    </div>
                    <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">En conversación</p>
                        <p class="text-2xl font-bold text-dark">{{ metrics.in_conversation }}</p>
                    </div>
                    <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Horas cotizadas</p>
                        <p class="text-2xl font-bold text-dark">{{ metrics.hours_quoted }}</p>
                    </div>
                    <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Horas ganadas</p>
                        <p class="text-2xl font-bold text-green-600">{{ metrics.hours_won }}</p>
                    </div>
                </div>

                <!-- Configuración -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6">
                    <button type="button" @click="showSettings = !showSettings" class="w-full flex justify-between items-center px-6 py-4 text-left">
                        <span class="font-semibold text-dark">Configuración de cálculo</span>
                        <span class="text-gray-400 text-sm">
                            US$ {{ settings.default_hourly_rate_usd }}/h · comisión {{ settings.default_workana_commission }}% · respaldo {{ fmtCop(settings.fallback_cop_rate) }}
                            <span class="ml-2">{{ showSettings ? '▲' : '▼' }}</span>
                        </span>
                    </button>
                    <form v-show="showSettings" @submit.prevent="saveSettings" class="px-6 pb-6 grid grid-cols-1 md:grid-cols-4 gap-4 items-end border-t border-gray-100 pt-4">
                        <div>
                            <InputLabel value="Tarifa por hora por defecto (USD)" />
                            <input v-model="settingsForm.default_hourly_rate_usd" type="number" step="0.5" min="1" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                        </div>
                        <div>
                            <InputLabel value="Comisión Workana por defecto (%)" />
                            <input v-model="settingsForm.default_workana_commission" type="number" step="0.5" min="0" max="100" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                        </div>
                        <div>
                            <InputLabel value="Tasa COP de respaldo" />
                            <input v-model="settingsForm.fallback_cop_rate" type="number" step="0.01" min="1" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                        </div>
                        <button type="submit" :disabled="settingsForm.processing" class="bg-primary text-white px-5 py-2.5 rounded-xl hover:bg-primary/90 font-semibold disabled:opacity-50">
                            Guardar
                        </button>
                    </form>
                </div>

                <!-- Lista -->
                <div v-if="proposals.length" class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Proyecto</th>
                                <th class="px-4 py-3 font-semibold">Cliente</th>
                                <th class="px-4 py-3 font-semibold">Estado</th>
                                <th class="px-4 py-3 font-semibold text-right">Horas</th>
                                <th class="px-4 py-3 font-semibold text-right">Valor</th>
                                <th class="px-4 py-3 font-semibold text-right">US$/h</th>
                                <th class="px-4 py-3 font-semibold"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="proposal in proposals" :key="proposal.id" class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <Link :href="route('admin.proposals.show', proposal.id)" class="font-medium text-dark hover:text-primary line-clamp-1">
                                        {{ proposal.project_title }}
                                    </Link>
                                    <p class="text-xs text-gray-400">{{ proposal.created_at ? new Date(proposal.created_at).toLocaleDateString('es-CO') : '' }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ proposal.client_name || '—' }}</td>
                                <td class="px-4 py-3">
                                    <span :class="statusClass(proposal.status)" class="inline-block px-2.5 py-1 text-xs font-semibold rounded-full">
                                        {{ statuses[proposal.status] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ proposal.total_hours }}</td>
                                <td class="px-4 py-3 text-right font-medium text-dark">{{ fmtCop(proposal.total_cop) }}</td>
                                <td class="px-4 py-3 text-right" :class="Number(proposal.effective_hourly_usd) < Number(proposal.hourly_rate_usd) ? 'text-red-600' : 'text-green-600'">
                                    {{ Number(proposal.effective_hourly_usd).toFixed(1) }}
                                </td>
                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <Link :href="route('admin.proposals.show', proposal.id)" class="text-primary hover:text-primary/80 font-medium">Ver</Link>
                                    <Link :href="route('admin.proposals.edit', proposal.id)" class="ml-3 text-gray-500 hover:text-gray-700 font-medium">Editar</Link>
                                    <button @click="destroy(proposal.id)" class="ml-3 text-red-500 hover:text-red-700 font-medium">Eliminar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Vacío -->
                <div v-else class="bg-white rounded-2xl p-12 text-center shadow-lg border border-gray-100">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No hay propuestas</h3>
                    <p class="text-gray-600 mb-6">Crea tu primera propuesta para Workana</p>
                    <Link :href="route('admin.proposals.create')" class="inline-flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-xl hover:bg-primary/90 shadow-lg font-semibold">
                        Crear propuesta
                    </Link>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';

const props = defineProps({
    proposals: Array,
    metrics: Object,
    settings: Object,
    statuses: Object,
});

const showSettings = ref(false);
const settingsForm = useForm({
    default_hourly_rate_usd: props.settings.default_hourly_rate_usd,
    default_workana_commission: props.settings.default_workana_commission,
    fallback_cop_rate: props.settings.fallback_cop_rate,
});
function saveSettings() {
    settingsForm.post(route('admin.proposals.settings'), { preserveScroll: true });
}

const fmtCop = (n) => new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(n || 0);
const fmtCopShort = (n) => {
    if (!n) return '$0';
    if (n >= 1000000) return '$' + (n / 1000000).toFixed(1) + 'M';
    if (n >= 1000) return '$' + Math.round(n / 1000) + 'K';
    return fmtCop(n);
};

function statusClass(status) {
    return {
        borrador: 'bg-gray-100 text-gray-600',
        enviada: 'bg-blue-100 text-blue-800',
        en_conversacion: 'bg-amber-100 text-amber-800',
        ganada: 'bg-green-100 text-green-800',
        perdida: 'bg-red-100 text-red-800',
    }[status] || 'bg-gray-100 text-gray-600';
}

function destroy(id) {
    if (confirm('¿Eliminar esta propuesta?')) {
        router.delete(route('admin.proposals.destroy', id));
    }
}
</script>
