<script setup>
import { nextTick, ref } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/Inputs/InputError.vue";
import InputLabel from "@/Components/Inputs/InputLabel.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import InputText from "@/Components/Inputs/InputText.vue";

const recovery = ref(false);

const form = useForm({
    code: "",
    recovery_code: "",
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = "";
    } else {
        codeInput.value.focus();
        form.recovery_code = "";
    }
};

const submit = () => {
    form.post(route("two-factor.login"));
};
</script>

<template>
    <Head title="Two-factor Confirmation" />
    <GuestLayout>
        <div class="text-center mb-5">
            <h1 class="block text-xl font-bold text-alterno">
                Autenticación de dos factores
            </h1>
        </div>
        <div class="mb-4 text-sm text-gray-600">
            <template v-if="!recovery">
                Por favor, confirme el acceso a su cuenta ingresando el código
                de autenticación proporcionado por su aplicación de
                autenticación.
            </template>

            <template v-else>
                Confirme el acceso a su cuenta ingresando uno de sus códigos de
                recuperación de emergencia.
            </template>
        </div>

        <form @submit.prevent="submit">
            <div v-if="!recovery">
                <InputLabel for="code" :value="$t('Código de autenticación')" />
                <InputText
                    id="code"
                    ref="codeInput"
                    v-model="form.code"
                    type="text"
                    inputmode="numeric"
                    class="mt-1 block w-full"
                    autofocus
                    autocomplete="one-time-code"
                />
                <InputError class="mt-2" :message="form.errors.code" />
            </div>

            <div v-else>
                <InputLabel for="recovery_code" :value="$t('Recovery Code')" />
                <InputText
                    id="recovery_code"
                    ref="recoveryCodeInput"
                    v-model="form.recovery_code"
                    type="text"
                    class="mt-1 block w-full"
                    autocomplete="one-time-code"
                />
                <InputError class="mt-2" :message="form.errors.recovery_code" />
            </div>

            <div class="flex items-center justify-between mt-4">
                <button
                    type="button"
                    class="text-sm text-gray-600 hover:text-gray-900 underline cursor-pointer"
                    @click.prevent="toggleRecovery"
                >
                    <template v-if="!recovery">
                        Use código de recuperación
                    </template>

                    <template v-else> Use código de autenticación </template>
                </button>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    {{ $t("Log in") }}
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
