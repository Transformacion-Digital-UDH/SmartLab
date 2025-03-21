<template>
    <Table
        :columns="columnas"
        :dataSource="usuarios_solicitud"
        rowKey="id"
        :pagination="false"
        :scroll="{ x: 800 }"
    >
        <template #bodyCell="{ column, record }">
            <!-- Renderizado para columna Fecha Solicitud -->
            <template v-if="column.key === 'fecha_solicitud'">
                <div>
                    <div>{{ dayjs(record.created_at).format("YYYY-MM-DD") }}</div>
                    <div>{{ dayjs(record.created_at).format("HH:mm") }}</div>
                </div>
            </template>

            <!-- Renderizado para columna Nombre (nombres + apellidos) -->
            <template v-if="column.key === 'nombre'">
                {{ record.nombres }} {{ record.apellidos }}
            </template>

            <!-- Renderizado para columna Acciones -->
            <template v-if="column.key === 'acciones'">
                <div class="flex items-center space-x-2">
                    <Button
                        type="default"
                        size="middle"
                        @click="editar(record)"
                        class="flex justify-center items-center"
                    >
                        <template #icon>
                            <EyeOutlined />
                        </template>
                    </Button>
                    <Popconfirm
                        title="¿Estás seguro?"
                        okText="Aprobar"
                        cancelText="Desaprobar"
                        @confirm="aprobar(record)"
                        @cancel="desaprobar(record)"
                    >
                        <Button
                            type="primary"
                            class="font-medium"
                            :loading="loadingStates[record.id]"
                        >
                            Aprobar
                        </Button>
                    </Popconfirm>
                </div>
            </template>
        </template>
    </Table>
</template>

<script setup>
import { reactive } from "vue";
import { Table, Button, Popconfirm, message } from "ant-design-vue";
import { router } from "@inertiajs/vue3";
import { EyeOutlined } from "@ant-design/icons-vue";
import dayjs from "dayjs";

const props = defineProps({
    usuarios_solicitud: Array,
});

const emitir = defineEmits(["editar", "actualizar-tabla"]);
const loadingStates = reactive({});

const columnas = [
    {
        title: "DNI",
        key: "dni",
        dataIndex: "dni",
        width: 120,
        sorter: (a, b) => a.dni.localeCompare(b.dni),
    },
    {
        title: "Nombre",
        key: "nombre",
        sorter: (a, b) =>
            `${a.nombres} ${a.apellidos}`.localeCompare(`${b.nombres} ${b.apellidos}`),
    },
    {
        title: "Email",
        key: "email",
        dataIndex: "email",
    },
    {
        title: "Celular",
        key: "celular",
        dataIndex: "celular",
        width: 130,
    },
    {
        title: "Fecha solicitud",
        key: "fecha_solicitud",
        dataIndex: "created_at",
        width: 150,
        sorter: (a, b) => new Date(a.created_at) - new Date(b.created_at),
    },
    {
        key: "acciones",
        fixed: "right",
        width: 180,
    },
];

function editar(usuario) {
    emitir("editar", usuario);
}

function aprobar(usuario) {
    loadingStates[usuario.id] = true;
    // Actualizamos el estado_cuenta a "Aprobada"
    usuario.estado_cuenta = "Aprobada";
    router.post(
        route("usuarios.update", usuario.id),
        { ...usuario, _method: "PUT" },
        {
            preserveScroll: true,
            onSuccess: () => {
                message.success("Usuario aprobado exitosamente");
                emitir("actualizar-tabla", { ...usuario, estado_cuenta: "Aprobada" });
                loadingStates[usuario.id] = false;
            },
            onError: (error) => {
                console.error("Error al aprobar el usuario:", error);
                message.error("Error al aprobar el usuario");
                loadingStates[usuario.id] = false;
            },
        }
    );
}

function desaprobar(usuario) {
    loadingStates[usuario.id] = true;
    // Actualizamos el estado_cuenta a "Desaprobada"
    usuario.estado_cuenta = "Desaprobada";
    router.post(
        route("usuarios.update", usuario.id),
        { ...usuario, _method: "PUT" },
        {
            preserveScroll: true,
            onSuccess: () => {
                message.success("Usuario desaprobado exitosamente");
                emitir("actualizar-tabla", { ...usuario, estado_cuenta: "Desaprobada" });
                loadingStates[usuario.id] = false;
            },
            onError: (error) => {
                console.error("Error al desaprobar el usuario:", error);
                message.error("Error al desaprobar el usuario");
                loadingStates[usuario.id] = false;
            },
        }
    );
}

</script>
