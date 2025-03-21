<script setup>
import { ref, computed, h } from "vue";
import {
    Table,
    Modal,
    message,
    Tag,
    InputSearch,
    Button,
} from "ant-design-vue";
import { router } from "@inertiajs/vue3";
import { FormOutlined, DeleteOutlined } from "@ant-design/icons-vue";

const props = defineProps({
    usuarios_registrados: Array,
});

const emitir = defineEmits(["editar", "actualizar-tabla", "abrir-modal-crear"]);

// Estado para el buscador
const valorBuscar = ref("");

// Filtrar usuarios registrados según el valor ingresado
const usuariosFiltrados = computed(() => {
    if (!valorBuscar.value) return props.usuarios_registrados;
    return props.usuarios_registrados.filter((usuario) => {
        const nombreCompleto =
            `${usuario.nombres} ${usuario.apellidos}`.toLowerCase();
        const dni = usuario.dni ? usuario.dni.toLowerCase() : "";
        const codigo = usuario.codigo ? usuario.codigo.toLowerCase() : "";
        const searchValue = valorBuscar.value.toLowerCase();
        return (
            nombreCompleto.includes(searchValue) ||
            dni.includes(searchValue) ||
            codigo.includes(searchValue)
        );
    });
});

// Función para asignar colores al estado de la cuenta
const estadoCuentaColor = (estado) => {
    const colores = {
        "En revisión": "orange",
        Aprobada: "green",
        Desaprobada: "red",
        Suspendida: "volcano",
    };
    return colores[estado] || "default";
};

const columnas = [
    { title: "Código", dataIndex: "codigo", key: "codigo", width: 120 },
    {
        title: "Nombre",
        dataIndex: "nombre_completo",
        key: "nombre_completo",
        sorter: (a, b) => {
            const nombreA = `${a.nombres || ""} ${a.apellidos || ""}`.trim();
            const nombreB = `${b.nombres || ""} ${b.apellidos || ""}`.trim();
            return nombreA.localeCompare(nombreB);
        },
        customRender: ({ record }) => {
            const nombres = record.nombres || "";
            const apellidos = record.apellidos || "";
            return `${nombres} ${apellidos}`.trim();
        },
        width: 180
    },

    { title: "DNI", dataIndex: "dni", key: "dni", width: 100 },
    { title: "Email", dataIndex: "email", key: "email", width: 220 },
    { title: "Celular", dataIndex: "celular", key: "celular", width: 120 },
    {
        title: "Estado cuenta",
        dataIndex: "estado_cuenta",
        key: "estado_cuenta",
        width: 150,
        filters: [
            { text: "En revisión", value: "En revisión" },
            { text: "Aprobada", value: "Aprobada" },
            { text: "Desaprobada", value: "Desaprobada" },
            { text: "Suspendida", value: "Suspendida" },
        ],
        onFilter: (value, record) => record.estado_cuenta === value,
        customRender: ({ text }) =>
            h(Tag, { color: estadoCuentaColor(text), bordered: false }, text),
    },
    {
        title: "Tipo",
        key: "tipo",
        width: 150,
        filters: [
            { text: "Miembro UDH", value: "Miembro UDH" },
            { text: "Externo UDH", value: "Externo UDH" },
        ],
        onFilter: (value, record) => {
            const tipo = record.email.endsWith("udh.edu.pe")
                ? "Miembro UDH"
                : "Externo UDH";
            return tipo === value;
        },
        customRender: ({ record }) => {
            const esMiembro = record.email.endsWith("udh.edu.pe");
            return h(
                Tag,
                { color: esMiembro ? "blue" : "purple", bordered: false },
                esMiembro ? "Miembro UDH" : "Externo UDH"
            );
        },
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
</script>

<template>
    <!-- Buscador y botón agregados encima de la tabla -->
    <div
        class="flex flex-col-reverse justify-end mb-6 gap-y-4 sm:flex-row sm:justify-between sm:items-center gap-x-4"
    >
        <div class="w-full">
            <InputSearch
                v-model:value="valorBuscar"
                placeholder="Buscar usuario por nombre, apellidos, dni o código"
                class="w-full"
                size="large"
                :allowClear="true"
            />
        </div>

        <Button
            type="primary"
            @click="$emit('abrir-modal-crear')"
            size="large"
            class="font-medium"
        >
            Agregar usuario
        </Button>
    </div>

    <Table
        :columns="columnas"
        :dataSource="usuariosFiltrados"
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
            </template>
        </template>
    </Table>
</template>
