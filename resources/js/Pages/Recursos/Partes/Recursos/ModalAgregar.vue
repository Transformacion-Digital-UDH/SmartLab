<template>
    <Modal
        title="Agregar recurso"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="recurso">
            <!-- Campo Nombre -->
            <FormItem label="Nombre" name="nombre" :rules="[{ required: true, message: 'Por favor ingrese el nombre' }]">
                <Input v-model:value="recurso.nombre" placeholder="Ingrese el nombre" autofocus />
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
import { ref, watch, onMounted, defineProps, defineEmits } from 'vue';
import { Modal, Form, FormItem, Input, Select, Button, message } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    areas: Array,
    equipos: Array,
});

const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

const recurso = ref({
    nombre: '',
    codigo: '',
    tipo: '',
    estado: '',
    descripcion: '',
    is_active: true,
    area_id: null,
    equipo_id: null,
});

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

// Cierra el modal y emite el evento para cerrar en el componente padre
const cerrarModal = () => {
    emitir('update:visible', false);
};

const buscarArea = (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};

const buscarEquipo = (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};

// Envía el formulario de creación del recurso
const enviarFormulario = async () => {
    cargando.value = true;
    try {
        const response = await axios.post(route('recursos.store'), recurso.value);
        message.success('Recurso agregado exitosamente');
        cerrarModal();
        emitir('actualizar-tabla', response.data["recurso"]);
    } catch (error) {
        message.error('Error al agregar el recurso');
        console.error('Error al guardar el recurso:', error);
    } finally {
        cargando.value = false;
    }
};

// Verificar si el modal se abre por primera vez y cargar opciones
watch(() => props.visible, (val) => {
    if (val) {
        recurso.value = {
            nombre: '',
            codigo: '',
            tipo: '',
            estado: '',
            descripcion: '',
            is_active: true,
            area_id: null,
            equipo_id: null,
        };
    }
});

onMounted(() => {
    // Cargar las opciones de áreas y equipos
    opcionesAreas.value = props.areas.map(area => ({
        label: area.nombre,
        value: area.id,
    }));
    opcionesEquipos.value = props.equipos.map(equipo => ({
        label: equipo.nombre,
        value: equipo.id,
    }));
});

</script>
