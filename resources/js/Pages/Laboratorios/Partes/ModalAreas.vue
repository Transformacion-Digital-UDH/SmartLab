<!-- Partes/AreasModal.vue -->
<template>
    <Modal :open="open" title="Áreas del laboratorio" @cancel="cerrar">
        <Table :columns="columnasAreas" :dataSource="areas" rowKey="id" />
    </Modal>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits, onMounted } from 'vue';
import { Modal, Table } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    open: Boolean,
    laboratorio_id: {
        type: Number,
        required: true,
        
    },
});

const emit = defineEmits(['update:open', 'cerrar']);

// Columnas de la tabla
const columnasAreas = [
    { title: 'Nombre', dataIndex: 'nombre', key: 'nombre' },
    { title: 'Descripción', dataIndex: 'descripcion', key: 'descripcion' },
];

const areas = ref([]);

// Función para cerrar el modal
const cerrar = () => {
    emit('update:open', false);  // Emitir evento para cerrar el modal
    emit('cerrar');
};

// Función para cargar las áreas
const cargarAreas = async () => {
    try {
        // const response = await axios.get(`/api/laboratorios/${props.laboratorio_id}/areas`);
        // areas.value = response.data;

        console.log("Cargando áreas del laboratorio:", props.laboratorio_id);
    } catch (error) {
        console.error("Error al cargar las áreas:", error);
    }
};

// Cargar áreas cuando el laboratorio_id cambia o cuando el modal se abre
watch(() => props.laboratorio_id, cargarAreas);
watch(() => props.open, (isOpen) => {
    if (isOpen) cargarAreas();
});

onMounted(() => {
    if (props.open) cargarAreas();
});
</script>
