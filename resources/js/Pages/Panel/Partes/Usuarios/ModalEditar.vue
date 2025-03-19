<template>
    <Modal
        title="Solicitud de cuenta"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="usuario">
            <!-- Campo de Nombre (solo lectura) -->
            <FormItem label="Nombre">
                <Input :value="nombreCompleto" disabled />
            </FormItem>

            <FormItem label="Correo">
                <Input :value="usuario.email" disabled />
            </FormItem>

            <!-- Información de contacto -->
            <div class="flex md:flex gap-x-3">
                <FormItem label="DNI" class="flex-1">
                    <Input :value="usuario.dni" disabled />
                </FormItem>
                <FormItem label="Celular" class="flex-1">
                    <!-- Campo modificable -->
                    <Input v-model:value="usuario.celular" />
                </FormItem>
            </div>

            <!-- Campo Razón de registro (solo lectura) -->
            <FormItem label="Razón de registro">
                <Input.TextArea auto-size :value="usuario.razon_registro" disabled />
            </FormItem>

            <!-- Campo Estado de cuenta (no editable, se modifica con Tags) -->
            <FormItem label="Estado de cuenta">
                <div class="flex items-center gap-x-4">
                    <Input
                        :value="displayEstadoCuenta"
                        disabled
                        style="width: 240px"
                    />
                    <div class="flex space-x-2">
                        <Tag
                            :bordered="false"
                            color="green"
                            style="cursor: pointer"
                            @click="usuario.estado_cuenta = 'Aprobada'"
                            class="hover:bg-green-100 flex items-center"
                            v-if="usuario.estado_cuenta !== 'Aprobada'"
                        >
                            <template #icon>
                                <CheckOutlined />
                            </template>
                            Aprobada
                        </Tag>
                        <Tag
                            :bordered="false"
                            color="orange"
                            style="cursor: pointer"
                            @click="usuario.estado_cuenta = 'Desaprobada'"
                            class="hover:bg-orange-100 flex items-center"
                            v-if="usuario.estado_cuenta !== 'Desaprobada'"
                        >
                            <template #icon>
                                <CloseOutlined />
                            </template>
                            Desaprobada
                        </Tag>
                        <Tag
                            :bordered="false"
                            color="default"
                            style="cursor: pointer"
                            @click="usuario.estado_cuenta = 'En revisión'"
                            class="hover:bg-gray-100 flex items-center"
                            v-if="usuario.estado_cuenta !== 'En revisión'"
                        >
                            En revisión
                        </Tag>
                    </div>
                </div>
            </FormItem>

            <!-- Alerta de cambios sin guardar -->
            <Alert
                v-if="isFormChanged"
                message="Hay cambios sin guardar"
                type="warning"
                show-icon
                style="margin-bottom: 16px; font-size: 12px; padding: 4px 8px"
            />

            <!-- Botones de Cancelar y Guardar -->
            <FormItem class="flex justify-end mb-0">
                <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
                <Button
                    type="primary"
                    htmlType="submit"
                    :loading="cargando"
                    :disabled="!isFormChanged"
                >
                    Guardar
                </Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import {
    Modal,
    Form,
    FormItem,
    Input,
    Button,
    Alert,
    Tag,
    message,
} from "ant-design-vue";
import { CheckOutlined, CloseOutlined } from "@ant-design/icons-vue";
import axios from "axios";

const props = defineProps({
    visible: Boolean,
    usuario: {
        type: Object,
        default: () => ({
            nombres: "",
            apellidos: "",
            dni: "",
            email: "",
            celular: "",
            estado_cuenta: "En revisión",
            razon_registro: "",
        }),
    },
});

const emitir = defineEmits(["update:visible", "actualizar-tabla"]);

// Creamos una copia reactiva del objeto usuario para manipularlo en el formulario
const usuario = ref({ ...props.usuario });
const cargando = ref(false);

// Computed para concatenar el nombre completo
const nombreCompleto = computed(() => {
    return `${usuario.value.nombres} ${usuario.value.apellidos}`;
});

// Computed para mostrar el estado de la cuenta
const displayEstadoCuenta = computed(() => {
    return usuario.value.estado_cuenta;
});

// Guardamos la copia original para detectar cambios
const originalUsuario = ref({ ...usuario.value });

// Computed para determinar si se han realizado cambios (solo en los campos editables)
const isFormChanged = computed(() => {
    return (
        originalUsuario.value.celular !== usuario.value.celular ||
        originalUsuario.value.estado_cuenta !== usuario.value.estado_cuenta
    );
});

// Actualizamos la copia reactiva si cambian las propiedades
watch(
    () => props.usuario,
    (newUsuario) => {
        usuario.value = { ...newUsuario };
        originalUsuario.value = { ...newUsuario };
    },
    { immediate: true }
);

// Función para enviar el formulario (actualizar la solicitud de aprobación)
const enviarFormulario = async () => {
    cargando.value = true;
    try {
        const response = await axios.post(
            route("usuarios.update", usuario.value.id),
            {
                ...usuario.value,
                _method: 'PUT'
            }
        );
        message.success("Cuenta actualizada exitosamente");
        cerrarModal();
        emitir("actualizar-tabla", response.data.usuario);
    } catch (error) {
        message.error("Error al actualizar la cuenta");
    } finally {
        cargando.value = false;
    }
};


// Función para cerrar el modal
const cerrarModal = () => {
    emitir("update:visible", false);
};
</script>
