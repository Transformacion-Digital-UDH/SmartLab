<template>
    <Drawer
        title="Editar equipo"
        :open="visible"
        @close="cerrarModal"
        placement="right"
        :footer="null"
        width="100&"
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
                <p class="text-xs text-gray-400 mb-4">Los recursos en naranja ya pertenecen a un equipo. Si lo asigna a este equipo, se quitará del otro.</p>
                <Transfer
                    v-model:targetKeys="recursosAsignados"
                    v-model:value="equipo.recursos"
                    :data-source="listaRecursos"
                    :titles="[' disponibles', ' asignados']"
                    show-search
                    @change="cambiarRecursos"
                    :filter-option="filtrarRecursos"
                    :list-style="{ width: '100%', height: '300px' }"
                    :locale="{
                        searchPlaceholder: 'Buscar aquí',
                        itemUnit: '',
                        itemsUnit: '',
                        notFoundContent: 'No hay datos disponibles'
                    }"
                >
                    <template #render="item">
                        <div
                            class="flex items-center gap-x-3 max-w-12"
                            :class="{'text-amber-500': item.equipo_id !== null, '': item.equipo_id === null}"
                        >
                            <img
                                :src="`${item.foto}`"
                                alt="Foto del recurso"
                                class="w-8 h-8 object-cover rounded-sm"
                            />
                            <div class="flex flex-col w-full">
                                <small class="text-xs font-medium">
                                    {{ item.codigo }}
                                </small>
                                <small>{{ item.nombre }}</small>
                            </div>
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
        </Form>

        <template #extra>
            <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
            <Button type="primary" v-on:click="enviarFormulario" :loading="cargando">Guardar</Button>
        </template>
    </Drawer>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { Modal, Drawer, Transfer, Form, FormItem, Input, Select, Button, message, Upload } from 'ant-design-vue';
import { PlusOutlined } from '@ant-design/icons-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    equipo: Object,
    areas: Array,
    recursos: Array,
});

const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

const equipo = ref({ ...props.equipo });
const cargando = ref(false);

console.log(equipo.value);

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
const listaRecursos = ref([]);
const recursosAsignados = ref([]);

// Cambiar recursos asignados
const cambiarRecursos = (keys) => {
    recursosAsignados.value = keys;
    equipo.value.recursos = keys.map(key => ({
        id: key,
        nombre: listaRecursos.value.find(item => item.key === key)?.nombre
    }));
};

const filtrarRecursos = (inputValue, option) => {
    return (
        option.codigo.toLowerCase().includes(inputValue.toLowerCase()) ||
        option.nombre.toLowerCase().includes(inputValue.toLowerCase())
    );
};


const cerrarModal = () => {
    emitir('update:visible', false);
};

const buscarArea = (input, option) => {
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
    return false; // Evita la subida automática
};

const cargarFotos = () => {
    fileList.value = equipo.value.fotos.map((foto) => ({
        uid: foto.id,
        name: foto.nombre,
        status: 'done',
        url: `/storage/${foto.ruta}`,
    }));
};

const marcarFotoEliminada = (file) => {
    if (file.uid && Number.isInteger(file.uid)) {
        fotosEliminadas.value.push(file.uid);
    }
    fileList.value = fileList.value.filter((item) => item.uid !== file.uid);
};

const enviarFormulario = async () => {
    cargando.value = true;

    const formData = new FormData();
    formData.append('_method', 'PUT');
    Object.keys(equipo.value).forEach((key) => {
        formData.append(key, equipo.value[key] || '');
    });

    // Agregar los recursos asignados al FormData
        recursosAsignados.value.forEach((recursoId) => {
        formData.append('recursos[]', recursoId);
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
        const { data } = await axios.post(route('equipos.update', equipo.value.id), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        message.success('Equipo actualizado exitosamente');
        cerrarModal();
        emitir('actualizar-tabla', data.equipo);
    } catch (error) {
        console.error(error);
        message.error('Error al actualizar el equipo');
    } finally {
        cargando.value = false;
    }
};

watch(() => props.visible, (val) => {
    if (val) {
        equipo.value = { ...props.equipo };
    }
});

onMounted(() => {
    opcionesAreas.value = props.areas.map(area => ({
        label: area.nombre,
        value: area.id,
    }));

    listaRecursos.value = props.recursos.map((recurso) => ({
        key: recurso.id.toString(),
        codigo: recurso.codigo || "Sin código",
        nombre: recurso.nombre,
        equipo_id: recurso.equipo_id || null,
        foto: recurso.fotos.length > 0 ? `/storage/${recurso.fotos[0].ruta}` : '/img/default-placeholder.webp',
    }));

    console.log("Recursos", listaRecursos.value);

    // Cargar recursos asignados al equipo al montar el componente
    recursosAsignados.value = props.equipo.recursos.map(recurso => recurso.id.toString());

    cargarFotos();
});

watch(() => props.visible, (val) => {
    if (val) {
        equipo.value = { ...props.equipo };

        // Actualizar recursos asignados al abrir el Drawer
        recursosAsignados.value = props.equipo.recursos.map(recurso => recurso.id.toString());
    }
});
</script>
