<template>
    <AppLayout title="Áreas">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0"
            >
                Áreas
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 px-4">
            <div
                class="flex flex-col-reverse justify-end gap-y-4 mb-6 sm:flex-row sm:justify-between sm:items-center gap-x-4"
            >
                <InputSearch
                    v-model:value="valorBuscar"
                    placeholder="Buscar área por nombre"
                    class="w-full"
                    size="large"
                />

                <Button
                    type="primary"
                    @click="abrirModalCrear"
                    size="large"
                    class="font-medium"
                    >Agregar área</Button
                >
            </div>

            <!-- Tabla de Áreas -->
            <TablaAreas
                :areas="areasFiltradas"
                @editar="abrirModalEditar"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal para agregar área -->
            <ModalAgregar
                v-model:visible="mostrarModalCrear"
                :laboratorios="props.laboratorios"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal para editar área -->
            <ModalEditar
                v-if="areaSeleccionada"
                v-model:visible="mostrarModalEditar"
                :area="areaSeleccionada"
                :laboratorios="props.laboratorios"
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
import TablaAreas from "./Partes/TablaAreas.vue";
import ModalAgregar from "./Partes/ModalAgregar.vue";
import ModalEditar from "./Partes/ModalEditar.vue";

const { props } = usePage();
const areas = ref(props.areas || []);
const mostrarModalCrear = ref(false);
const mostrarModalEditar = ref(false);
const areaSeleccionada = ref(null);
const valorBuscar = ref("");

const areasFiltradas = computed(() =>
    !valorBuscar.value
        ? areas.value
        : areas.value.filter(
            (area) =>
                area.nombre
                    .toLowerCase()
                    .includes(valorBuscar.value.toLowerCase())
        )
);

const actualizarTabla = () => {
    router.visit(route("areas.index"), { preserveScroll: true });
};

const abrirModalCrear = () => {
    mostrarModalCrear.value = true;
};

const abrirModalEditar = (area) => {
    mostrarModalEditar.value = true;
    areaSeleccionada.value = { ...area };
};
</script>
