<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/Inputs/InputError.vue";
import InputLabel from "@/Components/Inputs/InputLabel.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import InputText from "@/Components/Inputs/InputText.vue";

defineProps({
    status: String,
});

const form = useForm({
    email: "",
});

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <Head :title="$t('Olvidó')" />

    <GuestLayout>
        <div class="text-center mb-5">
            <h1 class="block text-xl font-bold text-alterno">
                ¿Olvidó su contraseña?
            </h1>
        </div>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            No hay problema. Ingresa tu correo electrónico y te enviaremos un
            enlace para que la restablezcas y elijas una nueva.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4">
                <InputLabel for="email" :value="$t('Email')" />
                <InputText
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

            <div class="flex items-center justify-between mt-5">
                <Link
                    :href="route('login')"
                    class="text-primario hover:underline decoration-2 font-bold"
                >
                    {{ $t("Log in") }}
                </Link>
                <PrimaryButton
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ $t("Restablecer") }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
