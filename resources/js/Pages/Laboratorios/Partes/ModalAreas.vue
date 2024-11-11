<template>
    <!-- Modal de Tabla de Áreas -->
    <Modal
        :open="visible"
        :title="`Áreas del ${laboratorio.nombre}`"
        :footer="null"
        @cancel="cerrarmodal"
    >
        <div class="my-3">
            <Button type="primary" @click="abrirModalAgregarArea" size="middle">
                Agregar área
            </Button>
        </div>

        <Table
            :columns="columnasAreas"
            :dataSource="areas"
            :pagination="false"
            rowKey="id"
        />
    </Modal>

    <!-- Modal para Agregar Área -->
    <Modal
        :open="modalAgregarAreaVisible"
        title="Agregar área"
        :footer="null"
        @cancel="cerrarModalAgregarArea"
        @ok="guardarArea"
    >
    <Form layout="vertical" @finish="guardarArea" :model="nuevaArea">
            <FormItem label="Nombre" name="nombre" :rules="[{ required: true, message: 'Por favor ingrese el nombre' }]">
                <Input v-model:value="nuevaArea.nombre" placeholder="Ingrese el nombre del área" />
            </FormItem>

            <FormItem label="Descripción" name="descripcion">
                <Input.TextArea v-model:value="nuevaArea.descripcion" placeholder="Ingrese una descripción" auto-size />
            </FormItem>

            <FormItem label="Aforo" name="aforo">
                <InputNumber
                    v-model:value="nuevaArea.aforo"
                    placeholder="Ingrese el aforo"
                    style="width: 100%;"
                    type="number"
                    step="1"
                    min="0"
                />
            </FormItem>

            <FormItem class="flex justify-end mb-0">
                <Button style="margin-right: 8px" @click="cerrarModalAgregarArea">Cancelar</Button>
                <Button type="primary" htmlType="submit" :loading="cargando">Guardar</Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits, onMounted } from 'vue';
import { Modal, Table, Form, FormItem, Input, InputNumber, Button, message } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    laboratorio: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update:visible']);

// Columnas de la tabla, incluyendo "Aforo"
const columnasAreas = [
    { title: 'Nombre', dataIndex: 'nombre', key: 'nombre' },
    { title: 'Descripción', dataIndex: 'descripcion', key: 'descripcion' },
    { title: 'Aforo', dataIndex: 'aforo', key: 'aforo' },
];

const areas = ref([]);
const modalAgregarAreaVisible = ref(false);
const nuevaArea = ref({
    nombre: '',
    descripcion: '',
    aforo: null,
});

// Cierra el modal de la tabla de áreas
const cerrarmodal = () => {
    emit('update:visible', false);
};

// Función para abrir el modal de agregar área
const abrirModalAgregarArea = () => {
    modalAgregarAreaVisible.value = true;
    cerrarmodal();
};

// Cierra el modal de agregar área
const cerrarModalAgregarArea = () => {
    modalAgregarAreaVisible.value = false;
};

// Función para cargar las áreas
const cargarAreas = async () => {
    try {
        const response = await axios.get(`/laboratorios/${props.laboratorio.id}/areas`);
        areas.value = response.data;
    } catch (error) {
        console.error("Error al cargar las áreas:", error);
    }
};

// Función para guardar la nueva área
const guardarArea = async () => {
    try {
        await axios.post('/areas', {
            nombre: nuevaArea.value.nombre,
            descripcion: nuevaArea.value.descripcion,
            aforo: nuevaArea.value.aforo,
            laboratorio_id: props.laboratorio.id,
        });

        message.success('Área agregada exitosamente');
        cargarAreas();
        cerrarModalAgregarArea();

        nuevaArea.value = { nombre: '', descripcion: '', aforo: null };
    } catch (error) {
        console.error("Error al guardar el área:", error);
    }
};

// Cargar áreas cuando el laboratorio o el estado del modal cambian
watch(() => props.laboratorio, cargarAreas);
watch(() => props.visible, (isOpen) => {
    if (isOpen) cargarAreas();
});

onMounted(() => {
    if (props.visible) cargarAreas();
});
</script>

<style scoped>
.my-3 {
    margin-top: 1rem;
    margin-bottom: 1rem;
}
</style>
