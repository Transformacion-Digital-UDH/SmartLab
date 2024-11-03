<template>
    <Modal
        :open="visible"
        title="Agregar laboratorio"
        @ok="enviarFormulario"
        @cancel="cerrarModal"
        :ok-button-props="{ loading: cargando }"
        okText="Guardar"
        cancelText="Cancelar"
        centered
    >
        <Form layout="vertical" @submit.prevent="enviarFormulario">
            <FormItem label="Nombre" :rules="[{ required: true, message: 'Por favor ingrese el nombre' }]">
                <Input v-model:value="laboratorio.nombre" placeholder="Ingrese el nombre" size="small"/>
            </FormItem>

            <FormItem label="Responsable">
                <Select v-model:value="laboratorio.responsable_id" placeholder="Seleccione un responsable" :options="opcionesResponsables" />
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
                <DatePicker v-model:value="laboratorio.inauguracion" style="width: 100%;" />
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
});
const emit = defineEmits(['update:visible', 'actualizarLista']);

const laboratorio = ref({
    nombre: '',
    codigo: '',
    descripcion: '',
    aforo: null,
    email: '',
    inauguracion: null,
    responsable_id: null,
});
const cargando = ref(false);
const opcionesResponsables = ref([]);
const responsablesCargados = ref(false);

// Cierra el modal y emite el evento para cerrar en el componente padre
const cerrarModal = () => {
    emit('update:visible', false);
};

// Envía el formulario de creación del laboratorio
const enviarFormulario = async () => {
    cargando.value = true;
    try {
        // Enviar la solicitud para crear el laboratorio
        const response = await axios.post(route('laboratorios.store'), laboratorio.value);
        message.success('Laboratorio agregado exitosamente');
        cerrarModal();
        emit('actualizarTabla', response.data["laboratorio"]);
    } catch (error) {
        message.error('Error al agregar el laboratorio');
        console.error('Error al guardar el laboratorio:', error);
    } finally {
        cargando.value = false;
    }
};

// Función para obtener los responsables desde el servidor
const cargarResponsables = async () => {
    try {
        const response = await axios.get(route('responsables.index'));
        opcionesResponsables.value = response.data.map(responsable => ({
            label: responsable.nombres,
            value: responsable.id,
        }));
        responsablesCargados.value = true;
    } catch (error) {
        console.error('Error al cargar responsables:', error);
    }
};

// Verificar si el modal se abre por primera vez y cargar responsables
watch(() => props.visible, (val) => {
    if (val) {
        // Resetear los datos del formulario
        laboratorio.value = {
            nombre: '',
            codigo: '',
            descripcion: '',
            aforo: null,
            email: '',
            inauguracion: null,
            responsable_id: null,
        };
        if (!responsablesCargados.value) {
            cargarResponsables();
        }
    }
});
</script>
