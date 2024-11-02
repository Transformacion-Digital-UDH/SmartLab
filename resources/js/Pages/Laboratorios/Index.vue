<template>
    <AppLayout title="Laboratorios">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Laboratorios
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <Button type="primary" @click="abrirModalCrear">Agregar laboratorio</Button>
            </div>

            <!-- Tabla de laboratorios -->
            <TablaLabs
                :laboratorios="laboratorios"
                @editar="abrirModalEditar"
                @eliminar="confirmarEliminacion"
                @mostrar-areas="abrirModalAreas"
            />

            <!-- Modal para agregar laboratorio -->
            <ModalAgregar
                v-model:visible="mostrarModalCrear"
                :responsables="responsables"
                @guardar="guardarLaboratorio"
            />

            <!-- Modal de Áreas separado en AreasModal -->
            <AreasModal
                v-if="labSeleccionadoId"
                v-model:open="mostrarModalAreas"
                :laboratorio_id="labSeleccionadoId"
                @cerrar="cerrarModalAreas"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button } from 'ant-design-vue';
import TablaLabs from './Partes/TablaLabs.vue';
import AreasModal from './Partes/AreasModal.vue';
import ModalAgregar from './Partes/ModalAgregar.vue';

const { props } = usePage();
const laboratorios = ref(props.laboratorios || []);
const responsables = ref(props.responsables || []);
const mostrarModalCrear = ref(false);
const mostrarModalAreas = ref(false);
const labSeleccionadoId = ref(null);

// Función para abrir el modal de creación
const abrirModalCrear = () => {
    mostrarModalCrear.value = true;
};

// Función para guardar el nuevo laboratorio
const guardarLaboratorio = (nuevoLaboratorio) => {
    router.post(route('laboratorios.store'), nuevoLaboratorio, {
        onSuccess: (page) => {
            laboratorios.value.push(page.props.laboratorio);
            mostrarModalCrear.value = false;
        }
    });
};

// Función para abrir el modal de áreas
const abrirModalAreas = (laboratorio) => {
    labSeleccionadoId.value = laboratorio.id;
    mostrarModalAreas.value = true;
};

// Función para cerrar el modal de áreas
const cerrarModalAreas = () => {
    mostrarModalAreas.value = false;
};

// Confirmar eliminación
const confirmarEliminacion = (laboratorio) => {
    Modal.confirm({
        title: '¿Estás seguro de eliminar este laboratorio?',
        content: `${laboratorio.nombre}`,
        okText: 'Confirmar',
        cancelText: 'Cancelar',
        onOk() {
            router.delete(route('laboratorios.destroy', laboratorio.id), {
                onSuccess: () => {
                    laboratorios.value = laboratorios.value.filter(lab => lab.id !== laboratorio.id);
                }
            });
        },
    });
};
</script>
