<template>
    <AppLayout title="Laboratorios">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Laboratorios
            </h2>
        </template>

        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <PrimaryButton @click="abrirModalCrear">Agregar laboratorio</PrimaryButton>
            </div>

            <!-- Tabla de laboratorios -->
            <DataTable
                :titulos="['Nombre', 'Código', 'Aforo', 'Email']"
                :columnasVisibles="['nombre', 'codigo', 'aforo', 'email']"
                :datos="laboratorios"
                :tieneAcciones="true"
                @editar="abrirModalEditar"
                @eliminar="eliminarLaboratorio"
            />

            <!-- Modal para agregar/editar laboratorio -->
            <FormModal
                :mostrar="mostrarModal"
                :estaEditando="estaEditando"
                :dataLab="laboratorioSeleccionado"
                @cerrar="cerrarModal"
                @enviar="manejarEnvio"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DataTable from '@/Components/DataTable.vue';
import FormModal from '@/Pages/Laboratorios/Partials/FormModal.vue';

const { props } = usePage();
const laboratorios = ref(props.laboratorios || []);
const mostrarModal = ref(false);
const estaEditando = ref(false);
const laboratorioSeleccionado = ref(null);

// Abrir el modal para crear un nuevo laboratorio
const abrirModalCrear = () => {
    laboratorioSeleccionado.value = null;
    estaEditando.value = false;
    mostrarModal.value = true;
};

// Abrir el modal para editar un laboratorio
const abrirModalEditar = (laboratorio) => {
    laboratorioSeleccionado.value = laboratorio;
    estaEditando.value = true;
    mostrarModal.value = true;
};

// Cerrar el modal
const cerrarModal = () => {
    mostrarModal.value = false;
};

const manejarEnvio = () => {
    // En lugar de recargar toda la página, hacer una solicitud a la API para obtener la lista de laboratorios
    router.get(route('laboratorios.index'), {}, {
        preserveScroll: true,
        onSuccess: (response) => {
            laboratorios.value = response.props.laboratorios; // Actualizar la lista 
        }
    });
};


// Eliminar un laboratorio
const eliminarLaboratorio = (laboratorio) => {
    router.delete(route('laboratorios.destroy', laboratorio.id), {
        onSuccess: () => {
            laboratorios.value = laboratorios.value.filter(lab => lab.id !== laboratorio.id);
        }
    });
};
</script>
