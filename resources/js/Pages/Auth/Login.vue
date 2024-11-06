<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar sesión" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="text-center mb-4">
            <h1 class="block text-2xl font-bold text-udh_3">Iniciar sesión</h1>
            <p class="mt-3 text-gray-600">
                ¿Aún no tienes una cuenta?
                <Link class="text-udh_1 hover:underline decoration-2 font-semibold" :href="route('register')">
                    Registrate aquí
                </Link>
            </p>
        </div>

        <a href="{{ route('google') }}"
            class="w-full py-2 px-3 inline-flex justify-center items-center gap-2 rounded-sm border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-0 transition-all ">
            <img src="/images/google.webp" class="w-4 h-4 mr-1" alt="google-icon">
            Continuar con Google
        </a>

        <InputError class="mt-2" :message="form.errors.google" />

        <div class="w-full flex justify-between items-center my-4">
            <hr class="h-px border-0 bg-gray-400 flex-1">
            <div class=" text-gray-600 text-xs leading-[18px] px-2.5">O</div>
            <hr class="h-px border-0 bg-gray-400 flex-1">
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <div class="flex justify-between items-center">
                    <InputLabel for="password" value="Password" />
                    <Link :href="route('password.request')" class="text-sm text-udh_1 font-semibold decoration-2 hover:underline">
                        ¿Olvidó su contraseña?
                    </Link>
                </div>
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-3  text-gray-600 dark:text-gray-400">Mantener sesión activa</span>
                </label>
            </div>

            <PrimaryButton class="w-full mt-6 py-3 justify-center bg-udh_3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Iniciar sesión
            </PrimaryButton>
        </form>
    </AuthenticationCard>
</template>
