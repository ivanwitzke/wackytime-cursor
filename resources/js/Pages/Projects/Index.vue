<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    projects: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Head title="Projetos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Projetos</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-gray-700">Nome</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-700">Primeira atividade</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-700">Última atividade</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            <tr v-for="project in projects" :key="project.id">
                                <td class="px-4 py-2 text-gray-900">{{ project.name }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ project.first_seen_at ?? '-' }}</td>
                                <td class="px-4 py-2 text-gray-600">{{ project.last_seen_at ?? '-' }}</td>
                            </tr>
                            <tr v-if="projects.length === 0">
                                <td class="px-4 py-6 text-center text-gray-500" colspan="3">Sem projetos rastreados ainda.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
