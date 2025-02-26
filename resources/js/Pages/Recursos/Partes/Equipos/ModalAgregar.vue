<template>
    <Drawer
        title="Agregar equipo"
        :open="visible"
        @close="cerrarModal"
        placement="right"
        :footer="null"
        width="100&"
    >
        <Form
            layout="vertical"
            @finish="enviarFormulario"
            :model="equipo"
        >
            <div class="flex gap-x-3">
                <FormItem label="Código *" name="codigo" class="w-1/5">
                    <Input
                        v-model:value="equipo.codigo"
                        placeholder="Ingrese el código"
                        autocomplete="off"
                    />
                    <InputError :message="errors.codigo?.[0]" />
                </FormItem>

                <FormItem label="Nombre *" name="nombre" class="w-4/5">
                    <Input
                        v-model:value="equipo.nombre"
                        placeholder="Ingrese el nombre"
                        autocomplete="off"
                    />
                    <InputError :message="errors.nombre?.[0]" />
                </FormItem>
            </div>

            <FormItem label="Descripción" name="descripcion">
                <Input.TextArea
                    v-model:value="equipo.descripcion"
                    placeholder="Ingrese una descripción"
                />
                <InputError :message="errors.descripcion?.[0]" />
            </FormItem>

            <div class="flex gap-x-3">
                <FormItem label="Tipo" name="tipo" class="w-full">
                    <Select
                        v-model:value="equipo.tipo"
                        placeholder="Seleccione el tipo"
                        :options="opcionesTipo"
                    />
                    <InputError :message="errors.tipo?.[0]" />
                </FormItem>

                <FormItem label="Estado actual" name="estado" class="w-full">
                    <Select
                        v-model:value="equipo.estado"
                        placeholder="Seleccione el estado"
                        :options="opcionesEstado"
                    />
                    <InputError :message="errors.estado?.[0]" />
                </FormItem>
            </div>

            <FormItem label="Área *" name="area_id" class="w-full">
                <Select
                    v-model:value="equipo.area_id"
                    placeholder="Seleccionar"
                    :options="opcionesAreas"
                    show-search
                    :filter-option="buscarArea"
                    allowClear
                />
                <InputError :message="errors.area_id?.[0]" />
            </FormItem>

            <FormItem label="Recursos que componen este equipo" name="recursos">
                <p class="text-xs text-gray-400 mb-4">Los recursos en naranja ya pertenecen a un equipo. Si lo asigna a este equipo, se quitará del otro.</p>
                <Transfer
                    v-model:targetKeys="recursosAsignados"
                    v-model:value="equipo.recursos"
                    :data-source="listaRecursos"
                    :render="renderRecurso"
                    :titles="[' disponibles', ' asignados']"
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
                        <div class="mt-2">Subir</div>
                    </div>
                </Upload>
                <Modal
                    :open="previewVisible"
                    title="Vista previa"
                    :footer="null"
                    @cancel="cerrarModalPrevisualizacion"
                >
                    <img
                        alt="Vista previa"
                        class="w-full"
                        :src="previewImage"
                    />
                </Modal>
            </FormItem>
        </Form>

        <template #extra>
            <Button style="margin-right: 8px" @click="cerrarModal"
                >Cancelar</Button
            >
            <Button
                type="primary"
                v-on:click="enviarFormulario"
                :loading="cargando"
                >Guardar</Button
            >
        </template>
    </Drawer>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import InputError from "@/Components/Inputs/InputError.vue";
import { PlusOutlined } from "@ant-design/icons-vue";
import axios from "axios";
import {
    Modal,
    Drawer,
    Form,
    FormItem,
    Input,
    Select,
    Button,
    message,
    Upload,
    Transfer,
} from "ant-design-vue";

const props = defineProps({
    visible: Boolean,
    areas: Array,
    recursos: Array,
});

const emitir = defineEmits(["update:visible", "actualizar-tabla"]);

const equipo = ref({
    nombre: "",
    codigo: "",
    tipo: "No reservable",
    estado: "Activo",
    descripcion: "",
    area_id: null,
    is_active: true,
    recursos: [],
});

const cargando = ref(false);
const errors = ref({});

const opcionesTipo = ref([
    { label: "Reservable", value: "Reservable" },
    { label: "No reservable", value: "No reservable" },
]);

const opcionesEstado = ref([
    { label: "Activo", value: "Activo" },
    { label: "Inactivo", value: "Inactivo" },
]);

// Variables
const opcionesAreas = ref([]);
const listaRecursos = ref([]);
const recursosAsignados = ref([]);

const cerrarModal = () => {
    emitir("update:visible", false);
};

// Cambiar recursos asignados
const cambiarRecursos = (keys) => {
    recursosAsignados.value = keys;
};

const filtrarRecursos = (inputValue, option) => {
    return (
        option.codigo.toLowerCase().includes(inputValue.toLowerCase()) ||
        option.nombre.toLowerCase().includes(inputValue.toLowerCase())
    );
};

const buscarArea = (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};

const fileList = ref([]);
const previewVisible = ref(false);
const previewImage = ref("");
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
        status: "done",
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
        formData.append(key, equipo.value[key] || "");
    });

    // Agregar los recursos asignados al FormData
    recursosAsignados.value.forEach((recursoId) => {
        formData.append("recursos[]", recursoId);
    });

    // Agregar las fotos al FormData
    fileList.value.forEach((file) => {
        if (file.originFileObj) {
            formData.append("fotos[]", file.originFileObj);
        }
    });

    try {
        const response = await axios.post(route("equipos.store"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        message.success("Equipo agregado exitosamente");
        cerrarModal();
        emitir("actualizar-tabla", response.data);
    } catch (error) {
        message.error("Error al agregar el equipo");
        if (error.response && error.response.data.errors) {
            errors.value = error.response.data.errors;
        }
    } finally {
        cargando.value = false;
    }
};

watch(
    () => props.visible,
    (val) => {
        if (val) {
            equipo.value = {
                nombre: "",
                codigo: "",
                tipo: "",
                estado: "",
                descripcion: "",
            };
        }
    }
);

onMounted(() => {
    opcionesAreas.value = props.areas.map((area) => ({
        label: `${area.nombre} (${area.laboratorio.codigo})`,
        value: area.id,
    }));

    listaRecursos.value = props.recursos.map((recurso) => ({
        key: recurso.id.toString(),
        codigo: recurso.codigo || "Sin código",
        nombre: recurso.nombre,
        equipo_id: recurso.equipo_id || null,
        foto:
            recurso.fotos.length > 0
                ? `/storage/${recurso.fotos[0].ruta}`
                : "/img/default-placeholder.webp",
    }));
});
</script>
