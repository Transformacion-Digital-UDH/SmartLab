<template>
    <Table :columns="columnas" :dataSource="reservas" rowKey="id" :pagination="false" :scroll="{ x: 800 }">
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'estado'">
                <Tag :color="estadoColor(record.estado)" :bordered="false">
                    {{ record.estado }}
                </Tag>
            </template>

            <template v-if="column.key === 'acciones'">
                <FormOutlined @click="editar(record)" class="text-blue-600" />
                <DeleteOutlined
                    @click="confirmarEliminacion(record)"
                    class="text-red-600 ml-2"
                />
            </template>

            <!-- Renderizado personalizado para la columna 'Fecha' -->
            <template v-if="column.key === 'fecha'">
                <div :style="getFechaStyle(record)">
                    <div class="font-medium">{{ dayjs(record.hora_inicio).format('YYYY-MM-DD') }}</div>
                    <div>{{ dayjs(record.hora_inicio).format('HH:mm') }} - {{ dayjs(record.hora_fin).format('HH:mm') }}</div>
                </div>
            </template>

            <!-- Renderizado personalizado para la columna 'Fecha de Creación' -->
            <template v-if="column.key === 'created_at'">
                <div>
                    <div>{{ dayjs(record.created_at).format('YYYY-MM-DD') }}</div>
                    <div>{{ dayjs(record.created_at).format('HH:mm') }}</div>
                </div>
            </template>
        </template>
    </Table>
</template>

<script setup>
import { Table, Modal, Tag, message } from "ant-design-vue";
import { router } from "@inertiajs/vue3";
import { FormOutlined, DeleteOutlined } from "@ant-design/icons-vue";
import dayjs from "dayjs"; // Importamos dayjs para formatear las fechas

const props = defineProps({
    reservas: Array,
});

const emitir = defineEmits(["editar", "actualizar-tabla"]);

// Mapeo de estados a colores
const estadoColor = (estado) => {
    const colores = {
        "Por aprobar": "warning",
        "Aprobada": "success",
        "No aprobada": "error",
        "Cancelada": "default",
    };
    return colores[estado] || "default";
};

// Función para aplicar estilos según la hora de inicio
const getFechaStyle = (record) => {
    const now = dayjs();
    const startTime = dayjs(record.hora_inicio);
    if (startTime.isBefore(now) || startTime.isSame(now)) {
        return { color: "orangered" };
    }
    if (startTime.diff(now, "hour", true) < 3) {
        return { color: "orange" };
    }
    return {};
};

// Definir las columnas de la tabla de reservas
const columnas = [
    {
        title: "Reserva",
        key: "fecha",
        dataIndex: "hora_inicio",
        customRender: ({ record }) => {
            return `${dayjs(record.hora_inicio).format("YYYY-MM-DD")} \n ${dayjs(record.hora_inicio).format("HH:mm")} - ${dayjs(record.hora_fin).format("HH:mm")}`;
        },
        width: 120,
        sorter: (a, b) => new Date(a.hora_inicio) - new Date(b.hora_inicio),
        defaultSortOrder: "ascend" // Orden por defecto: de los más prontos a los más lejanos
    },
    {
        title: "Reservable",
        key: "recurso_equipo",
        customRender: ({ record }) =>
            record.equipo?.nombre || record.recurso?.nombre || record.area?.nombre || "No especificado",
        sorter: (a, b) => {
            const reservableA = a.equipo?.nombre || a.recurso?.nombre || a.area?.nombre || "";
            const reservableB = b.equipo?.nombre || b.recurso?.nombre || b.area?.nombre || "";
            return reservableA.localeCompare(reservableB);
        },
    },
    {
        title: "Estado",
        dataIndex: "estado",
        key: "estado",
        sorter: (a, b) => a.estado.localeCompare(b.estado),
        width: 150
    },
    {
        title: "Solicitante",
        dataIndex: ["usuario", "nombres", "usuario", "apellidos"],
        key: "usuario",
        customRender: ({ record }) => `${record.usuario.nombres} ${record.usuario.apellidos}`,
    },
    {
        title: "Fecha registro",
        key: "created_at",
        dataIndex: "created_at",
        width: 150,
        sorter: (a, b) => new Date(a.created_at) - new Date(b.created_at),
    },
    { title: "Acciones", key: "acciones", fixed: "right", width: 90 },
];

// Emitir eventos para editar y eliminar
function editar(reserva) {
    emitir("editar", reserva);
}

const confirmarEliminacion = (reserva) => {
    Modal.confirm({
        title: "¿Estás seguro de eliminar esta reserva?",
        content: `Reserva de ${dayjs(reserva.hora_inicio).format("HH:mm")} a ${dayjs(reserva.hora_fin).format("HH:mm")}`,
        okText: "Confirmar",
        cancelText: "Cancelar",
        onOk() {
            router.delete(route("reservas.destroy", reserva), {
                preserveScroll: true,
                onSuccess: () => {
                    message.success("Reserva eliminada exitosamente");
                    emitir("actualizar-tabla");
                },
                onError: (error) => {
                    console.error("Error al eliminar la reserva:", error);
                    message.error("Error al eliminar la reserva");
                },
            });
        },
    });
};
</script>
