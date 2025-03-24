<template>
    <Drawer
        title="Agregar laboratorio"
        :open="visible"
        @close="cerrarModal"
        placement="right"
        :footer="null"
        width="100&"
        class="sm:min-w-[560px]"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="laboratorio">
            <FormItem label="Nombre *" name="nombre">
                <Input
                    v-model:value="laboratorio.nombre"
                    placeholder="Ingrese el nombre"
                    autocomplete="off"
                />
                <InputError :message="errors.nombre?.[0]" />
            </FormItem>

            <FormItem label="Responsable *" name="responsable_id">
                <Select
                    v-model:value="laboratorio.responsable_id"
                    placeholder="Seleccione un responsable"
                    :options="opcionesResponsables"
                    show-search
                    :filter-option="buscarResponsable"
                />
                <InputError :message="errors.responsable_id?.[0]" />
            </FormItem>

            <FormItem label="Coordinador *" name="coordinador_id">
                <Select
                    v-model:value="laboratorio.coordinador_id"
                    placeholder="Seleccione un coordinador"
                    :options="opcionesCoordinadores"
                    show-search
                    :filter-option="buscarResponsable"
                />
                <InputError :message="errors.coordinador_id?.[0]" />
            </FormItem>

            <div class="block md:flex gap-x-3">
                <FormItem label="Código *" name="codigo" class="w-full">
                    <Input
                        v-model:value="laboratorio.codigo"
                        placeholder="Ingrese el código"
                        autocomplete="off"
                    />
                    <InputError :message="errors.codigo?.[0]" />
                </FormItem>

                <FormItem label="Aforo" name="aforo" class="w-full">
                    <InputNumber
                        v-model:value="laboratorio.aforo"
                        placeholder="Ingrese el aforo"
                        class="w-full"
                        type="number"
                        step="1"
                        min="0"
                    />
                    <InputError :message="errors.aforo?.[0]" />
                </FormItem>
            </div>

            <div class="block md:flex gap-x-3">
                <FormItem
                    label="Correo del laboratorio"
                    name="email"
                    class="w-full"
                >
                    <Input
                        v-model:value="laboratorio.email"
                        placeholder="Ingrese el correo electrónico"
                    />
                    <InputError :message="errors.email?.[0]" />
                </FormItem>

                <FormItem
                    label="Fecha de inauguración"
                    name="inauguracion"
                    class="w-full"
                >
                    <Input
                        type="date"
                        v-model:value="laboratorio.inauguracion"
                    />
                    <InputError :message="errors.inauguracion?.[0]" />
                </FormItem>
            </div>

            <FormItem label="Descripción" name="descripcion">
                <Textarea
                    :auto-size="{ minRows: 3 }"
                    v-model:value="laboratorio.descripcion"
                    placeholder="Ingrese una descripción"
                />
                <InputError :message="errors.descripcion?.[0]" />
            </FormItem>
        </Form>

        <template #extra>
            <Button class="mr-3" @click="cerrarModal">Cancelar</Button>
            <Button
                type="primary"
                htmlType="submit"
                @click="enviarFormulario"
                :loading="cargando"
            >
                Guardar
            </Button>
        </template>
    </Drawer>
</template>

<script setup>
import { ref, watch } from "vue";
import InputError from "@/Components/Inputs/InputError.vue";
import axios from "axios";
import {
    Drawer,
    Form,
    FormItem,
    Input,
    Select,
    InputNumber,
    Button,
    message,
    Textarea,
} from "ant-design-vue";

const props = defineProps({
    visible: Boolean,
    usuarios: Array,
});

const cargando = ref(false);
const errors = ref({});
const laboratorio = ref({
    nombre: "",
    codigo: "",
    descripcion: "",
    aforo: null,
    email: "",
    inauguracion: null,
    responsable_id: null,
    coordinador_id: null,
});

const emitir = defineEmits(["update:visible", "actualizar-tabla"]);

const cerrarModal = () => {
    emitir("update:visible", false);
};

const buscarResponsable = (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};

const enviarFormulario = async () => {
    cargando.value = true;
    errors.value = {};

    try {
        const response = await axios.post(
            route("laboratorios.store"),
            laboratorio.value
        );
        message.success("Laboratorio agregado exitosamente");
        emitir("actualizar-tabla", response.data["laboratorio"]);
        cerrarModal();
    } catch (error) {
        message.error("Error al agregar el laboratorio");
        if (error.response && error.response.data.errors) {
            errors.value = error.response.data.errors;
        }
    } finally {
        cargando.value = false;
    }
};

const opcionesResponsables = ref([]);
const opcionesCoordinadores = ref([]);

watch(
    () => props.visible,
    (val) => {
        errors.value = {};
        if (val) {
            laboratorio.value = {
                nombre: "",
                codigo: "",
                descripcion: "",
                aforo: null,
                email: "",
                inauguracion: "",
                responsable_id: null,
            };

            opcionesResponsables.value = props.usuarios.map((responsable) => ({
                label: `${responsable.nombres} ${responsable.apellidos} - ${responsable.dni} - ${responsable.email}`,
                value: responsable.id,
            }));

            opcionesCoordinadores.value = props.usuarios.map((coordinador) => ({
                label: `${coordinador.nombres} ${coordinador.apellidos} - ${coordinador.dni} - ${coordinador.email}`,
                value: coordinador.id,
            }));
        }
    }
);
</script>
