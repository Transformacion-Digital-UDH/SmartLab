<template>
    <AppLayout title="Proyectos">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0"
            >
                Proyectos
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 px-4">
            <div
                class="flex flex-col-reverse justify-end gap-y-4 mb-6 sm:flex-row sm:justify-between sm:items-center gap-x-4"
            >
                <InputSearch
                    v-model:value="valorBuscar"
                    placeholder="Buscar proyecto por nombre o descripción"
                    class="w-full"
                    size="large"
                />
                <Button
                    type="primary"
                    @click="abrirModalCrear"
                    size="large"
                    class="font-medium"
                    >Agregar proyecto</Button
                >
            </div>

            <!-- Tabla de proyectos -->
            <TablaProyectos
                :proyectos="proyectosFiltrados"
                @editar="abrirModalEditar"
                @mostrar-participantes="abrirModalParticipantes"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal para agregar proyecto -->
            <ModalAgregar
                v-model:visible="mostrarModalCrear"
                :responsables="props.responsables"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal para editar proyecto -->
            <ModalEditar
                v-if="proyectoSeleccionado"
                v-model:visible="mostrarModalEditar"
                :proyecto="proyectoSeleccionado"
                :responsables="props.responsables"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal para participantes -->
            <ModalParticipantes
                v-if="proyectoSeleccionado"
                v-model:visible="mostrarModalParticipantes"
                :proyecto="proyectoSeleccionado"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Button, InputSearch } from "ant-design-vue";
import TablaProyectos from "./Partes/TablaProyectos.vue";
import ModalAgregar from "./Partes/ModalAgregar.vue";
import ModalEditar from "./Partes/ModalEditar.vue";
import ModalParticipantes from "./Partes/Participantes/ModalParticipantes.vue";

const { props } = usePage();
const proyectos = ref(props.proyectos || []);
const mostrarModalCrear = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalParticipantes = ref(false);
const proyectoSeleccionado = ref(null);
const valorBuscar = ref("");

// Filtrar proyectos según el término de búsqueda
const proyectosFiltrados = computed(() =>
    !valorBuscar.value
        ? proyectos.value
        : proyectos.value.filter(
              (proyecto) =>
                  proyecto.nombre
                      .toLowerCase()
                      .includes(valorBuscar.value.toLowerCase()) ||
                  proyecto.descripcion
                      ?.toLowerCase()
                      .includes(valorBuscar.value.toLowerCase())
          )
);

// Actualizar la tabla de proyectos
const actualizarTabla = () => {
    router.visit(route("proyectos.index"), { preserveScroll: true });
};

// Abrir modal para agregar un proyecto
const abrirModalCrear = () => {
    mostrarModalCrear.value = true;
};

// Abrir modal para editar un proyecto
const abrirModalEditar = (proyecto) => {
    mostrarModalEditar.value = true;
    proyectoSeleccionado.value = { ...proyecto };
};

// Abrir modal para mostrar participantes
const abrirModalParticipantes = (proyecto) => {
    mostrarModalParticipantes.value = true;
    proyectoSeleccionado.value = { ...proyecto };
};
</script>
