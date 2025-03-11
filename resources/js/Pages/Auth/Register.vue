<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import Checkbox from "@/Components/Inputs/Checkbox.vue";
import InputError from "@/Components/Inputs/InputError.vue";
import InputLabel from "@/Components/Inputs/InputLabel.vue";
import PrimaryButton from "@/Components/Buttons/PrimaryButton.vue";
import InputText from "@/Components/Inputs/InputText.vue";
import { ref } from "vue"; // Importar ref para estado reactivo

const form = useForm({
    email: "",
    password: "",
    password_confirmation: "",
    terms: false,
});

// Estado para controlar la visualización del modal
const showModal = ref(false);

// Función para validar si el correo es de la UDH
const isUDHEmail = (email) => {
    return email.endsWith('@udh.edu.pe');
};

// Método para enviar el formulario
const submitForm = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

// Método para cambiar el correo (limpiar el formulario)
const changeEmail = () => {
    form.email = "";
    showModal.value = false;
};

// Método para continuar con el registro a pesar de no ser correo UDH
const continueRegistration = () => {
    showModal.value = false;
    submitForm();
};

const submit = () => {
    if (!isUDHEmail(form.email)) {
        // Si no es correo de la UDH, mostrar el modal
        showModal.value = true;
    } else {
        // Si es correo de la UDH, enviar el formulario normalmente
        submitForm();
    }
};
</script>

<template>
    <Head :title="$t('Register')" />

    <GuestLayout>
        <!-- Modal para correo no UDH -->
        <div v-if="showModal" class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6 z-50 relative">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Correo externo detectado</h3>
                <p class="mb-4 text-gray-600">
                    El correo usado no pertenece a la Universidad de Huánuco. Los correos externos requieren aprobación del administrador.
                    ¿Desea cambiar de correo o continuar?
                </p>
                <div class="flex justify-end space-x-3">
                    <button 
                        @click="changeEmail"
                        class="bg-gray-200 px-4 py-2 rounded-md text-gray-700 hover:bg-gray-300"
                    >
                        Cambiar correo
                    </button>
                    <button 
                        @click="continueRegistration"
                        class="bg-primario px-4 py-2 rounded-md text-white hover:bg-primario-dark"
                    >
                        Continuar
                    </button>
                </div>
            </div>
        </div>

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
