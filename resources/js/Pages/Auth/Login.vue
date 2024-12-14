<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Checkbox from "@/Components/Inputs/Checkbox.vue";
import InputError from "@/Components/Inputs/InputError.vue";
import InputLabel from "@/Components/Inputs/InputLabel.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import InputText from "@/Components/Inputs/InputText.vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : "",
    })).post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head :title="$t('Log in')" />
    <GuestLayout>
        <div
            v-if="status"
            class="mb-4 font-medium text-sm text-green-600 dark:text-green-400 text-center"
        >
            {{ status }}
        </div>

        <div class="text-center mb-5">
            <h1 class="block text-2xl font-bold text-alterno">
                Iniciar sesión
            </h1>
            <p class="mt-3 text-sm text-gray-600">
                ¿Aún no tienes una cuenta?
                <Link
                    :href="route('register')"
                    class="text-primario hover:underline decoration-2 font-semibold"
                >
                    Registrate aquí
                </Link>
            </p>
        </div>

        <a
            type="button"
            class="w-full py-2 px-3 inline-flex justify-center items-center gap-2 rounded-sm border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-0 focus:ring-offset-0 focus:ring-offset-white focus:ring-primary transition-all text-sm"
        >
            <img src="/img/google.webp" class="w-4 h-4 mr-1" alt="google-img" />
            Continuar con Google
        </a>

        <InputError class="mt-2" :message="$page.props.errors.google" />

        <div class="w-full flex justify-between items-center">
            <hr class="my-7 border-1 bg-gray-200 flex-[2px]" />
            <div class="bg-white text-gray-600 text-xs leading-[18px] px-2.5">
                O
            </div>
            <hr class="my-7 border-1 bg-gray-200 flex-1" />
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" :value="$t('Email')" />
                <InputText
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <div class="flex justify-between items-center">
                    <InputLabel for="password" :value="$t('Password')" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-primario font-semibold decoration-2 hover:underline"
                    >
                        ¿Olvidó su contraseña?
                    </Link>
                </div>
                <div class="relative">
                    <InputText
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="mt-1 block w-full"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ml-3 text-sm text-gray-600">{{
                        $t("Remember me")
                    }}</span>
                </label>
            </div>

            <PrimaryButton
                class="w-full mt-6 py-3 justify-center"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                {{ $t("Log in") }}
            </PrimaryButton>
        </form>
    </GuestLayout>
</template>
