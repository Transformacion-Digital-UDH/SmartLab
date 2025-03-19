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

// Determinar si el correo es de la UDH
const esCorreoUDH = props.user.email
    ? props.user.email.endsWith('@udh.edu.pe')
    : false;

const form = useForm({
    dni: props.user.dni,
    codigo: props.user.codigo,
    nombres: props.user.nombres,
    apellidos: props.user.apellidos,
    celular: props.user.celular,
    razon_registro: props.user.razon_registro || '',
    is_active: true,
    // Si el correo es de la UDH se aprueba automáticamente; de lo contrario, queda en revisión.
    estado_cuenta: esCorreoUDH ? "Aprobada" : "En revisión",
});

const esEstudiante = isCodigo(form.codigo);

const submit = () => {
    form.errors = {};
    form.is_active = esCorreoUDH;

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
        <!-- <div class="text-center mb-5">
            <h1 class="block text-xl font-bold text-alterno">
                Datos Personales
            </h1>
        </div> -->

        <div class="mb-6">
            <p class="text-sm text-gray-600">
                Antes de continuar, le solicitamos amablemente que complete sus
                datos personales.
            </p>
        </div>

        <form @submit.prevent="submit">
            <!-- DNI y Celular en la misma fila -->
            <div class="mt-4 flex gap-4">
                <div class="w-1/2">
                    <InputLabel for="dni" :value="$t('DNI')" />
                    <InputText
                        id="dni"
                        maxlength="8"
                        v-model="form.dni"
                        type="text"
                        class="mt-1 block w-full"
                        required
                        autocomplete="off"
                    />
                    <InputError class="mt-2" :message="form.errors.dni" />
                </div>

                <div class="w-1/2">
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

            <!-- Campo de razón de registro con mismo estilo que los inputs -->
            <div class="mt-4" v-if="!esCorreoUDH">
                <InputLabel for="razon_registro" value="Razón de registro" />
                <p class="text-xs text-gray-500 mt-1">
                    Como usuario externo, por favor indique el motivo por el cual desea registrarse para utilizar los recursos de la UDH.
                </p>
                <textarea
                    id="razon_registro"
                    v-model="form.razon_registro"
                    class="w-full border-gray-300 border outline-none p-3 focus:border-primario focus:ring-primario rounded-md shadow-sm"
                    required
                    autocomplete="off"
                />

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
