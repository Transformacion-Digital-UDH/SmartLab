<script setup>
import { Link } from "@inertiajs/vue3";
import AplicationLogo from "@/Components/ApplicationLogo.vue";

</script>

<template>
    <div class="min-h-screen flex flex-col justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <Link :href="'/'">
            <AplicationLogo class="h-14 mb-4" />
        </Link>

        <div class="w-full sm:max-w-md mx-auto px-6">
            <div class="bg-white rounded-sm shadow-lg overflow-hidden p-4 sm:p-7">
                <slot />
            </div>
        </div>
    </div>
</template>
