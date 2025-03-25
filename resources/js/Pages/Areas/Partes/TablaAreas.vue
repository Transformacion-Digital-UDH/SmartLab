<template>
    <div>
        <!-- Tabla de Áreas -->
        <Table
            :columns="columnas"
            :dataSource="areas"
            rowKey="id"
            :pagination="false"
            :scroll="{ x: 800 }"
        >
            <template #bodyCell="{ column, record }">
                <!-- Renderizar Foto -->
                <template v-if="column.key === 'foto'">
                    <img
                        v-if="record.foto"
                        :src="`/storage/${record.foto}`"
                        alt="Foto del área"
                        class="w-16 h-16 object-cover rounded"
                    />
                    <img
                        v-else
                        :src="`/img/default-placeholder.webp`"
                        class="w-16 h-16 object-cover rounded"
                    />
                </template>

                <!-- Renderizar Tags para Tipo -->
                <template v-if="column.key === 'tipo'">
                    <Tag :color="colorTipo(record.tipo)" :bordered="false">
                        {{ record.tipo }}
                    </Tag>
                </template>

                <!-- Renderizar Nombre del Laboratorio -->
                <template v-if="column.key === 'laboratorio'">
                    {{ record.laboratorio?.nombre || 'Sin laboratorio' }}
                </template>

                <!-- Acciones -->
                <template v-if="column.key === 'acciones'">
                    <FormOutlined
                        @click="editar(record)"
                        class="text-blue-600"
                    />
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
import { ref, h } from "vue";
import { Table, Modal, message, Tag } from "ant-design-vue";
import { router } from "@inertiajs/vue3";
import { FormOutlined, DeleteOutlined } from "@ant-design/icons-vue";

const props = defineProps({
    areas: Array,
});

const emitir = defineEmits(["editar", "actualizar-tabla"]);

// Definir las columnas de la tabla
const columnas = [
    {
        title: "Foto",
        key: "foto",
        width: 120
    },
    {
        title: "Nombre",
        dataIndex: "nombre",
        key: "nombre",
        sorter: (a, b) => a.nombre.localeCompare(b.nombre),
    },
    {
        title: "Tipo",
        dataIndex: "tipo",
        key: "tipo",
        width: 150,
        sorter: (a, b) => a.tipo.localeCompare(b.tipo),
    },
    {
        title: "Laboratorio",
        dataIndex: ["laboratorio", "nombre"],
        key: "laboratorio",
        sorter: (a, b) => (a.laboratorio?.nombre || '').localeCompare(b.laboratorio?.nombre || ''),
    },
    {
        title: "Acciones",
        key: "acciones",
        fixed: "right",
        width: 100
    },
];

// Función para determinar el color del Tag según el tipo
const colorTipo = (tipo) => {
    const colores = {
        Reservable: "green",
        "No reservable": "orange",
    };
    return colores[tipo] || "default";
};

// Funciones para manejar eventos
function editar(area) {
    emitir("editar", area);
}

const confirmarEliminacion = (area) => {
    Modal.confirm({
        title: "Eliminar área",
        content: () =>
            h("div", [
                "Estas a punto de eliminar",
                " el área ",
                h("br"),
                h("b", area.nombre),
                ".",
            ]),
        okText: "Confirmar",
        cancelText: "Cancelar",
        onOk() {
            router.delete(route("areas.destroy", area), {
                preserveScroll: true,
                onSuccess: () => {
                    message.success("Área eliminada exitosamente");
                    emitir("actualizar-tabla");
                },
                onError: (error) => {
                    message.error("Error al eliminar el área");
                },
            });
        },
    });
};
</script>
