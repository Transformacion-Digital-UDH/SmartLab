<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
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

    .particle {
        animation: particle 10s linear infinite;
        background-color: rgba(255, 255, 255, 0.541);
        border-radius: 50%;
    }
</style>
<template>
    <Head title="Welcome" />

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
        <header class="flex items-center justify-between p-6">
            <div class="text-2xl font-bold">
                <img src="/img/UDHdark.webp" alt="" width="160px" class="w-16 sm:w-24 md:w-48 lg:w-48" >
            </div>
            <nav>
                <ul class="flex gap-6">
                    <li><a href="#about" class="hover:text-green-400">Labs</a></li>
                    <li><a href="#projects" class="hover:text-green-400">Galeria</a></li>
                </ul>
            </nav>
            <a href="/login" class="px-4 py-2 text-black bg-teal-400 rounded-lg hover:bg-teal-200">Login</a>
        </header>

        <!-- Hero Section -->
        <section class="px-4 py-20 text-center ">
            <h1 class="text-4xl font-bold md:text-5xl  font-bold animate-[float_3s_ease-in-out_infinite]">
                Hi’ <span class="text-teal-400">quieres dar vida a tu idea</span>
            </h1>
            <p class="mt-4 text-2xl md:text-4xl">Pero no sabes como iniciar?</p>
            <div class="flex flex-col items-center gap-4 mt-6 md:flex-row md:justify-center">
                <div class="p-4 text-black rounded-lg bg-amber-100">
                    <p>No te preocupes <span class="font-bold">nuestra universidad te ayuda</span></p>
                    <p>La UDH cuenta con laboratorios en diferentes areas
                        <br> para que puedas desarrollar tus proyectos</p>
                </div>
                <div class="p-4 text-black bg-teal-100 rounded-lg">
                    <h3 class="mb-3 font-bold">Que Lograre?</h3>
                    <div class="flex gap-4">
                        <div>
                            <p>Investigar</p>
                            <div class="w-20 h-2 overflow-hidden bg-gray-700 rounded-full">
                                <div class="h-full bg-green-400" style="width: 90%;"></div>
                            </div>
                        </div>
                        <div>
                            <p>Aprender</p>
                            <div class="w-20 h-2 overflow-hidden bg-gray-700 rounded-full">
                                <div class="h-full bg-green-400" style="width: 90%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-4 mt-10">
                        <div>
                            <p>Recursos</p>
                            <div class="w-20 h-2 overflow-hidden bg-gray-700 rounded-full">
                                <div class="h-full bg-green-400" style="width: 90%;"></div>
                            </div>
                        </div>
                        <div>
                            <p>Guia</p>
                            <div class="w-20 h-2 overflow-hidden bg-gray-700 rounded-full">
                                <div class="h-full bg-green-400" style="width: 90%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="px-6 py-2 mt-10 text-black rounded-lg bg-amber-100 hover:bg-amber-400 animate-pulse">Iniciar Proyecto</button>
        </section>

        <!-- Stats Section -->
        <section class="flex items-center justify-center h-auto p-5 bg-teal-500 md:h-64">
            <div class="grid max-w-6xl grid-cols-1 gap-8 text-center text-white md:grid-cols-4">
                <div>
                    <p class="text-3xl font-bold">25+</p>
                    <p class="mt-2">Encargados con experiencia</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">6,500+</p>
                    <p class="mt-2">Horas de laboratorio ofrecidas</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">100+</p>
                    <p class="mt-2">Proyectos ejecutados</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">6,561+</p>
                    <p class="mt-2">Estudiantes beneficiados</p>
                </div>
            </div>
        </section>
        
        <!-- About Section -->
        <section id="about" class="py-20 text-center">
            <h2 class="text-3xl font-bold  animate-[float_5s_ease-in-out_infinite]">Pero, como me pongo en contacto?</h2>
            <p class="max-w-3xl mx-auto mt-6">
                Ahora en su nueva plataforma podras encontrar toda la informacion que necesitas <br>
                ver horarios, ver la disponibilidad de los laboratorios, ver los recursos de los laboratorios <br>
                registrater e ir como invitado o volverte miembro, planificar tu cronograma de trabajo y más.
            </p>
            <button class="px-6 py-2 mt-6 text-black bg-teal-400 rounded-lg hover:bg-teal-500">Ver Galeria</button>
        </section>

        <!-- Skills Section -->
        <section id="skills" class="px-10 py-20 text-center bg-teal-500">
            <h2 class="text-3xl font-bold text-white">Requisitos?</h2>
            <div class="grid max-w-4xl grid-cols-2 gap-6 mx-auto mt-8 md:grid-cols-4">
                <div class="flex flex-col items-center p-6 bg-teal-300 rounded-lg">
                    <!-- Icono -->
                    <p class="mt-2">Ser parte de la UDH</p>
                </div>
                <div class="flex flex-col items-center p-6 bg-teal-300 rounded-lg">
                    <p class="mt-2">Tener un proyecto en mente</p>
                </div>
                <div class="flex flex-col items-center p-6 bg-teal-300 rounded-lg">
                    <p class="mt-2">Registrarte y ponerte en contacto</p>
                </div>
                <div class="flex flex-col items-center p-6 bg-teal-300 rounded-lg">
                    <p class="mt-2 ">Iniciar tu proyecto</p>
                </div>
            </div>
            <p class="mt-8 text-xl text-white">Ahora las posibilidades más cerca de ti</p>
        </section>

        <!-- Footer -->
        <footer class="py-6 text-center text-white ">
            <p>&copy; 2024 UDH LABS. All rights reserved.</p>
        </footer>
    </div>

</template>
