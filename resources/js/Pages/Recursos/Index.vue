<template>
    <AppLayout title="Recursos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">
                Inventario
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-4 sm:px-6 lg:px-8 px-4">
            <Tabs default-active-key="1" type="line" >
                <TabPane key="1" tab="Recursos">
                    <TablaRecursos
                        :recursos="recursos"
                        @editar="abrirModalEditarRecurso"
                        @abrir-crear="abrirModalCrearRecurso"
                        @actualizar-tabla="actualizarTablaRecursos"
                    />
                </TabPane>
                <TabPane key="2" tab="Equipos">
                    <TablaEquipos
                        :equipos="equipos"
                        @editar="abrirModalEditarEquipo"
                        @abrir-crear="abrirModalCrearEquipo"
                        @actualizar-tabla="actualizarTablaEquipos"
                    />
                </TabPane>
            </Tabs>

            <!-- Modal para agregar recurso -->
            <ModalAgregarRecurso
                v-if="mostrarModalCrearRecurso"
                v-model:visible="mostrarModalCrearRecurso"
                :areas="areas"
                :equipos="equipos"
                @actualizar-tabla="actualizarTablaRecursos"
            />

            <!-- Modal para editar recurso -->
            <ModalEditarRecurso
                v-if="recursoSeleccionado"
                v-model:visible="mostrarModalEditarRecurso"
                :recurso="recursoSeleccionado"
                :areas="areas"
                :equipos="equipos"
                @actualizar-tabla="actualizarTablaRecursos"
            />

            <!-- Modal para agregar equipo -->
            <ModalAgregarEquipo
                v-if="mostrarModalCrearEquipo"
                v-model:visible="mostrarModalCrearEquipo"
                :areas="areas"
                :recursos="recursos"
                @actualizar-tabla="actualizarTablaEquipos"
            />

            <!-- Modal para editar equipo -->
            <ModalEditarEquipo
                v-if="equipoSeleccionado"
                v-model:visible="mostrarModalEditarEquipo"
                :equipo="equipoSeleccionado"
                :areas="areas"
                :recursos="recursos"
                @actualizar-tabla="actualizarTablaEquipos"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Tabs, TabPane } from "ant-design-vue";
import TablaRecursos from "./Partes/Recursos/TablaRecursos.vue";
import ModalAgregarRecurso from "./Partes/Recursos/ModalAgregar.vue";
import ModalEditarRecurso from "./Partes/Recursos/ModalEditar.vue";
import TablaEquipos from "./Partes/Equipos/TablaEquipos.vue";
import ModalAgregarEquipo from "./Partes/Equipos/ModalAgregar.vue";
import ModalEditarEquipo from "./Partes/Equipos/ModalEditar.vue";

const { props } = usePage();
const areas = ref(props.areas || []);
const equipos = ref(props.equipos || []);
const recursos = ref(props.recursos || []);

// Variables y funciones para Recursos
const mostrarModalCrearRecurso = ref(false);
const mostrarModalEditarRecurso = ref(false);
const recursoSeleccionado = ref(null);

const actualizarTablaRecursos = () => {
    mostrarModalCrearRecurso.value = false;
    mostrarModalEditarRecurso.value = false;
    router.visit(route("recursos.index"), { preserveScroll: true });
};

const abrirModalCrearRecurso = () => {
    mostrarModalCrearRecurso.value = true;
};

const abrirModalEditarRecurso = (recurso) => {
    mostrarModalEditarRecurso.value = true;
    recursoSeleccionado.value = { ...recurso };
};

// Variables y funciones para Equipos
const mostrarModalCrearEquipo = ref(false);
const mostrarModalEditarEquipo = ref(false);
const equipoSeleccionado = ref(null);

const actualizarTablaEquipos = () => {
    mostrarModalCrearEquipo.value = false;
    mostrarModalEditarEquipo.value = false;
    router.visit(route("equipos.index"), { preserveScroll: true });
};

const abrirModalCrearEquipo = () => {
    mostrarModalCrearEquipo.value = true;
};

const abrirModalEditarEquipo = (equipo) => {
    mostrarModalEditarEquipo.value = true;
    equipoSeleccionado.value = { ...equipo };
};
</script>
