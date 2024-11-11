<template>
    <!-- Modal de Tabla de Áreas -->
    <Modal
        :open="visible"
        :title="`Áreas del ${laboratorio.nombre}`"
        :footer="null"
        width="800px"
        @cancel="cerrarmodal"
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
            rowKey="id"
        >
            <template #bodyCell="{ column, text, record }">
                <template v-if="['nombre', 'descripcion', 'aforo'].includes(column.dataIndex)">
                    <div>
                        <Input
                            v-if="editableData[record.key]"
                            v-model:value="editableData[record.key][column.dataIndex]"
                            style="margin: -5px 0"
                            size="small"
                        />
                        <template v-else>
                            {{ text }}
                        </template>
                    </div>
                </template>
                <template v-else-if="column.dataIndex === 'operation'">
                    <div class="editable-row-operations">
                        <span v-if="editableData[record.key]">
                            <TypographyLink @click="save(record.key, record.id)">Guardar</TypographyLink>
                            <Popconfirm title="¿Seguro que quieres cancelar?" @confirm="cancel(record.key)">
                                <a>Cancelar</a>
                            </Popconfirm>
                        </span>
                        <span v-else>
                            <FormOutlined @click="edit(record.key)" class="text-blue-600">Editar</FormOutlined>
                        </span>
                    </div>
                </template>
            </template>
        </Table>
    </Modal>

    <!-- Modal para Agregar Área -->
    <Modal
        :open="modalAgregarVisible"
        title="Agregar área"
        :footer="null"
        @cancel="cerrarModalAgregar"
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
                <Button style="margin-right: 8px" @click="cerrarModalAgregar">Cancelar</Button>
                <Button type="primary" htmlType="submit" :loading="cargando">Guardar</Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits, onMounted } from 'vue';
import { Modal, Table, Form, FormItem, Input, InputNumber, Button, message, TypographyLink, Popconfirm } from 'ant-design-vue';
import { FormOutlined } from "@ant-design/icons-vue";
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
    { title: 'Aforo', dataIndex: 'aforo', key: 'aforo', width: 100 },
    { title: 'Acciones', dataIndex: 'operation', key: 'operation' },
];

const areas = ref([]);
const modalAgregarVisible = ref(false);
const nuevaArea = ref({
    nombre: '',
    descripcion: '',
    aforo: null,
});
const cargando = ref(false);
const editableData = ref({});

// Cierra el modal de la tabla de áreas
const cerrarmodal = () => {
    emit('update:visible', false);
};

// Función para abrir el modal de agregar área
const abrirModalAgregar = () => {
    modalAgregarVisible.value = true;
    cerrarmodal();
};

// Cierra el modal de agregar área y vuelve a abrir el modal de tabla de áreas
const cerrarModalAgregar = () => {
    modalAgregarVisible.value = false;
    emit('update:visible', true);
};

// Función para cargar las áreas
const cargarAreas = async () => {
    try {
        const response = await axios.get(`/laboratorios/${props.laboratorio.id}/areas`);
        areas.value = response.data.map((area, index) => ({
            key: index.toString(),
            ...area,
        }));
        console.log("Áreas cargadas:", areas.value);
    } catch (error) {
        console.error("Error al cargar las áreas:", error);
    }
};

// Funciones para la edición de filas
const edit = (key) => {
    editableData.value[key] = { ...areas.value.find(area => area.key === key) };
};

const save = async (key, areaId) => {
    const index = areas.value.findIndex(area => area.key === key);
    if (index !== -1 && editableData.value[key]) {
        try {
            await axios.put(`/areas/${areaId}`, {
                nombre: editableData.value[key].nombre,
                descripcion: editableData.value[key].descripcion,
                aforo: editableData.value[key].aforo,
            });
            areas.value[index] = { ...editableData.value[key] };
            delete editableData.value[key];
            message.success('Área actualizada exitosamente');
        } catch (error) {
            console.error("Error al actualizar el área:", error);
        }
    }
};

const cancel = (key) => {
    delete editableData.value[key];
};

// Registrar la nueva área
const guardarArea = async () => {
    cargando.value = true;
    try {
        const response = await axios.post('/areas', {
            nombre: nuevaArea.value.nombre,
            descripcion: nuevaArea.value.descripcion,
            aforo: nuevaArea.value.aforo,
            laboratorio_id: props.laboratorio.id,
        });

        message.success('Área agregada exitosamente');
        cargarAreas();
        cerrarModalAgregar();

        nuevaArea.value = { nombre: '', descripcion: '', aforo: null };
    } catch (error) {
        console.error("Error al guardar el área:", error);
    } finally {
        cargando.value = false;
    }
};

// Cargar las áreas la primera vez que el componente se monta
onMounted(() => {
    cargarAreas();
});

// Observador para cargar áreas cuando cambia el laboratorio
watch(
    () => props.laboratorio.id,
    (nuevoLaboratorioId, previoLaboratorioId) => {
        if (nuevoLaboratorioId !== previoLaboratorioId) {
            cargarAreas();
        }
    }
);

</script>

<style scoped>
.editable-row-operations a {
  margin-right: 8px;
}
</style>
