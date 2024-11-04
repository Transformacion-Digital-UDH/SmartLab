<template>
    <Modal
        title="Agregar laboratorio"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="laboratorio">
            <FormItem label="Nombre" name="nombre" :rules="[{ required: true, message: 'Por favor ingrese el nombre' }]">
                <Input v-model:value="laboratorio.nombre" placeholder="Ingrese el nombre" autofocus />
            </FormItem>

            <FormItem label="Responsable" name="responsable_id">
                <Select
                    v-model:value="laboratorio.responsable_id"
                    placeholder="Seleccione un responsable"
                    :options="opcionesResponsables"
                    show-search
                    :filter-option="buscarResponsable"
                />
            </FormItem>

            <FormItem label="Código" name="codigo">
                <Input v-model:value="laboratorio.codigo" placeholder="Ingrese el código" />
            </FormItem>

            <FormItem label="Descripción" name="descripcion">
                <Input v-model:value="laboratorio.descripcion" placeholder="Ingrese una descripción" />
            </FormItem>

            <FormItem label="Aforo" name="aforo" >
                <InputNumber v-model:value="laboratorio.aforo"
                    placeholder="Ingrese el aforo" style="width: 100%;" type="number" step="1" min="0"
                />
            </FormItem>

            <FormItem label="Email" name="email" :rules="[{ type: 'email', message: 'Por favor ingrese un correo válido' }]">
                <Input v-model:value="laboratorio.email" placeholder="Ingrese el correo electrónico" />
            </FormItem>

            <FormItem label="Fecha de inauguración" name="inauguracion">
                <DatePicker v-model:value="laboratorio.inauguracion" style="width: 100%;" />
            </FormItem>

            <FormItem class="flex justify-end mb-0">
                <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
                <Button type="primary" htmlType="submit" :loading="cargando">Guardar</Button>
            </FormItem>

        </Form>
    </Modal>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue';
import { Modal, Form, FormItem, Input, Select, DatePicker, InputNumber, Button, message } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    responsables: Array,
});

const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

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


// Cierra el modal y emite el evento para cerrar en el componente padre
const cerrarModal = () => {
    emitir('update:visible', false);
};

const buscarResponsable = (input, option) => {
    return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
};

// Envía el formulario de creación del laboratorio
const enviarFormulario = async () => {
    cargando.value = true;
    try {
        // Enviar la solicitud para crear el laboratorio
        const response = await axios.post(route('laboratorios.store'), laboratorio.value);
        message.success('Laboratorio agregado exitosamente');
        cerrarModal();
        emitir('actualizar-tabla', response.data["laboratorio"]);
    } catch (error) {
        message.error('Error al agregar el laboratorio');
        console.error('Error al guardar el laboratorio:', error);
    } finally {
        cargando.value = false;
    }
};

// Verificar si el modal se abre por primera vez y cargar responsables
watch(() => props.visible, (val) => {
    if (val) {
        console.log('watch ag')

        laboratorio.value = {
            nombre: '',
            codigo: '',
            descripcion: '',
            aforo: null,
            email: '',
            inauguracion: '',
            responsable_id: null,
        };

        // Cargar las opciones de los responsables
        opcionesResponsables.value = props.responsables.map(responsable => ({
            label: responsable.nombres,
            value: responsable.id,
        }));
    }
});
</script>
