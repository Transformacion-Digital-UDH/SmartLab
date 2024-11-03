<template>
    <Table :columns="columnas" :dataSource="laboratorios" rowKey="id">
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
import { Table } from "ant-design-vue";
import {
    FormOutlined,
    DeleteOutlined,
    AppstoreAddOutlined,
} from "@ant-design/icons-vue";

const props = defineProps({
    laboratorios: Array,
});

// imprimir todos los laboratorios
console.log("Laboratorios:", props.laboratorios);

const emitir = defineEmits(["editar", "eliminar", "mostrar-areas"]);

// Definir las columnas de la tabla de laboratorios
const columnas = [
    {
        title: "Nombre",
        dataIndex: "nombre",
        key: "nombre",
        sorter: (a, b) => a.nombre.localeCompare(b.nombre),
    },
    { title: "Código", dataIndex: "codigo", key: "codigo" },
    {
        title: "Responsable",
        dataIndex: ["responsable", "nombres"],
        key: "nombres",
    },
    {
        title: "Aforo",
        dataIndex: "aforo",
        key: "aforo",
        sorter: (a, b) => a.aforo - b.aforo,
    },
    { title: "Email", dataIndex: "email", key: "email" },
    { title: "Acciones", key: "acciones" },
];

// Emitir eventos para editar, eliminar y ver áreas
function editar(laboratorio) {
    emitir("editar", laboratorio);
}

function confirmarEliminacion(laboratorio) {
    emitir("eliminar", laboratorio);
}

function mostrarAreas(laboratorio) {
    emitir("mostrar-areas", laboratorio);
}
</script>
