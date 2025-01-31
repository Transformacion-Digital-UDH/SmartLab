<script setup>
import { Head, Link } from '@inertiajs/vue3';

</script>
<style>
@keyframes float {
        0% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-15px);
        }
        100% {
            transform: translateY(0);
        }
    }

    @keyframes particle {
        0% {
            transform: translateY(0) scale(1);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) scale(0.5);
            opacity: 0;
        }
    }
</style>
<template>
    <Head title="SmartLabs" />
    <div class="font-sans text-white bg-gradient-to-b from-blue-900 to-gray-900">
        <!-- Background Particles -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-0 w-2 h-2 particle left-1/4 animate-particle"></div>
            <div class="absolute top-0 w-3 h-3 particle left-1/2 animate-particle" style="animation-delay: 1s;"></div>
            <div class="absolute top-0 w-1 h-1 particle left-3/4 animate-particle" style="animation-delay: 1s;"></div>
            <div class="absolute top-0 w-2 h-2 particle left-1/5 animate-particle" style="animation-delay: 1s;"></div>
            <div class="absolute top-0 w-1 h-1 particle left-4/5 animate-particle" style="animation-delay: 1s;"></div>
            <div class="absolute top-0 w-1 h-1 particle left-5/6 animate-particle" style="animation-delay: 1s;"></div>
        </div>
        <!-- Navbar -->
        <header class="flex items-center justify-between p-6 ">
            <div class="text-2xl font-bold">
                <img src="/img/UDHdark.webp" alt="" width="160px" class="w-16 sm:w-24 md:w-48 lg:w-48" >
            </div>
            <nav>
                <ul class="flex gap-4 pt-4">
                    <li><a href="/" class="hover:text-green-400">Inicio</a></li>
                    <li><a href="/galeria" class="hover:text-green-400">Galeria</a></li>
                </ul>
            </nav>
            <a href="/login" class="px-4 py-2 text-black bg-yellow-300 rounded-lg hover:bg-teal-200">Login</a>
        </header>

        <!-- Hero Section -->
        <section class="relative px-4 py-24 overflow-hidden text-center">
        <h1
            class="text-4xl font-bold md:text-5xl animate-[float_3s_ease-in-out_infinite]"
        >
            Se parte <span class="text-yellow-200">da vida a tu idea</span>
        </h1>


        <!-- Imagen Superpuesta -->
        <div
            class="absolute right-0 z-0 transform translate-x-1/2 top-2/4 -rotate-12"
        >
            <img
            src="https://via.placeholder.com/600x400"
            alt="Imagen Superpuesta"
            class="shadow-lg rounded-2xl"
            />
        </div>
        </section>
        
        <section class="px-5 py-8 lg:px-20">
        <!-- Galería de Imágenes -->
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            <!-- Imagen grande que ocupa 2 columnas -->
            <div class="col-span-2 row-span-1">
            <img
                src="https://via.placeholder.com/800x400"
                alt="Imagen Grande"
                class="object-cover w-full h-full rounded-lg shadow-md"
            />
            </div>
            <!-- Otras imágenes pequeñas -->
            <div>
            <img
                src="https://via.placeholder.com/400x400"
                alt="Imagen 2"
                class="object-cover w-full h-full rounded-lg shadow-md"
            />
            </div>
            <div>
            <img
                src="https://via.placeholder.com/400x400"
                alt="Imagen 3"
                class="object-cover w-full h-full rounded-lg shadow-md"
            />
            </div>
            <div>
            <img
                src="https://via.placeholder.com/400x400"
                alt="Imagen 4"
                class="object-cover w-full h-full rounded-lg shadow-md"
            />
            </div>
            <div>
            <img
                src="https://via.placeholder.com/400x400"
                alt="Imagen 5"
                class="object-cover w-full h-full rounded-lg shadow-md"
            />
            </div>
            <!-- Segunda fila -->
            <div>
            <img
                src="https://via.placeholder.com/400x400"
                alt="Imagen 6"
                class="object-cover w-full h-full rounded-lg shadow-md"
            />
            </div>
            <div>
            <img
                src="https://via.placeholder.com/400x400"
                alt="Imagen 7"
                class="object-cover w-full h-full rounded-lg shadow-md"
            />
            </div>
            <div>
            <img
                src="https://via.placeholder.com/400x400"
                alt="Imagen 8"
                class="object-cover w-full h-full rounded-lg shadow-md"
            />
            </div>
            <div>
            <img
                src="https://via.placeholder.com/400x400"
                alt="Imagen 9"
                class="object-cover w-full h-full rounded-lg shadow-md"
            />
            </div>
            <div>
            <img
                src="https://via.placeholder.com/400x400"
                alt="Imagen 10"
                class="object-cover w-full h-full rounded-lg shadow-md"
            />
            </div>
            <div>
            <img
                src="https://via.placeholder.com/400x400"
                alt="Imagen 11"
                class="object-cover w-full h-full rounded-lg shadow-md"
            />
            </div>
        </div>

        <!-- Sección de Video -->
        <div class="flex justify-center mt-12">
            <div class="w-full md:w-3/4 lg:w-2/3">
            <div class="relative w-full h-0 pb-[56.25%]"> <!-- Aspect Ratio 16:9 -->
                <iframe
                class="absolute top-0 left-0 w-full h-full rounded-lg shadow-lg"
                src="https://www.youtube.com/embed/dQw4w9WgXcQ"
                title="Video Principal"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                ></iframe>
            </div>
            </div>
        </div>
        </section>
        
        <!-- Footer -->
        <footer class="py-6 text-center text-white ">
            <p>&copy; 2024 UDH LABS. All rights reserved.</p>
        </footer>
    </div>
</template>