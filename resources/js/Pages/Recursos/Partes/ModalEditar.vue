<template>
    <Modal
        title="Editar recurso"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="recurso">
            <!-- Campo Nombre -->
            <FormItem label="Nombre" name="nombre" :rules="[{ required: true, message: 'Por favor ingrese el nombre' }]">
                <Input v-model:value="recurso.nombre" placeholder="Ingrese el nombre" />
            </FormItem>

            <!-- Campo Código -->
            <FormItem label="Código" name="codigo">
                <Input v-model:value="recurso.codigo" placeholder="Ingrese el código" />
            </FormItem>

            <!-- Campo Tipo -->
            <FormItem label="Tipo" name="tipo" :rules="[{ required: true, message: 'Seleccione el tipo' }]">
                <Select v-model:value="recurso.tipo" placeholder="Seleccione el tipo" :options="opcionesTipo" />
            </FormItem>

            <!-- Campo Estado -->
            <FormItem label="Estado" name="estado" :rules="[{ required: true, message: 'Seleccione el estado' }]">
                <Select v-model:value="recurso.estado" placeholder="Seleccione el estado" :options="opcionesEstado" />
            </FormItem>

            <!-- Campo Descripción -->
            <FormItem label="Descripción" name="descripcion">
                <Input v-model:value="recurso.descripcion" placeholder="Ingrese una descripción" />
            </FormItem>

            <!-- Campo Área -->
            <FormItem label="Área" name="area_id" :rules="[{ required: false, message: 'Seleccione un área' }]">
                <Select
                    v-model:value="recurso.area_id"
                    placeholder="Seleccione un área"
                    :options="opcionesAreas"
                    show-search
                    :filter-option="buscarArea"
                />
            </FormItem>

            <!-- Campo Equipo -->
            <FormItem label="Equipo (opcional)" name="equipo_id">
                <Select
                    v-model:value="recurso.equipo_id"
                    placeholder="Seleccione un equipo"
                    :options="opcionesEquipos"
                    show-search
                    :filter-option="buscarEquipo"
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
import { ref, watch, defineProps, defineEmits } from 'vue';
import { Modal, Form, FormItem, Input, Select, Button, message } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    recurso: {
        type: Object,
        default: () => ({
            nombre: '',
            codigo: '',
            tipo: '',
            estado: '',
            descripcion: '',
            is_active: true,
            area_id: null,
            equipo_id: null,
        }),
    },
    areas: Array,
    equipos: Array,
});

const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

const recurso = ref({ ...props.recurso });
const cargando = ref(false);

const opcionesTipo = ref([
    { label: 'Reservable', value: 'Reservable' },
    { label: 'No reservable', value: 'No reservable' },
    { label: 'Suministro', value: 'Suministro' },
]);

const opcionesEstado = ref([
    { label: 'Activo', value: 'Activo' },
    { label: 'Inactivo', value: 'Inactivo' },
    { label: 'Reservado', value: 'Reservado' },
    { label: 'Prestado', value: 'Prestado' },
]);

const opcionesActivo = ref([
    { label: 'Sí', value: true },
    { label: 'No', value: false },
]);

const opcionesAreas = ref([]);
const opcionesEquipos = ref([]);

watch(() => props.visible, (val) => {
    if (val) {
        recurso.value = { ...props.recurso };
        opcionesAreas.value = props.areas.map(area => ({
            label: area.nombre,
            value: area.id,
        }));
        opcionesEquipos.value = props.equipos.map(equipo => ({
            label: equipo.nombre,
            value: equipo.id,
        }));
    }
});

const cerrarModal = () => {
    emitir('update:visible', false);
};

const buscarArea = (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};

const buscarEquipo = (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};

const enviarFormulario = async () => {
    cargando.value = true;
    try {
        const response = await axios.put(route('recursos.update', props.recurso.id), recurso.value);
        message.success('Recurso actualizado exitosamente');
        cerrarModal();
        emitir('actualizar-tabla', response.data["recurso"]);
    } catch (error) {
        message.error('Error al actualizar el recurso');
        console.error('Error al guardar el recurso:', error);
    } finally {
        cargando.value = false;
    }
};
</script>
