<template>
    <AppLayout title="Usuarios">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">
                Usuarios
            </h2>
        </template>

        <div class="px-4 py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col-reverse justify-end mb-6 gap-y-4 sm:flex-row sm:justify-between sm:items-center gap-x-4">
                <div class="w-full">
                    <InputSearch 
                        v-model:value="valorBuscar"
                        placeholder="Buscar usuario por nombre, apellidos, dni o código" 
                        class="w-full"
                        size="large" 
                        @change="buscarUsuarios"
                        :allowClear="true" 
                    />
                </div>

                <Button type="primary" @click="abrirModalCrear" size="large" class="font-medium">
                    Agregar usuario
                </Button>
            </div>

            <!-- Tabla de usuarios -->
            <TablaUsuarios 
                :usuarios="usersFiltrados" 
                @editar="abrirModalEditar" 
                @mostrar-areas="abrirModalAreas"
                @actualizar-tabla="actualizarTabla" 
            />

            <!-- Modal para agregar usuario -->
            <ModalAgregar v-model:visible="mostrarModalCrear" @actualizar-tabla="actualizarTabla" />

            <!-- Modal para editar usuario -->
            <ModalEditar 
                v-if="userSeleccionado" 
                v-model:visible="mostrarModalEditar" 
                :usuario="userSeleccionado"
                @actualizar-tabla="actualizarTabla" 
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Button, InputSearch } from 'ant-design-vue';
import TablaUsuarios from './Partes/TablaUsuarios.vue';
import ModalAgregar from './Partes/ModalAgregar.vue';
import ModalEditar from './Partes/ModalEditar.vue';

const { props } = usePage();
const usuarios = ref(props.usuarios || []);
const mostrarModalCrear = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalAreas = ref(false);
const userSeleccionado = ref(null);
const valorBuscar = ref('');

// Filtrar usuarios
const usersFiltrados = computed(() => {
    return usuarios.value.filter((usuario) => {
        const nombreCompleto = `${usuario.nombres} ${usuario.apellidos}`.toLowerCase();
        const dni = usuario.dni ? usuario.dni.toLowerCase() : '';
        const codigo = usuario.codigo ? usuario.codigo.toLowerCase() : '';
        const searchValue = valorBuscar.value.toLowerCase();
        return nombreCompleto.includes(searchValue) || dni.includes(searchValue) || codigo.includes(searchValue);
    });
});

// Actualizar la tabla al cerrar modales
const actualizarTabla = () => {
    mostrarModalCrear.value = false;
    mostrarModalEditar.value = false;
    router.visit(route('usuarios.index'), { preserveScroll: true });
};

// Abrir modal de creación de usuario
const abrirModalCrear = () => {
    mostrarModalCrear.value = true;
};

// Abrir modal de edición de usuario
const abrirModalEditar = (usuario) => {
    mostrarModalEditar.value = true;
    userSeleccionado.value = { ...usuario };
};

// Abrir modal de áreas asignadas al usuario
const abrirModalAreas = (usuario) => {
    userSeleccionado.value = { ...usuario };
    mostrarModalAreas.value = true;
};

// Función de búsqueda
const buscarUsuarios = () => {
    // No se necesita hacer nada aquí ya que el filtrado se hace a nivel de front-end
};
</script>
