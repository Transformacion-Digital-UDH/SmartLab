<template>
    <Drawer
        title="Agregar equipo"
        :open="visible"
        @close="cerrarModal"
        placement="right"
        :footer="null"
        width="100&"
    >
        <Form layout="vertical" :model="equipo">
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

            <!-- Campo Área -->
            <FormItem label="Área" name="area_id" class="w-full">
                <Select
                    v-model:value="equipo.area_id"
                    placeholder="Seleccionar"
                    :options="opcionesAreas"
                    show-search
                    :filter-option="buscarArea"
                    allowClear
                />
            </FormItem>

            <!-- Transfer para asignar recursos -->
            <FormItem label="Recursos que componen este equipo" name="recursos">
                <Transfer
                    v-model:targetKeys="recursosAsignados"
                    v-model:value="equipo.recursos"
                    :data-source="listaRecursos"
                    :titles="['Disponibles', 'Asignados']"
                    show-search
                    @change="cambiarRecursos"
                    :list-style="{ width: '100%', height: '300px' }"
                    :locale="{
                        searchPlaceholder: 'Buscar aquí',
                        itemUnit: '',
                        itemsUnit: '',
                        notFoundContent: 'No hay datos disponibles'
                    }"
                >
                <template #render="item">
                    <div class="flex items-center gap-x-3">
                        <img
                            :src="`${item.foto}`"
                            alt="Foto del recurso"
                            class="w-8 h-8 object-cover rounded-sm"
                        />
                        <span>{{ item.codigo }} - {{ item.nombre }}</span>
                    </div>
                </template>
                </Transfer>
            </FormItem>


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


        </Form>

        <template #extra>
            <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
            <Button type="primary" v-on:click="enviarFormulario" :loading="cargando">Guardar</Button>
        </template>

    </Drawer>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { Modal, Form, FormItem, Input, Select, Button, message, Upload, Transfer, Drawer } from 'ant-design-vue';
import { PlusOutlined } from '@ant-design/icons-vue';
import axios, { formToJSON } from 'axios';

const props = defineProps({
    visible: Boolean,
    areas: Array,
    recursos: Array,
});

console.log(props);

const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

const equipo = ref({
    nombre: '',
    codigo: '',
    tipo: 'No reservable',
    estado: 'Activo',
    descripcion: '',
    area_id: null,
    is_active: true,
    recursos: [],
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

// Variables
const opcionesAreas = ref([]);
const listaRecursos = ref([]);
const recursosAsignados = ref([]);


const cerrarModal = () => {
    emitir('update:visible', false);
};

// Cambiar recursos asignados
const cambiarRecursos = (keys) => {
    recursosAsignados.value = keys;
};

const buscarArea = (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
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

    // Agregar los recursos asignados al FormData
    recursosAsignados.value.forEach((recursoId) => {
        formData.append('recursos[]', recursoId);
    });

    // Agregar las fotos al FormData
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

onMounted(() => {
    opcionesAreas.value = props.areas.map(area => ({
        label: area.nombre,
        value: area.id,
    }));

    listaRecursos.value = props.recursos.map((recurso) => ({
        key: recurso.id.toString(),
        codigo: recurso.codigo || 'Sin código',
        nombre: recurso.nombre,
        foto: recurso.fotos.length > 0 ? `/storage/${recurso.fotos[0].ruta}` : '/img/default-placeholder.webp',
    }));

    console.log(listaRecursos.value);
});

</script>
