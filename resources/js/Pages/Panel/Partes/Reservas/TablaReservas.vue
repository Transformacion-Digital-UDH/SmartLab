<template>
    <Table :columns="columnas" :dataSource="reservas" rowKey="id" :pagination="false" :scroll="{ x: 800 }">
        <template #bodyCell="{ column, record }">
            <!-- Renderizado personalizado para la columna 'Reserva' -->
            <template v-if="column.key === 'fecha'">
                <div>
                    <div class="font-medium">{{ dayjs(record.hora_inicio).format("YYYY-MM-DD") }}</div>
                    <div>{{ dayjs(record.hora_inicio).format("HH:mm") }} - {{ dayjs(record.hora_fin).format("HH:mm") }}</div>
                </div>
            </template>

            <!-- Renderizado personalizado para la columna 'Reservable' -->
            <template v-if="column.key === 'recurso_equipo'">
                {{ record.equipo?.nombre || record.recurso?.nombre || "No especificado" }}
            </template>

            <!-- Renderizado personalizado para la columna 'Solicitante' -->
            <template v-if="column.key === 'usuario'">
                {{ record.usuario.nombres }} {{ record.usuario.apellidos }}
            </template>

            <!-- Renderizado personalizado para la columna 'Fecha registro' -->
            <template v-if="column.key === 'created_at'">
                <div>
                    <div>{{ dayjs(record.created_at).format("YYYY-MM-DD") }}</div>
                    <div>{{ dayjs(record.created_at).format("HH:mm") }}</div>
                </div>
            </template>

            <!-- Renderizado personalizado para la columna 'Acciones' -->
            <template v-if="column.key === 'acciones'">
                <Button type="primary" @click="editar(record)">Visualizar</Button>
                <Popconfirm
                    title="¿Estás seguro de aprobar esta reserva?"
                    okText="Sí"
                    cancelText="No"
                    @confirm="aprobar(record)"
                >
                    <Button type="success" class="ml-2">Aprobar</Button>
                </Popconfirm>
            </template>
        </template>
    </Table>
</template>

<script setup>
import { Table, message, Popconfirm, Button } from "ant-design-vue";
import { router } from "@inertiajs/vue3";
import dayjs from "dayjs";

const props = defineProps({
    reservas: Array,
});

const emitir = defineEmits(["editar", "actualizar-tabla"]);

// Definir las columnas de la tabla de reservas
const columnas = [
    {
        title: "Reserva",
        key: "fecha",
        dataIndex: "hora_inicio",
        customRender: ({ record }) => {
            return `${dayjs(record.hora_inicio).format("YYYY-MM-DD")} \n ${dayjs(record.hora_inicio).format("HH:mm")} - ${dayjs(record.hora_fin).format("HH:mm")}`;
        },
        width: 200,
        sorter: (a, b) => new Date(a.hora_inicio) - new Date(b.hora_inicio),
    },
    {
        title: "Reservable",
        key: "recurso_equipo",
        customRender: ({ record }) =>
            record.equipo?.nombre || record.recurso?.nombre || "No especificado",
        sorter: (a, b) => {
            const equipoA = a.equipo?.nombre || a.recurso?.nombre || "";
            const equipoB = b.equipo?.nombre || b.recurso?.nombre || "";
            return equipoA.localeCompare(equipoB);
        },
    },
    {
        title: "Solicitante",
        key: "usuario",
        customRender: ({ record }) => `${record.usuario.nombres} ${record.usuario.apellidos}`,
    },
    {
        title: "Fecha registro",
        key: "created_at",
        dataIndex: "created_at",
        width: 200,
        sorter: (a, b) => new Date(a.created_at) - new Date(b.created_at),
    },
    {
        title: "Acciones",
        key: "acciones",
        fixed: "right",
        width: 150,
    },
];

// Función para emitir el evento de edición y abrir el ModalEditar.vue
function editar(reserva) {
    emitir("editar", reserva);
}

// Función para aprobar la reserva
function aprobar(reserva) {
  // Actualizamos el objeto de reserva con el estado "Aprobada"
  const reservaActualizada = { ...reserva, estado: "Aprobada" };
  // Utilizamos reserva.id para construir la ruta
  router.put(route("reservas.update", reserva.id), reservaActualizada, {
    preserveScroll: true,
    onSuccess: () => {
      message.success("Reserva aprobada exitosamente");
      emitir("actualizar-tabla", reservaActualizada);
    },
    onError: (error) => {
      console.error("Error al aprobar la reserva:", error);
      message.error("Error al aprobar la reserva");
    },
  });
}

</script>
