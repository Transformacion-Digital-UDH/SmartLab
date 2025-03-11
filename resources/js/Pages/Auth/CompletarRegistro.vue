<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

import InputError from "@/Components/Inputs/InputError.vue";
import InputLabel from "@/Components/Inputs/InputLabel.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import InputText from "@/Components/Inputs/InputText.vue";
import { isCodigo } from "@/helpers";

const props = defineProps({
    user: Object,
});

const form = useForm({
    dni: props.user.dni,
    codigo: props.user.codigo,
    nombres: props.user.nombres,
    apellidos: props.user.apellidos,
    celular: props.user.celular,
    razon_registro: props.user.razon_registro || '',
});

const esEstudiante = isCodigo(form.codigo);
const esCorreoUDH = props.user.email ? props.user.email.endsWith('@udh.edu.pe') : false;

const submit = () => {
    form.errors = {};
    
    // Asegurar que razon_registro se incluye siempre en la solicitud si el usuario no es de UDH
    if (!esCorreoUDH) {
        // Asegurarnos de que el valor nunca sea undefined o null
        form.razon_registro = form.razon_registro || '';
        
        // Mostrar en consola para depuración
        console.log('Enviando razon_registro:', form.razon_registro);
        
        // Agregamos un helper para debugging
        window.formData = form.data();
        console.log('Todos los datos del formulario:', window.formData);
    }
    
    // Antes de enviar el formulario, agregar un evento para ver qué se está enviando
    form.post(route("completar.registro"), {
        onSuccess: () => {
            console.log('Formulario enviado exitosamente');
        },
        onError: (errors) => {
            console.error('Errores en el formulario:', errors);
        },
        onFinish: () => {
            console.log('Solicitud finalizada');
        }
    });
};
</script>

<template>
    <Head title="Completar Registro" />
    <GuestLayout>
        <div class="text-center mb-5">
            <h1 class="block text-xl font-bold text-alterno">
                Datos Personales
            </h1>
        </div>

        <div class="mb-6">
            <p class="text-sm text-gray-600">
                Antes de continuar, le solicitamos amablemente que complete sus
                datos personales.
            </p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="dni" :value="$t('DNI')" />
                <InputText
                    id="dni"
                    v-model="form.dni"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="off"
                />
                <InputError class="mt-2" :message="form.errors.dni" />
            </div>
            <div class="mt-4" v-if="esEstudiante">
                <InputLabel for="codigo" :value="$t('Código de estudiante')" />
                <InputText
                    id="codigo"
                    v-model="form.codigo"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="off"
                />
                <InputError class="mt-2" :message="form.errors.codigo" />
            </div>
            <div class="mt-4">
                <InputLabel for="nombres" :value="$t('Names')" />
                <InputText
                    id="nombres"
                    v-model="form.nombres"
                    type="text"
                    class="mt-1 block w-full uppercase"
                    required
                    autocomplete="nombres"
                />
                <InputError class="mt-2" :message="form.errors.nombres" />
            </div>
            <div class="mt-4">
                <InputLabel for="apellidos" :value="$t('Surnames')" />
                <InputText
                    id="apellidos"
                    v-model="form.apellidos"
                    type="text"
                    class="mt-1 block w-full uppercase"
                    required
                    autocomplete="apellidos"
                />
                <InputError class="mt-2" :message="form.errors.apellidos" />
            </div>

            <div class="mt-4">
                <InputLabel for="celular" :value="$t('Cellular')" />
                <InputText
                    id="celular"
                    v-model="form.celular"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="off"
                />
                <InputError class="mt-2" :message="form.errors.celular" />
            </div>

            <!-- Campo de razón de registro (solo para correos externos) -->
            <div class="mt-4" v-if="!esCorreoUDH">
                <InputLabel for="razon_registro" value="Razón de registro" />
                <!-- Asegurar que tiene un nombre para el formulario -->
                <textarea
                    id="razon_registro"
                    v-model="form.razon_registro"
                    name="razon_registro"
                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    rows="4"
                    placeholder="Explique brevemente por qué desea utilizar los recursos de la Universidad de Huánuco"
                ></textarea>
                <p class="text-sm text-gray-500 mt-1">
                    Como usuario externo, por favor indique el motivo por el cual desea registrarse para utilizar los recursos de la Universidad.
                </p>
                <InputError class="mt-2" :message="form.errors.razon_registro" />
            </div>

            <div class="mt-8 flex items-center justify-between">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-primario hover:underline decoration-2 font-semibold"
                >
                    {{ $t("Log Out") }}
                </Link>

                <PrimaryButton
                    class="py-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ $t("Completar Registro") }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
