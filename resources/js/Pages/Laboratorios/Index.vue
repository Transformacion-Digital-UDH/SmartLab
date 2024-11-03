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
                @actualizarTabla="agregarLaboratorio"
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
import { Button, Modal, message } from 'ant-design-vue';
import TablaLabs from './Partes/TablaLabs.vue';
import AreasModal from './Partes/AreasModal.vue';
import ModalAgregar from './Partes/ModalAgregar.vue';

const { props } = usePage();
const laboratorios = ref(props.laboratorios || []);
const mostrarModalCrear = ref(false);
const mostrarModalAreas = ref(false);
const labSeleccionadoId = ref(null);

const abrirModalCrear = () => {
    mostrarModalCrear.value = true;
};

// Function to add the new laboratory to the list
const agregarLaboratorio = (nuevoLaboratorio) => {
    console.log('Nuevo laboratorio:', nuevoLaboratorio);
    laboratorios.value.push(nuevoLaboratorio);
    console.log('Laboratorios:', laboratorios.value);
    mostrarModalCrear.value = false;
};

// Function to open areas modal
const abrirModalAreas = (laboratorio) => {
    labSeleccionadoId.value = laboratorio.id;
    mostrarModalAreas.value = true;
};

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
                preserveScroll: true,
                onSuccess: () => {
                    laboratorios.value = laboratorios.value.filter(lab => lab.id !== laboratorio.id);
                    message.success('Laboratorio eliminado exitosamente');
                },
                onError: (error) => {
                    console.error('Error al eliminar el laboratorio:', error);
                    message.error('Error al eliminar el laboratorio');
                }
            });
        },
    });
};

</script>
