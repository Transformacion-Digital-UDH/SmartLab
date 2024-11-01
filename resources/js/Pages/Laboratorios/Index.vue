<template>
    <AppLayout title="Laboratorios">
    <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Laboratorios
        </h2>
    </template>

    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
        <PrimaryButton @click="abrirModalCrear">Agregar laboratorio</PrimaryButton>
        </div>

        <!-- Tabla de laboratorios -->
        <TablaLabs
        :laboratorios="laboratorios"
        @editar="abrirModalEditar"
        @eliminar="confirmarEliminacion"
        @mostrar-areas="abrirModalAreas"
        />

        <!-- Modal para agregar/editar laboratorio -->
        <FormModal
        :mostrar="mostrarModal"
        :estaEditando="estaEditando"
        :dataLab="laboratorioSeleccionado"
        @cerrar="cerrarModal"
        @enviar="manejarEnvio"
        />

        <!-- Modal de Áreas -->
        <Modal v-model:open="mostrarModalAreas" title="Áreas del laboratorio" @cancel="cerrarModalAreas">
            <Table :columns="columnasAreas" :dataSource="areas" rowKey="id" />
        </Modal>

    </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import FormModal from '@/Pages/Laboratorios/Partes/FormModal.vue';
import { Modal } from 'ant-design-vue';
import TablaLabs from './Partes/TablaLabs.vue';
import { Table } from 'ant-design-vue';

// Columnas para la tabla de áreas en el modal
const columnasAreas = [
    { title: 'Nombre', dataIndex: 'nombre', key: 'nombre' },
    { title: 'Descripción', dataIndex: 'descripcion', key: 'descripcion' },
];

const { props } = usePage();
const laboratorios = ref(props.laboratorios || []);
const mostrarModal = ref(false);
const mostrarModalAreas = ref(false);
const estaEditando = ref(false);
const laboratorioSeleccionado = ref(null);
const areas = ref([]);

const cerrarModal = () => {
    mostrarModal.value = false;
};


// Abrir el modal para crear un nuevo laboratorio
const abrirModalCrear = () => {
    laboratorioSeleccionado.value = null;
    estaEditando.value = false;
    mostrarModal.value = true;
};

// Abrir el modal para editar un laboratorio
const abrirModalEditar = (laboratorio) => {
    laboratorioSeleccionado.value = laboratorio;
    estaEditando.value = true;
    mostrarModal.value = true;
};

// Abrir el modal de áreas
const abrirModalAreas = (laboratorio) => {
    areas.value = laboratorio.areas || [];
    mostrarModalAreas.value = true;
};

// Cerrar el modal de áreas
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
        eliminarLaboratorio(laboratorio);
    },
    });
};

// Eliminar un laboratorio
const eliminarLaboratorio = (laboratorio) => {
    router.delete(route('laboratorios.destroy', laboratorio.id), {
    onSuccess: () => {
        laboratorios.value = laboratorios.value.filter(lab => lab.id !== laboratorio.id);
    }
    });
};

const manejarEnvio = (datosLaboratorio) => {
    if (estaEditando.value) {
        // Lógica para actualizar un laboratorio existente
        router.put(route('laboratorios.update', laboratorioSeleccionado.value.id), datosLaboratorio, {
            onSuccess: () => {
                mostrarModal.value = false;
                // Actualizar el laboratorio en la lista local si es necesario
                const index = laboratorios.value.findIndex(lab => lab.id === laboratorioSeleccionado.value.id);
                if (index !== -1) {
                    laboratorios.value[index] = datosLaboratorio;
                }
            },
            onError: () => {
                console.error("Error al actualizar el laboratorio.");
            }
        });
    } else {
        // Lógica para crear un nuevo laboratorio
        router.post(route('laboratorios.store'), datosLaboratorio, {
            onSuccess: (page) => {
                mostrarModal.value = false;
                // Agregar el nuevo laboratorio a la lista local
                laboratorios.value.push(page.props.laboratorio);
            },
            onError: () => {
                console.error("Error al crear el laboratorio.");
            }
        });
    }
};

</script>
