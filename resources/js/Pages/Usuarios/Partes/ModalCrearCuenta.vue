<template>
    <Modal
        title="Crear cuenta"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="usuario">
            <!-- Fila con DNI y Estado de cuenta (no modificables) -->
            <div class="block md:flex gap-x-3 pt-3">
                <FormItem label="DNI" name="dni" class="w-full">
                    <Input
                        v-model:value="usuario.dni"
                        placeholder="Ingrese el DNI"
                        class="rounded-lg"
                        disabled
                    />
                </FormItem>
                <FormItem
                    label="Estado de cuenta"
                    name="estado_cuenta"
                    class="w-full"
                >
                    <Input
                        v-model:value="usuario.estado_cuenta"
                        placeholder="Estado de cuenta"
                        class="rounded-lg"
                        disabled
                    />
                </FormItem>
            </div>

            <!-- Campo Email -->
            <FormItem
                label="Email"
                name="email"
                class="w-full"
                :rules="[
                    {
                        required: true,
                        message: 'Por favor ingrese un correo válido',
                    },
                    { type: 'email', message: 'Correo inválido' },
                ]"
            >
                <Input
                    v-model:value="usuario.email"
                    placeholder="Ingrese el correo electrónico"
                    class="rounded-lg"
                    autocomplete="off"
                />
            </FormItem>

            <!-- Campo Contraseña (obligatorio, se muestra vacío y sin sublabel) -->
            <FormItem
                label="Contraseña"
                name="password"
                class="w-full"
                :rules="[
                    {
                        required: true,
                        message: 'Por favor ingrese una contraseña',
                    },
                ]"
            >
                <Input.Password
                    v-model:value="usuario.password"
                    placeholder="Ingrese la contraseña"
                    class="rounded-lg"
                    autocomplete="new-password"
                />
            </FormItem>

            <!-- Botones -->
            <FormItem class="flex justify-end mb-0">
                <Button style="margin-right: 8px" @click="cerrarModal"
                    >Cancelar</Button
                >
                <Button type="primary" htmlType="submit" :loading="cargando">
                    Crear cuenta
                </Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script setup>
import { ref, watch } from "vue";
import { Modal, Form, FormItem, Input, Button, message } from "ant-design-vue";
import axios from "axios";

const props = defineProps({
    visible: Boolean,
    usuario: {
        type: Object,
        default: () => ({
            dni: "",
            email: "",
            password: "",
            estado_cuenta: "Aprobada",
            rol: "Invitado",
        }),
    },
});

const emitir = defineEmits(["update:visible", "actualizar-tabla"]);

const usuario = ref({ ...props.usuario });
const cargando = ref(false);

const cerrarModal = () => {
    emitir("update:visible", false);
};

const enviarFormulario = async () => {
    cargando.value = true;
    try {
        // La solicitud siempre se hace como update (PUT)
        const url = route("usuarios.update", props.usuario.id);
        const data = { ...usuario.value };

        // Siempre se usa PUT y se fuerza estado_cuenta a "Aprobada"
        data._method = "PUT";
        data.estado_cuenta = "Aprobada";
        // El campo rol ya viene con el valor "Invitado"

        const response = await axios.post(url, data);
        message.success("Usuario actualizado exitosamente");
        cerrarModal();
        emitir("actualizar-tabla", response.data.usuario);
    } catch (error) {
        message.error("Error al actualizar el usuario");
    } finally {
        cargando.value = false;
    }
};

// Inicializar el modelo del usuario cuando el modal se abre
watch(
    () => props.visible,
    (val) => {
        if (val) {
            usuario.value = {
                ...props.usuario,
                password: "",
                estado_cuenta: "Aprobada",
                rol: "Invitado",
            };
        }
    },
    { immediate: true }
);
</script>
