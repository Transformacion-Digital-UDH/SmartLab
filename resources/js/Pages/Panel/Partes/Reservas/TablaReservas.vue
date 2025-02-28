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
          <div class="flex items-center space-x-2">
            <!-- Botón de visualizar solo con ícono -->
            <Button type="default" size="middle" @click="editar(record)" class="flex justify-center items-center">
              <template #icon>
                <EyeOutlined />
              </template>
            </Button>
            <!-- Botón de aprobar con popconfirm personalizado -->
            <Popconfirm
              title="¿Estás seguro?"
              okText="Aprobar"
              cancelText="Desaprobar"
              @confirm="aprobar(record)"
              @cancel="desaprobar(record)"
            >
              <Button type="primary" class="font-medium" :loading="loadingStates[record.id]">
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
  import { Table, message, Popconfirm, Button } from "ant-design-vue";
  import { router } from "@inertiajs/vue3";
  import dayjs from "dayjs";
  import { EyeOutlined } from "@ant-design/icons-vue";

  const props = defineProps({
    reservas: Array,
  });

  const emitir = defineEmits(["editar", "actualizar-tabla"]);

  // Creamos un objeto reactivo para llevar el estado de carga por reserva
  const loadingStates = reactive({});

  // Definir las columnas de la tabla
  const columnas = [
    {
      title: "Reserva",
      key: "fecha",
      dataIndex: "hora_inicio",
      customRender: ({ record }) => {
        return `${dayjs(record.hora_inicio).format("YYYY-MM-DD")} \n ${dayjs(record.hora_inicio).format("HH:mm")} - ${dayjs(record.hora_fin).format("HH:mm")}`;
      },
      width: 150,
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
      width: 150,
      sorter: (a, b) => new Date(a.created_at) - new Date(b.created_at),
    },
    {
      key: "acciones",
      fixed: "right",
      width: 180,
    },
  ];

  // Función para emitir el evento de edición y abrir el modal correspondiente
  function editar(reserva) {
    emitir("editar", reserva);
  }

  // Función para aprobar la reserva usando la ruta creada
  function aprobar(reserva) {
    loadingStates[reserva.id] = true; // Activamos el loading para esta reserva
    router.patch(route("reservas.aprobar", reserva.id), {}, {
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
    });
  }

  // Función para desaprobar la reserva usando la ruta creada
  function desaprobar(reserva) {
    loadingStates[reserva.id] = true; // Activamos el loading para esta reserva
    router.patch(route("reservas.desaprobar", reserva.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        message.success("Reserva desaprobada exitosamente");
        emitir("actualizar-tabla", { ...reserva, estado: "No aprobada" });
        loadingStates[reserva.id] = false;
      },
      onError: (error) => {
        console.error("Error al desaprobar la reserva:", error);
        message.error("Error al desaprobar la reserva");
        loadingStates[reserva.id] = false;
      },
    });
  }
  </script>
