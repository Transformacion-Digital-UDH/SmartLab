<template>
    <AppLayout title="Recursos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">
                Inventario
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <Tabs default-active-key="1" type="line" >
                <TabPane key="1" tab="Recursos">
                    <TablaRecursos
                        :recursos="recursosFiltrados"
                        @editar="abrirModalEditar"
                        @abrir-crear="abrirModalCrear"
                        @actualizar-tabla="actualizarTabla"
                    />
                </TabPane>
                <TabPane key="2" tab="Equipos">
                    <!-- Contenido del tab Equipos -->
                    <p>Aquí irá el contenido relacionado a equipos.</p>
                </TabPane>
            </Tabs>

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
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Tabs, TabPane } from "ant-design-vue";
import TablaRecursos from "./Partes/Recursos/TablaRecursos.vue";
import ModalAgregar from "./Partes/Recursos/ModalAgregar.vue";
import ModalEditar from "./Partes/Recursos/ModalEditar.vue";

const { props } = usePage();
const recursos = ref(props.recursos || []);
const areas = ref(props.areas || []);
const equipos = ref(props.equipos || []);
const mostrarModalCrear = ref(false);
const mostrarModalEditar = ref(false);
const recursoSeleccionado = ref(null);
const valorBuscar = ref("");

// Filtrar recursos por nombre o código
const recursosFiltrados = computed(() =>
    !valorBuscar.value
        ? recursos.value
        : recursos.value.filter((recurso) =>
            recurso.nombre.toLowerCase().includes(valorBuscar.value.toLowerCase()) ||
            recurso.codigo?.toLowerCase().includes(valorBuscar.value.toLowerCase())
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

</script>

