<script setup>

defineProps({
    name: String,
    des: {
        type: String,
        default: 'Activos',
    },
})
</script>

<template>
    <div
        class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-md sm:rounded-lg p-4 border-gray-100 border"
    >
        <h3 class="text-base text-gray-500 font-normal">{{ name }}</h3>
        <p class="text-2xl font-bold pt-6 mb-0">10</p>
        <span class="text-gray-500 text-sm">{{ des }}</span>
        
    </div>
</template>
