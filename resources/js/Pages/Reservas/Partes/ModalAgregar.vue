<template>
    <Modal
        title="Agregar Reserva"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="reserva">
            <!-- Campos del formulario -->
            <FormItem label="Recurso" name="recurso_id">
                <Select
                    v-model:value="reserva.recurso_id"
                    placeholder="Seleccione un recurso"
                    :options="opcionesRecursos"
                    show-search
                    :filter-option="buscarRecurso"
                />
            </FormItem>

            <FormItem label="Equipo" name="equipo_id">
                <Select
                    v-model:value="reserva.equipo_id"
                    placeholder="Seleccione un equipo"
                    :options="opcionesEquipos"
                    show-search
                    :filter-option="buscarEquipo"
                />
            </FormItem>

            <FormItem label="Hora de Inicio" name="hora_inicio">
                <Input type="datetime-local" v-model:value="reserva.hora_inicio" />
            </FormItem>

            <FormItem label="Hora de Fin" name="hora_fin">
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
import { Modal, Form, FormItem, Input, Select, Button, message } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    recursos: Array,
    equipos: Array,
});

const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

const reserva = ref({
    recurso_id: null,
    equipo_id: null,
    hora_inicio: '',
    hora_fin: '',
    estado: 'Por aprobar',
});

const cargando = ref(false);

// Opciones de recursos y equipos
const opcionesRecursos = ref(props.recursos.map(r => ({ label: r.nombre, value: r.id })));
const opcionesEquipos = ref(props.equipos.map(e => ({ label: e.nombre, value: e.id })));

const estadoOptions = ref([
    { label: 'Por aprobar', value: 'Por aprobar' },
    { label: 'Aprobada', value: 'Aprobada' },
    { label: 'No aprobada', value: 'No aprobada' },
    { label: 'Finalizada', value: 'Finalizada' },
]);

// Cerrar modal
const cerrarModal = () => {
    emitir('update:visible', false);
};

// Funciones de búsqueda para los selects
const buscarRecurso = (input, option) => option.label.toLowerCase().includes(input.toLowerCase());
const buscarEquipo = (input, option) => option.label.toLowerCase().includes(input.toLowerCase());

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

// Inicializar las opciones de recursos y equipos
watch(() => props.visible, (val) => {
    if (val) {
        reserva.value = {
            recurso_id: null,
            equipo_id: null,
            hora_inicio: '',
            hora_fin: '',
            estado: 'Por aprobar',
        };
    }
});
</script>
