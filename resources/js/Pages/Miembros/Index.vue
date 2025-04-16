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
                    @change="buscarMiembros"
                />
                <Button type="primary" @click="abrirModalCrear" size="large" class="font-medium">Agregar miembro</Button>
            </div>

            <!-- Tabla de miembros -->
            <TablaMiembros
                :miembros="miembros"
                @editar="abrirModalEditar"
                @mostrar-areas="abrirModalAreas"
                @actualizar-tabla="actualizarTabla"
                @cambiar-pagina="cambiarPagina"
            />

            <!-- Modal para agregar miembro -->
            <ModalAgregar
                v-model:visible="mostrarModalCrear"
                :usuarios="usuarios"
                :miembros="miembros.data || []"
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
import { ref, computed, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button, InputSearch } from 'ant-design-vue';
import TablaMiembros from './Partes/TablaMiembros.vue';
import ModalAgregar from './Partes/ModalAgregar.vue';
import ModalEditar from './Partes/ModalEditar.vue';

const { props } = usePage();
const miembros = ref(props.miembros || {});
const usuarios = ref(props.usuarios || []);
const mostrarModalCrear = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalAreas = ref(false);
const userSeleccionado = ref(null);
const valorBuscar = ref('');

// Ya no necesitamos filtrar los datos aquí, lo haremos en el servidor
// cuando cambiemos de página o busquemos

const actualizarTabla = () => {
    mostrarModalCrear.value = false;
    mostrarModalEditar.value = false;
    router.visit(route('miembros.index'), { 
        preserveScroll: true,
        data: {
            search: valorBuscar.value,
            page: miembros.value.current_page
        }
    });
};

const buscarMiembros = () => {
    router.visit(route('miembros.index'), {
        data: {
            search: valorBuscar.value,
            page: 1 // Volver a página 1 cuando se realiza una búsqueda
        },
        preserveState: false, // No preservar el estado para forzar actualización
        preserveScroll: true,
        only: ['miembros'] // Solo actualizar los datos de miembros
    });
};

const cambiarPagina = (pagina) => {
    router.visit(route('miembros.index'), {
        data: {
            page: pagina,
            search: valorBuscar.value
        },
        preserveState: false, // No preservar el estado para forzar actualización
        preserveScroll: true,
        only: ['miembros'] // Solo actualizar los datos de miembros
    });
};

// Eliminamos la función cambiarTamanoPagina ya que no la necesitamos más

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
