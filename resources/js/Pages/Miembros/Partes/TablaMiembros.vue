<template>
    <Table :columns="columnas" :dataSource="miembros" rowKey="id" :pagination="false" :scroll="{ x: 800 }">
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'acciones'">
                <FormOutlined @click="editar(record)" class="text-blue-600" />
                <DeleteOutlined
                    @click="confirmarEliminacion(record)"
                    class="ml-2 text-red-600"
                />
            </template>
            <template v-if="column.key === 'nombre'">
                {{ record.nombres }} {{ record.apellidos }}
            </template>
        </template>
    </Table>
</template>

<script setup>
import { Table, Modal, message } from "ant-design-vue";
import { router } from '@inertiajs/vue3';
import {
    FormOutlined,
    DeleteOutlined
} from "@ant-design/icons-vue";

const props = defineProps({
    miembros: Array,
});

const emitir = defineEmits(["editar", "actualizar-tabla"]);

const columnas = [
    { title: "Código", dataIndex: "codigo", key: "codigo" },
    {
        title: "Nombre",
        key: "nombre",
        sorter: (a, b) => (a.nombres + " " + a.apellidos).localeCompare(b.nombres + " " + b.apellidos),
    },
    { title: "DNI", dataIndex: "dni", key: "dni", width: 100 },
    { title: "Email", dataIndex: "email", key: "email" },
    { title: "Celular", dataIndex: "celular", key: "celular" },
    { title: "Acciones", key: "acciones", fixed: "right", width: 90 },
];

function editar(miembro) {
    emitir("editar", miembro);
}

const confirmarEliminacion = (miembro) => {
    Modal.confirm({
        title: '¿Estás seguro de eliminar este miembro?',
        content: `${miembro.nombres} ${miembro.apellidos}`,
        okText: 'Confirmar',
        cancelText: 'Cancelar',
        onOk() {
            router.delete(route('miembros.destroy', miembro), {
                preserveScroll: true,
                onSuccess: () => {
                    message.success('Miembro eliminado exitosamente');
                    emitir('actualizar-tabla');
                },
                onError: (error) => {
                    console.error('Error al eliminar el miembro:', error);
                    message.error('Error al eliminar el miembro');
                }
            });
        },
    });
};
</script>
