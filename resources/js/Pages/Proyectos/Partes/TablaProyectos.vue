<template>
    <Table :columns="columnas" :dataSource="proyectos" rowKey="id" :pagination="false" :scroll="{ x: 800 }">
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'acciones'">
                <FormOutlined @click="editar(record)" class="text-blue-600" />
                <DeleteOutlined
                    @click="confirmarEliminacion(record)"
                    class="text-red-600 ml-2"
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
} from "@ant-design/icons-vue";

const props = defineProps({
    proyectos: Array,
});

const emitir = defineEmits(["editar", "actualizar-tabla"]);

// Definir las columnas de la tabla de proyectos
const columnas = [
    { title: "Nombre", dataIndex: "nombre", key: "nombre", sorter: (a, b) => a.nombre.localeCompare(b.nombre) },
    { title: "Descripción", dataIndex: "descripcion", key: "descripcion" },
    { title: "Responsable", dataIndex: ["responsable", "nombres"], key: "nombres" },
    { title: "Fecha Inicio", dataIndex: "fecha_inicio", key: "fecha_inicio", width: 150 },
    { title: "Fecha Fin", dataIndex: "fecha_fin", key: "fecha_fin", width: 150 },
    { title: "Acciones", key: "acciones", fixed: "right", width: 90 },
];

// Emitir eventos para editar y eliminar
function editar(proyecto) {
    emitir("editar", proyecto);
}

const confirmarEliminacion = (proyecto) => {
    Modal.confirm({
        title: '¿Estás seguro de eliminar este proyecto?',
        content: `${proyecto.nombre}`,
        okText: 'Confirmar',
        cancelText: 'Cancelar',
        onOk() {
            router.delete(route('proyectos.destroy', proyecto), {
                preserveScroll: true,
                onSuccess: () => {
                    message.success('Proyecto eliminado exitosamente');
                    emitir('actualizar-tabla');
                },
                onError: (error) => {
                    console.error('Error al eliminar el proyecto:', error);
                    message.error('Error al eliminar el proyecto');
                }
            });
        },
    });
};
</script>
