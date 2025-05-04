<template>
    <AppLayout title="Reservas">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0"
            >
                Reservas
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 px-4">
            <div
                class="flex flex-col-reverse justify-end gap-y-4 mb-6 sm:flex-row sm:justify-between sm:items-center gap-x-4"
            >
                <InputSearch
                    v-model:value="valorBuscar"
                    placeholder="Buscar reserva por reservante, equipo o recurso"
                    class="w-full"
                    size="large"
                />

                <Button
                    type="primary"
                    @click="abrirModalCrear"
                    size="large"
                    class="font-medium"
                    >Agregar reserva</Button
                >
            </div>

            <!-- Tabla de reservas -->
            <TablaReservas
                :reservas="reservasFiltradas"
                @editar="abrirModalEditar"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal para agregar reserva -->
            <ModalAgregar
                v-model:visible="mostrarModalCrear"
                :usuarios="props.usuarios"
                :equipos="props.equipos"
                :recursos="props.recursos"
                :areas="props.areas"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal para editar reserva -->
            <ModalEditar
                v-if="reservaSeleccionada"
                v-model:visible="mostrarModalEditar"
                :reserva="reservaSeleccionada"
                :usuarios="props.usuarios"
                :equipos="props.equipos"
                :recursos="props.recursos"
                :areas="props.areas"
                @actualizar-tabla="actualizarTabla"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Button, InputSearch } from "ant-design-vue";
import TablaReservas from "./Partes/TablaReservas.vue";
import ModalAgregar from "./Partes/ModalAgregar.vue";
import ModalEditar from "./Partes/ModalEditar.vue";

const { props } = usePage();
const reservas = ref(props.reservas || []);
const mostrarModalCrear = ref(false);
const mostrarModalEditar = ref(false);
const reservaSeleccionada = ref(null);
const valorBuscar = ref("");

// Filtro de reservas
const reservasFiltradas = computed(() =>
    !valorBuscar.value
        ? reservas.value
        : reservas.value.filter(
            (reserva) =>
                reserva.usuario.name
                    .toLowerCase()
                    .includes(valorBuscar.value.toLowerCase()) ||
                (reserva.equipo?.nombre || "")
                    .toLowerCase()
                    .includes(valorBuscar.value.toLowerCase()) ||
                (reserva.recurso?.nombre || "")
                    .toLowerCase()
                    .includes(valorBuscar.value.toLowerCase())
        )
);

// Actualizar la tabla
const actualizarTabla = () => {
    router.visit(route("reservas.index"), { preserveScroll: true });
};

// Abrir el modal de creación
const abrirModalCrear = () => {
    mostrarModalCrear.value = true;
};

// Abrir el modal de edición
const abrirModalEditar = (reserva) => {
    mostrarModalEditar.value = true;
    reservaSeleccionada.value = { ...reserva };
};
</script>
