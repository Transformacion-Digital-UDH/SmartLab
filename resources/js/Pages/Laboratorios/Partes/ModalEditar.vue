<template>
    <Modal
        :open="visible"
        title="Editar laboratorio"
        @ok="enviarFormulario"
        @cancel="cerrarModal"
        :ok-button-props="{ loading: cargando }"
        okText="Guardar cambios"
        cancelText="Cancelar"
        centered
    >
        <Form layout="vertical" @submit.prevent="enviarFormulario">
            <FormItem label="Nombre" :rules="[{ required: true, message: 'Por favor ingrese el nombre' }]">
                <Input v-model:value="laboratorio.nombre" placeholder="Ingrese el nombre" />
            </FormItem>

            <FormItem label="Responsable">
                <Select
                    v-model:value="laboratorio.responsable_id"
                    placeholder="Seleccione un responsable"
                    :options="opcionesResponsables"
                    show-search
                    :filter-option="buscarResponsable"
                />
            </FormItem>

            <FormItem label="Código">
                <Input v-model:value="laboratorio.codigo" placeholder="Ingrese el código" />
            </FormItem>

            <FormItem label="Descripción">
                <Input v-model:value="laboratorio.descripcion" placeholder="Ingrese una descripción" />
            </FormItem>

            <FormItem label="Aforo">
                <InputNumber v-model:value="laboratorio.aforo" placeholder="Ingrese el aforo" style="width: 100%;" />
            </FormItem>

            <FormItem label="Email">
                <Input v-model:value="laboratorio.email" placeholder="Ingrese el correo electrónico" />
            </FormItem>

            <FormItem label="Fecha de inauguración">
                <DatePicker style="width: 100%;" :format="'YYYY-MM-DD'"/>
            </FormItem>
        </Form>
    </Modal>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue';
import { Modal, Form, FormItem, Input, Select, DatePicker, InputNumber, message } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    responsables: Array,
    laboratorio: {
        type: Object,
        default: () => ({
            nombre: '',
            codigo: '',
            descripcion: '',
            aforo: null,
            email: '',
            inauguracion: null,
            responsable_id: '',
        }),
    },
});
const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

const laboratorio = ref({ ...props.laboratorio });
const cargando = ref(false);
const opcionesResponsables = ref([]);

opcionesResponsables.value = props.responsables.map(responsable => ({
    label: responsable.nombres,
    value: responsable.id,
}));

// Cierra el modal y emite el evento para cerrar en el componente padre
const cerrarModal = () => {
    emitir('update:visible', false);
};

const buscarResponsable = (input, option) => {
    return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
};

// Envía el formulario de edición del laboratorio
const enviarFormulario = async () => {
    cargando.value = true;
    try {
        // Enviar la solicitud para actualizar el laboratorio
        const response = await axios.put(route('laboratorios.update', props.laboratorio.id), laboratorio.value);
        message.success('Laboratorio actualizado exitosamente');
        cerrarModal();
        emitir('actualizar-tabla', response.data["laboratorio"]);
    } catch (error) {
        message.error('Error al actualizar el laboratorio');
    } finally {
        cargando.value = false;
    }
};

// Verificar si el modal se abre y cargar los valores del laboratorio
watch(() => props.visible, (val) => {
    if (val) {
        console.log('watch ed')
        laboratorio.value = { ...props.laboratorio };
    }

});
</script>
