<template>
    <Modal
        :open="visible"
        :title="`Participantes del ${proyecto.nombre}`"
        :footer="null"
        width="900px"
        centered
        @cancel="cerrarModal"
    >
        <p class="text-xs text-gray-500 pt-2">
            <span class="font-medium text-udh_3">Responsable del proyecto:</span>
            {{ proyecto.responsable.dni }} - {{ proyecto.responsable.nombres }} {{ proyecto.responsable.apellidos }} - {{ proyecto.responsable.email }} - {{ proyecto.responsable.celular }}
        </p>

        <!-- Select múltiple para agregar usuarios -->
        <div class="my-3 flex items-center gap-2">
            <Select
                mode="multiple"
                :options="opcionesUsuarios"
                :value="usuariosSeleccionados"
                @change="actualizarUsuariosSeleccionados"
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
            size="small"
            rowKey="id"
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'acciones'">
                    <Popconfirm
                        title="¿Estás seguro de quitar este participante?"
                        okText="Sí"
                        cancelText="No"
                        @confirm="quitarParticipante(record.id)"
                    >
                        <Button danger type="text">Quitar</Button>
                    </Popconfirm>
                </template>
            </template>
        </Table>

    </Modal>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';
import { Modal, Table, message, Select, Button, Popconfirm } from 'ant-design-vue';
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
    {
        title: 'DNI',
        dataIndex: 'dni',
        key: 'dni',
        width: 120,
        sorter: (a, b) => a.dni.localeCompare(b.dni),
    },
    {
        title: 'Nombre Completo',
        key: 'nombreCompleto',
        customRender: ({ record }) => `${record.nombres} ${record.apellidos}`,
        sorter: (a, b) => {
            const nombreCompletoA = `${a.nombres} ${a.apellidos}`.toLowerCase();
            const nombreCompletoB = `${b.nombres} ${b.apellidos}`.toLowerCase();
            return nombreCompletoA.localeCompare(nombreCompletoB);
        },
    },
    { title: 'Correo', dataIndex: 'correo', key: 'correo' },
    { title: 'Celular', dataIndex: 'celular', key: 'celular', width: 150 },
    { key: 'acciones', width: 100, fixed: "right" },
];



const participantes = ref([]);
const opcionesUsuarios = ref([]);
const usuariosSeleccionados = ref([]);
const cargandoUsuarios = ref(false);

// Actualiza el array de usuarios seleccionados
const actualizarUsuariosSeleccionados = (value) => {
    console.log("Usuarios seleccionados: ", value); // Verifica si los datos están llegando correctamente
    usuariosSeleccionados.value = value;
};

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
        }));
        console.log("Participantes: ", participantes.value);
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
        console.log("Opciones de usuarios: ", opcionesUsuarios.value);
    } catch (error) {
        console.error("Error al cargar usuarios:", error);
        message.error('Error al cargar usuarios');
    } finally {
        cargandoUsuarios.value = false;
    }
};

// Agregar participantes al proyecto
const agregarParticipantes = async () => {
    console.log("Usuarios seleccionados para agregar: ", usuariosSeleccionados.value);
    if (usuariosSeleccionados.value.length === 0) {
        message.warning('Selecciona al menos un usuario');
        return;
    }

    try {
        await axios.post(route('proyectos.agregar-participantes', { proyecto: props.proyecto.id }), {
            usuario_ids: usuariosSeleccionados.value,
        });
        message.success('Participantes agregados exitosamente');
        usuariosSeleccionados.value = [];
        cargarParticipantes();
    } catch (error) {
        console.error('Error al agregar participantes:', error);
        message.error('No se pudieron agregar los participantes');
    }
};

// Quitar participante del proyecto
const quitarParticipante = async (participanteId) => {
    try {
        await axios.delete(route('proyectos.quitar-participante', { proyecto: props.proyecto.id, participanteId: participanteId }));
        message.success('Participante quitado exitosamente');
        cargarParticipantes();
    } catch (error) {
        console.error('Error al quitar el participante:', error);
        message.error('No se pudo quitar el participante');
    }
};

// Recargar responsables al cambiar de proyecto
watch(() => props.visible, (val) => {
    if (val) {
        cargarParticipantes();
    }
});

// Ejecutar al montar el componente
onMounted(() => {
    cargarUsuarios();
    cargarParticipantes();
});
</script>
