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

            <!-- Tabla de laboratorios utilizando Ant Design Table -->
            <Table :columns="columns" :dataSource="laboratorios" rowKey="id">
                <template #acciones="{ record }">
                    <FormOutlined @click="abrirModalEditar(record)" class="text-blue-600" />
                    <DeleteOutlined @click="confirmarEliminar(record)" class="text-red-600 ml-2" />
                    <AppstoreAddOutlined @click="abrirModalAreas(record)" class="text-green-600 ml-2" />
                </template>
            </Table>

            <!-- Modal para agregar/editar laboratorio -->
            <FormModal
                :mostrar="mostrarModal"
                :estaEditando="estaEditando"
                :dataLab="laboratorioSeleccionado"
                @cerrar="cerrarModal"
                @enviar="manejarEnvio"
            />

            <!-- Modal de Áreas -->
            <Modal v-model:visible="mostrarModalAreas" title="Áreas del laboratorio" @cancel="cerrarModalAreas">
                <Table :columns="areaColumns" :dataSource="areas" rowKey="id" />
            </Modal>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import FormModal from '@/Pages/Laboratorios/Partials/FormModal.vue';
import Table from 'ant-design-vue/es/table';
import { Modal } from 'ant-design-vue';
import { FormOutlined, DeleteOutlined, AppstoreAddOutlined } from '@ant-design/icons-vue';

// Definir las columnas de la tabla de laboratorios
const columns = [
    { title: 'Nombre', dataIndex: 'nombre', key: 'nombre', sorter: (a, b) => a.nombre.localeCompare(b.nombre) },
    { title: 'Código', dataIndex: 'codigo', key: 'codigo' },
    { title: 'Responsable', dataIndex: ['responsable', 'nombre'], key: 'responsable' },
    { title: 'Aforo', dataIndex: 'aforo', key: 'aforo', sorter: (a, b) => a.aforo - b.aforo },
    { title: 'Email', dataIndex: 'email', key: 'email' },
    { title: 'Acciones', key: 'acciones', slots: { customRender: 'acciones' } },
];

// Columnas para la tabla de áreas en el modal
const areaColumns = [
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
    areas.value = laboratorio.areas || []; // Asigna las áreas del laboratorio seleccionado
    mostrarModalAreas.value = true;
};

// Cerrar el modal de áreas
const cerrarModalAreas = () => {
    mostrarModalAreas.value = false;
};

// Confirmar eliminación
const confirmarEliminar = (laboratorio) => {
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
</script>
