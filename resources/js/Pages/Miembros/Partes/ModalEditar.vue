<template>
    <Modal
        title="Agregar Miembro"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="miembro">
            <FormItem label="Nombres" name="nombres" :rules="[{ required: true, message: 'Por favor ingrese los nombres' }]">
                <Input v-model:value="miembro.nombres" placeholder="Ingrese los nombres" />
            </FormItem>

            <FormItem label="Apellidos" name="apellidos" :rules="[{ required: true, message: 'Por favor ingrese los apellidos' }]">
                <Input v-model:value="miembro.apellidos" placeholder="Ingrese los apellidos" />
            </FormItem>

            <FormItem label="DNI" name="dni" :rules="[{ required: true, message: 'Por favor ingrese el DNI' }]">
                <Input v-model:value="miembro.dni" placeholder="Ingrese el DNI" />
            </FormItem>

            <FormItem label="Email" name="email" :rules="[{ type: 'email', message: 'Por favor ingrese un correo válido' }]">
                <Input v-model:value="miembro.email" placeholder="Ingrese el correo electrónico" />
            </FormItem>

            <FormItem label="Celular" name="celular">
                <Input v-model:value="miembro.celular" placeholder="Ingrese número de celular" />
            </FormItem>

            <FormItem label="Rol" name="rol" :rules="[{ required: true, message: 'Por favor seleccione un rol' }]">
                <Select
                    v-model:value="miembro.rol"
                    placeholder="Seleccione un rol"
                    :options="opcionesRoles"
                />
            </FormItem>

            <FormItem label="Activo" name="is_active">
                <Select v-model:value="miembro.is_active" placeholder="Seleccione estado">
                    <Select.Option value="1">Activo</Select.Option>
                    <Select.Option value="0">Inactivo</Select.Option>
                </Select>
            </FormItem>

            <FormItem label="Código" name="codigo">
                <Input v-model:value="miembro.codigo" placeholder="Ingrese el código" />
            </FormItem>

            <FormItem label="Contraseña" name="password" :rules="[{ message: 'Por favor ingrese una contraseña' }]">
                <Input type="password" v-model:value="miembro.password" placeholder="*************" />
            </FormItem>

            <FormItem class="flex justify-end mb-0">
                <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
                <Button type="primary" htmlType="submit" :loading="cargando">Guardar</Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue';
import { Modal, Form, FormItem, Input, Select, Button, message } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    miembro: {
        type: Object,
        default: () => ({
            nombres: '',
            apellidos: '',
            dni: '',
            email: '',
            celular: '',
            rol: '',
            is_active: '1',
            codigo: '',
            password: '',
        }),
    },
});

const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

const miembro = ref({ ...props.miembro });
const cargando = ref(false);

const opcionesRoles = ref([
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
        const url = props.miembro.id
            ? route('miembros.update', props.miembro.id)
            : route('miembros.store');

        const data = { ...miembro.value };

        if (props.miembro.id) {
            data._method = 'PUT';
        }

        const response = await axios.post(url, data);
        
        message.success(`Miembro ${props.miembro.id ? 'actualizado' : 'creado'} exitosamente`);
        cerrarModal();
        emitir('actualizar-tabla', response.data.miembro);
    } catch (error) {
        message.error(`Error al ${props.miembro.id ? 'actualizar' : 'crear'} el miembro`);
    } finally {
        cargando.value = false;
    }
};

watch(() => props.visible, (val) => {
    if (val) {
        miembro.value = { ...props.miembro };
    }
});
</script>
