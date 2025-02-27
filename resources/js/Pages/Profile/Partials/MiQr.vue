<script setup>
import { ref, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import ActionSection from "@/Components/ActionSection.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { QRCode } from "ant-design-vue";
import html2canvas from "html2canvas";

const user = usePage().props.auth.user;

// Código o DNI para generar el QR
const codigoODNI = ref(
    user.codigo && user.codigo.trim() ? user.codigo : user.dni
);

const icon = ref("");
const mensajeDescarga = ref("");

// Descargar el fotochek
const descargarFotochek = async () => {
    const descargarDiv = document.getElementById("descargar");
    if (!descargarDiv) {
        mensajeDescarga.value = "No se encontró el contenido para descargar.";
        return;
    }

    mensajeDescarga.value = "Generando la imagen, por favor espera...";
    try {
        const canvas = await html2canvas(descargarDiv, {
            useCORS: true,
            scale: 10, // Aumenta la calidad de la imagen
        });

        // Crear el enlace para descargar la imagen
        const enlace = document.createElement("a");
        enlace.href = canvas.toDataURL("image/png");
        enlace.download = `QR-${codigoODNI.value}.png`;
        enlace.click();

        mensajeDescarga.value = "Imagen descargada exitosamente.";
    } catch (error) {
        console.error("Error al generar la imagen:", error);
        mensajeDescarga.value =
            "Ocurrió un error al generar la imagen. Intenta nuevamente.";
    }
};

// Convertir la imagen a Base64
const getImageBase64 = async (url) => {
    const response = await fetch(url);
    const blob = await response.blob();

    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result); // Cadena Base64
        reader.onerror = reject;
        reader.readAsDataURL(blob); // Convertir blob en base64
    });
};

// Convertir la imagen a Base64
onMounted(async () => {
    const imageUrl = `${window.location.origin}/img/logotipo.png`;
    icon.value = await getImageBase64(imageUrl);
});
</script>

<template>
    <ActionSection>
        <template #title> Mi Código QR Personal </template>

        <template #description>
            Descarga este código QR para identificarse y acceder a los
            laboratorios de investigación.
        </template>

        <template #content>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Columna izquierda: QR -->
                <div class="flex flex-col items-center">
                    <div
                        class="py-4 px-8 inline-block bg-gray-100 rounded text-center"
                        id="descargar"
                    >
                        <span class="font-bold text-lg block mt-4">{{
                            user.nombres
                        }}</span>
                        <p class="text-lg">{{ user.apellidos }}</p>
                        <div class="bg-white rounded-[8px]">
                            <QRCode
                                :size="250"
                                :value="codigoODNI"
                                :icon="icon"
                                :icon-size="55"
                                error-level="H"
                                type="png"
                            />
                        </div>
                        <p class="text-sm mt-2">{{ codigoODNI }}</p>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        {{ mensajeDescarga }}
                    </p>
                </div>

                <!-- Columna derecha: Información -->
                <div class="space-y-4 text-gray-600 dark:text-gray-400">
                    <div>
                        <p class="font-semibold">Descargar</p>
                        <p>
                            Haz clic en el botón de abajo para descargar tu
                            código QR.
                        </p>
                        <div class="my-8">
                            <PrimaryButton @click="descargarFotochek">
                                Descargar Código QR
                            </PrimaryButton>
                        </div>
                    </div>

                    <div>
                        <p class="font-semibold">¿Cómo usarlo?</p>
                        <p>
                            Para ingresar a los laboratorios de investigación,
                            escanea tu código QR utilizando el lector ubicado en
                            la puerta de entrada de los laboratorios.
                        </p>
                    </div>

                    <div>
                        <p class="font-semibold">¿Problemas?</p>
                        <p>
                            Si tienes problemas al usar tu código QR, solicita
                            ayuda al Responsable del Laboratorio.
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </ActionSection>
</template>
