<template>
    <Modal
        title="Editar reserva"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="reserva">
            <FormItem label="Hora de inicio" name="hora_inicio" :rules="[{ required: true, message: 'Por favor ingrese la hora de inicio' }]">
                <Input v-model:value="reserva.hora_inicio" type="datetime-local" placeholder="Seleccione la hora de inicio" />
            </FormItem>

            <FormItem label="Hora de fin" name="hora_fin" :rules="[{ required: true, message: 'Por favor ingrese la hora de fin' }]">
                <Input v-model:value="reserva.hora_fin" type="datetime-local" placeholder="Seleccione la hora de fin" />
            </FormItem>

            <FormItem label="Estado" name="estado" :rules="[{ required: true, message: 'Por favor seleccione un estado' }]">
                <Select v-model:value="reserva.estado" placeholder="Seleccione el estado">
                    <Select.Option value="Por aprobar">Por aprobar</Select.Option>
                    <Select.Option value="Aprobada">Aprobada</Select.Option>
                    <Select.Option value="No aprobada">No aprobada</Select.Option>
                    <Select.Option value="Finalizada">Finalizada</Select.Option>
                </Select>
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

            <FormItem label="Recurso" name="recurso_id">
                <Select
                    v-model:value="reserva.recurso_id"
                    placeholder="Seleccione un recurso"
                    :options="opcionesRecursos"
                    show-search
                    :filter-option="buscarRecurso"
                />
            </FormItem>

            <FormItem class="flex justify-end mb-0">
                <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
                <Button type="primary" htmlType="submit" :loading="cargando">Guardar</Button>
            </FormItem>
        </Form>
    </Modal>
</template>


<script setup>
import { ref, watch, defineEmits } from 'vue';
import { Modal, Form, FormItem, Input, Select, Button, message } from 'ant-design-vue';
import axios from 'axios';

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
        }),
    },
    equipos: Array,
    recursos: Array,
});

const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

const reserva = ref({ ...props.reserva });
const cargando = ref(false);
const opcionesEquipos = ref([]);
const opcionesRecursos = ref([]);

opcionesEquipos.value = props.equipos.map(equipo => ({
    label: equipo.nombre,
    value: equipo.id,
}));

opcionesRecursos.value = props.recursos.map(recurso => ({
    label: recurso.nombre,
    value: recurso.id,
}));

// Cierra el modal y emite el evento para cerrar en el componente padre
const cerrarModal = () => {
    emitir('update:visible', false);
};

// Funciones de búsqueda para equipos y recursos
const buscarEquipo = (input, option) => {
    return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
};

const buscarRecurso = (input, option) => {
    return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
};

// Envía el formulario de edición de la reserva
const enviarFormulario = async () => {
    cargando.value = true;
    try {
        // Enviar la solicitud para actualizar la reserva
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

// Verificar si el modal se abre y cargar los valores de la reserva
watch(() => props.visible, (val) => {
    if (val) {
        reserva.value = { ...props.reserva };
    }
});
</script>
