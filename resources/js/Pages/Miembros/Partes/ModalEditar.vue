<template>
    <Modal
        title="Editar miembro"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="miembro">
            <div class="block md:flex gap-x-3 pt-3">
                <FormItem
                    label="Nombres"
                    name="nombres"
                    class="w-full"
                    :rules="[
                        {
                            required: true,
                            message: 'Por favor ingrese los nombres',
                        },
                    ]"
                >
                    <Input
                        v-model:value="miembro.nombres"
                        placeholder="Ingrese los nombres"
                        class="rounded-lg"
                        autofocus
                    />
                </FormItem>

                <FormItem
                    label="Apellidos"
                    name="apellidos"
                    class="w-full"
                    :rules="[
                        {
                            required: true,
                            message: 'Por favor ingrese los apellidos',
                        },
                    ]"
                >
                    <Input
                        v-model:value="miembro.apellidos"
                        placeholder="Ingrese los apellidos"
                        class="rounded-lg"
                    />
                </FormItem>
            </div>

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
                    v-model:value="miembro.email"
                    placeholder="Ingrese el correo electrónico"
                    class="rounded-lg"
                    autocomplete="off"
                />
            </FormItem>

            <!-- Mostrar el campo Contraseña solo si el rol NO es "Libre" -->
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
                    v-model:value="miembro.password"
                    placeholder="Ingrese la contraseña"
                    class="rounded-lg"
                    autocomplete="new-password"
                />
                <small class="text-gray-400">La contraseña se muestra encriptada por seguridad</small>
            </FormItem>


            <div class="block md:flex gap-x-3">
                <FormItem
                    label="DNI"
                    name="dni"
                    class="w-full"
                    :rules="[
                        { required: true, message: 'Por favor ingrese el DNI' },
                    ]"
                >
                    <Input
                        v-model:value="miembro.dni"
                        placeholder="Ingrese el DNI"
                        class="rounded-lg"
                    />
                </FormItem>

                <FormItem label="Celular" name="celular" class="w-full">
                    <Input
                        v-model:value="miembro.celular"
                        placeholder="Ingrese número de celular"
                        class="rounded-lg"
                    />
                </FormItem>
            </div>

            <div class="block md:flex gap-x-3">
                <FormItem
                    label="Código universitario"
                    name="codigo"
                    class="w-full"
                >
                    <Input
                        v-model:value="miembro.codigo"
                        placeholder="Ingrese el código"
                        class="rounded-lg"
                    />
                </FormItem>

                <FormItem label="Tipo de cuenta" name="rol" class="w-full">
                    <Input
                        v-model:value="miembro.pivot.rol"
                        placeholder="Seleccione un rol"
                        disabled
                    />
                </FormItem>
            </div>

            <FormItem class="flex justify-end mb-0">
                <Button style="margin-right: 8px" @click="cerrarModal">
                    Cancelar
                </Button>
                <Button type="primary" htmlType="submit" :loading="cargando">
                    Guardar cambios
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
    miembro: {
        type: Object,
        required: true, // Asegura que siempre se pase un miembro para editar
    },
});

const emitir = defineEmits(["update:visible", "actualizar-tabla"]);

const miembro = ref({ ...props.miembro });
const cargando = ref(false);

const cerrarModal = () => {
    emitir("update:visible", false);
};

const enviarFormulario = async () => {
    cargando.value = true;
    try {
        const url = route("usuarios.update", miembro.value.id);
        const data = { ...miembro.value };
        data._method = 'PUT';

        const response = await axios.post(url, data);

        message.success("Miembro actualizado exitosamente");
        cerrarModal();
        emitir("actualizar-tabla", response.data.miembro);
    } catch (error) {
        message.error("Error al actualizar el miembro");
    } finally {
        cargando.value = false;
    }
};

watch(
    () => props.visible,
    (val) => {
        if (val) {
            miembro.value = { ...props.miembro }; // Inicializa el formulario con los datos del miembro
        }
    }
);
</script>
