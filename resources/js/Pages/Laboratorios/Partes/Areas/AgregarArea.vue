<template>
    <!-- Modal para Agregar Área -->
    <Modal
        :open="visible"
        title="Agregar área"
        :footer="null"
        @cancel="cerrarModal"
    >
        <Form
            layout="vertical"
            @finish="guardarArea"
            :model="nuevaArea"
            class="mt-4"
        >
            <FormItem label="Nombre *" name="nombre">
                <Input
                    v-model:value="nuevaArea.nombre"
                    placeholder="Ingrese el nombre del área"
                    autocomplete="off"
                />
                <InputError :message="errors.nombre?.[0]" />
            </FormItem>

            <FormItem label="Descripción" name="descripcion">
                <Input.TextArea
                    v-model:value="nuevaArea.descripcion"
                    placeholder="Ingrese una descripción"
                    auto-size
                />
                <InputError :message="errors.descripcion?.[0]" />
            </FormItem>

            <FormItem label="Aforo" name="aforo">
                <InputNumber
                    v-model:value="nuevaArea.aforo"
                    placeholder="Ingrese el aforo"
                    class="w-full"
                    type="number"
                    step="1"
                    min="0"
                />
                <InputError :message="errors.aforo?.[0]" />
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
import { ref } from "vue";
import axios from "axios";
import InputError from "@/Components/Inputs/InputError.vue";
import {
    Modal,
    Form,
    FormItem,
    Input,
    InputNumber,
    Button,
    message,
} from "ant-design-vue";

const props = defineProps({
    visible: Boolean,
    laboratorio_id: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(["close", "areaGuardada"]);

const nuevaArea = ref({
    nombre: "",
    descripcion: "",
    aforo: null,
});

const cargando = ref(false);
const errors = ref({});

const cerrarModal = () => {
    errors.value = {};
    emit("close");
    nuevaArea.value = { nombre: "", descripcion: "", aforo: null };
};

const guardarArea = async () => {
    cargando.value = true;
    errors.value = {};
    try {
        await axios.post(route("areas.store"), {
            nombre: nuevaArea.value.nombre,
            descripcion: nuevaArea.value.descripcion,
            aforo: nuevaArea.value.aforo,
            laboratorio_id: props.laboratorio_id,
        });

        message.success("Área agregada exitosamente");
        emit("areaGuardada");
        cerrarModal();
    } catch (error) {
        message.error("Error al agregar el área");
        if (error.response && error.response.data.errors) {
            errors.value = error.response.data.errors;
        }
    } finally {
        cargando.value = false;
    }
};
</script>
