<template>
    <Modal
        title="Agregar equipo"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
        width="650px"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="equipo">
            <div class="flex gap-x-3">
                <!-- Campo Código -->
                <FormItem label="Código" name="codigo" class="w-1/5">
                    <Input v-model:value="equipo.codigo" placeholder="Ingrese el código" />
                </FormItem>

                <!-- Campo Nombre -->
                <FormItem label="Nombre" name="nombre" class="w-4/5" :rules="[{ required: true, message: 'Por favor ingrese el nombre' }]">
                    <Input v-model:value="equipo.nombre" placeholder="Ingrese el nombre" />
                </FormItem>
            </div>

            <!-- Campo Descripción -->
            <FormItem label="Descripción" name="descripcion">
                <Input.TextArea v-model:value="equipo.descripcion" placeholder="Ingrese una descripción" />
            </FormItem>

            <div class="flex gap-x-3">
                <!-- Campo Tipo -->
                <FormItem label="Tipo" name="tipo" class="w-full" :rules="[{ required: true, message: 'Seleccione el tipo' }]">
                    <Select v-model:value="equipo.tipo" placeholder="Seleccione el tipo" :options="opcionesTipo" />
                </FormItem>

                <!-- Campo Estado -->
                <FormItem label="Estado actual" name="estado" class="w-full" :rules="[{ required: true, message: 'Seleccione el estado' }]">
                    <Select v-model:value="equipo.estado" placeholder="Seleccione el estado" :options="opcionesEstado" />
                </FormItem>
            </div>

            <!-- Fotos del equipo -->
            <FormItem label="Fotos del equipo">
                <Upload
                    list-type="picture-card"
                    :file-list="fileList"
                    @preview="manejarPrevisualizacion"
                    @remove="quitarFoto"
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
import { ref, watch } from 'vue';
import { Modal, Form, FormItem, Input, Select, Button, message, Upload } from 'ant-design-vue';
import { PlusOutlined } from '@ant-design/icons-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
});

const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

const equipo = ref({
    nombre: '',
    codigo: '',
    tipo: 'No reservable',
    estado: 'Activo',
    descripcion: '',
});

const cargando = ref(false);
const opcionesTipo = ref([
    { label: 'Reservable', value: 'Reservable' },
    { label: 'No reservable', value: 'No reservable' },
]);

const opcionesEstado = ref([
    { label: 'Activo', value: 'Activo' },
    { label: 'Inactivo', value: 'Inactivo' },
]);

const cerrarModal = () => {
    emitir('update:visible', false);
};

const fileList = ref([]);
const previewVisible = ref(false);
const previewImage = ref('');
const maxFiles = 5;

const manejarPrevisualizacion = (file) => {
    previewImage.value = file.url || file.thumbUrl;
    previewVisible.value = true;
};

const cerrarModalPrevisualizacion = () => {
    previewVisible.value = false;
};

const procesarFotoNueva = (file) => {
    fileList.value.push({
        uid: file.uid,
        name: file.name,
        status: 'done',
        originFileObj: file,
    });
    return false;
};

const quitarFoto = (file) => {
    fileList.value = fileList.value.filter((item) => item.uid !== file.uid);
};

const enviarFormulario = async () => {
    cargando.value = true;

    const formData = new FormData();
    Object.keys(equipo.value).forEach((key) => {
        formData.append(key, equipo.value[key] || '');
    });

    fileList.value.forEach((file) => {
        if (file.originFileObj) {
            formData.append('fotos[]', file.originFileObj);
        }
    });

    try {
        const response = await axios.post(route('equipos.store'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        message.success('Equipo agregado exitosamente');
        cerrarModal();
        emitir('actualizar-tabla', response.data);
    } catch (error) {
        message.error('Error al agregar el equipo');
        console.error('Error al guardar el equipo:', error);
    } finally {
        cargando.value = false;
    }
};

watch(() => props.visible, (val) => {
    if (val) {
        equipo.value = {
            nombre: '',
            codigo: '',
            tipo: '',
            estado: '',
            descripcion: '',
        };
    }
});
</script>
