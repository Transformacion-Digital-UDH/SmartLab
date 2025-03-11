<script setup>
import { Table, Modal, message, Tag } from "ant-design-vue";
import { router } from "@inertiajs/vue3";
import { h } from "vue";
import {
    FormOutlined,
    DeleteOutlined,
    AppstoreAddOutlined,
} from "@ant-design/icons-vue";

const props = defineProps({
    usuarios: Array,
});

const emitir = defineEmits([
    "editar",
    "mostrar-asistencias",
    "actualizar-tabla",
]);

const getRoleColor = (role) => {
    switch (role) {
        case "Admin":
            return "red";
        case "Miembro":
            return "blue";
        case "Invitado":
            return "orange";
        case "Libre":
            return "green";
        default:
            return "gray";
    }
};

const columnas = [
    { title: "Código", dataIndex: "codigo", key: "codigo", width: 120 },
    {
        title: "Nombre Completo",
        dataIndex: "nombre_completo",
        key: "nombre_completo",
        sorter: (a, b) => a.nombre_completo.localeCompare(b.nombre_completo),
        customRender: ({ record }) => `${record.nombres} ${record.apellidos}`,
    },
    { title: "DNI", dataIndex: "dni", key: "dni", width: 100 },
    { title: "Email", dataIndex: "email", key: "email" },
    { title: "Celular", dataIndex: "celular", key: "celular", width: 120 },
    {
        title: "Rol",
        dataIndex: "rol",
        key: "rol",
        width: 120,
        customRender: ({ text }) => h(Tag, { color: getRoleColor(text) }, text),
    },
    { title: "Acciones", key: "acciones", fixed: "right", width: 90 },
];

function editar(usuario) {
    emitir("editar", usuario);
}

const confirmarEliminacion = (usuario) => {
    Modal.confirm({
        title: "¿Estás seguro de eliminar este usuario?",
        content: `${usuario.nombres} ${usuario.apellidos}`,
        okText: "Confirmar",
        cancelText: "Cancelar",
        onOk() {
            router.delete(route("usuarios.destroy", usuario), {
                preserveScroll: true,
                onSuccess: () => {
                    message.success("Usuario eliminado exitosamente");
                    emitir("actualizar-tabla");
                },
                onError: (error) => {
                    console.error("Error al eliminar el usuario:", error);
                    message.error("Error al eliminar el usuario");
                },
            });
        },
    });
};

function mostrarAsistencias(usuario) {
    emitir("mostrar-asistencias", usuario);
}
</script>

<template>
    <Table
        :columns="columnas"
        :dataSource="usuarios"
        rowKey="id"
        :pagination="false"
        :scroll="{ x: 800 }"
    >
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'acciones'">
                <FormOutlined @click="editar(record)" class="text-blue-600" />
                <DeleteOutlined
                    @click="confirmarEliminacion(record)"
                    class="ml-2 text-red-600"
                />
                <!-- <AppstoreAddOutlined
          @click="mostrarAsistencias(record)"
          class="ml-2 text-green-600"
        /> -->
            </template>
        </template>
    </Table>
</template>
