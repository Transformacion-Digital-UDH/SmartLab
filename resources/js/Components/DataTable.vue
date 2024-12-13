<template>
    <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th
            v-for="(titulo, index) in titulos"
            :key="index"
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
            >
            {{ titulo }}
            </th>
            <th v-if="tieneAcciones" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Acciones
            </th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="(item, index) in datos" :key="index">
            <!-- Mostrar solo los valores correspondientes a las columnas visibles -->
            <td
            v-for="(columna, colIndex) in columnasVisibles"
            :key="colIndex"
            class="px-6 py-4 whitespace-nowrap"
            >
            {{ item[columna] }} <!-- Accedemos a las claves de 'item' usando las columnas visibles -->
            </td>
            <td v-if="tieneAcciones" class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button @click="editar(item)" class="text-indigo-600 hover:text-indigo-900">Editar</button>
                <button @click="eliminar(item)" class="text-red-600 hover:text-red-900 ml-4">Eliminar</button>
            </td>
        </tr>
        </tbody>
    </table>

    <div v-if="paginacion" class="mt-4">
        <nav class="flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
            <button
            @click="paginaAnterior"
            :disabled="!paginacion.anterior"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
            Anterior
            </button>
            <button
            @click="paginaSiguiente"
            :disabled="!paginacion.siguiente"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
            Siguiente
            </button>
        </div>
        </nav>
    </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    columnasVisibles: Array,  // Campos a mostrar (ej. ['nombre', 'codigo', 'aforo'])
    titulos: Array,           // Títulos de los campos a mostrar (ej. ['Nombre', 'Código', 'Aforo', 'Email'])
    datos: Array,             // Data completa
    paginacion: Object,       // Objeto de paginación (si existe)
    tieneAcciones: Boolean,   // Bandera para mostrar o no la columna de acciones
});

const emit = defineEmits(['editar', 'eliminar', 'paginaSiguiente', 'paginaAnterior']);

const editar = (item) => {
    emit('editar', item);
};

const eliminar = (item) => {
    emit('eliminar', item);
};

const paginaSiguiente = () => {
    emit('paginaSiguiente');
};

const paginaAnterior = () => {
    emit('paginaAnterior');
};
</script>
