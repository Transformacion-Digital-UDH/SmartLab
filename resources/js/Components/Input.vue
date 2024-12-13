<script setup>
import { onMounted, ref } from 'vue';

// Definir las props que el componente aceptará
const props = defineProps({
    modelValue: [String, Number], // Soporta valores de texto y número
    type: {
        type: String,
        default: 'text' // El tipo por defecto es texto
    },
    id: {
        type: String,
        default: ''
    },
    placeholder: {
        type: String,
        default: ''
    },
    autofocus: {
        type: Boolean,
        default: false
    },
    disabled: {
        type: Boolean,
        default: false
    },
    customClass: {
        type: String,
        default: ''
    }
});

// Definir los eventos que el componente emitirá
const emit = defineEmits(['update:modelValue']);

// Referencia para el input
const input = ref(null);

// Enfocar el campo automáticamente si la propiedad autofocus es verdadera
onMounted(() => {
    if (props.autofocus && input.value) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        :id="id"
        ref="input"
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        :class="'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm ' + customClass"
    >
</template>
