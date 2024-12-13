
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
    name: props.user.name,
    phone: props.user.phone,
});

const submit = () => {
    form.errors = {};
    form.post(route("completar.registro"));
};
</script>

<template>
    <Head title="Completar Registro" />
    <GuestLayout>
        <div class="text-center mb-5">
            <h1 class="block text-xl font-bold text-alterno">Datos Personales</h1>
        </div>

        <div class="mb-6">
            <p class="text-sm text-gray-600">
                Antes de continuar, le solicitamos amablemente que complete sus
                datos personales.
            </p>
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" :value="$t('Name')" />
                <InputText
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-5">
                <InputLabel for="phone" :value="$t('Phone')" />
                <InputText
                    id="phone"
                    v-model="form.phone"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autocomplete="off"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
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
