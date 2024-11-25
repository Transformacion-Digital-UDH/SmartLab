<template>
    <AppLayout title="Recursos">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0"
            >
                Inventario
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <InputSearch
                    v-model:value="valorBuscar"
                    placeholder="Buscar recurso por nombre"
                    style="width: 350px"
                    size="large"
                />
                <Button
                    type="primary"
                    @click="abrirModalCrear"
                    size="large"
                    class="font-medium"
                    >Agregar recurso</Button
                >
            </div>

            <!-- Tabla de recursos -->
            <TablaRecursos
                :recursos="recursosFiltrados"
                @editar="abrirModalEditar"
                @mostrar-areas="abrirModalAreas"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal para agregar recurso -->
            <ModalAgregar
                v-model:visible="mostrarModalCrear"
                :areas="areas"
                :equipos="equipos"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal para editar recurso -->
            <ModalEditar
                v-if="recursoSeleccionado"
                v-model:visible="mostrarModalEditar"
                :recurso="recursoSeleccionado"
                :areas="areas"
                :equipos="equipos"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal de Áreas separado en AreasModal -->
            <ModalAreas
                v-if="recursoSeleccionadoId"
                v-model:open="mostrarModalAreas"
                :recurso_id="recursoSeleccionadoId"
                @cerrar="cerrarModalAreas"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Button, InputSearch } from "ant-design-vue";
import TablaRecursos from "./Partes/TablaRecursos.vue";
import ModalAreas from "./Partes/ModalAreas.vue";
import ModalAgregar from "./Partes/ModalAgregar.vue";
import ModalEditar from "./Partes/ModalEditar.vue";

const { props } = usePage();
const recursos = ref(props.recursos || []);
const areas = ref(props.areas || []);
const equipos = ref(props.equipos || []);
const mostrarModalCrear = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalAreas = ref(false);
const recursoSeleccionadoId = ref(null);
const recursoSeleccionado = ref(null);
const valorBuscar = ref("");

const recursosFiltrados = computed(() =>
    !valorBuscar.value
        ? recursos.value
        : recursos.value.filter((recurso) =>
              recurso.nombre
                  .toLowerCase()
                  .includes(valorBuscar.value.toLowerCase())
          )
);

const actualizarTabla = () => {
    mostrarModalCrear.value = false;
    mostrarModalEditar.value = false;
    router.visit(route("recursos.index"), { preserveScroll: true });
};

const abrirModalCrear = () => {
    mostrarModalCrear.value = true;
};

const abrirModalEditar = (recurso) => {
    mostrarModalEditar.value = true;
    recursoSeleccionado.value = { ...recurso };
};

const abrirModalAreas = (recurso) => {
    recursoSeleccionadoId.value = recurso.id;
    mostrarModalAreas.value = true;
};

const cerrarModalAreas = () => {
    mostrarModalAreas.value = false;
};
</script>
