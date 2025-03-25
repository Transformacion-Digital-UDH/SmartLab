<template>
    <Modal
        title="Editar área"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
        width="600px"
    >
        <Form
            layout="vertical"
            @finish="enviarFormulario"
            :model="area"
            class="mt-4"
        >
            <FormItem label="Laboratorio" name="laboratorio_id">
                <Input
                    v-model:value="area.laboratorio.nombre"
                    disabled
                    placeholder="Laboratorio asignado"
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
    InputNumber,
    Select,
    Button,
    message,
    Upload,
} from "ant-design-vue";

const props = defineProps({
    visible: Boolean,
    area: Object,
});

const emitir = defineEmits(["update:visible", "actualizar-tabla"]);

const area = ref({
    ...props.area,
    laboratorio: props.area.laboratorio || { nombre: "Sin laboratorio" },
});
const cargando = ref(false);

const opcionesTipo = ref([
    { label: "Reservable", value: "Reservable" },
    { label: "No reservable", value: "No reservable" },
]);

const cerrarModal = () => {
    area.value = {};
    errors.value = {};
    emitir("update:visible", false);
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
    return false; // Evita la subida automática
};

const cargarFotos = () => {
    if (area.value.fotos) {
        fileList.value = area.value.fotos.map((foto) => ({
            uid: foto.id,
            name: foto.nombre,
            status: "done",
            url: `/storage/${foto.ruta}`,
        }));
    }
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
    formData.append("_method", "PUT");
    Object.keys(area.value).forEach((key) => {
        if (key !== "laboratorio") {
            // Excluimos el objeto laboratorio
            formData.append(key, area.value[key] || "");
        }
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
            route("areas.update", area.value.id),
            formData,
            {
                headers: { "Content-Type": "multipart/form-data" },
            }
        );
        message.success("Área actualizada exitosamente");
        cerrarModal();
        emitir("actualizar-tabla", data.area);
    } catch (error) {
        message.error("Error al actualizar área");
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
            area.value = {
                ...props.area,
                laboratorio: props.area.laboratorio || {
                    nombre: "Sin laboratorio",
                },
            };
            cargarFotos();
        }
    }
);

onMounted(() => {
    cargarFotos();
});
</script>
