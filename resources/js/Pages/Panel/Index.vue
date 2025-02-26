<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import CardItems from "./Partes/CardItems.vue";
import BarraIngresos from "./Charts/BarraIngresos.vue";
</script>

<template>
    <AppLayout :title="$t('Dashboard')">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">
                {{ $t("Dashboard") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-gray-50 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-4">
                    <h3 class="text-xl font-bold pb-1">Métricas claves</h3>
                    <div class="flex flex-wrap">
                        <div class="w-full lg:w-2/6">
                            <div class="grid grid-cols-2 gap-4">
                                <CardItems name="Usuarios" des="Registros"/>
                                <CardItems name="Proyectos" des="Ejecutándose"/>
                                <CardItems name="Asistencias" des="Ingresos hoy"/>
                                <CardItems name="Equipos" des="Activos"/>
                            </div>
                        </div>
                        <!-- Gráfica: Caja del 60% -->
                        <div class="w-full lg:w-4/6 mt-4 lg:mt-0">
                            <BarraIngresos class="mx-2" />
                        </div>
                    </div>
                </div>
                <!-- Bloque del Calendar debajo de todo -->
                <div class="mt-8 bg-white dark:bg-gray-900 p-4 rounded-lg shadow">
                    <h3 class="text-xl font-bold mb-2">Calendario de reservas</h3>
                    <iframe
                        src="https://calendar.google.com/calendar/embed?src=c_dac0fb127f0d05b67bdaa949b26ddcf6baed5cbedc7bc9af8024c0d34ceacfc4%40group.calendar.google.com&ctz=America%2FLima&mode=WEEK"
                        style="border: 0;"
                        width="100%"
                        height="600"
                        frameborder="0"
                        scrolling="no">
                    </iframe>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
