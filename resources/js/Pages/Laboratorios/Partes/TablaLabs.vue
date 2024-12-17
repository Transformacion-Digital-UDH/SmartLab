<template>
    <Table :columns="columnas" :dataSource="laboratorios" rowKey="id" :pagination="false" :scroll="{ x: 800 }">
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'acciones'">
                <FormOutlined @click="editar(record)" class="text-blue-600" />
                <DeleteOutlined
                    @click="confirmarEliminacion(record)"
                    class="text-red-600 ml-2"
                />
                <AppstoreAddOutlined
                    @click="mostrarAreas(record)"
                    class="text-green-600 ml-2"
                />
            </template>
        </template>
    </Table>
</template>

<script setup>
import { Table, Modal, message } from "ant-design-vue";
import { router } from '@inertiajs/vue3';
import {
    FormOutlined,
    DeleteOutlined,
    AppstoreAddOutlined,
} from "@ant-design/icons-vue";

const props = defineProps({
    laboratorios: Array,
});

const emitir = defineEmits(["editar", "mostrar-areas", "actualizar-tabla"]);

// Definir las columnas de la tabla de laboratorios
const columnas = [
    { title: "Código", dataIndex: "codigo", key: "codigo", width: 120 },
    {
        title: "Nombre",
        dataIndex: "nombre",
        key: "nombre",
        sorter: (a, b) => a.nombre.localeCompare(b.nombre),
    },
    {
        title: "Responsable",
        customRender: ({ record }) => `${record.responsable.nombres} ${record.responsable.apellidos}`,
        key: "nombres",
    },
    {
        title: "Aforo",
        dataIndex: "aforo",
        key: "aforo",
        sorter: (a, b) => a.aforo - b.aforo,
        width: 100,
    },
    { title: "Email", dataIndex: "email", key: "email", responsive: ['sm'] },
    { title: "Acciones", key: "acciones", fixed: "right", width: 90 },
];

// Emitir eventos para editar, ver áreas y eliminar
function editar(laboratorio) {
    emitir("editar", laboratorio);
}

function mostrarAreas(laboratorio) {
    emitir("mostrar-areas", laboratorio);
}

const confirmarEliminacion = (laboratorio) => {
    Modal.confirm({
        title: '¿Estás seguro de eliminar este laboratorio?',
        content: `${laboratorio.nombre}`,
        okText: 'Confirmar',
        cancelText: 'Cancelar',
        onOk() {
            router.delete(route('laboratorios.destroy', laboratorio), {
                preserveScroll: true,
                onSuccess: () => {
                    message.success('Laboratorio eliminado exitosamente');
                    emitir('actualizar-tabla');
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
