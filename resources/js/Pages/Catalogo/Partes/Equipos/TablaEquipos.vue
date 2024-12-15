<template>
    <div>
        <!-- Buscar y Agregar recurso -->
        <div
            class="flex flex-col-reverse justify-end gap-y-4 mb-6 sm:flex-row sm:justify-between sm:items-center gap-x-4"
        >
            <InputSearch
                v-model:value="valorBuscar"
                placeholder="Buscar recurso por nombre o código"
                class="w-full"
                size="large"
            />

            <Button
                type="primary"
                @click="abrirModalCrear"
                size="large"
                class="font-medium"
            >
                Agregar recurso
            </Button>
        </div>

        <!-- Tabla de recursos -->
        <Table
            :columns="columnas"
            :dataSource="recursosFiltrados"
            rowKey="id"
            :pagination="false"
        >
            <template #bodyCell="{ column, record }">
                <!-- Renderizar Tags para Tipo -->
                <template v-if="column.key === 'tipo'">
                    <Tag :color="colorTipo(record.tipo)" :bordered="false">
                        {{ record.tipo }}
                    </Tag>
                </template>

                <!-- Renderizar Tags para Estado -->
                <template v-if="column.key === 'estado'">
                    <Tag :color="colorEstado(record.estado)" :bordered="false">
                        {{ record.estado }}
                    </Tag>
                </template>

                <!-- Acciones -->
                <template v-if="column.key === 'acciones'">
                    <FormOutlined @click="editar(record)" class="text-blue-600" />
                    <DeleteOutlined
                        @click="confirmarEliminacion(record)"
                        class="text-red-600 ml-2"
                    />
                </template>
            </template>
        </Table>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { defineEmits } from "vue";
import { Table, Modal, message, InputSearch, Button, Tag } from "ant-design-vue";
import { router } from "@inertiajs/vue3";
import { FormOutlined, DeleteOutlined } from "@ant-design/icons-vue";

const props = defineProps({
    recursos: Array,
});

const emitir = defineEmits(["editar", "actualizar-tabla", "abrir-crear"]);

// Estado para el buscador
const valorBuscar = ref("");

// Recursos filtrados por nombre o código
const recursosFiltrados = computed(() =>
    !valorBuscar.value
        ? props.recursos
        : props.recursos.filter((recurso) =>
            recurso.nombre.toLowerCase().includes(valorBuscar.value.toLowerCase()) ||
            recurso.codigo?.toLowerCase().includes(valorBuscar.value.toLowerCase())
        )
);

// Definir las columnas de la tabla
const columnas = [
    { title: "Código", dataIndex: "codigo", key: "codigo", width: 100 },
    {
        title: "Nombre",
        dataIndex: "nombre",
        key: "nombre",
        sorter: (a, b) => a.nombre.localeCompare(b.nombre),
    },
    {
        title: "Área",
        dataIndex: ["area", "nombre"],
        key: "area",
    },
    {
        title: "Tipo",
        dataIndex: "tipo",
        key: "tipo",
        width: 150,
        sorter: (a, b) => a.tipo.localeCompare(b.tipo)
    },
    {
        title: "Estado",
        dataIndex: "estado",
        key: "estado",
        width: 150,
        sorter: (a, b) => a.estado.localeCompare(b.estado)
    },
    { title: "Acciones", key: "acciones", fixed: "right", width: 100 },
];

// Función para determinar el color del Tag según el tipo
const colorTipo = (estado) => {
    const colores = {
        "Reservable": "green",
        "No reservable": "orange",
        "Suministro": "blue",
    };
    return colores[estado] || "default";
}

// Función para determinar el color del Tag según el estado
const colorEstado = (estado) => {
    const colores = {
        "Activo": "green",
        "Inactivo": "error",
        "Reservado": "blue",
        "Prestado": "orange",
    };
    return colores[estado] || "default";
}

// Funciones para manejar eventos
function editar(recurso) {
    emitir("editar", recurso);
}

const confirmarEliminacion = (recurso) => {
    Modal.confirm({
        title: "¿Estás seguro de eliminar este recurso?",
        content: `${recurso.nombre}`,
        okText: "Confirmar",
        cancelText: "Cancelar",
        onOk() {
            router.delete(route("recursos.destroy", recurso), {
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
}

function abrirModalCrear() {
    emitir("abrir-crear");
}
</script>
