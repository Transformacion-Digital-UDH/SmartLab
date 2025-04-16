<template>
    <Table
        :columns="columnas"
        :dataSource="miembros.data"
        rowKey="id"
        :pagination="false"
        :scroll="{ x: 800 }"
    >
        <template #bodyCell="{ column, record }">
            <template v-if="column.key === 'acciones'">
                <FormOutlined @click="editar(record)" class="text-blue-600" />
                <DeleteOutlined
                    @click="confirmarEliminacion(record)"
                    class="ml-2 text-red-600"
                />
            </template>
            <template v-if="column.key === 'nombre'">
                {{ record.nombres }} {{ record.apellidos }}
            </template>
        </template>
    </Table>
    
    <!-- Paginación personalizada sin selector de tamaño de página -->
    <div class="flex justify-center mt-4">
        <Pagination
            :current="miembros.current_page"
            :total="miembros.total"
            :pageSize="miembros.per_page"
            @change="cambiarPagina"
        />
    </div>
</template>

<script setup>
import { Table, Modal, message, Pagination } from "ant-design-vue";
import { router } from '@inertiajs/vue3';
import {
    FormOutlined,
    DeleteOutlined
} from "@ant-design/icons-vue";

const props = defineProps({
    miembros: Object, // Ahora es un objeto de paginación
});

console.log(props.miembros);

const emitir = defineEmits(["editar", "actualizar-tabla", "cambiar-pagina"]);

const columnas = [
    { title: "Código", dataIndex: "codigo", key: "codigo", width: 120 },
    {
        title: "Nombre",
        key: "nombre",
        sorter: (a, b) => (a.nombres + " " + a.apellidos).localeCompare(b.nombres + " " + b.apellidos),
    },
    { title: "DNI", dataIndex: "dni", key: "dni", width: 100 },
    { title: "Email", dataIndex: "email", key: "email" },
    { title: "Celular", dataIndex: "celular", key: "celular", width: 140 },
    { title: "Acciones", key: "acciones", fixed: "right", width: 90 },
];

function editar(miembro) {
    emitir("editar", miembro);
}

const cambiarPagina = (pagina) => {
    emitir("cambiar-pagina", pagina);
};

// Eliminamos la función cambiarTamanoPagina ya que no la necesitamos más

const confirmarEliminacion = (miembro) => {
    Modal.confirm({
        title: '¿Estás seguro de eliminar este miembro?',
        content: `${miembro.nombres} ${miembro.apellidos}`,
        okText: 'Confirmar',
        cancelText: 'Cancelar',
        onOk() {
            router.delete(route('miembros.destroy', miembro.id), {
                data: {
                    laboratorio_id: miembro.pivot.laboratorio_id,
                    rol: miembro.pivot.rol
                },
                preserveScroll: true,
                onSuccess: () => {
                    message.success('Miembro eliminado exitosamente');
                    emitir('actualizar-tabla');
                },
                onError: (error) => {
                    console.error('Error al eliminar el miembro:', error);
                    message.error('Error al eliminar el miembro');
                }
            });
        },
    });
};
</script>
