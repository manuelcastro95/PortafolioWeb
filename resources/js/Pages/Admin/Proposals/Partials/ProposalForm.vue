<template>
    <form @submit.prevent="submit" class="space-y-8">
        <!-- 1. Captura del proyecto -->
        <section class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <h2 class="text-xl font-bold text-dark mb-1">1. Captura del proyecto</h2>
            <p class="text-sm text-gray-500 mb-4">
                Pega la URL de Workana e intenta extraer los datos. Si Workana bloquea la lectura
                (responde 403 al servidor), completa los campos a mano: nada queda bloqueado.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 mb-4">
                <input
                    v-model="form.workana_url"
                    type="text"
                    placeholder="https://www.workana.com/job/..."
                    class="flex-1 rounded-xl border-gray-300 focus:border-primary focus:ring-primary"
                />
                <button
                    type="button"
                    @click="scrape"
                    :disabled="scraping || !form.workana_url"
                    class="inline-flex items-center justify-center gap-2 bg-secondary text-white px-5 py-2.5 rounded-xl hover:bg-secondary/90 disabled:opacity-50 font-semibold whitespace-nowrap"
                >
                    <span v-if="scraping">Extrayendo…</span>
                    <span v-else>Extraer de Workana</span>
                </button>
            </div>
            <p v-if="scrapeMsg" :class="scrapeOk ? 'text-green-600' : 'text-amber-600'" class="text-sm mb-4">
                {{ scrapeMsg }}
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <InputLabel value="Título del proyecto *" />
                    <input v-model="form.project_title" type="text" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                    <InputError :message="form.errors.project_title" class="mt-1" />
                </div>
                <div class="md:col-span-2">
                    <InputLabel value="Descripción" />
                    <textarea v-model="form.project_description" rows="4" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary"></textarea>
                </div>
                <div>
                    <InputLabel value="Habilidades (separadas por coma)" />
                    <input :value="skillsText" @input="onSkillsInput" type="text" placeholder="Laravel, Vue.js, MySQL" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <InputLabel value="Fecha de publicación" />
                        <input v-model="form.published_at" type="date" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                    </div>
                    <div>
                        <InputLabel value="Nº de propuestas" />
                        <input v-model="form.proposals_count" type="number" min="0" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                    </div>
                </div>
            </div>

            <h3 class="text-sm font-bold text-gray-700 mt-6 mb-3">Datos del cliente</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-1">
                    <InputLabel value="Nombre" />
                    <input v-model="form.client_name" type="text" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                </div>
                <div>
                    <InputLabel value="Proyectos publicados" />
                    <input v-model="form.client_published_projects" type="number" min="0" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                </div>
                <div>
                    <InputLabel value="Proyectos pagos" />
                    <input v-model="form.client_paid_projects" type="number" min="0" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                </div>
                <div>
                    <InputLabel value="Miembro desde" />
                    <input v-model="form.client_member_since" type="text" placeholder="2021" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                </div>
            </div>
        </section>

        <!-- 2. Economía -->
        <section class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <h2 class="text-xl font-bold text-dark mb-4">2. Economía</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <InputLabel value="Tarifa por hora (USD) *" />
                    <input v-model="form.hourly_rate_usd" type="number" step="0.5" min="1" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                </div>
                <div>
                    <InputLabel value="Comisión Workana (%) *" />
                    <input v-model="form.workana_commission" type="number" step="0.5" min="0" max="100" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                </div>
                <div>
                    <InputLabel value="Tasa USD → COP *" />
                    <div class="flex gap-2 mt-1">
                        <input v-model="form.usd_to_cop_rate" type="number" step="0.01" min="1" class="block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                        <button type="button" @click="refreshRate" :disabled="loadingRate" class="px-3 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium whitespace-nowrap disabled:opacity-50">
                            {{ loadingRate ? '…' : 'Actualizar' }}
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ rateSourceLabel }}</p>
                </div>
            </div>

            <!-- Resumen económico en vivo -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6">
                <div class="rounded-xl bg-soft p-4">
                    <p class="text-xs text-gray-500">Horas totales</p>
                    <p class="text-2xl font-bold text-dark">{{ totalHours }}</p>
                </div>
                <div class="rounded-xl bg-soft p-4">
                    <p class="text-xs text-gray-500">Valor total</p>
                    <p class="text-2xl font-bold text-dark">{{ fmtCop(totalCop) }}</p>
                </div>
                <div class="rounded-xl p-4" :class="effHourlyUsd < form.hourly_rate_usd ? 'bg-red-50' : 'bg-green-50'">
                    <p class="text-xs text-gray-500">Precio/hora efectivo</p>
                    <p class="text-2xl font-bold" :class="effHourlyUsd < form.hourly_rate_usd ? 'text-red-600' : 'text-green-600'">
                        US$ {{ effHourlyUsd.toFixed(2) }}
                    </p>
                    <p class="text-xs text-gray-400">{{ fmtCop(effHourlyCop) }}/h</p>
                </div>
                <div class="rounded-xl bg-soft p-4">
                    <p class="text-xs text-gray-500">Neto tras comisión</p>
                    <p class="text-2xl font-bold text-dark">{{ fmtCop(netCop) }}</p>
                    <p class="text-xs text-gray-400">-{{ fmtCop(commissionCop) }}</p>
                </div>
            </div>
            <p v-if="totalHours > 0 && effHourlyUsd < form.hourly_rate_usd" class="text-sm text-red-600 mt-3">
                ⚠️ El precio por hora efectivo (US$ {{ effHourlyUsd.toFixed(2) }}) está por debajo de tu tarifa objetivo (US$ {{ form.hourly_rate_usd }}).
            </p>
        </section>

        <!-- 3. Fases -->
        <section class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-dark">3. Estimación por fases</h2>
                <button type="button" @click="addPhase" class="inline-flex items-center gap-1 bg-primary text-white px-4 py-2 rounded-xl hover:bg-primary/90 text-sm font-semibold">
                    + Agregar fase
                </button>
            </div>

            <div v-if="form.phases.length === 0" class="text-gray-400 text-sm py-6 text-center">
                Sin fases todavía. Agrega una para empezar a estimar.
            </div>

            <div v-for="(phase, pi) in form.phases" :key="pi" class="border border-gray-200 rounded-xl p-4 mb-4">
                <div class="flex flex-col sm:flex-row gap-3 mb-3">
                    <input v-model="phase.name" type="text" placeholder="Nombre de la fase (ej. Núcleo)" class="flex-1 rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500 whitespace-nowrap">Monto fijo COP</span>
                        <input v-model="phase.amount_cop" type="number" min="0" :placeholder="autoPhaseCop(phase)" class="w-36 rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                        <button type="button" @click="removePhase(pi)" class="text-red-500 hover:text-red-700 text-sm font-medium">Quitar</button>
                    </div>
                </div>

                <div v-for="(item, ii) in phase.items" :key="ii" class="flex gap-2 mb-2">
                    <input v-model="item.description" type="text" placeholder="Tarea" class="flex-1 rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary" />
                    <input v-model="item.hours" type="number" step="0.5" min="0" placeholder="h" class="w-20 rounded-lg border-gray-300 text-sm focus:border-primary focus:ring-primary" />
                    <button type="button" @click="removeItem(phase, ii)" class="text-gray-400 hover:text-red-600 px-2">✕</button>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mt-3 pt-3 border-t border-gray-100">
                    <div class="flex items-center gap-2">
                        <button type="button" @click="addItem(phase)" class="text-primary hover:text-primary/80 text-sm font-medium">+ Ítem</button>
                        <span class="text-gray-300">|</span>
                        <select @change="addFromCatalog(phase, $event)" class="rounded-lg border-gray-300 text-sm py-1 focus:border-primary focus:ring-primary">
                            <option value="">+ Desde catálogo…</option>
                            <option v-for="task in catalog" :key="task.id" :value="task.id">
                                {{ task.name }} ({{ task.default_hours }} h)
                            </option>
                        </select>
                    </div>
                    <div class="text-sm text-gray-600">
                        <span class="font-semibold">{{ phaseHours(phase) }} h</span>
                        <span class="mx-2">·</span>
                        <span class="font-semibold">{{ fmtCop(phaseCop(phase)) }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. Bloques de texto -->
        <section class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <h2 class="text-xl font-bold text-dark mb-1">4. Bloques de texto</h2>
            <p class="text-sm text-gray-500 mb-4">Edítalos para esta propuesta sin tocar la plantilla base.</p>
            <div class="space-y-4">
                <div v-for="(block, key) in form.text_blocks" :key="key">
                    <InputLabel :value="block.title" />
                    <textarea v-model="block.content" rows="3" class="mt-1 block w-full rounded-xl border-gray-300 text-sm focus:border-primary focus:ring-primary"></textarea>
                </div>
            </div>
        </section>

        <!-- 5. Tareas de la propuesta -->
        <section class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <h2 class="text-xl font-bold text-dark mb-4">5. Tareas de la propuesta (Workana)</h2>
            <div class="space-y-2">
                <div v-for="(task, ti) in form.workana_tasks" :key="task.key" class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center py-2 border-b border-gray-50">
                    <span class="md:col-span-3 text-sm font-medium text-gray-700">{{ task.label }}</span>
                    <select v-model="task.status" class="md:col-span-3 rounded-lg border-gray-300 text-sm py-1.5 focus:border-primary focus:ring-primary">
                        <option v-for="(label, val) in taskStatuses" :key="val" :value="val">{{ label }}</option>
                    </select>
                    <input v-model="task.note" type="text" placeholder="Nota (opcional)" class="md:col-span-6 rounded-lg border-gray-300 text-sm py-1.5 focus:border-primary focus:ring-primary" />
                </div>
            </div>
        </section>

        <!-- 6. Detalles + seguimiento -->
        <section class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
            <h2 class="text-xl font-bold text-dark mb-4">6. Detalles y seguimiento</h2>

            <div class="flex justify-between items-center mb-2">
                <InputLabel value="Detalles de la propuesta (texto libre)" />
                <button type="button" @click="generateDetails" class="text-primary hover:text-primary/80 text-sm font-medium">
                    Generar desde bloques y fases
                </button>
            </div>
            <textarea v-model="form.details_text" rows="10" class="block w-full rounded-xl border-gray-300 text-sm focus:border-primary focus:ring-primary"></textarea>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                <div>
                    <InputLabel value="¿Cuánto tiempo necesitas?" />
                    <input v-model="form.delivery_time" type="text" placeholder="4 semanas" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                </div>
                <div>
                    <InputLabel value="Estado" />
                    <select v-model="form.status" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary">
                        <option v-for="(label, val) in statuses" :key="val" :value="val">{{ label }}</option>
                    </select>
                </div>
                <div>
                    <InputLabel value="Fecha de envío" />
                    <input v-model="form.sent_at" type="date" class="mt-1 block w-full rounded-xl border-gray-300 focus:border-primary focus:ring-primary" />
                </div>
            </div>
        </section>

        <!-- Acciones -->
        <div class="flex justify-end gap-3">
            <Link :href="route('admin.proposals.index')" class="px-6 py-3 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold">
                Cancelar
            </Link>
            <button type="submit" :disabled="form.processing" class="bg-primary text-white px-8 py-3 rounded-xl hover:bg-primary/90 shadow-lg font-semibold disabled:opacity-50">
                {{ proposal ? 'Guardar cambios' : 'Crear propuesta' }}
            </button>
        </div>
    </form>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    proposal: { type: Object, default: null },
    catalog: { type: Array, default: () => [] },
    textTemplates: { type: Array, default: () => [] },
    workanaTasks: { type: Array, default: () => [] },
    taskStatuses: { type: Object, default: () => ({}) },
    statuses: { type: Object, default: () => ({}) },
    settings: { type: Object, default: () => ({}) },
    currentRate: { type: Object, default: () => ({}) },
});

// --- Construir estado inicial del formulario ---
function initTextBlocks() {
    if (props.proposal?.text_blocks && Object.keys(props.proposal.text_blocks).length) {
        return JSON.parse(JSON.stringify(props.proposal.text_blocks));
    }
    const blocks = {};
    props.textTemplates.forEach((t) => {
        blocks[t.key] = { title: t.title, content: t.content };
    });
    return blocks;
}

function initTasks() {
    if (props.proposal?.workana_tasks && props.proposal.workana_tasks.length) {
        return JSON.parse(JSON.stringify(props.proposal.workana_tasks));
    }
    return props.workanaTasks.map((t) => ({ key: t.key, label: t.label, status: 'no_incluido', note: '' }));
}

const p = props.proposal;
const form = useForm({
    workana_url: p?.workana_url ?? '',
    project_title: p?.project_title ?? '',
    project_description: p?.project_description ?? '',
    skills: p?.skills ?? [],
    published_at: p?.published_at ? String(p.published_at).substring(0, 10) : '',
    proposals_count: p?.proposals_count ?? null,
    client_name: p?.client_name ?? '',
    client_published_projects: p?.client_published_projects ?? null,
    client_paid_projects: p?.client_paid_projects ?? null,
    client_member_since: p?.client_member_since ?? '',
    hourly_rate_usd: p?.hourly_rate_usd ?? props.settings.default_hourly_rate_usd ?? 18,
    workana_commission: p?.workana_commission ?? props.settings.default_workana_commission ?? 10,
    usd_to_cop_rate: p?.usd_to_cop_rate ?? props.currentRate.rate ?? props.settings.fallback_cop_rate ?? 3245,
    phases: p?.phases ? JSON.parse(JSON.stringify(p.phases)) : [],
    workana_tasks: initTasks(),
    text_blocks: initTextBlocks(),
    details_text: p?.details_text ?? '',
    delivery_time: p?.delivery_time ?? '',
    status: p?.status ?? 'borrador',
    sent_at: p?.sent_at ? String(p.sent_at).substring(0, 10) : '',
});

// --- Habilidades como texto ---
const skillsText = computed(() => (form.skills || []).join(', '));
function onSkillsInput(e) {
    form.skills = e.target.value.split(',').map((s) => s.trim()).filter((s) => s.length);
}

// --- Cálculos en vivo ---
const copRate = computed(() => parseFloat(form.usd_to_cop_rate) || 0);
const rate = computed(() => parseFloat(form.hourly_rate_usd) || 0);

function phaseHours(phase) {
    return (phase.items || []).reduce((s, i) => s + (parseFloat(i.hours) || 0), 0);
}
function autoPhaseCop(phase) {
    return String(Math.round(phaseHours(phase) * rate.value * copRate.value));
}
function phaseCop(phase) {
    if (phase.amount_cop !== null && phase.amount_cop !== '' && phase.amount_cop !== undefined) {
        return parseFloat(phase.amount_cop) || 0;
    }
    return Math.round(phaseHours(phase) * rate.value * copRate.value);
}
const totalHours = computed(() => form.phases.reduce((s, p) => s + phaseHours(p), 0));
const totalCop = computed(() => form.phases.reduce((s, p) => s + phaseCop(p), 0));
const effHourlyCop = computed(() => (totalHours.value > 0 ? Math.round(totalCop.value / totalHours.value) : 0));
const effHourlyUsd = computed(() => (copRate.value > 0 ? effHourlyCop.value / copRate.value : 0));
const commissionCop = computed(() => Math.round(totalCop.value * (parseFloat(form.workana_commission) || 0) / 100));
const netCop = computed(() => totalCop.value - commissionCop.value);

const fmtCop = (n) => new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', maximumFractionDigits: 0 }).format(n || 0);

// --- Fases ---
function addPhase() {
    form.phases.push({ name: '', amount_cop: '', items: [] });
}
function removePhase(i) {
    form.phases.splice(i, 1);
}
function addItem(phase) {
    phase.items.push({ description: '', hours: 0 });
}
function removeItem(phase, i) {
    phase.items.splice(i, 1);
}
function addFromCatalog(phase, event) {
    const id = parseInt(event.target.value);
    event.target.value = '';
    const task = props.catalog.find((t) => t.id === id);
    if (task) {
        phase.items.push({ description: task.name, hours: parseFloat(task.default_hours) });
    }
}

// --- Tasa ---
const loadingRate = ref(false);
const rateInfo = ref(props.currentRate || {});
const rateSourceLabel = computed(() => {
    if (!rateInfo.value.source) return '';
    return rateInfo.value.source === 'api' ? 'Tasa en vivo (API)' : 'Tasa de respaldo';
});
async function refreshRate() {
    loadingRate.value = true;
    try {
        const { data } = await window.axios.get(route('admin.proposals.rate'), { params: { force: 1 } });
        rateInfo.value = data;
        form.usd_to_cop_rate = data.rate;
    } catch (e) {
        // best effort
    } finally {
        loadingRate.value = false;
    }
}

// --- Scraping ---
const scraping = ref(false);
const scrapeMsg = ref('');
const scrapeOk = ref(false);
async function scrape() {
    scraping.value = true;
    scrapeMsg.value = '';
    try {
        const { data } = await window.axios.post(route('admin.proposals.scrape'), { url: form.workana_url });
        if (data.success) {
            const d = data.data;
            if (d.project_title) form.project_title = d.project_title;
            if (d.project_description) form.project_description = d.project_description;
            if (d.skills && d.skills.length) form.skills = d.skills;
            if (d.published_at) form.published_at = d.published_at;
            if (d.proposals_count != null) form.proposals_count = d.proposals_count;
            if (d.client_name) form.client_name = d.client_name;
            if (d.client_published_projects != null) form.client_published_projects = d.client_published_projects;
            if (d.client_paid_projects != null) form.client_paid_projects = d.client_paid_projects;
            if (d.client_member_since) form.client_member_since = d.client_member_since;
            scrapeOk.value = true;
            scrapeMsg.value = 'Datos extraídos. Revisa y completa lo que falte.';
        } else {
            scrapeOk.value = false;
            scrapeMsg.value = reasonMessage(data.reason);
        }
    } catch (e) {
        scrapeOk.value = false;
        scrapeMsg.value = 'No se pudo leer la página. Completa los datos a mano.';
    } finally {
        scraping.value = false;
    }
}
function reasonMessage(reason) {
    if (reason && reason.startsWith('http_403')) {
        return 'Workana bloqueó la lectura desde el servidor (403). Pega los datos a mano.';
    }
    if (reason === 'url_invalida') return 'La URL no parece de Workana.';
    return 'No se pudo extraer automáticamente. Completa los datos a mano.';
}

// --- Generar detalles ---
function generateDetails() {
    const parts = [];
    const b = form.text_blocks;
    if (b.presentacion?.content) parts.push(b.presentacion.content);

    if (form.phases.length) {
        let fases = 'Propuesta de trabajo por fases:\n';
        form.phases.forEach((ph) => {
            fases += `\n• ${ph.name || 'Fase'} (${phaseHours(ph)} h)\n`;
            (ph.items || []).forEach((it) => {
                if (it.description) fases += `   - ${it.description}\n`;
            });
        });
        parts.push(fases.trim());
    }
    if (b.como_trabaja?.content) parts.push(b.como_trabaja.content);
    if (b.stack?.content) parts.push('Stack: ' + b.stack.content);
    if (b.enlaces?.content) parts.push(b.enlaces.content);
    if (b.cierre?.content) parts.push(b.cierre.content);

    form.details_text = parts.join('\n\n');
}

// --- Submit ---
function submit() {
    if (props.proposal) {
        form.put(route('admin.proposals.update', props.proposal.id));
    } else {
        form.post(route('admin.proposals.store'));
    }
}
</script>
