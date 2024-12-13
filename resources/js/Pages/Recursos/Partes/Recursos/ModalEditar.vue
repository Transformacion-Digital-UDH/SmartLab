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
            <FormItem label="Área" name="area_id">
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

            <!-- Cargar fotos del recurso -->
            <FormItem label="Fotos del recurso">
                <Upload
                    list-type="picture-card"
                    :file-list="fileList"
                    @preview="manejarPrevisualizacion"
                    @change="manejarCambio"
                    :before-upload="() => false"
                >
                    <template #default>
                        <div v-if="fileList.length < maxFiles">
                            <PlusOutlined />
                            <div style="margin-top: 8px">Subir</div>
                        </div>
                    </template>
                </Upload>
                <Modal
                    :open="previewVisible"
                    title="Vista previa"
                    :footer="null"
                    @cancel="manejarCancelacion"
                >
                    <img alt="Vista previa" style="width: 100%" :src="previewImage" />
                </Modal>
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
import { Modal, Form, FormItem, Input, Select, Button, message, Upload } from 'ant-design-vue';
import { PlusOutlined } from '@ant-design/icons-vue';
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
            fotos: [],
        }),
    },
    areas: Array,
    equipos: Array,
});

console.log('Recurso:', props.recurso);
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

const opcionesAreas = ref([]);
const opcionesEquipos = ref([]);

const cerrarModal = () => {
    emitir('update:visible', false);
};

const buscarArea = (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};

const buscarEquipo = (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};

const fileList = ref([]);
const previewVisible = ref(false);
const previewImage = ref('');
const maxFiles = 5; // Máximo de fotos permitidas
const fotosEliminadas = ref([]);

const manejarPrevisualizacion = (file) => {
    previewImage.value = file.url || file.thumbUrl;
    previewVisible.value = true;
};

const manejarCancelacion = () => {
    previewVisible.value = false;
};

const manejarCambio = ({ fileList: newFileList }) => {
    fileList.value = newFileList;
    console.log('Nueva lista de archivos:', fileList.value);
};

const cargarFotos = () => {
    if (recurso.value.fotos && recurso.value.fotos.length > 0) {
        fileList.value = recurso.value.fotos.map((foto) => ({
            uid: foto,  // Asumiendo que las rutas son únicas
            name: foto,
            status: 'done',
            url: `/storage/${foto.ruta}`,
        }));
    }
    console.log('Fotos:', fileList.value);
};


const enviarFormulario = async () => {
    cargando.value = true;
    try {
        // Crear un FormData para incluir archivos y datos
        const formData = new FormData();

        // Emular el método PUT con '_method'
        formData.append('_method', 'PUT');

        // Agregar datos del recurso
        Object.keys(recurso.value).forEach((key) => {
            const value = recurso.value[key];
            formData.append(key, value !== null && value !== undefined && value !== '' ? value : null);
        });

        // Agregar rutas de fotos existentes
        recurso.value.fotos.forEach((foto) => {
            formData.append('fotos[]', foto);
        });

        // Agregar imágenes
        fileList.value.forEach((file) => {
            formData.append('fotos[]', file.originFileObj || file);
        });

        // Enviar solicitud con el método POST, pero con '_method' indicando PUT
        const response = await axios.post(route('recursos.update', props.recurso.id), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });

        // Mostrar mensaje de éxito
        message.success('Recurso actualizado exitosamente');
        cerrarModal();
        emitir('actualizar-tabla', response.data.recurso);
    } catch (error) {
        // Manejar errores
        message.error('Error al actualizar el recurso');
        console.error('Error al guardar el recurso:', error);
    } finally {
        // Restablecer el estado de carga
        cargando.value = false;
    }
};





watch(() => props.visible, (val) => {
    if (val) {
        recurso.value = { ...props.recurso };
    }
});

onMounted(() => {
    opcionesAreas.value = props.areas.map(area => ({
        label: area.nombre,
        value: area.id,
    }));
    opcionesEquipos.value = props.equipos.map(equipo => ({
        label: equipo.nombre,
        value: equipo.id,
    }));

    cargarFotos();
});

</script>
