<template>
    <Modal
      title="Agregar reserva"
      :open="visible"
      @cancel="cerrarModal"
      centered
      :footer="null"
    >
      <Form layout="vertical" @finish="enviarFormulario" :model="reserva">
        <!-- Solicitante: ahora se selecciona mediante un Select -->
        <FormItem label="Solicitante" name="usuario_id">
          <Select
            v-model:value="reserva.usuario_id"
            placeholder="Seleccione un usuario"
            :options="opcionesUsuarios"
            show-search
            :filter-option="buscarUsuario"
            @change="onUsuarioSelected"
          />
        </FormItem>

        <!-- Información de contacto según el usuario seleccionado -->
        <div class="flex md:flex gap-x-3">
          <FormItem label="Correo" class="flex-1">
            <Input :value="selectedUsuario?.email || ''" disabled />
          </FormItem>
          <FormItem label="Celular" class="flex-1">
            <Input :value="selectedUsuario?.celular || ''" disabled />
          </FormItem>
        </div>

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

        <!-- Fecha y rango de hora en la misma fila -->
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
            label="Hora de reserva"
            name="hora_range"
            :rules="[{ required: true, message: 'Por favor seleccione el rango de hora' }]"
            class="flex-1"
          >
            <TimeRangePicker v-model:value="reserva.hora_range" format="HH:mm" />
          </FormItem>
        </div>

        <!-- Campo Estado con acciones (similar al editar) -->
        <FormItem style="margin-bottom: 16px;" label="Estado">
          <div class="flex items-center gap-x-4">
            <div>
              <!-- Se muestra el estado dinámico con displayEstado -->
              <Input :value="displayEstado" disabled style="width: 240px;" />
            </div>
            <!-- Sólo se muestran las acciones si la reserva NO está finalizada -->
            <div v-if="!isFinalized" class="flex space-x-2">
              <template v-if="reserva.estado != 'Aprobada'">
                <Tag
                  :bordered="false"
                  color="green"
                  style="cursor: pointer;"
                  @click="reserva.estado = 'Aprobada'"
                  class="hover:bg-green-100 flex items-center"
                >
                  <template #icon>
                    <CheckOutlined />
                  </template>
                  Aprobada
                </Tag>
              </template>
              <template v-if="reserva.estado != 'No aprobada'">
                <Tag
                  :bordered="false"
                  color="orange"
                  style="cursor: pointer;"
                  @click="reserva.estado = 'No aprobada'"
                  class="hover:bg-orange-100 flex items-center"
                >
                  <template #icon>
                    <CloseOutlined />
                  </template>
                  No aprobada
                </Tag>
              </template>
              <template v-if="reserva.estado != 'Por aprobar'">
                <Tag
                  :bordered="false"
                  color="default"
                  style="cursor: pointer;"
                  @click="reserva.estado = 'Por aprobar'"
                  class="hover:bg-gray-100 flex items-center"
                >
                  Por aprobar
                </Tag>
              </template>
            </div>
          </div>
          <div v-if="actionLoadingText" class="mt-1">
            <span style="font-size: 14px; color: #999">{{ actionLoadingText }}</span>
          </div>
        </FormItem>

        <!-- Alerta de cambios sin guardar -->
        <Alert
          v-if="isFormChanged"
          message="Hay cambios sin guardar"
          type="warning"
          show-icon
          style="margin-bottom: 16px; font-size: 12px; padding: 4px 8px;"
        />

        <FormItem class="flex justify-end mb-0">
          <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
          <Button
            type="primary"
            htmlType="submit"
            :loading="cargando"
            :disabled="!isFormChanged"
          >
            Guardar
          </Button>
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
    Alert,
    message,
    DatePicker,
    TimeRangePicker,
    Tag,
  } from 'ant-design-vue';
  import axios from 'axios';
  import dayjs from 'dayjs';
  import { CheckOutlined, CloseOutlined } from '@ant-design/icons-vue';

  const props = defineProps({
    visible: Boolean,
    // Para agregar, si bien podemos recibir un objeto reserva por defecto,
    // definimos valores iniciales en caso de creación.
    reserva: {
      type: Object,
      default: () => ({
        hora_inicio: '',
        hora_fin: '',
        estado: 'Por aprobar',
        equipo_id: null,
        recurso_id: null,
        area_id: null,
        usuario_id: null,
      }),
    },
    equipos: Array,
    recursos: Array,
    areas: {
      type: Array,
      default: () => [],
    },
    usuarios: {
      type: Array,
      default: () => [],
    },
  });

  const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

  // Inicializamos la reserva para creación
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
  const actionLoadingText = ref("");

  // Se guarda una copia original para detectar cambios
  const originalReserva = ref({ ...reserva.value });

  // Función para extraer campos editables (incluye usuario_id) y formatear fechas/hora_range
  const getEditableReserva = (r) => {
    return {
      recurso_id: r.recurso_id,
      equipo_id: r.equipo_id,
      area_id: r.area_id,
      fecha: r.fecha ? r.fecha.format('YYYY-MM-DD') : null,
      hora_range:
        r.hora_range && r.hora_range.length
          ? r.hora_range.map((t) => t.format('HH:mm'))
          : [],
      estado: r.estado,
      usuario_id: r.usuario_id,
    };
  };

  // Computed para detectar si hay cambios en el formulario
  const isFormChanged = computed(() => {
    return (
      JSON.stringify(getEditableReserva(originalReserva.value)) !==
      JSON.stringify(getEditableReserva(reserva.value))
    );
  });

  // Computed para obtener la hora final a partir de la fecha y rango de hora
  const computedHoraFin = computed(() => {
    if (
      reserva.value.fecha &&
      reserva.value.hora_range &&
      reserva.value.hora_range.length === 2
    ) {
      const dateStr = dayjs(reserva.value.fecha).format('YYYY-MM-DD');
      const endTime = dayjs(reserva.value.hora_range[1]).format('HH:mm:ss');
      return dayjs(`${dateStr} ${endTime}`);
    }
    if (reserva.value.hora_fin) {
      return dayjs(reserva.value.hora_fin);
    }
    return null;
  });

  // Computed para saber si la reserva ya finalizó
  const isFinalized = computed(() => {
    const horaFin = computedHoraFin.value;
    if (horaFin) {
      return horaFin.isBefore(dayjs()) || horaFin.isSame(dayjs());
    }
    return false;
  });

  // Computed para mostrar el estado (incluye indicador de "Finalizada /")
  const displayEstado = computed(() => {
    return isFinalized.value
      ? `Finalizada / ${reserva.value.estado}`
      : reserva.value.estado;
  });

  // Computed para obtener el usuario seleccionado
  const selectedUsuario = computed(() => {
    return props.usuarios.find((usuario) => usuario.id === reserva.value.usuario_id);
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

  const opcionesUsuarios = ref(
    props.usuarios.map((usuario) => ({
      label: `${usuario.nombres} ${usuario.apellidos} - ${usuario.dni}`,
      value: usuario.id,
    }))
  );

  // Funciones para filtrar las opciones de cada select
  const buscarEquipo = (input, option) =>
    option.label.toLowerCase().includes(input.toLowerCase());
  const buscarRecurso = (input, option) =>
    option.label.toLowerCase().includes(input.toLowerCase());
  const buscarArea = (input, option) =>
    option.label.toLowerCase().includes(input.toLowerCase());
  const buscarUsuario = (input, option) =>
    option.label.toLowerCase().includes(input.toLowerCase());

  // Métodos para actualizar la selección en cada tab
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

  const onUsuarioSelected = (value) => {
    reserva.value.usuario_id = value;
  };

  // Función para deshabilitar fechas pasadas en el DatePicker
  const disabledDate = (current) => {
    return current && current < dayjs().startOf('day');
  };

  // Función para enviar el formulario y crear la reserva (POST)
  const enviarFormulario = async () => {
    if (
      reserva.value.fecha &&
      reserva.value.hora_range &&
      reserva.value.hora_range.length === 2
    ) {
      const dateStr = dayjs(reserva.value.fecha).format('YYYY-MM-DD');
      const startTime = dayjs(reserva.value.hora_range[0]).format('HH:mm');
      const endTime = dayjs(reserva.value.hora_range[1]).format('HH:mm');
      reserva.value.hora_inicio = `${dateStr} ${startTime}:00`;
      reserva.value.hora_fin = `${dateStr} ${endTime}:00`;
    }
    cargando.value = true;
    try {
      const response = await axios.post(route('reservas.store'), reserva.value);
      message.success('Reserva creada exitosamente');
      cerrarModal();
      emitir('actualizar-tabla', response.data['reserva']);
    } catch (error) {
      message.error('Error al crear la reserva');
      console.error(error);
    } finally {
      cargando.value = false;
    }
  };

  // Función para cerrar el modal
  const cerrarModal = () => {
    emitir('update:visible', false);
  };

  // Reinicializamos el formulario al abrir el modal
  watch(
    () => props.visible,
    (val) => {
      if (val) {
        reserva.value = {
          usuario_id: null,
          recurso_id: null,
          equipo_id: null,
          area_id: null,
          hora_inicio: '',
          hora_fin: '',
          estado: 'Por aprobar',
          fecha: null,
          hora_range: [],
        };
        activeTab.value = 'recurso';
        originalReserva.value = { ...reserva.value };
      }
    }
  );
  </script>
