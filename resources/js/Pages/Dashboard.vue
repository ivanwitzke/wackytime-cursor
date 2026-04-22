<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    report: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        required: true,
    },
});

const width = (item) => `${Math.max(item.percentage || 0, 2)}%`;
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
                <form method="get" :action="route('dashboard')" class="flex flex-wrap items-end gap-3 rounded-lg bg-white p-4 shadow-sm">
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase text-gray-500">Início</label>
                        <input type="date" name="start" :value="filters.start" class="rounded border border-gray-300 px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium uppercase text-gray-500">Fim</label>
                        <input type="date" name="end" :value="filters.end" class="rounded border border-gray-300 px-3 py-2 text-sm" />
                    </div>
                    <PrimaryButton>Aplicar</PrimaryButton>
                </form>

                <div class="grid gap-4 md:grid-cols-4">
                    <div class="rounded-lg bg-white p-4 shadow-sm md:col-span-1">
                        <div class="text-sm text-gray-500">Período selecionado</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900">{{ report.totals.text }}</div>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <h3 class="mb-3 font-medium text-gray-800">Tempo por dia</h3>
                        <div class="space-y-3">
                            <div v-for="item in report.daily" :key="item.name" class="space-y-1">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">{{ item.name }}</span>
                                    <span class="font-medium text-gray-900">{{ item.seconds }}s</span>
                                </div>
                                <div class="h-2 rounded bg-gray-100">
                                    <div class="h-2 rounded bg-indigo-500" :style="{ width: width(item) }"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <h3 class="mb-3 font-medium text-gray-800">Projetos</h3>
                        <div class="space-y-3">
                            <div v-for="item in report.projects" :key="item.name" class="space-y-1">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">{{ item.name }}</span>
                                    <span class="font-medium text-gray-900">{{ item.percentage }}%</span>
                                </div>
                                <div class="h-2 rounded bg-gray-100">
                                    <div class="h-2 rounded bg-emerald-500" :style="{ width: width(item) }"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <h3 class="mb-3 font-medium text-gray-800">Linguagens</h3>
                        <div class="space-y-3">
                            <div v-for="item in report.languages" :key="item.name" class="space-y-1">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">{{ item.name }}</span>
                                    <span class="font-medium text-gray-900">{{ item.percentage }}%</span>
                                </div>
                                <div class="h-2 rounded bg-gray-100">
                                    <div class="h-2 rounded bg-sky-500" :style="{ width: width(item) }"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-white p-4 shadow-sm">
                        <h3 class="mb-3 font-medium text-gray-800">Editores</h3>
                        <div class="space-y-3">
                            <div v-for="item in report.editors" :key="item.name" class="space-y-1">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">{{ item.name }}</span>
                                    <span class="font-medium text-gray-900">{{ item.percentage }}%</span>
                                </div>
                                <div class="h-2 rounded bg-gray-100">
                                    <div class="h-2 rounded bg-fuchsia-500" :style="{ width: width(item) }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
