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
                    <a @click="abrirModalEditar(record)" class="text-blue-600">Editar</a>
                    <a @click="eliminarLaboratorio(record)" class="text-red-600 ml-2">Eliminar</a>
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

// Definir las columnas de la tabla
const columns = [
    {
        title: 'Nombre',
        dataIndex: 'nombre',
        key: 'nombre',
        sorter: (a, b) => a.nombre.localeCompare(b.nombre),  
        sortDirections: ['ascend', 'descend'],
    },
    {
        title: 'Código',
        dataIndex: 'codigo',
        key: 'codigo',
    },
    {
        title: 'Aforo',
        dataIndex: 'aforo',
        key: 'aforo',
        sorter: (a, b) => a.aforo - b.aforo,
        sortDirections: ['ascend', 'descend'],
    },
    {
        title: 'Email',
        dataIndex: 'email',
        key: 'email',
    },
    {
        title: 'Acciones',
        key: 'acciones',
        slots: { customRender: 'acciones' },

    },
];

const { props } = usePage();
const laboratorios = ref(props.laboratorios || []);
const mostrarModal = ref(false);
const estaEditando = ref(false);
const laboratorioSeleccionado = ref(null);

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

// Cerrar el modal
const cerrarModal = () => {
    mostrarModal.value = false;
};

const manejarEnvio = () => {
    router.get(route('laboratorios.index'), {}, {
        preserveScroll: true,
        onSuccess: (response) => {
            laboratorios.value = response.props.laboratorios;
        }
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
