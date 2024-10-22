<template>
    <Modal :show="mostrar" @close="cerrarModal">
        <div class="px-6 py-4">
            <div class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                {{ estaEditando ? 'Editar laboratorio' : 'Agregar laboratorio' }}
            </div>

            <form @submit.prevent="enviarFormulario">
                <!-- Campos del formulario -->
                <InputLabel for="nombre" value="Nombre" />
                <Input v-model="form.nombre" id="nombre" class="mt-1 block w-full" required autofocus />
                <InputError v-if="errores.nombre" :message="errores.nombre" />

                <InputLabel for="codigo" value="Código" class="mt-4" />
                <Input v-model="form.codigo" id="codigo" class="mt-1 block w-full" />
                <InputError :message="errores.codigo" />

                <InputLabel for="descripcion" value="Descripción" class="mt-4" />
                <textarea v-model="form.descripcion" id="descripcion" class="mt-1 block w-full"></textarea>
                <InputError :message="errores.descripcion" />

                <InputLabel for="aforo" value="Aforo" class="mt-4" />
                <Input v-model="form.aforo" id="aforo" type="number" class="mt-1 block w-full" />
                <InputError v-if="errores.aforo" :message="errores.aforo" />

                <InputLabel for="email" value="Email" class="mt-4" />
                <Input v-model="form.email" id="email" type="email" class="mt-1 block w-full" />
                <InputError v-if="errores.email" :message="errores.email" />

                <InputLabel for="inauguracion" value="Fecha de Inauguración" class="mt-4" />
                <Input v-model="form.inauguracion" id="inauguracion" type="date" class="mt-1 block w-full" />
                <InputError v-if="errores.inauguracion" :message="errores.inauguracion" />

                <InputLabel for="responsable_id" value="Responsable" class="mt-4" />
                <Input v-model="form.responsable_id" id="responsable_id" type="number" class="mt-1 block w-full" />
                <InputError v-if="errores.responsable_id" :message="errores.responsable_id" />
            </form>
        </div>

        <div class="px-6 py-4 bg-gray-100 dark:bg-gray-900 text-right">
            <SecondaryButton @click="cerrarModal">Cancelar</SecondaryButton>
            <PrimaryButton class="ml-3" @click="enviarFormulario">
                {{ estaEditando ? 'Guardar cambios' : 'Crear laboratorio' }}
            </PrimaryButton>
        </div>
    </Modal>
</template>

<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Input from '@/Components/Input.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    mostrar: Boolean,
    estaEditando: Boolean,
    dataLab: Object,
});

const emit = defineEmits(['cerrar', 'enviar']);

// Estado inicial del formulario
const formularioInicial = {
    nombre: '',
    codigo: '',
    descripcion: '',
    aforo: '',
    email: '',
    inauguracion: '',
    responsable_id: '',
};

// Estado del formulario y de los errores
const form = ref({ ...formularioInicial });
const errores = ref({});

// Actualizar el formulario si está en modo edición, cada vez que el modal se abre para edición
watch(() => props.mostrar, (nuevoValor) => {
    if (nuevoValor && props.estaEditando && props.dataLab) {
        form.value = { ...props.dataLab };
    }
});

// Limpiar el formulario y los errores
const limpiarFormulario = () => {
    form.value = { ...formularioInicial };
};

// Cerrar el modal
const cerrarModal = () => {
    if (props.estaEditando) {
        limpiarFormulario();
    }
    errores.value = {};
    emit('cerrar');
};

// Función para enviar el formulario al backend
const enviarFormulario = () => {
    const method = props.estaEditando ? 'put' : 'post';
    const url = props.estaEditando ? route('laboratorios.update', props.dataLab.id) : route('laboratorios.store');

    router[method](url, form.value, {
        onError: (erroresValidacion) => {
            errores.value = erroresValidacion || {};
        },
        onSuccess: () => {
            limpiarFormulario();
            cerrarModal();
            emit('enviar');
        }
    });
};

</script>
