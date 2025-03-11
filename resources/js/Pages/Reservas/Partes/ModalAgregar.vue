<template>
    <Modal
        title="Agregar reserva"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="reserva">
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

            <!-- Campos del formulario -->
            <FormItem label="Hora de inicio" name="hora_inicio">
                <Input type="datetime-local" v-model:value="reserva.hora_inicio" />
            </FormItem>

            <FormItem label="Hora de fin" name="hora_fin">
                <Input type="datetime-local" v-model:value="reserva.hora_fin" />
            </FormItem>

            <FormItem label="Estado" name="estado">
                <Select v-model:value="reserva.estado" :options="estadoOptions" />
            </FormItem>

            <FormItem class="flex justify-end mb-0">
                <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
                <Button type="primary" htmlType="submit" :loading="cargando">Guardar</Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Modal, Form, FormItem, Input, Select, Button, Tabs, TabPane, message } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    recursos: Array,
    equipos: Array,
    areas: {
        type: Array,
        default: () => [],
    },
});

const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

const reserva = ref({
    recurso_id: null,
    equipo_id: null,
    area_id: null,
    hora_inicio: '',
    hora_fin: '',
    estado: 'Por aprobar',
});

const cargando = ref(false);
const activeTab = ref('recurso'); // Recurso por defecto

// Opciones de recursos, equipos y áreas
const opcionesRecursos = ref(
    props.recursos.map((recurso) => ({
        label: recurso.nombre,
        value: recurso.id,
    }))
);

const opcionesEquipos = ref(
    props.equipos.map((equipo) => ({
        label: equipo.nombre,
        value: equipo.id,
    }))
);

const opcionesAreas = ref(
    props.areas.map((area) => ({
        label: area.nombre,
        value: area.id,
    }))
);

// Funciones de búsqueda para los selects
const buscarRecurso = (input, option) => option.label.toLowerCase().includes(input.toLowerCase());
const buscarEquipo = (input, option) => option.label.toLowerCase().includes(input.toLowerCase());
const buscarArea = (input, option) => option.label.toLowerCase().includes(input.toLowerCase());

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

// Cerrar modal
const cerrarModal = () => {
    emitir('update:visible', false);
};

// Enviar el formulario
const enviarFormulario = async () => {
    cargando.value = true;
    try {
        const response = await axios.post(route('reservas.store'), reserva.value);
        
        message.success('Reserva creada exitosamente');
        cerrarModal();
        emitir('actualizar-tabla', response.data);
    } catch (error) {
        message.error('Error al crear la reserva');
        console.error(error);
    } finally {
        cargando.value = false;
    }
};

// Inicializar las opciones de recursos, equipos y áreas
watch(() => props.visible, (val) => {
    if (val) {
        reserva.value = {
            recurso_id: null,
            equipo_id: null,
            area_id: null,
            hora_inicio: '',
            hora_fin: '',
            estado: 'Por aprobar',
        };
        activeTab.value = 'recurso';
    }
});
</script>
