<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Checkbox from "@/Components/Inputs/Checkbox.vue";
import InputError from "@/Components/Inputs/InputError.vue";
import InputLabel from "@/Components/Inputs/InputLabel.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import InputText from "@/Components/Inputs/InputText.vue";

const form = useForm({
    email: "",
    password: "",
    password_confirmation: "",
    terms: false,
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head :title="$t('Register')" />

    <GuestLayout>
        <div class="text-center mb-6">
            <h1 class="block text-2xl font-bold text-prim">
                {{ $t("Register") }}
            </h1>
            <p class="mt-3 text-sm text-gray-600">
                ¿Ya tienes una cuenta?
                <Link
                    :href="route('login')"
                    class="text-primario hover:underline decoration-2 font-semibold"
                >
                    Inicia sesión aquí
                </Link>
            </p>
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
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-8">
                <InputLabel for="password" :value="$t('Password')" />
                <InputText
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    :value="$t('Confirm Password')"
                />
                <InputText
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="new-password"
                />
                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div
                v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature"
                class="mt-4"
            >
                <label for="terms">
                    <div class="flex items-start">
                        <Checkbox
                            id="terms"
                            v-model:checked="form.terms"
                            name="terms"
                            class="mt-1"
                            required
                        />

                        <span class="ml-3 text-sm text-gray-600">
                            He leído y acepto los
                            <a
                                :href="route('terms.show')"
                                target="_blank"
                                class="hover:underline hover:text-primario decoration-2 font-semibold"
                            >
                                Términos de uso
                            </a>
                            y la
                            <a
                                :href="route('policy.show')"
                                target="_blank"
                                class="hover:underline hover:text-primario decoration-2 font-semibold"
                            >
                                Política de Privacidad
                            </a>
                            .
                        </span>
                    </div>
                    <InputError class="mt-2" :message="form.errors.terms" />
                </label>
            </div>

            <PrimaryButton
                class="w-full mt-6 py-3 justify-center"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                {{ $t("Register") }}
            </PrimaryButton>
        </form>
    </GuestLayout>
</template>
