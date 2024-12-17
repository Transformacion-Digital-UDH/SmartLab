<script setup>
import { Head } from '@inertiajs/vue3';
import Banner from '@/Components/Banner.vue';
import Navbar from './Navbar.vue';

defineProps({
    title: String,
});
</script>

<template>
    <div>
        <Head :title="title" />
        <Banner />
        <div class="min-h-screen max-h-screen bg-gray-100 dark:!bg-gray-900 sm:flex">
            <Navbar />
            <div class="w-full max-h-screen overflow-auto">
                <header v-if="$slots.header" class="bg-white dark:!bg-gray-800 shadow-sm sticky top-0 z-10">
                    <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8 ">
                        <slot name="header" />
                    </div>
                </header>

                <main >
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>


