<template>
    <Modal
      title="Solicitud de reserva"
      :open="visible"
      @cancel="cerrarModal"
      centered
      :footer="null"
    >
      <Form layout="vertical" @finish="enviarFormulario" :model="reserva">
        <!-- Campo Estado (encima de todos los campos) -->
        <FormItem style="margin-bottom: 16px;">
          <div class="flex items-center gap-x-4">
            <div>
              <Input
                v-model:value="reserva.estado"
                disabled
              />
            </div>
            <div class="flex space-x-2">
              <template v-if="reserva.estado === 'Por aprobar'">
                <Tag
                  :bordered="false"
                  color="green"
                  style="cursor: pointer;"
                  @click="aprobarReserva"
                  class="hover:bg-green-100 flex items-center"
                >
                  <template #icon>
                    <CheckOutlined />
                  </template>
                  Aprobar
                </Tag>
                <Tag
                  :bordered="false"
                  color="orange"
                  style="cursor: pointer; margin-left: 8px;"
                  @click="desaprobarReserva"
                  class="hover:bg-orange-100 flex items-center"
                >
                  <template #icon>
                    <CloseOutlined />
                  </template>
                  Desaprobar
                </Tag>
              </template>
              <template v-else-if="reserva.estado === 'Aprobada'">
                <a-tag
                  color="orange"
                  style="cursor: pointer;"
                  @click="desaprobarReserva"
                >
                  <template #icon>
                    <CloseOutlined />
                  </template>
                  Desaprobar
                </a-tag>
              </template>
              <template v-else-if="reserva.estado === 'No aprobada'">
                <a-tag
                  color="green"
                  style="cursor: pointer;"
                  @click="aprobarReserva"
                >
                  <template #icon>
                    <CheckOutlined />
                  </template>
                  Aprobar
                </a-tag>
              </template>
            </div>
          </div>
          <!-- Label de loading para la acción -->
          <div v-if="actionLoadingText" class="mt-1">
            <span style="font-size: 14px; color: #999">
              {{ actionLoadingText }}
            </span>
          </div>
        </FormItem>

        <!-- Tabs para seleccionar entre Recurso, Equipo o Área -->
        <Tabs v-model:activeKey="activeTab" type="line">
          <TabPane key="recurso" tab="Recurso">
            <FormItem label="Recurso" name="recurso_id">
              <Select
                v-model:value="reserva.recurso_id"
                placeholder="Seleccione un recurso"
                :options="opcionesRecursos"
                show-search
                :filter-option="buscarRecurso"
                @change="onRecursoSelected"
              />
            </FormItem>
          </TabPane>
          <TabPane key="equipo" tab="Equipo">
            <FormItem label="Equipo" name="equipo_id">
              <Select
                v-model:value="reserva.equipo_id"
                placeholder="Seleccione un equipo"
                :options="opcionesEquipos"
                show-search
                :filter-option="buscarEquipo"
                @change="onEquipoSelected"
              />
            </FormItem>
          </TabPane>
          <TabPane key="area" tab="Área">
            <FormItem label="Área" name="area_id">
              <Select
                v-model:value="reserva.area_id"
                placeholder="Seleccione un área"
                :options="opcionesAreas"
                show-search
                :filter-option="buscarArea"
                @change="onAreaSelected"
              />
            </FormItem>
          </TabPane>
        </Tabs>

        <!-- Fecha y Rango de hora en la misma fila -->
        <div class="flex gap-x-3">
          <FormItem
            label="Fecha"
            name="fecha"
            :rules="[{ required: true, message: 'Por favor seleccione la fecha' }]"
            class="flex-1"
          >
            <DatePicker
              v-model:value="reserva.fecha"
              :disabledDate="disabledDate"
              style="width: 100%;"
            />
          </FormItem>
          <FormItem
            label="Rango de hora"
            name="hora_range"
            :rules="[{ required: true, message: 'Por favor seleccione el rango de hora' }]"
            class="flex-1"
          >
            <TimeRangePicker
              v-model:value="reserva.hora_range"
              format="HH:mm"
              style="width: 100%;"
            />
          </FormItem>
        </div>

        <!-- Resto del formulario -->
        <!-- Campo de Usuario (solo lectura) -->
        <FormItem label="Solicitante">
          <Input :value="nombreUsuario" disabled />
        </FormItem>

        <!-- Información de contacto -->
        <div class="flex md:flex gap-x-3">
          <FormItem label="Correo" class="flex-1">
            <Input :value="reserva.usuario?.email" disabled />
          </FormItem>
          <FormItem label="Celular" class="flex-1">
            <Input :value="reserva.usuario?.celular" disabled />
          </FormItem>
        </div>

        <FormItem class="flex justify-end mb-0">
          <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
          <Button type="primary" htmlType="submit" :loading="cargando">Guardar</Button>
        </FormItem>
      </Form>
    </Modal>
  </template>

  <script setup>
  import { ref, watch, computed } from 'vue';
  import {
    Modal,
    Form,
    FormItem,
    Input,
    Select,
    Button,
    Tabs,
    TabPane,
    message,
    DatePicker,
    TimeRangePicker,
    Tag,
  } from 'ant-design-vue';
  import { router } from '@inertiajs/vue3';
  import axios from 'axios';
  import dayjs from 'dayjs';
  import { CheckOutlined, CloseOutlined } from '@ant-design/icons-vue';

  // Definición de propiedades
  const props = defineProps({
    visible: Boolean,
    reserva: {
      type: Object,
      default: () => ({
        hora_inicio: '',
        hora_fin: '',
        estado: 'Por aprobar',
        equipo_id: null,
        recurso_id: null,
        area_id: null,
      }),
    },
    equipos: Array,
    recursos: Array,
    areas: {
      type: Array,
      default: () => [],
    },
  });

  const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

  // Se agrega "fecha" y "hora_range" al objeto reserva para vincularlos al formulario
  const reserva = ref({
    ...props.reserva,
    fecha: props.reserva.hora_inicio ? dayjs(props.reserva.hora_inicio) : null,
    hora_range:
      props.reserva.hora_inicio && props.reserva.hora_fin
        ? [dayjs(props.reserva.hora_inicio), dayjs(props.reserva.hora_fin)]
        : [],
  });

  const cargando = ref(false);
  const activeTab = ref('recurso');

  // Variable para mostrar el mensaje de loading en aprobar/desaprobar
  const actionLoadingText = ref("");

  // Para controlar el loading de las acciones
  const loadingStates = ref({});

  // Actualizar el tab activo y los nuevos campos cuando cambie props.reserva
  watch(
    () => props.reserva,
    (newReserva) => {
      if (newReserva.recurso_id) {
        activeTab.value = 'recurso';
      } else if (newReserva.equipo_id) {
        activeTab.value = 'equipo';
      } else if (newReserva.area_id) {
        activeTab.value = 'area';
      } else {
        activeTab.value = 'recurso';
      }
      reserva.value = {
        ...newReserva,
        fecha: newReserva.hora_inicio ? dayjs(newReserva.hora_inicio) : null,
        hora_range:
          newReserva.hora_inicio && newReserva.hora_fin
            ? [dayjs(newReserva.hora_inicio), dayjs(newReserva.hora_fin)]
            : [],
      };
    },
    { immediate: true }
  );

  // Computed para obtener el nombre del usuario en formato "Nombres Apellidos - DNI"
  const nombreUsuario = computed(() => {
    if (reserva.value.usuario) {
      return `${reserva.value.usuario.nombres} ${reserva.value.usuario.apellidos} - ${reserva.value.usuario.dni}`;
    }
    return '';
  });

  // Opciones para cada select
  const opcionesEquipos = ref(
    props.equipos.map((equipo) => ({
      label: equipo.nombre,
      value: equipo.id,
    }))
  );

  const opcionesRecursos = ref(
    props.recursos.map((recurso) => ({
      label: recurso.nombre,
      value: recurso.id,
    }))
  );

  const opcionesAreas = ref(
    props.areas.map((area) => ({
      label: area.nombre,
      value: area.id,
    }))
  );

  // Funciones para filtrar opciones
  const buscarEquipo = (input, option) =>
    option.label.toLowerCase().includes(input.toLowerCase());
  const buscarRecurso = (input, option) =>
    option.label.toLowerCase().includes(input.toLowerCase());
  const buscarArea = (input, option) =>
    option.label.toLowerCase().includes(input.toLowerCase());

  // Al seleccionar en un tab se deseleccionan los otros
  const onRecursoSelected = (value) => {
    reserva.value.recurso_id = value;
    reserva.value.equipo_id = null;
    reserva.value.area_id = null;
  };

  const onEquipoSelected = (value) => {
    reserva.value.equipo_id = value;
    reserva.value.recurso_id = null;
    reserva.value.area_id = null;
  };

  const onAreaSelected = (value) => {
    reserva.value.area_id = value;
    reserva.value.equipo_id = null;
    reserva.value.recurso_id = null;
  };

  // Función para deshabilitar fechas pasadas en el DatePicker
  const disabledDate = (current) => {
    return current && current < dayjs().startOf('day');
  };

  // Funciones para aprobar y desaprobar la reserva usando tags
  function aprobarReserva() {
    loadingStates.value[reserva.value.id] = true;
    actionLoadingText.value = "Aprobando reserva y actualizando Google Calendar";
    router.patch(route("reservas.aprobar", reserva.value.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        message.success("Reserva aprobada exitosamente");
        reserva.value.estado = "Aprobada";
        emitir("actualizar-tabla", { ...reserva.value });
        loadingStates.value[reserva.value.id] = false;
        actionLoadingText.value = "";
      },
      onError: (error) => {
        console.error("Error al aprobar la reserva:", error);
        message.error("Error al aprobar la reserva");
        loadingStates.value[reserva.value.id] = false;
        actionLoadingText.value = "";
      },
    });
  }

  function desaprobarReserva() {
    loadingStates.value[reserva.value.id] = true;
    actionLoadingText.value = "Desaprobando reserva y actualizando Google Calendar";
    router.patch(route("reservas.desaprobar", reserva.value.id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        message.success("Reserva desaprobada exitosamente");
        reserva.value.estado = "No aprobada";
        emitir("actualizar-tabla", { ...reserva.value });
        loadingStates.value[reserva.value.id] = false;
        actionLoadingText.value = "";
      },
      onError: (error) => {
        console.error("Error al desaprobar la reserva:", error);
        message.error("Error al desaprobar la reserva");
        loadingStates.value[reserva.value.id] = false;
        actionLoadingText.value = "";
      },
    });
  }

  // Función para cerrar el modal
  const cerrarModal = () => {
    emitir('update:visible', false);
  };

  // Envío del formulario (se combinan fecha y rango de hora en hora_inicio y hora_fin)
  const enviarFormulario = async () => {
    if (reserva.value.fecha && reserva.value.hora_range && reserva.value.hora_range.length === 2) {
      const dateStr = dayjs(reserva.value.fecha).format('YYYY-MM-DD');
      const startTime = dayjs(reserva.value.hora_range[0]).format('HH:mm');
      const endTime = dayjs(reserva.value.hora_range[1]).format('HH:mm');
      reserva.value.hora_inicio = `${dateStr} ${startTime}:00`;
      reserva.value.hora_fin = `${dateStr} ${endTime}:00`;
    }
    cargando.value = true;
    try {
      const response = await axios.put(route('reservas.update', props.reserva.id), reserva.value);
      message.success('Reserva actualizada exitosamente');
      cerrarModal();
      emitir('actualizar-tabla', response.data["reserva"]);
    } catch (error) {
      message.error('Error al actualizar la reserva');
    } finally {
      cargando.value = false;
    }
  };
  </script>
