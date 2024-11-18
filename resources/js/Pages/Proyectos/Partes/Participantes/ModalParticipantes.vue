<template>
    <Modal
        :open="visible"
        :title="`Participantes del proyecto: ${proyecto.nombre}`"
        :footer="null"
        width="800px"
        @cancel="cerrarModal"
    >
        <!-- Select múltiple para agregar usuarios -->
        <div class="my-3 flex items-center gap-2">
            <Select
                mode="multiple"
                :options="opcionesUsuarios"
                v-model="usuariosSeleccionados"
                placeholder="Selecciona usuarios para agregar"
                class="w-full"
            />
            <Button type="primary" @click="agregarParticipantes">Agregar</Button>
        </div>

        <!-- Tabla de participantes -->
        <Table
            :columns="columnasParticipantes"
            :dataSource="participantes"
            :pagination="false"
            :scroll="{ x: 800 }"
            rowKey="id"
        />
    </Modal>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch } from 'vue';
import { Modal, Table, message, Select, Button } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    proyecto: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['update:visible']);

// Columnas de la tabla de participantes
const columnasParticipantes = [
    { title: 'DNI', dataIndex: 'dni', key: 'dni', width: 120 },
    { title: 'Nombres', dataIndex: 'nombres', key: 'nombres' },
    { title: 'Apellidos', dataIndex: 'apellidos', key: 'apellidos' },
    { title: 'Correo', dataIndex: 'correo', key: 'correo' },
    { title: 'Celular', dataIndex: 'celular', key: 'celular', width: 150 },
];

const participantes = ref([]);
const opcionesUsuarios = ref([]);
const usuariosSeleccionados = ref([]);
const cargandoUsuarios = ref(false);

// Cierra el modal y emite el evento para cerrar en el componente padre
const cerrarModal = () => {
    emit('update:visible', false);
};

// Cargar participantes del proyecto
const cargarParticipantes = async () => {
    try {
        const response = await axios.get(route('proyectos.participantes', { proyecto: props.proyecto.id }));
        participantes.value = response.data.map((participante) => ({
            id: participante.id,
            dni: participante.usuario.dni,
            nombres: participante.usuario.nombres,
            apellidos: participante.usuario.apellidos,
            correo: participante.usuario.email,
            celular: participante.usuario.celular,
            rol: participante.rol || 'Sin especificar',
        }));
        console.log("participantes: ", participantes.value);
    } catch (error) {
        console.error('Error al cargar los participantes:', error);
        message.error('No se pudieron cargar los participantes');
    }
};

// Cargar la lista de usuarios desde la API
const cargarUsuarios = async () => {
    cargandoUsuarios.value = true;
    try {
        const response = await axios.get(route('usuarios.json'));
        opcionesUsuarios.value = response.data.map(usuario => ({
            label: `${usuario.dni} - ${usuario.nombres} ${usuario.apellidos} - ${usuario.email}`,
            value: usuario.id,
        }));
        console.log(opcionesUsuarios.value);
    } catch (error) {
        console.error("Error al cargar usuarios:", error);
        message.error('Error al cargar usuarios');
    } finally {
        cargandoUsuarios.value = false;
    }
};

// Agregar participantes al proyecto
const agregarParticipantes = async () => {
    if (usuariosSeleccionados.value.length === 0) {
        message.warning('Selecciona al menos un usuario');
        return;
    }

    try {
        await axios.post(route('proyectos.agregar-participantes', { proyecto_id: props.proyecto.id }), {
            usuarios: usuariosSeleccionados.value,
        });
        message.success('Participantes agregados exitosamente');
        usuariosSeleccionados.value = [];
        cargarParticipantes();
    } catch (error) {
        console.error('Error al agregar participantes:', error);
        message.error('No se pudieron agregar los participantes');
    }
};

// Cargar los participantes y usuarios cuando el modal se abre
watch(() => props.visible, (val) => {
    if (val) {
        cargarParticipantes();
        cargarUsuarios();
    }
});
</script>
