<template>
    <Table
        :columns="columnas"
        :dataSource="reservas"
        rowKey="id"
        :pagination="false"
        :scroll="{ x: 800 }"
    >
        <template #bodyCell="{ column, record }">
            <!-- Renderizado personalizado para la columna 'Reserva' -->
            <template v-if="column.key === 'fecha'">
                <div :style="getReservaStyle(record)">
                    <div class="font-medium">
                        {{ dayjs(record.hora_inicio).format("YYYY-MM-DD") }}
                    </div>
                    <div>
                        {{ dayjs(record.hora_inicio).format("HH:mm") }} -
                        {{ dayjs(record.hora_fin).format("HH:mm") }}
                    </div>
                </div>
            </template>

            <!-- Renderizado personalizado para la columna 'Solicitante' -->
            <template v-if="column.key === 'usuario'">
                {{ record.usuario.nombres }} {{ record.usuario.apellidos }}
            </template>

            <!-- Renderizado personalizado para la columna 'Fecha registro' -->
            <template v-if="column.key === 'created_at'">
                <div>
                    <div>
                        {{ dayjs(record.created_at).format("YYYY-MM-DD") }}
                    </div>
                    <div>{{ dayjs(record.created_at).format("HH:mm") }}</div>
                </div>
            </template>

            <!-- Renderizado personalizado para la columna 'Acciones' -->
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
import dayjs from "dayjs";
import { EyeOutlined } from "@ant-design/icons-vue";

const props = defineProps({
    reservas: Array,
});

const emitir = defineEmits(["editar", "actualizar-tabla"]);
const loadingStates = reactive({});

const columnas = [
    {
        title: "Reserva",
        key: "fecha",
        dataIndex: "hora_inicio",
        customRender: ({ record }) => {
            // Este customRender es opcional ya que usamos el slot bodyCell
            return `${dayjs(record.hora_inicio).format("YYYY-MM-DD")}
        ${dayjs(record.hora_inicio).format("HH:mm")} - ${dayjs(
                record.hora_fin
            ).format("HH:mm")}`;
        },
        width: 150,
        sorter: (a, b) => new Date(a.hora_inicio) - new Date(b.hora_inicio),
    },
    {
        title: "Reservable",
        key: "recurso_equipo",
        customRender: ({ record }) =>
            record.equipo?.nombre ||
            record.recurso?.nombre ||
            record.area?.nombre ||
            "No especificado",
        sorter: (a, b) => {
            const nombreA =
                a.equipo?.nombre || a.recurso?.nombre || a.area?.nombre || "";
            const nombreB =
                b.equipo?.nombre || b.recurso?.nombre || b.area?.nombre || "";
            return nombreA.localeCompare(nombreB);
        },
    },
    {
        title: "Solicitante",
        key: "usuario",
        customRender: ({ record }) =>
            `${record.usuario.nombres} ${record.usuario.apellidos}`,
    },
    {
        title: "Fecha registro",
        key: "created_at",
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

// Función para obtener el estilo condicional de la columna "Reserva"
function getReservaStyle(record) {
    const startTime = dayjs(record.hora_inicio);
    const now = dayjs();
    if (startTime.isBefore(now) || startTime.isSame(now)) {
        // Reserva en el pasado o ahora: naranja rojizo
        return { color: "orangered" };
    } else if (startTime.diff(now, "hour", true) < 3) {
        // Reserva a menos de 3 horas de distancia: naranja claro
        return { color: "orange" };
    }
    return {};
}

function editar(reserva) {
    emitir("editar", reserva);
}

function aprobar(reserva) {
    loadingStates[reserva.id] = true;
    router.patch(
        route("reservas.aprobar", reserva.id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                message.success("Reserva aprobada exitosamente");
                emitir("actualizar-tabla", { ...reserva, estado: "Aprobada" });
                loadingStates[reserva.id] = false;
            },
            onError: (error) => {
                console.error("Error al aprobar la reserva:", error);
                message.error("Error al aprobar la reserva");
                loadingStates[reserva.id] = false;
            },
        }
    );
}

function desaprobar(reserva) {
    loadingStates[reserva.id] = true;
    router.patch(
        route("reservas.desaprobar", reserva.id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                message.success("Reserva desaprobada exitosamente");
                emitir("actualizar-tabla", {
                    ...reserva,
                    estado: "No aprobada",
                });
                loadingStates[reserva.id] = false;
            },
            onError: (error) => {
                console.error("Error al desaprobar la reserva:", error);
                message.error("Error al desaprobar la reserva");
                loadingStates[reserva.id] = false;
            },
        }
    );
}
</script>
