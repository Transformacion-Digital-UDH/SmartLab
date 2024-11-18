<template>
    <!-- Modal para Agregar Participante -->
    <Modal
        :open="visible"
        title="Agregar participante"
        :footer="null"
        @cancel="cerrarModal"
    >
        <Form layout="vertical" @finish="guardarParticipante" :model="nuevoParticipante">
            <FormItem
                label="Seleccione un participante"
                name="usuario_id"
                :rules="[{ required: true, message: 'Por favor seleccione un participante' }]">
                <Select
                    v-model:value="nuevoParticipante.usuario_id"
                    placeholder="Seleccione un participante"
                    :options="opcionesUsuarios"
                    show-search
                    :filter-option="buscarUsuario"
                    :loading="cargandoUsuarios"
                />
            </FormItem>

            <FormItem class="flex justify-end mb-0">
                <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
                <Button type="primary" htmlType="submit" :loading="cargando">Agregar</Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Modal, Form, FormItem, Select, Button, message } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    proyecto_id: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['close', 'participanteAgregado']);

const nuevoParticipante = ref({
    usuario_id: null,
});

const cargando = ref(false);
const cargandoUsuarios = ref(false);
const opcionesUsuarios = ref([]);

// Cargar la lista de usuarios desde la API
const cargarUsuarios = async () => {
    cargandoUsuarios.value = true;
    try {
        const response = await axios.get(route('usuarios.json'));
        opcionesUsuarios.value = response.data.map(usuario => ({
            label: `${usuario.nombres} ${usuario.apellidos}`,
            value: usuario.id,
        }));
    } catch (error) {
        console.error("Error al cargar usuarios:", error);
        message.error('Error al cargar usuarios');
    } finally {
        cargandoUsuarios.value = false;
    }
};

// Buscar un usuario en el select
const buscarUsuario = (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};

// Cerrar el modal y resetear el formulario
const cerrarModal = () => {
    emit('close');
    nuevoParticipante.value = { usuario_id: null };
};

// Guardar el participante en el proyecto
const guardarParticipante = async () => {
    cargando.value = true;
    try {
        await axios.post(route('proyectos.agregarParticipante', { proyecto: props.proyecto_id }), {
            usuario_id: nuevoParticipante.value.usuario_id,
        });

        message.success('Participante agregado exitosamente');
        emit('participanteAgregado');
        cerrarModal();
    } catch (error) {
        console.error("Error al agregar participante:", error);
        message.error('Error al agregar participante');
    } finally {
        cargando.value = false;
    }
};

// Cargar usuarios al montar el componente
onMounted(() => {
    cargarUsuarios();
});

</script>
