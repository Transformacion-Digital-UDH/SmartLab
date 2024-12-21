<template>
    <Modal
        title="Editar recurso"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
        width="650px"
    >
        <Form
            layout="vertical"
            @finish="enviarFormulario"
            :model="recurso"
            class="mt-4"
        >
            <div class="flex gap-x-3">
                <FormItem label="Código *" name="codigo" class="w-2/5">
                    <Input
                        v-model:value="recurso.codigo"
                        placeholder="Ingrese el código"
                        autocomplete="off"
                    />
                    <InputError :message="errors.codigo?.[0]" />
                </FormItem>

                <FormItem label="Nombre *" name="nombre" class="w-3/5">
                    <Input
                        v-model:value="recurso.nombre"
                        placeholder="Ingrese el nombre"
                        autocomplete="off"
                    />
                    <InputError :message="errors.nombre?.[0]" />
                </FormItem>
            </div>

            <FormItem label="Descripción" name="descripcion">
                <Input.TextArea
                    v-model:value="recurso.descripcion"
                    placeholder="Ingrese una descripción"
                />
                <InputError :message="errors.descripcion?.[0]" />
            </FormItem>

            <div class="flex gap-x-3">
                <FormItem label="Tipo" name="tipo" class="w-full">
                    <Select
                        v-model:value="recurso.tipo"
                        placeholder="Seleccione el tipo"
                        :options="opcionesTipo"
                    />
                    <InputError :message="errors.tipo?.[0]" />
                </FormItem>

                <FormItem label="Estado actual" name="estado" class="w-full">
                    <Select
                        v-model:value="recurso.estado"
                        placeholder="Seleccione el estado"
                        :options="opcionesEstado"
                    />
                    <InputError :message="errors.estado?.[0]" />
                </FormItem>
            </div>

            <div class="flex gap-x-3">
                <FormItem label="Área" name="area_id" class="w-full">
                    <Select
                        v-model:value="recurso.area_id"
                        placeholder="Seleccionar"
                        :options="opcionesAreas"
                        show-search
                        :filter-option="buscarArea"
                        allowClear
                    />
                    <InputError :message="errors.area_id?.[0]" />
                </FormItem>

                <FormItem
                    label="Equipo (opcional)"
                    name="equipo_id"
                    class="w-full"
                >
                    <Select
                        v-model:value="recurso.equipo_id"
                        placeholder="Seleccionar"
                        :options="opcionesEquipos"
                        show-search
                        :filter-option="buscarEquipo"
                        allowClear
                    />
                    <InputError :message="errors.equipo_id?.[0]" />
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

            <FormItem class="flex justify-end mb-0">
                <Button class="mr-3" @click="cerrarModal"> Cancelar </Button>
                <Button type="primary" htmlType="submit" :loading="cargando">
                    Actualizar
                </Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import InputError from "@/Components/Inputs/InputError.vue";
import { PlusOutlined } from "@ant-design/icons-vue";
import axios from "axios";
import {
    Modal,
    Form,
    FormItem,
    Input,
    Select,
    Button,
    message,
    Upload,
} from "ant-design-vue";

const props = defineProps({
    visible: Boolean,
    recurso: Object,
    areas: Array,
    equipos: Array,
});

const emitir = defineEmits(["update:visible", "actualizar-tabla"]);

const recurso = ref({ ...props.recurso });
const cargando = ref(false);

const opcionesTipo = ref([
    { label: "Reservable", value: "Reservable" },
    { label: "No reservable", value: "No reservable" },
    { label: "Suministro", value: "Suministro" },
]);

const opcionesEstado = ref([
    { label: "Activo", value: "Activo" },
    { label: "Inactivo", value: "Inactivo" },
    { label: "Reservado", value: "Reservado" },
    { label: "Prestado", value: "Prestado" },
]);

const opcionesAreas = ref([]);
const opcionesEquipos = ref([]);

const cerrarModal = () => {
    recurso.value = {};
    errors.value = {};
    emitir("update:visible", false);
};

const buscarArea = (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};

const buscarEquipo = (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};

const fileList = ref([]);
const previewVisible = ref(false);
const previewImage = ref("");
const maxFiles = 5; // Máximo de fotos permitidas
const fotosEliminadas = ref([]);
const errors = ref({});

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
        status: "done",
        originFileObj: file,
    });
    return false; // Evita la subida automática
};

// Cargar fotos existentes en el recurso
const cargarFotos = () => {
    fileList.value = recurso.value.fotos.map((foto) => ({
        uid: foto.id,
        name: foto.nombre,
        status: "done",
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
    formData.append("_method", "PUT");
    Object.keys(recurso.value).forEach((key) => {
        formData.append(key, recurso.value[key] || "");
    });

    // Agregar fotos eliminadas
    fotosEliminadas.value.forEach((id) =>
        formData.append("fotos_eliminadas[]", id)
    );

    // Agregar fotos nuevas
    fileList.value.forEach((file) => {
        if (file.originFileObj) {
            formData.append("fotos_nuevas[]", file.originFileObj);
        }
    });

    try {
        const { data } = await axios.post(
            route("recursos.update", recurso.value.id),
            formData,
            {
                headers: { "Content-Type": "multipart/form-data" },
            }
        );
        message.success("Recurso actualizado exitosamente");
        cerrarModal();
        emitir("actualizar-tabla", data.recurso);
    } catch (error) {
        message.error("Error al actualizar recurso");
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
            recurso.value = { ...props.recurso };
            cargarFotos();
        }
    }
);

onMounted(() => {
    cargarFotos();
    opcionesAreas.value = props.areas.map((area) => ({
        label: area.nombre,
        value: area.id,
    }));
    opcionesEquipos.value = props.equipos.map((equipo) => ({
        label: equipo.nombre,
        value: equipo.id,
    }));
});
</script>
