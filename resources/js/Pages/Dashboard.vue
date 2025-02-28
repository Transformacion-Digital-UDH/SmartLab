<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
</script>

<template>
    <AppLayout :title="$t('Dashboard')">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0"
            >
                {{ $t("Dashboard") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4"
                >
                    <h3>Métricas claves</h3>
                    <div class="flex flex-wrap">
                        <div class="w-full sm:w-2/6">
                            <div class="grid grid-cols-2 gap-4">
                                <div
                                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4"
                                >
                                    <h3 class="text-xl font-bold">
                                        Usuarios
                                    </h3>
                                    <p class="text-3xl font-bold pt-5 mb-0">10</p>
                                    <span>Registros</span>
                                </div>
                                <div
                                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4"
                                >
                                    <h3 class="text-xl font-bold">
                                        Usuarios
                                    </h3>
                                    <p class="text-3xl font-bold pt-5 mb-0">10</p>
                                    <span>Registros</span>
                                </div>
                                <div
                                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4"
                                >
                                    <h3 class="text-xl font-bold">
                                        Usuarios
                                    </h3>
                                    <p class="text-3xl font-bold pt-5 mb-0">10</p>
                                    <span>Registros</span>
                                </div>
                                <div
                                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4"
                                >
                                    <h3 class="text-xl font-bold">
                                        Usuarios
                                    </h3>
                                    <p class="text-3xl font-bold pt-5 mb-0">10</p>
                                    <span>Registros</span>
                                </div>


                            </div>
                        </div>
                        <!-- Caja del 60% -->
                        <div class="w-full sm:w-4/6">
                            Caja 60%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
