<template>
    <Modal
        title="Agregar Usuario"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="usuario">
            <FormItem label="Nombres" name="nombres" :rules="[{ required: true, message: 'Por favor ingrese los nombres' }]">
                <Input v-model:value="usuario.nombres" placeholder="Ingrese los nombres" />
            </FormItem>

            <FormItem label="Apellidos" name="apellidos" :rules="[{ required: true, message: 'Por favor ingrese los apellidos' }]">
                <Input v-model:value="usuario.apellidos" placeholder="Ingrese los apellidos" />
            </FormItem>

            <FormItem label="DNI" name="dni" :rules="[{ required: true, message: 'Por favor ingrese el DNI' }]">
                <Input v-model:value="usuario.dni" placeholder="Ingrese el DNI" />
            </FormItem>

            <FormItem label="Email" name="email" :rules="[{ type: 'email', message: 'Por favor ingrese un correo válido' }]">
                <Input v-model:value="usuario.email" placeholder="Ingrese el correo electrónico" />
            </FormItem>

            <FormItem label="Celular" name="celular">
                <Input v-model:value="usuario.celular" placeholder="Ingrese número de celular" />
            </FormItem>

            <FormItem label="Rol" name="rol" :rules="[{ required: true, message: 'Por favor seleccione un rol' }]">
                <Select
                    v-model:value="usuario.rol"
                    placeholder="Seleccione un rol"
                    :options="opcionesRoles"
                />
            </FormItem>

            <FormItem label="Código" name="codigo">
                <Input v-model:value="usuario.codigo" placeholder="Ingrese el código" />
            </FormItem>

            <FormItem label="Contraseña" name="password" :rules="[{ message: 'Por favor ingrese una contraseña' }]">
                <Input type="password" v-model:value="usuario.password" placeholder="*************" />
            </FormItem>

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
    usuario: {
        type: Object,
        default: () => ({
            nombres: '',
            apellidos: '',
            dni: '',
            email: '',
            rol: '',
            is_active: '1',
            codigo: '',
            password: '',
        }),
    },
});

const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

const usuario = ref({ ...props.usuario });
const cargando = ref(false);

const opcionesRoles = ref([
    { label: 'Libre', value: 'Libre' },
    { label: 'Invitado', value: 'Invitado' },
    { label: 'Miembro', value: 'Miembro' },
    { label: 'Coordinador', value: 'Coordinador' },
    { label: 'Responsable', value: 'Responsable' },
    //{ label: 'Administrador', value: 'Admin' },
]);

const cerrarModal = () => {
    emitir('update:visible', false);
};

const enviarFormulario = async () => {
    cargando.value = true;
    try {
        const url = props.usuario.id
            ? route('usuarios.update', props.usuario.id)
            : route('usuarios.store');

        const data = { ...usuario.value };

        if (props.usuario.id) {
            data._method = 'PUT';
        }

        const response = await axios.post(url, data);
        
        message.success(`Usuario ${props.usuario.id ? 'actualizado' : 'creado'} exitosamente`);
        cerrarModal();
        emitir('actualizar-tabla', response.data.usuario);
    } catch (error) {
        message.error(`Error al ${props.usuario.id ? 'actualizar' : 'crear'} el usuario`);
    } finally {
        cargando.value = false;
    }
};

watch(() => props.visible, (val) => {
    if (val) {
        usuario.value = { ...props.usuario };
    }
});
</script>
