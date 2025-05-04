<script setup>
import { defineProps } from "vue";
import { Table, Modal, message, Tag } from "ant-design-vue";
import { router } from "@inertiajs/vue3";
import {
    FormOutlined,
    DeleteOutlined,
    AppstoreAddOutlined,
    EditOutlined,
} from "@ant-design/icons-vue";

const props = defineProps({
    asistencias: Array,
});

const emitir = defineEmits(["editar", "actualizar-tabla"]);

// Definir las columnas de la tabla de laboratorios
const columnas = [
    {
        title: "Nombres",
        dataIndex: "nombres",
        key: "nombres",
        sorter: (a, b) => a.nombres.localeCompare(b.nombre),
        width: 320,
    },
    {
        title: "DNI",
        dataIndex: "dni",
        key: "dni",
        width: 120,
    },
    {
        title: "Estado",
        dataIndex: "tipo",
        key: "tipo",
        width: 130,
    },
    {
        title: "Rol",
        dataIndex: "rol",
        key: "rol",
        sorter: (a, b) => a.rol.localeCompare(b.nombre),
        width: 110,
    },
    {
        title: "Entrada",
        dataIndex: "hora_entrada",
        key: "entrada",
        width: 120,
        sorter: (a, b) => a.hora_entrada.localeCompare(b.hora_entrada),
    },
    {
        title: "Salida",
        dataIndex: "hora_salida",
        key: "salida",
        width: 120,
        sorter: (a, b) => a.hora_salida.localeCompare(b.hora_salida),
    },
    { title: "Acciones", key: "acciones", fixed: "right", width: 100 },
];

// Emitir eventos para editar, ver áreas y eliminar
function editar(asistencia) {
    emitir("editar", asistencia);
}

const confirmarEliminacion = (asistencia) => {
    Modal.confirm({
        title: "¿Estás seguro de eliminar la asistencia?",
        content: ``,
        okText: "Eliminar",
        cancelText: "Cancelar",
        onOk() {
            router.delete(route("asistencia.eliminar", asistencia.id), {
                preserveScroll: true,
                onSuccess: () => {
                    message.success("Recurso eliminado exitosamente");
                    emitir("actualizar-tabla");
                },
                onError: (error) => {
                    console.error("Error al eliminar el recurso:", error);
                    message.error("Error al eliminar el recurso");
                },
            });
        },
    });
};
</script>
<template>
    <Table
        :columns="columnas"
        :data-source="asistencias"
        :pagination="false"
        :scroll="{ y: '70vh' }"
    >
        <!-- Head -->
        <template #headerCell="{ column }">
            <template v-if="column.key === 'nombres'">
                <span class="flex items-center gap-2"> Nombres </span>
            </template>
            <template v-if="column.key === 'dni'">
                <span> DNI </span>
            </template>
            <template v-if="column.key === 'tipo'">
                <span> Estado </span>
            </template>
            <template v-if="column.key === 'rol'">
                <span> Rol </span>
            </template>
            <template v-if="column.key === 'entrada'">
                <span> Entrada </span>
            </template>
            <template v-if="column.key === 'salida'">
                <span> Salida </span>
            </template>
            <template v-if="column.key === 'acciones'"> Acciones </template>
        </template>
        <!-- Body -->
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'nombres'">
                <a>
                    {{ record.nombres }}
                </a>
            </template>
            <template v-if="column.key === 'tipo'">
                <Tag v-if="record.hora_salida" :bordered="false" color="green">
                    Completado
                </Tag>
                <Tag v-else :bordered="false" color="orange">
                    Por completar
                </Tag>
            </template>
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
