<template>
    <AppLayout title="Laboratorios">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Laboratorios
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <InputSearch
                    v-model:value="valorBuscar"
                    placeholder="Buscar laboratorio por nombre"
                    style="width: 350px"
                    size="large"
                />
                <Button type="primary" @click="abrirModalCrear" size="large" class="font-medium" >Agregar laboratorio</Button>
            </div>

            <!-- Tabla de laboratorios -->
            <TablaLabs
                :laboratorios="labsFiltrados"
                @editar="abrirModalEditar"
                @mostrar-areas="abrirModalAreas"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal para agregar laboratorio -->
            <ModalAgregar
                v-model:visible="mostrarModalCrear"
                :responsables="props.responsables"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal para editar laboratorio -->
            <ModalEditar
                v-if="labSeleccionado"
                v-model:visible="mostrarModalEditar"
                :laboratorio="labSeleccionado"
                :responsables="props.responsables"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal de Áreas separado en AreasModal -->
            <ModalAreas
                v-if="labSeleccionadoId"
                v-model:open="mostrarModalAreas"
                :laboratorio_id="labSeleccionadoId"
                @cerrar="cerrarModalAreas"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button, InputSearch } from 'ant-design-vue';
import TablaLabs from './Partes/TablaLabs.vue';
import ModalAreas from './Partes/ModalAreas.vue';
import ModalAgregar from './Partes/ModalAgregar.vue';
import ModalEditar from './Partes/ModalEditar.vue';

const { props } = usePage();
const laboratorios = ref(props.laboratorios || []);
const mostrarModalCrear = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalAreas = ref(false);
const labSeleccionadoId = ref(null);
const labSeleccionado = ref(null);
const valorBuscar = ref('');

const labsFiltrados = computed(() => !valorBuscar.value
    ? laboratorios.value
    : laboratorios.value.filter(lab =>
        lab.nombre.toLowerCase().includes(valorBuscar.value.toLowerCase())
    )
);


const actualizarTabla = () => {
    mostrarModalCrear.value = false;
    mostrarModalEditar.value = false;
    router.visit(route('laboratorios.index'), { preserveScroll: true });
};

const abrirModalCrear = () => {
    mostrarModalCrear.value = true;
};

const abrirModalEditar = (laboratorio) => {
    mostrarModalEditar.value = true;
    labSeleccionado.value = { ...laboratorio };
};

const abrirModalAreas = (laboratorio) => {
    labSeleccionadoId.value = laboratorio.id;
    mostrarModalAreas.value = true;
};

const cerrarModalAreas = () => {
    mostrarModalAreas.value = false;
};

console.log("visble agregar", mostrarModalCrear.value);
console.log("visble editar", mostrarModalEditar.value);
</script>
