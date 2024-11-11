<template>
    <AppLayout title="Laboratorios">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Laboratorios
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 px-4">
            <div class="flex flex-col-reverse justify-end gap-y-4 mb-6
                sm:flex-row sm:justify-between sm:items-center gap-x-4"
            >
                <InputSearch
                    v-model:value="valorBuscar"
                    placeholder="Buscar laboratorio por nombre o código"
                    class="w-full"
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
                v-if="labSeleccionado"
                v-model:visible="mostrarModalAreas"
                :laboratorio="labSeleccionado"
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
const labSeleccionado = ref(null);
const valorBuscar = ref('');

const labsFiltrados = computed(() => !valorBuscar.value
    ? laboratorios.value
    : laboratorios.value.filter(lab =>
        lab.nombre.toLowerCase().includes(valorBuscar.value.toLowerCase()) ||
        lab.codigo.toLowerCase().includes(valorBuscar.value.toLowerCase())
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
    labSeleccionado.value = { ...laboratorio };
    mostrarModalAreas.value = true;
};

const cerrarModalAreas = () => {
    mostrarModalAreas.value = false;
};

console.log("visble agregar", mostrarModalCrear.value);
console.log("visble editar", mostrarModalEditar.value);
</script>
