<template>
    <Modal
    title="Editar laboratorio"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="laboratorio">
            <FormItem label="Nombre" name="nombre" :rules="[{ required: true, message: 'Por favor ingrese el nombre' }]">
                <Input v-model:value="laboratorio.nombre" placeholder="Ingrese el nombre" />
            </FormItem>

            <FormItem label="Responsable" name="responsable_id">
                <Select
                    v-model:value="laboratorio.responsable.nombres"
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
                <Textarea v-model:value="laboratorio.descripcion" placeholder="Ingrese una descripción" auto-size />
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
                <Input type="date" v-model:value="laboratorio.inauguracion" style="width: 100%;" />
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
import { Modal, Form, FormItem, Input, Select, InputNumber, Button, Textarea, message } from 'ant-design-vue';
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
console.log(opcionesResponsables.value);

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
        laboratorio.value = { ...props.laboratorio };
    }

});

</script>
