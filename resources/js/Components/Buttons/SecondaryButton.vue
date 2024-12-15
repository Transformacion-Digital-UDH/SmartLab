<script setup>
defineProps({
    type: {
        type: String,
        default: "submit",
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:ring-offset-2 disabled:opacity-50 transition ease-in-out duration-150 hover:opacity-85"
    >
        <slot />
    </button>
</template>
