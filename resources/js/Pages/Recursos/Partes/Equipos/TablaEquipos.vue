<template>
    <div>
        <!-- Buscar y Agregar equipo -->
        <div
            class="flex flex-col-reverse justify-end gap-y-4 mb-6 sm:flex-row sm:justify-between sm:items-center gap-x-4"
        >
            <InputSearch
                v-model:value="valorBuscar"
                placeholder="Buscar equipo por nombre o código"
                class="w-full"
                size="large"
            />

            <Button
                type="primary"
                @click="abrirModalCrear"
                size="large"
                class="font-medium"
            >
                Agregar equipo
            </Button>
        </div>

        <!-- Tabla de equipos -->
        <Table
            :columns="columnas"
            :dataSource="equiposFiltrados"
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

                <!-- Renderizar Tags para Estado -->
                <template v-if="column.key === 'estado'">
                    <Tag :color="colorEstado(record.estado)" :bordered="false">
                        {{ record.estado }}
                    </Tag>
                </template>

                <!-- Renderizar Primera Foto -->
                <template v-if="column.key === 'foto'">
                    <img
                        v-if="record.fotos && record.fotos.length > 0"
                        :src="`/storage/${record.fotos[0].ruta}`"
                        alt="Foto del equipo del laboratorio"
                        class="w-16 h-16 object-cover rounded"
                    />
                    <img v-else
                        :src="`/img/default-placeholder.webp`"
                        class="w-16 h-16 object-cover rounded"
                    />
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
import {
    Table,
    Modal,
    message,
    InputSearch,
    Button,
    Tag,
} from "ant-design-vue";
import { router } from "@inertiajs/vue3";
import { FormOutlined, DeleteOutlined } from "@ant-design/icons-vue";

const props = defineProps({
    equipos: Array,
});

const emitir = defineEmits(["editar", "actualizar-tabla", "abrir-crear"]);

// Estado para el buscador
const valorBuscar = ref("");

// Equipos filtrados por nombre o código
const equiposFiltrados = computed(() =>
    !valorBuscar.value
        ? props.equipos
        : props.equipos.filter(
              (equipo) =>
                  equipo.nombre
                      .toLowerCase()
                      .includes(valorBuscar.value.toLowerCase()) ||
                  equipo.codigo
                      ?.toLowerCase()
                      .includes(valorBuscar.value.toLowerCase())
          )
);

// Definir las columnas de la tabla
const columnas = [
    {
        title: "Código",
        dataIndex: "codigo",
        key: "codigo",
        width: 100,
        sorter: (a, b) => a.codigo.localeCompare(b.codigo),
    },
    { title: "Foto", key: "foto", width: 120 },
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
        sorter: (a, b) => a.area.nombre.localeCompare(b.area.nombre),
    },
    {
        title: "Tipo",
        dataIndex: "tipo",
        key: "tipo",
        width: 150,
        sorter: (a, b) => a.tipo.localeCompare(b.tipo),
    },
    {
        title: "Estado",
        dataIndex: "estado",
        key: "estado",
        width: 150,
        sorter: (a, b) => a.estado.localeCompare(b.estado),
    },
    { title: "Acciones", key: "acciones", fixed: "right", width: 100 },
];

// Función para determinar el color del Tag según el tipo
const colorTipo = (estado) => {
    const colores = {
        Reservable: "green",
        "No reservable": "orange",
        Suministro: "blue",
    };
    return colores[estado] || "default";
};

// Función para determinar el color del Tag según el estado
const colorEstado = (estado) => {
    const colores = {
        Activo: "green",
        Inactivo: "error",
        Reservado: "blue",
        Prestado: "orange",
    };
    return colores[estado] || "default";
};

function abrirModalCrear() {
    emitir("abrir-crear");
}

// Funciones para manejar eventos
function editar(equipo) {
    emitir("editar", equipo);
}

const confirmarEliminacion = (equipo) => {
    Modal.confirm({
        title: "¿Estás seguro de eliminar este equipo?",
        content: () =>
            h("div", [
                "Estas a punto de eliminar",
                " al equipo ",
                h("br"),
                h("b", equipo.codigo + " - " + equipo.nombre),
                ".",
            ]),
        okText: "Confirmar",
        cancelText: "Cancelar",
        onOk() {
            router.delete(route("equipos.destroy", equipo), {
                preserveScroll: true,
                onSuccess: () => {
                    message.success("Equipo eliminado exitosamente");
                    emitir("actualizar-tabla");
                },
                onError: () => {
                    message.error("Error al eliminar el equipo");
                },
            });
        },
    });
};
</script>
