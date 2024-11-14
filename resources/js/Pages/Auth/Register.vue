<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

const form = useForm({
    nombres: '',
    apellidos: '',
    dni: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Registrarse" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="text-center mb-5">
            <h1 class="block text-2xl font-bold text-udh_3">Registro</h1>
            <p class="mt-3 text-sm text-gray-600">
                ¿Ya tienes una cuenta?
                <Link class="text-udh_1 hover:underline decoration-2 font-semibold" :href="route('login')">
                    Inicia sesión aquí
                </Link>
            </p>
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4">
                <InputLabel for="nombres" value="Nombres" />
                <TextInput
                    id="nombres"
                    v-model="form.nombres"
                    type="text"
                    class="block mt-1 w-full"
                    required
                    autofocus
                    autocomplete="given-name"
                />
                <InputError class="mt-2" :message="form.errors.nombres" />
            </div>

            <div class="mt-4">
                <InputLabel for="apellidos" value="Apellidos" />
                <TextInput
                    id="apellidos"
                    v-model="form.apellidos"
                    type="text"
                    class="block mt-1 w-full"
                    required
                    autocomplete="family-name"
                />
                <InputError class="mt-2" :message="form.errors.apellidos" />
            </div>

            <div class="mt-4">
                <InputLabel for="dni" value="DNI" />
                <TextInput
                    id="dni"
                    v-model="form.dni"
                    type="text"
                    class="block mt-1 w-full"
                    required
                    maxlength="8"
                    autocomplete="dni"
                />
                <InputError class="mt-2" :message="form.errors.dni" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Correo Electrónico" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="block mt-1 w-full"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Contraseña" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="block mt-1 w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmar Contraseña" />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="block mt-1 w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature" class="mt-4">
                <InputLabel for="terms">
                    <div class="flex items-start">
                        <Checkbox id="terms" v-model="form.terms" name="terms" required class="hidden" />

                        <div class="text-xs text-center">
                            Al registrarte, aceptas nuestras
                            <Link target="_blank" :href="route('terms.show')" class="underline text-gray-600 hover:text-udh_1 rounded-md focus:outline-none hover:font-bold">
                                Términos de Servicio
                            </Link>
                            y nuestra
                            <Link target="_blank" :href="route('policy.show')" class="underline text-gray-600 hover:text-udh_1 rounded-md focus:outline-none hover:font-bold">
                                Política de Privacidad
                            </Link>.
                        </div>
                    </div>
                </InputLabel>
            </div>

            <PrimaryButton class="w-full mt-6 py-3 justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Registrarse
            </PrimaryButton>
        </form>
    </AuthenticationCard>
</template>
