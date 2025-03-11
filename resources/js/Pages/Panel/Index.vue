<script setup>
import { ref, computed } from 'vue';
import { Select } from 'ant-design-vue';
import AppLayout from "@/Layouts/AppLayout.vue";
import CardItems from "./Partes/CardItems.vue";
import BarraIngresos from "./Charts/BarraIngresos.vue";
import TablaReservas from "./Partes/Reservas/TablaReservas.vue";
import ModalEditar from "./Partes/Reservas/ModalEditar.vue";
import { router } from "@inertiajs/vue3";
import { ExperimentOutlined } from "@ant-design/icons-vue";

const props = defineProps({
    reservas: Array,
    equipos: Array,
    recursos: Array,
    areas: Array,
    laboratorios: Array,
    metricas: Object,
});

const reservas = ref(props.reservas || []);
const mostrarModalEditar = ref(false);
const reservaSeleccionada = ref(null);

// Para seleccionar el calendario del laboratorio
const selectedCalendarId = ref(
    props.laboratorios.length ? props.laboratorios[0].google_calendar_id : ''
);

// Opciones para el Select (etiqueta: laboratorio, valor: google_calendar_id)
const labOptions = computed(() =>
    props.laboratorios.map(lab => ({
        label: lab.nombre,
        value: lab.google_calendar_id
    }))
);

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
                <!-- Métricas -->
                <div class="bg-gray-50 dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">Métricas claves</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="grid grid-cols-2 gap-4">
                            <CardItems name="Usuarios" des="Registros" :valor="metricas.usuarios" />
                            <CardItems name="Proyectos" des="Ejecutándose" :valor="metricas.proyectos" />
                            <CardItems name="Asistencias" des="Ingresos hoy" :valor="metricas.asistencias" />
                            <CardItems name="Reservas" des="Hoy" :valor="metricas.reservas" />
                        </div>
                        <div class="lg:col-span-2">
                            <BarraIngresos class="w-full" />
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-4">Reservas aprobadas</h3>
                    <TablaReservas
                        :reservas="reservas"
                        @editar="abrirModalEditar"
                        @actualizar-tabla="actualizarTabla"
                    />
                </div>

                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Calendario de reservas</h3>
                        <Select v-model:value="selectedCalendarId"                             :options="labOptions"
                        style="width: 300px;"
                        size="middle"
                        >
                            <template #suffixIcon>
                                <ExperimentOutlined />
                            </template>

                        </Select>

                    </div>
                    <iframe
                        :src="`https://calendar.google.com/calendar/embed?src=${selectedCalendarId}&ctz=America%2FLima&mode=WEEK`"
                        class="w-full border-none rounded-lg"
                        height="600px"
                    ></iframe>
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
