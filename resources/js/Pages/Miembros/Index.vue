<template>
    <AppLayout title="Miembros">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">
                Miembros
            </h2>
        </template>

        <div class="px-4 py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col-reverse justify-end mb-6 gap-y-4 sm:flex-row sm:justify-between sm:items-center gap-x-4">
                <InputSearch
                    v-model:value="valorBuscar"
                    placeholder="Buscar miembro por nombre o código"
                    class="w-full"
                    size="large"
                />
                <Button type="primary" @click="abrirModalCrear" size="large" class="font-medium">Agregar Nuevo Miembro</Button>
            </div>

            <!-- Tabla de miembros -->
            <TablaMiembros
                :miembros="usersFiltrados"
                @editar="abrirModalEditar"
                @mostrar-areas="abrirModalAreas"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal para agregar miembro -->
            <ModalAgregar
                v-model:visible="mostrarModalCrear"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal para editar miembro -->
            <ModalEditar
                v-if="userSeleccionado"
                v-model:visible="mostrarModalEditar"
                :miembro="userSeleccionado"
                @actualizar-tabla="actualizarTabla"
            />

        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button, InputSearch } from 'ant-design-vue';
import TablaMiembros from './Partes/TablaMiembros.vue';
import ModalAreas from './Partes/ModalAreas.vue';
import ModalAgregar from './Partes/ModalAgregar.vue';
import ModalEditar from './Partes/ModalEditar.vue';

const { props } = usePage();
const miembros = ref(props.miembros || []);
const mostrarModalCrear = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalAreas = ref(false);
const userSeleccionado = ref(null);
const valorBuscar = ref('');

const usersFiltrados = computed(() => !valorBuscar.value
    ? miembros.value
    : miembros.value.filter(user =>
        user.nombres.toLowerCase().includes(valorBuscar.value.toLowerCase()) ||
        user.codigo.toLowerCase().includes(valorBuscar.value.toLowerCase())
    )
);

const actualizarTabla = () => {
    mostrarModalCrear.value = false;
    mostrarModalEditar.value = false;
    router.visit(route('miembros.index'), { preserveScroll: true });
};

const abrirModalCrear = () => {
    mostrarModalCrear.value = true;
};

const abrirModalEditar = (miembro) => {
    mostrarModalEditar.value = true;
    userSeleccionado.value = { ...miembro };
};

const abrirModalAreas = (miembro) => {
    userSeleccionado.value = { ...miembro };
    mostrarModalAreas.value = true;
};

const cerrarModalAreas = () => {
    mostrarModalAreas.value = false;
};
</script>
