<template>
    <Table :columns="columnas" :dataSource="recursos" rowKey="id" :pagination="false">
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
import { defineProps, defineEmits } from "vue";
import { Table, Modal, message } from "ant-design-vue";
import { router } from '@inertiajs/vue3';
import {
    FormOutlined,
    DeleteOutlined,
    AppstoreAddOutlined,
} from "@ant-design/icons-vue";

const props = defineProps({
    recursos: Array,
});

const emitir = defineEmits(["editar", "mostrar-areas", "actualizar-tabla"]);

// Definir las columnas de la tabla de recursos
const columnas = [
    { title: "Código", dataIndex: "codigo", key: "codigo" },
    {
        title: "Nombre",
        dataIndex: "nombre",
        key: "nombre",
        sorter: (a, b) => a.nombre.localeCompare(b.nombre),
    },
    {
        title: "Área",
        dataIndex: ["area", "nombre"],
        key: "area",
    },
    {
        title: "Tipo",
        dataIndex: "tipo",
        key: "tipo",
    },
    {
        title: "Estado",
        dataIndex: "estado",
        key: "estado",
    },
    { title: "Acciones", key: "acciones" },
];

// Emitir eventos para editar, eliminar y ver áreas
function editar(recurso) {
    emitir("editar", recurso);
}

const confirmarEliminacion = (recurso) => {
    Modal.confirm({
        title: '¿Estás seguro de eliminar este recurso?',
        content: `${recurso.nombre}`,
        okText: 'Confirmar',
        cancelText: 'Cancelar',
        onOk() {
            router.delete(route('recursos.destroy', recurso), {
                preserveScroll: true,
                onSuccess: () => {
                    message.success('Recurso eliminado exitosamente');
                    emitir('actualizar-tabla');
                },
                onError: (error) => {
                    console.error('Error al eliminar el recurso:', error);
                    message.error('Error al eliminar el recurso');
                }
            });
        },
    });
};

function mostrarAreas(recurso) {
    emitir("mostrar-areas", recurso);
}
</script>
