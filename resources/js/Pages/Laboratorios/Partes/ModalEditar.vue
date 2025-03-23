<template>
    <Drawer
        title="Editar laboratorio"
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

            <FormItem label="Coordinador" name="coordinador_id">
                <Select
                    v-model:value="laboratorio.coordinador_id"
                    placeholder="Seleccione un coordinador"
                    :options="opcionesResponsables"
                    show-search
                    :filter-option="buscarResponsable"
                    allowClear
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
                <FormItem label="Email" name="email" class="w-full">
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
                        class="w-full"
                    />
                    <InputError :message="errors.inauguracion?.[0]" />
                </FormItem>
            </div>

            <FormItem label="Descripción" name="descripcion">
                <Textarea
                    v-model:value="laboratorio.descripcion"
                    placeholder="Ingrese una descripción"
                    :auto-size="{ minRows: 3 }"
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
                Actualizar
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
    Textarea,
    message,
} from "ant-design-vue";

const props = defineProps({
    visible: Boolean,
    usuarios: Array,
    laboratorio: {
        type: Object,
        default: () => ({
            nombre: "",
            codigo: "",
            descripcion: "",
            aforo: null,
            email: "",
            inauguracion: null,
            responsable: null,
            coordinador: null,
            responsable_id: "",
            coordinador_id: "",
        }),
    },
});

const emitir = defineEmits(["update:visible", "actualizar-tabla"]);

const laboratorio = ref({ ...props.laboratorio });
const cargando = ref(false);
const errores = ref({});
const errors = errores; // alias para utilizar "errors" en el template

// Configuramos las opciones para el select de responsables
const opcionesResponsables = ref(
    props.usuarios.map((responsable) => ({
        label: `${responsable.nombres} ${responsable.apellidos} - ${responsable.dni} - ${responsable.email}`,
        value: responsable.id,
    }))
);

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
        const response = await axios.put(
            route("laboratorios.update", props.laboratorio.id),
            laboratorio.value
        );
        message.success("Laboratorio actualizado exitosamente");
        emitir("actualizar-tabla", response.data["laboratorio"]);
        cerrarModal();
    } catch (error) {
        message.error("Error al actualizar el laboratorio");
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
        errors.value = {};
        if (val) {
            laboratorio.value = { ...props.laboratorio };
            if (laboratorio.value.responsable) {
                laboratorio.value.responsable_id =
                    laboratorio.value.responsable.id;
            }
            if (laboratorio.value.coordinador) {
                laboratorio.value.coordinador_id =
                    laboratorio.value.coordinador.id;
            }
        }
    },
    { immediate: true }
);
</script>
