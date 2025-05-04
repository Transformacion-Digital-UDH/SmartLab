<script setup>
import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { Select } from "ant-design-vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import CardItems from "./Partes/CardItems.vue";
import BarraIngresos from "./Charts/BarraIngresos.vue";
import TablaReservas from "./Partes/Reservas/TablaReservas.vue";
import ModalEditarReserva from "./Partes/Reservas/ModalEditar.vue";
import TablaUsuarios from "./Partes/Usuarios/TablaUsuarios.vue";
import ModalEditarUsuario from "./Partes/Usuarios/ModalEditar.vue";
import { router } from "@inertiajs/vue3";
import { ExperimentOutlined } from "@ant-design/icons-vue";

const props = defineProps({
    reservas: Array,
    equipos: Array,
    recursos: Array,
    areas: Array,
    laboratorios: Array,
    metricas: Object,
    asistenciasMensuales: {
        type: Array,
        default: () => [],
    },
    etiquetasMeses: {
        type: Array,
        default: () => [],
    },
    usuarios_solicitud: {
        type: Array,
        default: () => [],
    },
});

const { auth, laboratoriosParticipante } = usePage().props;
const labSeleccionado = ref(auth.user.laboratorio_seleccionado);

console.log(laboratoriosParticipante);
console.log(labSeleccionado.value);

const reservas = ref(props.reservas || []);
const mostrarModalEditarReserva = ref(false);
const reservaSeleccionada = ref(null);

const mostrarModalEditarUsuario = ref(false);
const usuarioSeleccionado = ref(null);

// Opciones para el Select (etiqueta: laboratorio, valor: google_calendar_id)
const labOptions = computed(() =>
    props.laboratorios.map((lab) => ({
        label: lab.nombre,
        value: lab.google_calendar_id,
        id: lab.id, // se incluye el id para facilitar la comparación
    }))
);

// Inicializar selectedCalendarId:
// Si labSeleccionado existe y se encuentra en labOptions, se usa su google_calendar_id,
// de lo contrario se asigna el google_calendar_id del primer laboratorio en la lista.
const selectedCalendarId = ref("");
if (props.laboratorios.length) {
    const labEncontrado = props.laboratorios.find(
        (lab) => lab.id === labSeleccionado.value
    );
    selectedCalendarId.value = labEncontrado
        ? labEncontrado.google_calendar_id
        : props.laboratorios[0].google_calendar_id;
}

// Actualizar la tabla
const actualizarTabla = () => {
    router.visit(route("dashboard"), { preserveScroll: true });
};

// Abrir el modal de edición de reserva
const abrirModalEditarReserva = (reserva) => {
    mostrarModalEditarReserva.value = true;
    reservaSeleccionada.value = { ...reserva };
};

// Abrir el modal de edición de usuario
const abrirModalEditarUsuario = (usuario) => {
    mostrarModalEditarUsuario.value = true;
    usuarioSeleccionado.value = { ...usuario };
};
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">
                Dashboard
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                <!-- Métricas -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium mb-4">Métricas claves</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="grid grid-cols-2 gap-4">
                            <CardItems
                                name="Usuarios"
                                des="Registros"
                                :valor="metricas.usuarios"
                            />
                            <CardItems
                                name="Proyectos"
                                des="Ejecutándose"
                                :valor="metricas.proyectos"
                            />
                            <CardItems
                                name="Asistencias"
                                des="Ingresos hoy"
                                :valor="metricas.asistencias"
                            />
                            <CardItems
                                name="Reservas"
                                des="Hoy"
                                :valor="metricas.reservas"
                            />
                        </div>
                        <div class="lg:col-span-2">
                            <BarraIngresos
                                class="w-full"
                                :datos="asistenciasMensuales"
                                :etiquetas="etiquetasMeses"
                            />
                        </div>
                    </div>
                </div>

                <!-- Sección de reservas -->
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-4">
                        Reservas por aprobar
                    </h3>
                    <TablaReservas
                        :reservas="reservas"
                        @editar="abrirModalEditarReserva"
                        @actualizar-tabla="actualizarTabla"
                    />
                </div>

                <!-- Calendario -->
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">
                            Calendario de reservas
                        </h3>
                        <Select
                            v-model:value="selectedCalendarId"
                            :options="labOptions"
                            style="width: 300px"
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

                <!-- Sección de usuarios en solicitud -->
                <div class="bg-white dark:bg-gray-900 p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium mb-4">
                        Usuarios por aprobar
                    </h3>
                    <TablaUsuarios
                        :usuarios_solicitud="usuarios_solicitud"
                        @editar="abrirModalEditarUsuario"
                        @actualizar-tabla="actualizarTabla"
                    />
                </div>
            </div>
        </div>

        <!-- Modal para editar reserva -->
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <ModalEditarReserva
                v-if="reservaSeleccionada"
                v-model:visible="mostrarModalEditarReserva"
                :reserva="reservaSeleccionada"
                :equipos="props.equipos"
                :recursos="props.recursos"
                :areas="props.areas"
                @actualizar-tabla="actualizarTabla"
            />
        </div>

        <!-- Modal para editar usuario -->
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <ModalEditarUsuario
                v-if="usuarioSeleccionado"
                v-model:visible="mostrarModalEditarUsuario"
                :usuario="usuarioSeleccionado"
                @actualizar-tabla="actualizarTabla"
            />
        </div>
    </AppLayout>
</template>
