<template>
    <AppLayout title="Usuarios">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">
                Usuarios
            </h2>
        </template>

        <div class="px-4 py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex flex-col-reverse justify-end mb-6 gap-y-4 sm:flex-row sm:justify-between sm:items-center gap-x-4">
                <InputSearch
                    v-model:value="valorBuscar"
                    placeholder="Buscar usuario por nombre o código"
                    class="w-full"
                    size="large"
                />
                <Button type="primary" @click="abrirModalCrear" size="large" class="font-medium">Agregar usuario</Button>
            </div>

            <!-- Tabla de usuarios -->
            <TablaUsuarios
                :usuarios="usersFiltrados"
                @editar="abrirModalEditar"
                @mostrar-areas="abrirModalAreas"
                @actualizar-tabla="actualizarTabla"
            />

            <!-- Modal para agregar usuario -->
            <ModalAgregar
                v-model:visible="mostrarModalCrear"
                @actualizar-tabla="actualizarTabla"
            />

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
console.log(usuarios.value);
const mostrarModalCrear = ref(false);
const mostrarModalEditar = ref(false);
const mostrarModalAreas = ref(false);
const userSeleccionado = ref(null);
const valorBuscar = ref('');

const usersFiltrados = computed(() => !valorBuscar.value
    ? usuarios.value
    : usuarios.value.filter(user =>
        user.nombres.toLowerCase().includes(valorBuscar.value.toLowerCase()) ||
        user.codigo.toLowerCase().includes(valorBuscar.value.toLowerCase())
    )
);

const actualizarTabla = () => {
    mostrarModalCrear.value = false;
    mostrarModalEditar.value = false;
    router.visit(route('usuarios.index'), { preserveScroll: true });
};

const abrirModalCrear = () => {
    mostrarModalCrear.value = true;
};

const abrirModalEditar = (usuario) => {
    mostrarModalEditar.value = true;
    userSeleccionado.value = { ...usuario };
};

const abrirModalAreas = (usuario) => {
    userSeleccionado.value = { ...usuario };
    mostrarModalAreas.value = true;
};

</script>
