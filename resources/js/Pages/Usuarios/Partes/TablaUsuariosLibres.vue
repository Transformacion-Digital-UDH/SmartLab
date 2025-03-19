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
import {
    FormOutlined,
    DeleteOutlined,
    UserAddOutlined,
} from "@ant-design/icons-vue";

const props = defineProps({
    usuarios_libres: Array,
});

const emitir = defineEmits([
    "editar",
    "actualizar-tabla",
    "abrir-modal-crear",
    "abrir-modal-crear-cuenta",
]);

// Estado para el buscador
const valorBuscar = ref("");

// Filtrar usuarios libres según el valor ingresado
const usuariosFiltrados = computed(() => {
    if (!valorBuscar.value) return props.usuarios_libres;
    return props.usuarios_libres.filter((usuario) => {
        const nombreCompleto =
            `${usuario.nombres} ${usuario.apellidos}`.toLowerCase();
        const dni = usuario.dni ? usuario.dni.toLowerCase() : "";
        const searchValue = valorBuscar.value.toLowerCase();
        return (
            nombreCompleto.includes(searchValue) || dni.includes(searchValue)
        );
    });
});

// Función para asignar color al laboratorio
const laboratorioColor = (laboratorio) => {
    return laboratorio == "No registrado" ? "default" : "blue";
};

const columnas = [
    { title: "DNI", dataIndex: "dni", key: "dni", width: 100 },
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
    },

    { title: "Celular", dataIndex: "celular", key: "celular", width: 120 },
    { title: "Email", dataIndex: "email", key: "email" },
    {
        title: "Laboratorio registro",
        dataIndex: "laboratorio_registro",
        key: "laboratorio_registro",
        customRender: ({ record }) => {
            const laboratorio = record.laboratorio_registro || "No registrado";
            return h(
                Tag,
                { color: laboratorioColor(laboratorio), bordered: false },
                laboratorio
            );
        },
    },
    { title: "Acciones", key: "acciones", fixed: "right", width: 120 },
];

function editar(usuario) {
    emitir("editar", usuario);
}

function abrirModalCrearCuenta(usuario) {
    emitir("abrir-modal-crear-cuenta", usuario);
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
    <!-- Buscador y botón para agregar usuario -->
    <div
        class="flex flex-col-reverse justify-end mb-6 gap-y-4 sm:flex-row sm:justify-between sm:items-center gap-x-4"
    >
        <div class="w-full">
            <InputSearch
                v-model:value="valorBuscar"
                placeholder="Buscar usuario por nombre, apellidos o dni"
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
                <UserAddOutlined
                    @click="abrirModalCrearCuenta(record)"
                    class="ml-2 text-green-600"
                />
            </template>
        </template>
    </Table>
</template>
