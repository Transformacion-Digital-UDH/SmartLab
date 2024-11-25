<script setup>
import { Input, InputPassword } from 'ant-design-vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

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
                    Regístrate aquí
                </Link>
            </p>
        </div>

        <a href="{{ route('google') }}"
           class="w-full py-2 px-3 inline-flex justify-center items-center gap-2 rounded-sm border font-medium bg-white text-gray-700 shadow-sm align-middle hover:bg-gray-50 focus:outline-none focus:ring-0 transition-all">
            <img src="/images/google.webp" class="w-4 h-4 mr-1" alt="google-icon">
            Continuar con Google
        </a>

        <div class="w-full flex justify-between items-center my-4">
            <hr class="h-px border-0 bg-gray-400 flex-1">
            <div class="text-gray-600 text-xs leading-[18px] px-2.5">O</div>
            <hr class="h-px border-0 bg-gray-400 flex-1">
        </div>

        <form @submit.prevent="submit">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <Input
                    id="email"
                    v-model:value="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    placeholder="Ingrese su email"
                    required
                    autofocus
                />
                <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">
                    {{ form.errors.email }}
                </p>
            </div>

            <div class="mt-4">
                <div class="flex justify-between items-center">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <Link :href="route('password.request')" class="text-sm text-udh_1 font-semibold decoration-2 hover:underline">
                        ¿Olvidó su contraseña?
                    </Link>
                </div>
                <InputPassword
                    id="password"
                    v-model:value="form.password"
                    placeholder="Ingrese su contraseña"
                    required
                />
                <p v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                    {{ form.errors.password }}
                </p>
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <input
                        type="checkbox"
                        v-model="form.remember"
                        class="form-checkbox rounded text-primary focus:ring-0"
                    />
                    <span class="ms-3 text-gray-600 dark:text-gray-400">Mantener sesión activa</span>
                </label>
            </div>

            <PrimaryButton class="w-full mt-6 py-3 justify-center bg-udh_3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Iniciar sesión
            </PrimaryButton>
        </form>
    </AuthenticationCard>
</template>

