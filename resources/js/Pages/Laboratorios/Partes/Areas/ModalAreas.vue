<template>
    <Modal
        :open="visible"
        :title="`Áreas del ${laboratorio.nombre}`"
        :footer="null"
        width="800px"
        @cancel="cerrarModal"
    >
        <div class="my-3">
            <Button type="primary" @click="abrirModalAgregar" size="middle">
                Agregar área
            </Button>
        </div>

        <Table
            :columns="columnasAreas"
            :dataSource="areas"
            :pagination="false"
            :scroll="{ x: 500 }"
            rowKey="id"
        >
            <template #bodyCell="{ column, text, record }">
                <template v-if="['nombre', 'descripcion', 'aforo'].includes(column.dataIndex)">
                    <div>
                        <Input
                            v-if="datosEditables[record.key]"
                            v-model:value="datosEditables[record.key][column.dataIndex]"
                            style="margin: -5px 0"
                            size="small"
                        />
                        <template v-else>
                            {{ text }}
                        </template>
                    </div>
                </template>
                <template v-else-if="column.dataIndex === 'operation'">
                    <div>
                        <span v-if="datosEditables[record.key]">
                            <CheckOutlined @click="guardar(record.key, record.id)" class="text-green-600 mr-2" />
                            <CloseOutlined @click="cancelar(record.key)" class="text-red-600" />
                        </span>
                        <span v-else>
                            <FormOutlined @click="editar(record.key)" class="text-blue-600 mr-2" />
                            <Popconfirm
                                title="Confirmar acción"
                                okText="Sí"
                                cancelText="No"
                                @confirm="eliminarArea(record.id)"
                            >
                                <DeleteOutlined class="text-red-600" />
                            </Popconfirm>
                        </span>
                    </div>
                </template>
            </template>
        </Table>

        <AgregarArea
            :visible="modalAgregarVisible"
            :laboratorio_id="laboratorio.id"
            @close="cerrarModalAgregar"
            @areaGuardada="cargarAreas"
        />
    </Modal>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits, onMounted } from 'vue';
import { Modal, Table, Input, Button, message, Popconfirm } from 'ant-design-vue';
import { FormOutlined, DeleteOutlined, CheckOutlined, CloseOutlined } from "@ant-design/icons-vue";
import axios from 'axios';
import AgregarArea from './AgregarArea.vue';

const props = defineProps({
    visible: Boolean,
    laboratorio: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update:visible']);

// Columnas de la tabla
const columnasAreas = [
    { title: 'Nombre', dataIndex: 'nombre', key: 'nombre' },
    { title: 'Descripción', dataIndex: 'descripcion', key: 'descripcion' },
    { title: 'Aforo', dataIndex: 'aforo', key: 'aforo', width: 80 },
    { title: 'Acciones', dataIndex: 'operation', key: 'operation', fixed: 'right', width: 100 },
];

const areas = ref([]);
const modalAgregarVisible = ref(false);
const datosEditables = ref({});

const cerrarModal = () => {
    emit('update:visible', false);
};

const abrirModalAgregar = () => {
    modalAgregarVisible.value = true;
    cerrarModal();
};

const cerrarModalAgregar = () => {
    modalAgregarVisible.value = false;
    emit('update:visible', true);
};

const cargarAreas = async () => {
    try {
        const response = await axios.get(route('areas.index', { laboratorio_id: props.laboratorio.id }));
        areas.value = response.data.map((area, index) => ({
            key: index.toString(),
            ...area,
        }));
    } catch (error) {
        console.error("Error al cargar las áreas:", error);
    }
};

const editar = (key) => {
    datosEditables.value[key] = { ...areas.value.find(area => area.key === key) };
};

const guardar = async (key, areaId) => {
    const index = areas.value.findIndex(area => area.key === key);
    if (index !== -1 && datosEditables.value[key]) {
        try {
            await axios.put(route('areas.update', { area_id: areaId }), {
                nombre: datosEditables.value[key].nombre,
                descripcion: datosEditables.value[key].descripcion,
                aforo: datosEditables.value[key].aforo,
            });
            areas.value[index] = { ...datosEditables.value[key] };
            delete datosEditables.value[key];
            message.success('Área actualizada exitosamente');
        } catch (error) {
            console.error("Error al actualizar el área:", error);
        }
    }
};

const cancelar = (key) => {
    delete datosEditables.value[key];
};

const eliminarArea = async (areaId) => {
    try {
        await axios.delete(route('areas.destroy', { area_id: areaId }));
        message.success('Área eliminada exitosamente');
        cargarAreas();
    } catch (error) {
        console.error("Error al eliminar el área:", error);
    }
};

onMounted(() => {
    cargarAreas();
});

watch(
    () => props.laboratorio.id,
    (nuevoLaboratorioId, previoLaboratorioId) => {
        if (nuevoLaboratorioId !== previoLaboratorioId) {
            cargarAreas();
        }
    }
);

</script>

