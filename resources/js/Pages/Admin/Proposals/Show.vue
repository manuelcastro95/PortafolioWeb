<template>
    <AdminLayout>
        <Head :title="proposal.project_title" />
        <div class="min-h-screen bg-gradient-to-br from-soft via-warm to-white py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <!-- Header -->
                <div class="mb-6">
                    <Link :href="route('admin.proposals.index')" class="text-sm text-gray-500 hover:text-gray-700">&larr; Volver a propuestas</Link>
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mt-2">
                        <div>
                            <h1 class="text-3xl font-bold text-dark">{{ proposal.project_title }}</h1>
                            <p v-if="proposal.client_name" class="text-gray-600">Cliente: {{ proposal.client_name }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <select
                                :value="proposal.status"
                                @change="changeStatus($event)"
                                class="rounded-xl border-gray-300 text-sm py-2 focus:border-primary focus:ring-primary"
                            >
                                <option v-for="(label, val) in statuses" :key="val" :value="val">{{ label }}</option>
                            </select>
                            <Link :href="route('admin.proposals.edit', proposal.id)" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-xl font-semibold text-sm">
                                Editar
                            </Link>
                            <a :href="route('admin.proposals.pdf', proposal.id)" class="bg-primary text-white px-4 py-2 rounded-xl hover:bg-primary/90 font-semibold text-sm">
                                Descargar PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div v-if="$page.props.flash?.success" class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Resumen económico -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                    <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Horas totales</p>
                        <p class="text-2xl font-bold text-dark">{{ proposal.total_hours }}</p>
                    </div>
                    <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Valor total</p>
                        <p class="text-2xl font-bold text-dark">{{ fmtCop(proposal.total_cop) }}</p>
                    </div>
                    <div class="rounded-xl p-4 shadow-sm border" :class="belowTarget ? 'bg-red-50 border-red-100' : 'bg-green-50 border-green-100'">
                        <p class="text-xs text-gray-500">Precio/hora efectivo</p>
                        <p class="text-2xl font-bold" :class="belowTarget ? 'text-red-600' : 'text-green-600'">US$ {{ Number(proposal.effective_hourly_usd).toFixed(2) }}</p>
                        <p class="text-xs text-gray-400">{{ fmtCop(proposal.effective_hourly_cop) }}/h · objetivo US$ {{ proposal.hourly_rate_usd }}</p>
                    </div>
                    <div class="rounded-xl bg-white p-4 shadow-sm border border-gray-100">
                        <p class="text-xs text-gray-500">Neto tras comisión ({{ proposal.workana_commission }}%)</p>
                        <p class="text-2xl font-bold text-dark">{{ fmtCop(proposal.net_cop) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Columna principal: campos para copiar -->
                    <div class="lg:col-span-2 space-y-4">
                        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                            <h2 class="text-lg font-bold text-dark mb-1">Campos para Workana</h2>
                            <p class="text-sm text-gray-500 mb-4">Ten Workana en una pestaña y esto en otra. Copia cada campo.</p>
                            <div class="space-y-4">
                                <CopyBox label="Valor total (COP)" :value="Math.round(proposal.total_cop)" mono />
                                <CopyBox label="Horas de trabajo" :value="proposal.total_hours" mono />
                                <CopyBox label="¿Cuánto tiempo necesitas?" :value="proposal.delivery_time || ''" />
                                <CopyBox label="Detalles de la propuesta" :value="proposal.details_text || ''" />
                            </div>
                        </div>

                        <!-- Tareas de la propuesta -->
                        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                            <h2 class="text-lg font-bold text-dark mb-1">Tareas de la propuesta</h2>
                            <p class="text-sm text-gray-500 mb-4">
                                En Workana, por cada tarea: deja en <strong>Incluido</strong>, deja en <strong>Extra</strong> con su precio,
                                o <strong>bórrala con la ×</strong>. Las tres cosas importan: una fila a medio llenar no deja enviar.
                            </p>

                            <!-- Incluido -->
                            <div class="mb-4">
                                <h3 class="text-sm font-bold text-green-700 mb-2">Dejar en “Incluido” ({{ incluidas.length }})</h3>
                                <ul v-if="incluidas.length" class="flex flex-wrap gap-2">
                                    <li v-for="t in incluidas" :key="t.key" class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">{{ t.label }}</li>
                                </ul>
                                <p v-else class="text-sm text-gray-400">—</p>
                            </div>

                            <!-- Extra -->
                            <div class="mb-4">
                                <h3 class="text-sm font-bold text-amber-700 mb-2">Dejar en “Extra” con su precio ({{ extras.length }})</h3>
                                <ul v-if="extras.length" class="space-y-1">
                                    <li v-for="t in extras" :key="t.key" class="flex justify-between text-sm">
                                        <span class="text-gray-700">{{ t.label }}</span>
                                        <span class="font-semibold text-amber-700">{{ fmtCop(t.valor) }}</span>
                                    </li>
                                </ul>
                                <p v-else class="text-sm text-gray-400">—</p>
                                <p v-if="extras.length" class="text-xs text-gray-500 mt-2 border-t border-gray-100 pt-2">
                                    Suma de extras: <strong>{{ fmtCop(proposal.extras_cop) }}</strong>.
                                    <span class="text-amber-600">Confirma en el formulario de Workana si se suman al Valor total o van aparte.</span>
                                </p>
                            </div>

                            <!-- Borrar -->
                            <div>
                                <h3 class="text-sm font-bold text-gray-500 mb-2">Borrar con la × ({{ borrar.length }})</h3>
                                <ul v-if="borrar.length" class="flex flex-wrap gap-2">
                                    <li v-for="t in borrar" :key="t.key" class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-500 line-through">{{ t.label }}</li>
                                </ul>
                                <p v-else class="text-sm text-gray-400">Ninguna: todas las tareas van en la propuesta.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Columna lateral: fases + cliente -->
                    <div class="space-y-4">
                        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                            <h2 class="text-lg font-bold text-dark mb-4">Fases</h2>
                            <div v-for="(phase, i) in proposal.phase_breakdown" :key="i" class="mb-4 last:mb-0">
                                <div class="flex justify-between items-baseline">
                                    <span class="font-semibold text-dark">{{ phase.name }}</span>
                                    <span class="text-sm text-gray-500">{{ phase.hours }} h</span>
                                </div>
                                <p class="text-primary font-semibold text-sm">{{ fmtCop(phase.cop) }}</p>
                                <ul class="mt-1 text-xs text-gray-500 list-disc list-inside">
                                    <li v-for="(item, j) in phase.items" :key="j">{{ item.description }} ({{ item.hours }} h)</li>
                                </ul>
                            </div>
                            <div class="border-t border-gray-100 pt-3 mt-3 flex justify-between font-bold text-dark">
                                <span>Total</span>
                                <span>{{ fmtCop(proposal.total_cop) }}</span>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
                            <h2 class="text-lg font-bold text-dark mb-3">Cliente / proyecto</h2>
                            <dl class="text-sm space-y-1.5 text-gray-600">
                                <div class="flex justify-between"><dt>Propuestas</dt><dd class="font-medium text-dark">{{ proposal.proposals_count ?? '—' }}</dd></div>
                                <div class="flex justify-between"><dt>Proyectos publicados</dt><dd class="font-medium text-dark">{{ proposal.client_published_projects ?? '—' }}</dd></div>
                                <div class="flex justify-between"><dt>Proyectos pagos</dt><dd class="font-medium text-dark">{{ proposal.client_paid_projects ?? '—' }}</dd></div>
                                <div class="flex justify-between"><dt>Miembro desde</dt><dd class="font-medium text-dark">{{ proposal.client_member_since ?? '—' }}</dd></div>
                                <div v-if="proposal.workana_url" class="pt-2">
                                    <a :href="proposal.workana_url" target="_blank" class="text-primary hover:underline break-all">Ver en Workana ↗</a>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import CopyBox from './Partials/CopyBox.vue';

const props = defineProps({
    proposal: Object,
    statuses: Object,
    taskStatuses: Object,
    allTasks: { type: Array, default: () => [] },
});

const incluidas = computed(() => (props.proposal.workana_tasks || []).filter((t) => t.status === 'incluido'));
const extras = computed(() => (props.proposal.workana_tasks || []).filter((t) => t.status === 'extra'));
const borrar = computed(() => {
    const present = new Set((props.proposal.workana_tasks || []).map((t) => t.key));
    return props.allTasks.filter((t) => !present.has(t.key));
});

const fmtCop = (n) => new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(n || 0);

const belowTarget = computed(() => Number(props.proposal.effective_hourly_usd) < Number(props.proposal.hourly_rate_usd));

function changeStatus(event) {
    const status = event.target.value;
    const payload = { status };
    if (status !== 'borrador' && !props.proposal.sent_at) {
        payload.sent_at = new Date().toISOString().substring(0, 10);
    }
    router.patch(route('admin.proposals.status', props.proposal.id), payload, { preserveScroll: true });
}
</script>
