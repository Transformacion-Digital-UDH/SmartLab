<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    mensaje: {
        type: String,
        default: 'Operación realizada con exito.',
    },
    show: {
        type: Boolean,
        default: false,
    },
    duration: {
        type: Number,
        default: 3000, // Duración en milisegundos
    },
});

const emit = defineEmits(['close']);

const visible = ref(false);

onMounted(() => {
    // Muestra el toast cuando el componente se monta
    visible.value = props.show;

    // Desaparece automáticamente después de un tiempo
    setTimeout(() => {
        visible.value = false;
        emit('close'); // Emite el evento close cuando desaparece
    }, props.duration);
});

// Reinicia el timer si el prop show cambia
watch(() => props.show, (newVal) => {
    if (newVal) {
        visible.value = true;
        setTimeout(() => {
            visible.value = false;
            emit('close');
        }, props.duration);
    }
});
</script>

<template>
    <!-- Transición para el toast -->
    <transition name="fade">
        <div v-if="visible" class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50">
            {{ mensaje }}
        </div>
    </transition>
</template>

<style scoped>
/* Transición para el fade in/out */
.fade-enter-active, .fade-leave-active {
    transition: opacity 0.5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <=2.1.8 */ {
    opacity: 0;
}
</style>
