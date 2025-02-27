<template>
    <Modal
        title="Editar proyecto"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="proyecto">
            <FormItem label="Nombre" name="nombre" :rules="[{ required: true, message: 'Por favor ingrese el nombre' }]">
                <Input v-model:value="proyecto.nombre" placeholder="Ingrese el nombre del proyecto" />
            </FormItem>

            <FormItem label="Responsable" name="responsable_id">
                <Select
                    v-model:value="proyecto.responsable_id"
                    placeholder="Seleccione un responsable"
                    :options="opcionesResponsables"
                    show-search
                    :filter-option="buscarResponsable"
                />
            </FormItem>

            <FormItem label="Estado" name="estado" :rules="[{ required: true, message: 'Por favor seleccione un estado' }]">
                <Select v-model:value="proyecto.estado" placeholder="Seleccione un estado">
                    <Select.Option value="Sin iniciar">Sin iniciar</Select.Option>
                    <Select.Option value="En proceso">En proceso</Select.Option>
                    <Select.Option value="Completado">Completado</Select.Option>
                    <Select.Option value="Cancelado">Cancelado</Select.Option>
                </Select>
            </FormItem>

            <FormItem label="Descripción" name="descripcion">
                <Textarea v-model:value="proyecto.descripcion" placeholder="Ingrese una descripción" auto-size />
            </FormItem>

            <FormItem label="Fecha de inicio" name="fecha_inicio">
                <Input type="date" v-model:value="proyecto.fecha_inicio" style="width: 100%;" />
            </FormItem>

            <FormItem label="Fecha de finalización" name="fecha_fin">
                <Input type="date" v-model:value="proyecto.fecha_fin" style="width: 100%;" />
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
import { Modal, Form, FormItem, Input, Select, Button, Textarea, message } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    responsables: Array,
    proyecto: {
        type: Object,
        default: () => ({
            nombre: '',
            descripcion: '',
            fecha_inicio: null,
            fecha_fin: null,
            responsable_id: '',
            estado: 'Sin iniciar',
        }),
    },
});

const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

const proyecto = ref({ ...props.proyecto });
const cargando = ref(false);
const opcionesResponsables = ref([]);

opcionesResponsables.value = props.responsables.map(responsable => ({
    label: `${responsable.dni} - ${responsable.nombres} ${responsable.apellidos} - ${responsable.email}`,
    value: responsable.id,
}));

// Cierra el modal y emite el evento para cerrar en el componente padre
const cerrarModal = () => {
    emitir('update:visible', false);
};

// Busca responsables en el select
const buscarResponsable = (input, option) => {
    return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
};

// Envía el formulario de edición del proyecto
const enviarFormulario = async () => {
    cargando.value = true;
    try {
        // Enviar la solicitud para actualizar el proyecto
        const response = await axios.put(route('proyectos.update', props.proyecto.id), proyecto.value);
        message.success('Proyecto actualizado exitosamente');
        cerrarModal();
        emitir('actualizar-tabla', response.data["proyecto"]);
    } catch (error) {
        message.error('Error al actualizar el proyecto');
    } finally {
        cargando.value = false;
    }
};

// Verificar si el modal se abre y cargar los valores del proyecto
watch(() => props.visible, (val) => {
    if (val) {
        proyecto.value = { ...props.proyecto };
    }
});
</script>
