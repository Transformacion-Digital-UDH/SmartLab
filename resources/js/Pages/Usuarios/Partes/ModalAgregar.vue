<template>
    <Modal
        title="Agregar usuario"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="usuario">
            <div class="block md:flex gap-x-3">
                <FormItem label="Nombres" name="nombres" class="w-full"
                    :rules="[{ required: true, message: 'Por favor ingrese los nombres' }]">
                    <Input v-model:value="usuario.nombres" placeholder="Ingrese los nombres" class="rounded-lg"
                        autofocus />
                </FormItem>

                <FormItem label="Apellidos" name="apellidos" class="w-full"
                    :rules="[{ required: true, message: 'Por favor ingrese los apellidos' }]">
                    <Input v-model:value="usuario.apellidos" placeholder="Ingrese los apellidos" class="rounded-lg" />
                </FormItem>
            </div>

            <div class="block md:flex gap-x-3">
                <FormItem label="DNI" name="dni" class="w-full"
                    :rules="[{ required: true, message: 'Por favor ingrese el DNI' }]">
                    <Input v-model:value="usuario.dni" placeholder="Ingrese el DNI" class="rounded-lg" />
                </FormItem>

                <FormItem label="Email" name="email" class="w-full"
                    :rules="[{ type: 'email', message: 'Por favor ingrese un correo válido' }]">
                    <Input v-model:value="usuario.email" placeholder="Ingrese el correo electrónico"
                        class="rounded-lg" />
                </FormItem>
            </div>

            <div class="block md:flex gap-x-3">
                <FormItem label="Celular" name="celular" class="w-full">
                    <Input v-model:value="usuario.celular" placeholder="Ingrese número de celular" />
                </FormItem>

                <FormItem label="Código" name="codigo" class="w-full">
                    <Input v-model:value="usuario.codigo" placeholder="Ingrese el código" class="rounded-lg" />
                </FormItem>
            </div>

            <div class="block md:flex gap-x-3">
                <FormItem label="Rol" name="rol" class="w-full"
                    :rules="[{ required: true, message: 'Por favor seleccione un rol' }]">
                    <Select v-model:value="usuario.rol" placeholder="Seleccione un rol" :options="opcionesRoles" />
                </FormItem>

                <FormItem label="Contraseña" name="password" class="w-full"
                    :rules="[{ required: true, message: 'Por favor ingrese una contraseña' }]">
                    <Input type="password" v-model:value="usuario.password" placeholder="Ingrese la contraseña"
                        class="rounded-lg" />
                </FormItem>
            </div>

            <FormItem class="flex justify-end mb-0">
                <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
                <Button type="primary" htmlType="submit" :loading="cargando">Guardar</Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script setup>
import { ref, watch } from 'vue';
import { Modal, Form, FormItem, Input, Select, Button, message } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
});

const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

const usuario = ref({
    nombres: '',
    apellidos: '',
    dni: '',
    email: '',
    rol: 'Invitado',
    is_active: '1',
    codigo: '',
    password: '',
});

const cargando = ref(false);

const opcionesRoles = ref([
    { label: 'Invitado', value: 'Invitado' },
    { label: 'Miembro', value: 'Miembro' },
    { label: 'Coordinador', value: 'Coordinador' },
    { label: 'Responsable', value: 'Responsable' },
    { label: 'Admin', value: 'Admin' },
]);

const cerrarModal = () => {
    emitir('update:visible', false);
};

const enviarFormulario = async () => {
    cargando.value = true;
    try {
        const response = await axios.post(route('usuarios.store'), usuario.value);
        message.success('Usuario agregado exitosamente');
        cerrarModal();
        emitir('actualizar-tabla', response.data["usuario"]);
    } catch (error) {
        message.error('Error al agregar el usuario');
        console.error('Error al guardar el usuario:', error);
    } finally {
        cargando.value = false;
    }
};

watch(() => props.visible, (val) => {
    if (val) {
        usuario.value = {
            nombres: '',
            apellidos: '',
            dni: '',
            email: '',
            rol: 'Invitado',
            is_active: '1',
            codigo: '',
            password: '',
        };
    }
});
</script>
