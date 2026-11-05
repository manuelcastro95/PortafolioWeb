<template>
    <AdminLayout>
        <Head title="Nueva propuesta" />
        <div class="min-h-screen bg-gradient-to-br from-soft via-warm to-white py-8 px-4 sm:px-6 lg:px-8">
            <div class="max-w-5xl mx-auto">
                <div class="mb-8">
                    <Link :href="route('admin.proposals.index')" class="text-sm text-gray-500 hover:text-gray-700">&larr; Volver a propuestas</Link>
                    <h1 class="text-4xl font-bold text-dark mt-2">Nueva propuesta</h1>
                    <p class="text-gray-600">Arma la propuesta para Workana en minutos.</p>
                </div>

                <ProposalForm
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
    catalog: Array,
    textTemplates: Array,
    workanaTasks: Array,
    taskStatuses: Object,
    statuses: Object,
    settings: Object,
    currentRate: Object,
});
</script>
