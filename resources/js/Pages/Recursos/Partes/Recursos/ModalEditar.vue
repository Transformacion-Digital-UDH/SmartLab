<template>
    <Modal
        title="Editar recurso"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
        width="650px"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="recurso">
            <div class="flex gap-x-3">
                <!-- Campo Código -->
                <FormItem label="Código" name="codigo" class="w-1/5">
                    <Input v-model:value="recurso.codigo" placeholder="Ingrese el código" />
                </FormItem>

                <!-- Campo Nombre -->
                <FormItem label="Nombre" name="nombre" class="w-4/5" :rules="[{ required: true, message: 'Por favor ingrese el nombre' }]">
                    <Input v-model:value="recurso.nombre" placeholder="Ingrese el nombre" />
                </FormItem>
            </div>

            <!-- Campo Descripción -->
            <FormItem label="Descripción" name="descripcion">
                <Input.TextArea v-model:value="recurso.descripcion" placeholder="Ingrese una descripción" />
            </FormItem>

            <div class="flex gap-x-3">
                <!-- Campo Tipo -->
                <FormItem label="Tipo" name="tipo" class="w-full" :rules="[{ required: true, message: 'Seleccione el tipo' }]">
                    <Select v-model:value="recurso.tipo" placeholder="Seleccione el tipo" :options="opcionesTipo" />
                </FormItem>

                <!-- Campo Estado -->
                <FormItem label="Estado actual" name="estado" class="w-full" :rules="[{ required: true, message: 'Seleccione el estado' }]">
                    <Select v-model:value="recurso.estado" placeholder="Seleccione el estado" :options="opcionesEstado" />
                </FormItem>
            </div>

            <div class="flex gap-x-3">
                <!-- Campo Área -->
                <FormItem label="Área" name="area_id" class="w-full">
                    <Select
                        v-model:value="recurso.area_id"
                        placeholder="Seleccionar"
                        :options="opcionesAreas"
                        show-search
                        :filter-option="buscarArea"
                        allowClear
                    />
                </FormItem>

                <!-- Campo Equipo -->
                <FormItem label="Equipo (opcional)" name="equipo_id" class="w-full">
                    <Select
                        v-model:value="recurso.equipo_id"
                        placeholder="Seleccionar"
                        :options="opcionesEquipos"
                        show-search
                        :filter-option="buscarEquipo"
                        allowClear
                    />
                </FormItem>
            </div>

            <!-- Fotos del recurso -->
            <FormItem label="Fotos del recurso">
                <Upload
                    list-type="picture-card"
                    :file-list="fileList"
                    @preview="manejarPrevisualizacion"
                    @remove="marcarFotoEliminada"
                    :before-upload="procesarFotoNueva"
                    :multiple="true"
                >
                    <div v-if="fileList.length < maxFiles">
                        <PlusOutlined />
                        <div style="margin-top: 8px">Subir</div>
                    </div>
                </Upload>
                <Modal
                    :open="previewVisible"
                    title="Vista previa"
                    :footer="null"
                    @cancel="cerrarModalPrevisualizacion"
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
import { ref, watch, onMounted } from 'vue';
import { Modal, Form, FormItem, Input, Select, Button, message, Upload } from 'ant-design-vue';
import { PlusOutlined } from '@ant-design/icons-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    recurso: Object,
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

// Cerrar el modal de previsualización
const cerrarModalPrevisualizacion = () => {
    previewVisible.value = false;
};

// Procesar foto nueva antes de subir
const procesarFotoNueva = (file) => {
    fileList.value.push({
        uid: file.uid,
        name: file.name,
        status: 'done',
        originFileObj: file,
    });
    return false; // Evita la subida automática
};

// Cargar fotos existentes en el recurso
const cargarFotos = () => {
    fileList.value = recurso.value.fotos.map((foto) => ({
        uid: foto.id,
        name: foto.nombre,
        status: 'done',
        url: `/storage/${foto.ruta}`,
    }));
};

// Marcar una foto como eliminada
const marcarFotoEliminada = (file) => {
    if (file.uid && Number.isInteger(file.uid)) {
        fotosEliminadas.value.push(file.uid);
    }
    fileList.value = fileList.value.filter((item) => item.uid !== file.uid);
};

// Enviar formulario al backend
const enviarFormulario = async () => {
    cargando.value = true;

    const formData = new FormData();
    formData.append('_method', 'PUT');
    Object.keys(recurso.value).forEach((key) => {
        formData.append(key, recurso.value[key] || '');
    });

    // Agregar fotos eliminadas
    fotosEliminadas.value.forEach((id) => formData.append('fotos_eliminadas[]', id));

    // Agregar fotos nuevas
    fileList.value.forEach((file) => {
        if (file.originFileObj) {
            formData.append('fotos_nuevas[]', file.originFileObj);
        }
    });

    try {
        const { data } = await axios.post(route('recursos.update', recurso.value.id), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        message.success('Recurso actualizado exitosamente');
        cerrarModal();
        emitir('actualizar-tabla', data.recurso);
    } catch (error) {
        console.error(error);
        message.error('Error al actualizar el recurso');
    } finally {
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
