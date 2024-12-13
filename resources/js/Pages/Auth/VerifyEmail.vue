<script setup>
import { computed } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";

const props = defineProps({
    status: String,
});

const form = useForm({});

const submit = () => {
    form.post(route("verification.send"));
};

const verificationLinkSent = computed(
    () => props.status === "verification-link-sent"
);
</script>

<template>
    <Head :title="$t('Verificar correo')" />

    <GuestLayout>
        <div class="mb-4 text-sm text-gray-600">
            <p>
                <b class="text-prim">¡Gracias por registrarte!</b>
                Para comenzar verifica tu dirección de correo electrónico.
            </p>
            <p class="d-block pt-3">
                Por favor,
                <b class="text-prim">revisa tu bandeja de entrada</b> y haz
                clic en el enlace de verificación que te hemos enviado al correo
                <b class="text-prim font-bold">{{ $page.props.auth.user.email }}</b>. Si no encuentras el
                correo, puedes solicitar que te enviemos otro.
            </p>
        </div>

        <div
            v-if="verificationLinkSent"
            class="mb-4 font-medium text-sm text-green-600"
        >
            Se ha enviado un nuevo enlace de verificación.
        </div>

        <form @submit.prevent="submit">
            <PrimaryButton
                class="w-full mt-2 py-3 justify-center"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                SOLICITAR OTRO CORREO
            </PrimaryButton>

            <div class="mt-6 flex items-center justify-between">
                <Link
                    :href="route('profile.show')"
                    class="text-primario hover:underline decoration-2 font-semibold"
                >
                    {{ $t("Edit Profile") }}
                </Link>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-primario hover:underline decoration-2 font-semibold"
                >
                    {{ $t("Log Out") }}
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
