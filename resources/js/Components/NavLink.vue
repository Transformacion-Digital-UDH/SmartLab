<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
    as: {
        type: String,
        default: 'a', // Por defecto se renderiza como un enlace
    },
});

const classes = computed(() => {
    return props.active
        ? 'inline-flex items-center px-1 py-1 border-b-2 bg-gray-100 border-primario dark:border-indigo-600 text-sm font-medium leading-5 text-gray-900 dark:text-gray-100 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 py-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out';
});
</script>

<template>
    <component
        :is="as === 'a' ? Link : as"
        :href="as === 'a' ? href : null"
        :class="classes"
    >
        <slot />
    </component>
</template>
