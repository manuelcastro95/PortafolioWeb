<template>
    <AdminLayout>
        <Head title="Editar propuesta" />
        <div class="min-h-screen bg-gradient-to-br from-soft via-warm to-white py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-5xl mx-auto">
                <div class="mb-8 flex justify-between items-start">
                    <div>
                        <Link :href="route('admin.proposals.index')" class="text-sm text-gray-500 hover:text-gray-700">&larr; Volver a propuestas</Link>
                        <h1 class="text-4xl font-bold text-dark mt-2">Editar propuesta</h1>
                        <p class="text-gray-600">{{ proposal.project_title }}</p>
                    </div>
                    <Link :href="route('admin.proposals.show', proposal.id)" class="bg-secondary text-white px-5 py-2.5 rounded-xl hover:bg-secondary/90 font-semibold whitespace-nowrap">
                        Ver salida →
                    </Link>
                </div>

                <ProposalForm
                    :proposal="proposal"
                    :catalog="catalog"
                    :text-templates="textTemplates"
                    :workana-tasks="workanaTasks"
                    :task-statuses="taskStatuses"
                    :statuses="statuses"
                    :settings="settings"
                    :current-rate="currentRate"
                />
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import ProposalForm from './Partials/ProposalForm.vue';

defineProps({
    proposal: Object,
    catalog: Array,
    textTemplates: Array,
    workanaTasks: Array,
    taskStatuses: Object,
    statuses: Object,
    settings: Object,
    currentRate: Object,
});
</script>
