<template>
    <Table :columns="columnas" :dataSource="usuarios" rowKey="id" :pagination="false" :scroll="{ x: 800 }">
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'acciones'">
                <FormOutlined @click="editar(record)" class="text-blue-600" />
                <DeleteOutlined
                    @click="confirmarEliminacion(record)"
                    class="ml-2 text-red-600"
                />
            <!---------
                <AppstoreAddOutlined
                    @click="mostrarAsistencias(record)"
                    class="ml-2 text-green-600"
                />----------->
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
    usuarios: Array,
});

const emitir = defineEmits(["editar", "mostrar-asistencias", "actualizar-tabla"]);

const columnas = [
    { title: "Código", dataIndex: "codigo", key: "codigo" },
    {
        title: "Nombres",
        dataIndex: "nombres",
        key: "nombres",
        sorter: (a, b) => a.nombres.localeCompare(b.nombres),
    },
    {
        title: "Apellidos",
        dataIndex: "apellidos",
        key: "apellidos",
        sorter: (a, b) => a.apellidos.localeCompare(b.apellidos),
    },
    { title: "DNI", dataIndex: "dni", key: "dni", width: 100 },
    { title: "Email", dataIndex: "email", key: "email" },
    { title: "Rol", dataIndex: "rol", key: "rol", width: 100 },
    {
        title: "Activo",
        dataIndex: "is_active",
        key: "is_active",
        width: 80,
        customRender: ({ text }) => (text ? "Sí" : "No"),
    },
    { title: "Acciones", key: "acciones", fixed: "right", width: 90 },
];

function editar(usuario) {
    emitir("editar", usuario);
}

const confirmarEliminacion = (usuario) => {
    Modal.confirm({
        title: '¿Estás seguro de eliminar este usuario?',
        content: `${usuario.nombres} ${usuario.apellidos}`,
        okText: 'Confirmar',
        cancelText: 'Cancelar',
        onOk() {
            router.delete(route('usuarios.destroy', usuario), {
                preserveScroll: true,
                onSuccess: () => {
                    message.success('Usuario eliminado exitosamente');
                    emitir('actualizar-tabla');
                },
                onError: (error) => {
                    console.error('Error al eliminar el usuario:', error);
                    message.error('Error al eliminar el usuario');
                }
            });
        },
    });
};

function mostrarAsistencias(usuario) {
    emitir("mostrar-asistencias", usuario);
}
</script>
