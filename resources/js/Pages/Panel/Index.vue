<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import CardItems from "./Partes/CardItems.vue";
import BarraIngresos from "./Charts/BarraIngresos.vue";
import TablaReservas from "./Partes/Reservas/TablaReservas.vue";
import ModalEditar from "./Partes/Reservas/ModalEditar.vue";
import { router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    reservas: Array,
    equipos: Array,
    recursos: Array,
    areas: Array,
});

const reservas = ref(props.reservas || []);
const mostrarModalEditar = ref(false);
const reservaSeleccionada = ref(null);

// Actualizar la tabla
const actualizarTabla = () => {
    router.visit(route("dashboard"), { preserveScroll: true });
};

// Abrir el modal de edición
const abrirModalEditar = (reserva) => {
    mostrarModalEditar.value = true;
    reservaSeleccionada.value = { ...reserva };
};
</script>

<template>
    <AppLayout :title="$t('Dashboard')">
        <template #header>
            <h2 class="text-lg font-medium text-gray-800 dark:text-gray-200 leading-tight">
                {{ $t("Dashboard") }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <div class="bg-gray-50 dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">Métricas claves</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="grid grid-cols-2 gap-4">
                            <CardItems name="Usuarios" des="Registros" />
                            <CardItems name="Proyectos" des="Ejecutándose" />
                            <CardItems name="Asistencias" des="Ingresos hoy" />
                            <CardItems name="Equipos" des="Activos" />
                        </div>
                        <div class="lg:col-span-2">
                            <BarraIngresos class="w-full" />
                        </div>
                    </div>
                </div>

                <!-- Sección de reservas -->
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-4">Reservas por aprobar</h3>
                    <TablaReservas
                        :reservas="reservas"
                        @editar="abrirModalEditar"
                        @actualizar-tabla="actualizarTabla"
                    />
                </div>

                <!-- Calendario -->
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-4">Calendario de reservas</h3>
                    <iframe
                        src="https://calendar.google.com/calendar/embed?src=c_dac0fb127f0d05b67bdaa949b26ddcf6baed5cbedc7bc9af8024c0d34ceacfc4%40group.calendar.google.com&ctz=America%2FLima&mode=WEEK"
                        class="w-full border-none rounded-lg" height="600px">
                    </iframe>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <ModalEditar
                v-if="reservaSeleccionada"
                v-model:visible="mostrarModalEditar"
                :reserva="reservaSeleccionada"
                :equipos="props.equipos"
                :recursos="props.recursos"
                :areas="props.areas"
                @actualizar-tabla="actualizarTabla"
            />
        </div>
    </AppLayout>
</template>
