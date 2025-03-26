<template>
    <Modal
        title="Agregar área"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
        width="620px"
    >
        <Form
            layout="vertical"
            @finish="enviarFormulario"
            :model="area"
            class="mt-4"
        >

            <FormItem label="Laboratorio *" name="laboratorio_id">
                <Select
                    :disabled="!($page.props.auth.user.rol === 'Admin' && $page.props.auth.user.laboratorio_seleccionado === null)"
                    v-model:value="area.laboratorio_id"
                    placeholder="Seleccione laboratorio"
                    :options="opcionesLaboratorios"
                    show-search
                    :filter-option="filtrarLaboratorios"
                />
                <InputError :message="errors.laboratorio_id?.[0]" />
            </FormItem>


            <FormItem label="Nombre *" name="nombre">
                <Input
                    v-model:value="area.nombre"
                    placeholder="Ingrese el nombre del área"
                    autocomplete="off"
                />
                <InputError :message="errors.nombre?.[0]" />
            </FormItem>

            <div class="flex gap-x-3">
                <FormItem label="Tipo *" name="tipo" class="w-full">
                    <Select
                        v-model:value="area.tipo"
                        placeholder="Seleccione el tipo"
                        :options="opcionesTipo"
                    />
                    <InputError :message="errors.tipo?.[0]" />
                </FormItem>

                <FormItem label="Aforo" name="aforo" class="w-full">
                    <InputNumber
                        v-model:value="area.aforo"
                        placeholder="Ingrese el aforo"
                        :min="1"
                        style="width: 100%"
                    />
                    <InputError :message="errors.aforo?.[0]" />
                </FormItem>
            </div>

            <FormItem label="Descripción" name="descripcion">
                <Input.TextArea
                    v-model:value="area.descripcion"
                    placeholder="Ingrese una descripción del área"
                    :rows="2"
                />
                <InputError :message="errors.descripcion?.[0]" />
            </FormItem>


            <!-- Fotos del área -->
            <FormItem label="Fotos del área">
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


            <FormItem class="flex justify-end mb-0">
                <Button class="mr-3" @click="cerrarModal"> Cancelar </Button>
                <Button type="primary" htmlType="submit" :loading="cargando">
                    Guardar
                </Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import InputError from "@/Components/Inputs/InputError.vue";
import { PlusOutlined } from "@ant-design/icons-vue";
import axios from "axios";
import {
    Modal,
    Form,
    FormItem,
    Input,
    InputNumber,
    Select,
    Button,
    message,
    Upload,
} from "ant-design-vue";

const page = usePage();
const props = defineProps({
    visible: Boolean,
    laboratorios: Array,
});

const emitir = defineEmits(["update:visible", "actualizar-tabla"]);

// Función para datos iniciales del formulario
const initialFormData = () => ({
    nombre: "",
    descripcion: "",
    tipo: "No reservable",
    aforo: null,
    laboratorio_id: page.props.auth.user.laboratorio_seleccionado || null
});

// Estado reactivo
const area = ref(initialFormData());
const cargando = ref(false);
const errors = ref({});
const fileList = ref([]);
const previewVisible = ref(false);
const previewImage = ref("");
const maxFiles = 5;

// Opciones del formulario
const opcionesTipo = ref([
    { label: "Reservable", value: "Reservable" },
    { label: "No reservable", value: "No reservable" },
]);

// Computed para opciones de laboratorios
const opcionesLaboratorios = computed(() => {
    return props.laboratorios?.map(lab => ({
        label: lab.nombre,
        value: lab.id
    })) || [];
});

// Función para reiniciar el formulario
const resetForm = () => {
    area.value = initialFormData();
    fileList.value = [];
    errors.value = {};
};

// Función para cerrar el modal
const cerrarModal = () => {
    resetForm();
    emitir("update:visible", false);
};

// Función para filtrar laboratorios en el select
const filtrarLaboratorios = (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};

// Funciones para manejo de fotos
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

// Función para enviar el formulario
const enviarFormulario = async () => {
    cargando.value = true;

    const formData = new FormData();
    Object.keys(area.value).forEach((key) => {
        formData.append(key, area.value[key] || "");
    });

    // Agregar fotos
    fileList.value.forEach((file) => {
        if (file.originFileObj) {
            formData.append("fotos[]", file.originFileObj);
        }
    });

    try {
        const response = await axios.post(route("areas.store"), formData, {
            headers: { "Content-Type": "multipart/form-data" },
        });
        message.success("Área agregada exitosamente");
        cerrarModal();
        emitir("actualizar-tabla", response.data.area);
    } catch (error) {
        message.error("Error al agregar área");
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        }
    } finally {
        cargando.value = false;
    }
};

// Watcher para reiniciar el formulario al abrir el modal
watch(
    () => props.visible,
    (visible) => {
        if (visible) {
            resetForm();
        }
    }
);

</script>
