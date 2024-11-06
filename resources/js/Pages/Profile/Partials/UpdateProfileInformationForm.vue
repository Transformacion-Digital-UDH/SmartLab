<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    nombres: props.user.nombres,
    apellidos: props.user.apellidos,
    dni: props.user.dni,
    email: props.user.email,
    codigo: props.user.codigo,
    rol: props.user.rol,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
        <template #title>
            Información del perfil
        </template>

        <template #description>
            Actualiza la información de perfil y correo electrónico de tu cuenta.
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
                <input id="photo" ref="photoInput" type="file" class="hidden" @change="updatePhotoPreview">

                <InputLabel for="photo" value="Foto de Perfil" />

                <div v-show="!photoPreview" class="mt-2">
                    <img :src="user.profile_photo_url" :alt="user.nombres" class="rounded-full h-20 w-20 object-cover">
                </div>

                <div v-show="photoPreview" class="mt-2">
                    <span
                        class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                        :style="'background-image: url(\'' + photoPreview + '\');'"
                    />
                </div>

                <SecondaryButton class="mt-2 me-2" type="button" @click.prevent="selectNewPhoto">
                    Seleccionar nueva foto
                </SecondaryButton>

                <SecondaryButton v-if="user.profile_photo_path" type="button" class="mt-2" @click.prevent="deletePhoto">
                    Eliminar foto
                </SecondaryButton>

                <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- Nombres -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="nombres" value="Nombres" />
                <TextInput id="nombres" v-model="form.nombres" type="text" class="mt-1 block w-full" required autocomplete="nombres" />
                <InputError :message="form.errors.nombres" class="mt-2" />
            </div>

            <!-- Apellidos -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="apellidos" value="Apellidos" />
                <TextInput id="apellidos" v-model="form.apellidos" type="text" class="mt-1 block w-full" required autocomplete="apellidos" />
                <InputError :message="form.errors.apellidos" class="mt-2" />
            </div>

            <!-- DNI -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="dni" value="DNI" />
                <TextInput id="dni" v-model="form.dni" type="text" class="mt-1 block w-full" required autocomplete="dni" />
                <InputError :message="form.errors.dni" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="email" value="Email" />
                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autocomplete="username" />
                <InputError :message="form.errors.email" class="mt-2" />

                <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                    <p class="text-sm mt-2 dark:text-white">
                        Tu dirección de correo no está verificada.

                        <Link :href="route('verification.send')" method="post" as="button" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" @click.prevent="sendEmailVerification">
                            Haz clic aquí para reenviar el correo de verificación.
                        </Link>
                    </p>

                    <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                        Se ha enviado un nuevo enlace de verificación a tu correo.
                    </div>
                </div>
            </div>

            <!-- Código -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel for="codigo" value="Código" />
                <TextInput id="codigo" v-model="form.codigo" type="text" class="mt-1 block w-full" autocomplete="codigo" />
                <InputError :message="form.errors.codigo" class="mt-2" />
            </div>

        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="me-3">
                Guardado.
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Guardar
            </PrimaryButton>
        </template>
    </FormSection>
</template>
