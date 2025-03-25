<template>
    <div>
        <!-- Buscar y Agregar Área -->
        <div
            class="flex flex-col-reverse justify-end gap-y-4 mb-6 sm:flex-row sm:justify-between sm:items-center gap-x-4"
        >
            <InputSearch
                v-model:value="valorBuscar"
                placeholder="Buscar área por nombre"
                class="w-full"
                size="large"
            />

            <Button
                type="primary"
                @click="abrirModalCrear"
                size="large"
                class="font-medium"
            >
                Agregar área
            </Button>
        </div>

        <!-- Tabla de Áreas -->
        <Table
            :columns="columnas"
            :dataSource="areasFiltradas"
            rowKey="id"
            :pagination="false"
            :scroll="{ x: 800 }"
        >
            <template #bodyCell="{ column, record }">
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
import { ref, h, computed } from "vue";
import { Table, Modal, message, InputSearch, Button, Tag } from "ant-design-vue";
import { router } from "@inertiajs/vue3";
import { FormOutlined, DeleteOutlined } from "@ant-design/icons-vue";

const props = defineProps({
    areas: Array,
});

const emitir = defineEmits(["editar", "actualizar-tabla", "abrir-crear"]);

// Estado para el buscador
const valorBuscar = ref("");

// Filtrar áreas por nombre
const areasFiltradas = computed(() =>
    !valorBuscar.value
        ? props.areas
        : props.areas.filter((area) =>
              area.nombre.toLowerCase().includes(valorBuscar.value.toLowerCase())
          )
);

// Definir las columnas de la tabla
const columnas = [
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
    { title: "Acciones", key: "acciones", fixed: "right", width: 100 },
];

// Función para determinar el color del Tag según el tipo
const colorTipo = (tipo) => {
    const colores = {
        Docente: "green",
        Estudiante: "blue",
        Administrativo: "orange",
    };
    return colores[tipo] || "default";
};

function abrirModalCrear() {
    emitir("abrir-crear");
}

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
