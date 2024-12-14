<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";

import InputError from "@/Components/Inputs/InputError.vue";
import InputLabel from "@/Components/Inputs/InputLabel.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import InputText from "@/Components/Inputs/InputText.vue";

const props = defineProps({
    user: Object,
});

const form = useForm({
    dni: props.user.dni,
    codigo: props.user.codigo,
    nombres: props.user.nombres,
    apellidos: props.user.apellidos,
    celular: props.user.celular,
});

const esEstudiante = () => !!form.codigo?.trim();

const submit = () => {
    form.errors = {};
    form.post(route("completar.registro"));
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
            <div class="mt-4" v-if="esEstudiante()">
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
                    class="mt-1 block w-full"
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
                    class="mt-1 block w-full"
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
