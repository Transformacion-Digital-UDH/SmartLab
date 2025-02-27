<template>
    <Modal
        title="Solicitud de reserva"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="reserva">
            <!-- Campo de Usuario (solo lectura) -->
            <FormItem label="Solicitante">
                <Input :value="nombreUsuario" disabled />
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

            <FormItem
                label="Hora de inicio"
                name="hora_inicio"
                :rules="[{ required: true, message: 'Por favor ingrese la hora de inicio' }]"
            >
                <Input
                    v-model:value="reserva.hora_inicio"
                    type="datetime-local"
                    placeholder="Seleccione la hora de inicio"
                />
            </FormItem>

            <FormItem
                label="Hora de fin"
                name="hora_fin"
                :rules="[{ required: true, message: 'Por favor ingrese la hora de fin' }]"
            >
                <Input
                    v-model:value="reserva.hora_fin"
                    type="datetime-local"
                    placeholder="Seleccione la hora de fin"
                />
            </FormItem>

            <FormItem
                label="Estado"
                name="estado"
                :rules="[{ required: true, message: 'Por favor seleccione un estado' }]"
            >
                <Select v-model:value="reserva.estado" placeholder="Seleccione el estado">
                    <Select.Option value="Por aprobar">Por aprobar</Select.Option>
                    <Select.Option value="Aprobada">Aprobada</Select.Option>
                    <Select.Option value="No aprobada">No aprobada</Select.Option>
                    <Select.Option value="Finalizada">Finalizada</Select.Option>
                </Select>
            </FormItem>

            <FormItem class="flex justify-end mb-0">
                <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
                <Button type="primary" htmlType="submit" :loading="cargando">Guardar</Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { Modal, Form, FormItem, Input, Select, Button, Tabs, TabPane, message } from 'ant-design-vue';
import axios from 'axios';

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

const reserva = ref({ ...props.reserva });
const cargando = ref(false);
const activeTab = ref('recurso'); // Recurso por defecto

// Computed para obtener el nombre del usuario en formato "Nombres Apellidos"
const nombreUsuario = computed(() => {
    if (reserva.value.usuario) {
        return `${reserva.value.usuario.nombres} ${reserva.value.usuario.apellidos}`;
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

// Función para cerrar el modal
const cerrarModal = () => {
    emitir('update:visible', false);
};

// Envío del formulario
const enviarFormulario = async () => {
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

// Actualizar la reserva local cuando el modal se abre
watch(
    () => props.visible,
    (val) => {
        if (val) {
            reserva.value = { ...props.reserva };
        }
    }
);
</script>
