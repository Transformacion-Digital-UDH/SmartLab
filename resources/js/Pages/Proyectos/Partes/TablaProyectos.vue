<template>
    <Table :columns="columnas" :dataSource="proyectos" rowKey="id" size="larg" :pagination="false" :scroll="{ x: 800 }">
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'estado'">
                <Tag :color="estadoColor(record.estado)" :bordered="false">
                    {{ record.estado }}
                </Tag>
            </template>
            <template v-else-if="column.key === 'acciones'">
                <FormOutlined @click="editar(record)" class="text-blue-600" />
                <DeleteOutlined
                    @click="confirmarEliminacion(record)"
                    class="text-red-600 ml-2"
                />
                <TeamOutlined
                    @click="mostrarParticipantes(record)"
                    class="text-green-600 ml-2"
                />

            </template>
        </template>
    </Table>
</template>

<script setup>
import { Table, Modal, message, Tag } from "ant-design-vue";
import { router } from '@inertiajs/vue3';
import {
    FormOutlined,
    DeleteOutlined,
    TeamOutlined,
} from "@ant-design/icons-vue";

const props = defineProps({
    proyectos: Array,
});

const emitir = defineEmits(["editar", "mostrar-participantes", "actualizar-tabla"]);

// Mapeo de estados a colores
const estadoColor = (estado) => {
    const colores = {
        "Sin iniciar": "default",
        "En proceso": "processing",
        "Completado": "success",
        "Cancelado": "error",
    };
    return colores[estado] || "default";
};

// Definir las columnas de la tabla de proyectos
const columnas = [
    { title: "Nombre", dataIndex: "nombre", key: "nombre", sorter: (a, b) => a.nombre.localeCompare(b.nombre) },
    { title: "Responsable", dataIndex: ["responsable", "nombres"], key: "nombres" },
    {
        title: "Fecha inicio",
        dataIndex: "fecha_inicio",
        key: "fecha_inicio",
        width: 150,
        sorter: (a, b) => new Date(a.fecha_inicio) - new Date(b.fecha_inicio),
    },
    {
        title: "Fecha fin",
        dataIndex: "fecha_fin",
        key: "fecha_fin",
        width: 150,
        sorter: (a, b) => new Date(a.fecha_fin) - new Date(b.fecha_fin),
    },
    { title: "Estado", dataIndex: "estado", key: "estado", sorter: (a, b) => a.estado.localeCompare(b.estado), width: 150 },
    { title: "Acciones", key: "acciones", fixed: "right", width: 90 },
];


// Emitir eventos para editar, ver participantes y eliminar
function editar(proyecto) {
    emitir("editar", proyecto);
}

function mostrarParticipantes(laboratorio) {
    emitir("mostrar-participantes", laboratorio);
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
