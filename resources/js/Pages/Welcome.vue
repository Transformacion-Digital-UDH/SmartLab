<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from "vue";

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

const counters = ref([
    { value: 25, displayValue: 0, label: "Encargados con experiencia" },
    { value: 6500, displayValue: 0, label: "Horas de laboratorio ofrecidas" },
    { value: 100, displayValue: 0, label: "Proyectos ejecutados" },
    { value: 6561, displayValue: 0, label: "Estudiantes beneficiados" },
]);

function animateCounter(counter) {
    const duration = 2000; // Duración en milisegundos (2 segundos)
    const start = performance.now();

    function updateCounter(timestamp) {
        const progress = Math.min((timestamp - start) / duration, 1);
        counter.displayValue = Math.floor(progress * counter.value);

        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        } else {
            counter.displayValue = counter.value;
        }
    }

    requestAnimationFrame(updateCounter);
}

onMounted(() => {
    counters.value.forEach((counter) => animateCounter(counter));
});

const items = ref([
    {
        image: "https://via.placeholder.com/400x200",
        title: "LTD - DEV: Copiloto",
        description:
            "Découvrez les événements récents qui méritent votre attention.",
    },
    {
        image: "https://via.placeholder.com/400x200",
        title: "STARTLAB: Lamparas Temáticas",
        description:
            "Découvrez les événements récents qui méritent votre attention.",
    },
    {
        image: "https://via.placeholder.com/400x200",
        title:
            "LTD - IA: Asistente de Voz",
        description: "Découvrez les événements récents qui méritent votre attention.",
    },
    {
        image: "https://via.placeholder.com/400x200",
        title: "LTD - Recolector Digital",
        description:
            "Découvrez les événements récents qui méritent votre attention.",
    },
    {
        image: "https://via.placeholder.com/400x200",
        title: "LTD - San Sebastián Digital",
        description:
            "Découvrez les événements récents qui méritent votre attention.",
    },
    {
        image: "https://via.placeholder.com/400x200",
        title: "LTD - Turismo VR",
        description:
            "Découvrez les événements récents qui méritent votre attention.",
    },
]);

const currentIndex = ref(0);

function next() {
    currentIndex.value = (currentIndex.value + 1) % items.value.length;
}

function prev() {
    currentIndex.value =
        (currentIndex.value - 1 + items.value.length) % items.value.length;
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

/* Animación personalizada para girar y flotar */
@keyframes float-and-rotate {
    0% {
        transform: translateY(0) rotate(0deg);
    }

    50% {
        transform: translateY(-10px) rotate(10deg);
    }

    100% {
        transform: translateY(0) rotate(0deg);
    }
}

/* Aplicar la animación en Tailwind */
.animate-float-and-rotate {
    animation: float-and-rotate 10s ease-in-out infinite;
}
</style>
<template>

    <Head title="Bienvenido" />

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
                <img src="/img/UDHdark.webp" alt="" width="160px" class="w-16 sm:w-24 md:w-40 lg:w-40">
            </div>
            <nav>
                <ul class="flex gap-6 pt-4">
                    <li><a href="/" class="hover:text-green-400">Inicio</a></li>
                    <li><a href="/galeria" class="hover:text-green-400">Galeria</a></li>
                </ul>
            </nav>
            <div class="flex space-x-2">
                <template v-if="$page.props.auth && $page.props.auth.user">
                    <!-- Usuario autenticado -->
                    <Link :href="route('dashboard')"
                        class="px-4 py-2 text-black bg-yellow-300 rounded-lg hover:bg-teal-200">
                    Ingresar
                    </Link>
                    <Link :href="route('logout')" method="post" as="button"
                        class="px-4 py-2 text-black bg-red-300 rounded-lg hover:bg-red-400">
                    Cerrar sesión
                    </Link>
                </template>
                <template v-else>
                    <!-- Usuario no autenticado -->
                    <Link :href="route('login')"
                        class="px-4 py-2 text-black bg-yellow-300 rounded-lg hover:bg-teal-200">
                    Login
                    </Link>
                    <Link v-if="canRegister" :href="route('register')"
                        class="px-4 py-2 text-black bg-teal-300 rounded-lg hover:bg-yellow-200">
                    Registrarse
                    </Link>
                </template>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative px-4 py-24 overflow-hidden text-center">
            <h1 class="text-4xl font-bold md:text-5xl font-bold animate-[float_3s_ease-in-out_infinite]">
                Hi’ <span class="text-yellow-200">quieres dar vida a tu idea</span>
            </h1>
            <p class="mt-4 text-2xl md:text-4xl">Pero no sabes como iniciar?</p>

            <!-- Contenido principal -->
            <div class="flex flex-col items-center gap-4 mt-6 md:flex-row md:justify-center">
                <div class="z-10 p-4 text-black rounded-lg bg-amber-100">
                    <p>
                        No te preocupes <span class="font-bold">nuestra universidad te ayuda</span>
                    </p>
                    <p>
                        La UDH cuenta con laboratorios en diferentes areas <br />
                        para que puedas desarrollar tus proyectos
                    </p>
                </div>
                <div class="z-10 p-4 text-white rounded-lg bg-sky-200/20">
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

            <!-- Botón -->
            <button class="px-6 py-2 mt-10 text-black rounded-lg bg-amber-100 hover:bg-amber-400 animate-pulse">
                Iniciar Proyecto
            </button>

            <!-- Imagen Superpuesta -->
            <div class="absolute right-0 z-0 transform translate-x-1/4 top-2/4 -rotate-12">
                <img src="/img/robot.webp" alt="Imagen robot saludando" class="shadow-lg rounded-2xl" />
            </div>
        </section>

        <!-- Stats Section
        <section class="flex items-center justify-center h-auto p-5 bg-white md:h-64">
            <div class="grid max-w-6xl grid-cols-1 gap-8 text-center text-blue-800 md:grid-cols-4">
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
        </section>-->
        <section class="flex items-center justify-center h-auto p-5 bg-white md:h-64">
            <div class="grid max-w-6xl grid-cols-1 gap-8 text-center text-blue-800 md:grid-cols-4">
                <div v-for="(counter, index) in counters" :key="index">
                    <p class="text-3xl font-bold">{{ counter.displayValue }}+</p>
                    <p class="mt-2">{{ counter.label }}</p>
                </div>
            </div>
        </section>

        <!-- Labs Section -->
        <div class="container p-6 py-20 mx-auto ">
            <!-- Título -->
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-yellow-200">Nuestros laboratorios</h1>
                <button class="px-4 py-2 text-sm text-black bg-yellow-400 rounded hover:bg-yellow-500">
                    Ver todos
                </button>
            </div>

            <!-- Contenido -->
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <!-- Primera Columna: Contenido principal -->
                <div class="md:col-span-2">
                    <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                        <!-- Imagen -->
                        <div class="flex items-center justify-center h-56 bg-gray-300">
                            <span class="text-lg text-gray-500">Image</span>
                        </div>
                        <!-- Descripción -->
                        <div class="p-6">
                            <p class="mb-2 text-sm text-gray-500">Ingenieria de Sistemas e Informática</p>
                            <h2 class="mb-4 text-2xl font-bold text-gray-800">Laboratorio de Transformación Digital
                                (LTD)</h2>
                            <p class="text-sm leading-relaxed text-gray-600">
                                Descubre mucho más, crea e innova con proyectos tecnológicos...
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Segunda Columna: Historias más pequeñas -->
                <div class="space-y-4">
                    <!-- Tarjeta 1 -->
                    <div class="flex items-center bg-white rounded-lg shadow-md">
                        <div class="flex items-center justify-center w-24 h-24 bg-gray-300">
                            <span class="text-gray-500">Lab</span>
                        </div>
                        <div class="p-4">
                            <p class="mb-1 text-xs text-gray-500">Administración</p>
                            <h3 class="text-sm font-bold text-gray-800">StartLab: Inicia las bases de tu producto</h3>
                        </div>
                    </div>
                    <!-- Tarjeta 2 -->
                    <div class="flex items-center bg-white rounded-lg shadow-md">
                        <div class="flex items-center justify-center w-24 h-24 bg-gray-300">
                            <span class="text-gray-500">Lab</span>
                        </div>
                        <div class="p-4">
                            <p class="mb-1 text-xs text-gray-500">Arquitectura</p>
                            <h3 class="text-sm font-bold text-gray-800">ArquiLab: Diseña, crea, plantea nuevas
                                estructuras</h3>
                        </div>
                    </div>
                    <!-- Tarjeta 3 -->
                    <div class="flex items-center bg-white rounded-lg shadow-md">
                        <div class="flex items-center justify-center w-24 h-24 bg-gray-300">
                            <span class="text-gray-500">Lab</span>
                        </div>
                        <div class="p-4">
                            <p class="mb-1 text-xs text-gray-500">Enfermería</p>
                            <h3 class="text-sm font-bold text-gray-800">BioLab: Investiga organismos</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carrusel Section
        <div class="flex items-center justify-center py-20 bg-gradient-to-b from-white to-blue-400">
            <div class="container px-6 py-10 mx-auto">
                <div class="mb-8 text-center">
                    <p class="text-lg italic text-blue-900">Conoce Más</p>
                    <h1 class="text-4xl font-bold text-blue-900">Nuestros Proyectos</h1>
                </div>
                <div class="relative overflow-hidden">
                <div id="carousel" class="flex transition-transform duration-700 ease-in-out">
                    <div class="flex-shrink-0 w-full px-4 md:w-1/3">
                    <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                        <img src="https://via.placeholder.com/400x200" alt="Actualité 1" class="object-cover w-full h-48">
                        <div class="p-4">
                        <h2 class="mb-2 text-lg font-bold text-gray-600">LTD: Copiloto </h2>
                        <p class="text-sm leading-relaxed text-gray-600">
                            Nous sommes ravis de vous présenter notre nouveau site internet, plus moderne et intuitif.
                        </p>
                        </div>
                    </div>
                    </div>
                    <div class="flex-shrink-0 w-full px-4 md:w-1/3">
                    <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                        <img src="https://via.placeholder.com/400x200" alt="Actualité 2" class="object-cover w-full h-48">
                        <div class="p-4">
                        <h2 class="mb-2 text-lg font-bold text-gray-600">STARTLAB: Lamparas Temáticas</h2>
                        <p class="text-sm leading-relaxed text-gray-600">
                            Si vous avez manqué quelque chose, retrouvez les dernières infos ici en un clic !
                        </p>
                        </div>
                    </div>
                    </div>
                    <div class="flex-shrink-0 w-full px-4 md:w-1/3">
                    <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                        <img src="https://via.placeholder.com/400x200" alt="Actualité 3" class="object-cover w-full h-48">
                        <div class="p-4">
                        <h2 class="mb-2 text-lg font-bold text-gray-600">LTD - IA: Asistente de Voz</h2>
                        <p class="text-sm leading-relaxed text-gray-600">
                            Découvrez les événements récents qui méritent votre attention.
                        </p>
                        </div>
                    </div>
                    </div>
                </div>
                <button id="prev" class="absolute left-0 p-2 text-gray-800 transform -translate-y-1/2 bg-white rounded-full shadow-lg top-1/2 hover:bg-gray-200">
                    &#8592;
                </button>
                <button id="next" class="absolute right-0 p-2 text-gray-800 transform -translate-y-1/2 bg-white rounded-full shadow-lg top-1/2 hover:bg-gray-200">
                    &#8594;
                </button>
                </div>
                <div class="mt-8 text-center">
                <button class="px-6 py-2 font-bold text-green-600 bg-white rounded-lg shadow hover:bg-green-100">
                    Ver todos los proyectos
                </button>
                </div>
            </div>

        </div>-->
        <section class="flex items-center justify-center py-20 bg-gradient-to-b from-white to-blue-400">
            <div class="container px-6 py-10 mx-auto">
                <!-- Título -->
                <div class="mb-8 text-center">
                    <p class="text-lg italic text-blue-900">Conoce Más</p>
                    <h1 class="text-4xl font-bold text-blue-900">Nuestros Proyectos</h1>
                </div>

                <!-- Carrusel -->
                <div class="relative overflow-hidden">
                    <div class="flex transition-transform duration-700 ease-in-out"
                        :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
                        <div v-for="(item, index) in items" :key="index" class="flex-shrink-0 w-full px-4 md:w-1/3">
                            <div class="overflow-hidden bg-white rounded-lg shadow-lg">
                                <img :src="item.image" alt="Actualité" class="object-cover w-full h-48" />
                                <div class="p-4">
                                    <h2 class="mb-2 text-lg font-bold text-gray-600">{{ item.title }}</h2>
                                    <p class="text-sm leading-relaxed text-gray-600">{{ item.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Botones de Navegación -->
                    <button @click="prev"
                        class="absolute left-0 p-2 transform -translate-y-1/2 bg-blue-800 rounded-full shadow-lg top-1/2 hover:bg-sky-500">
                        &#8592;
                    </button>
                    <button @click="next"
                        class="absolute right-0 p-2 transform -translate-y-1/2 bg-blue-800 rounded-full shadow-lg top-1/2 hover:bg-sky-500">
                        &#8594;
                    </button>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="px-5 py-20 text-center bg-gradient-to-b from-blue-400 to-blue-600">
            <h2 class="text-3xl font-bold  animate-[float_5s_ease-in-out_infinite]">Pero, como me pongo en contacto?
            </h2>
            <p class="max-w-3xl mx-auto mt-6">
                Ahora en su nueva plataforma podras encontrar toda la informacion que necesitas
                ver horarios, ver la disponibilidad de los laboratorios, ver los recursos de los laboratorios
                registrater e ir como invitado o volverte miembro, planificar tu cronograma de trabajo y más.
            </p>
            <button class="px-6 py-2 mt-6 text-white bg-blue-800 rounded-lg hover:bg-blue-900">Login</button>
        </section>

        <!-- Skills Section -->
        <section id="skills"
            class="relative px-10 py-24 overflow-hidden text-center bg-gradient-to-b from-blue-600 to-blue-900 min-h-svh ">
            <h2 class="text-3xl font-bold text-white">Requisitos?</h2>
            <div class="grid max-w-4xl grid-cols-2 gap-6 mx-auto mt-8 md:grid-cols-4">
                <div class="flex flex-col items-center p-6 rounded-lg bg-sky-500">
                    <!-- Icono -->
                    <p class="mt-2">Ser parte de la UDH</p>
                </div>
                <div class="flex flex-col items-center p-6 rounded-lg bg-sky-500">
                    <p class="mt-2">Tener un proyecto en mente</p>
                </div>
                <div class="flex flex-col items-center p-6 rounded-lg bg-sky-500">
                    <p class="mt-2">Registrarte y ponerte en contacto</p>
                </div>
                <div class="flex flex-col items-center p-6 rounded-lg bg-sky-500">
                    <p class="mt-2 ">Iniciar tu proyecto</p>
                </div>
            </div>
            <p class="mt-8 text-xl font-bold text-white">Ahora las posibilidades más cerca de ti!!!</p>
            <!-- Imagen Superpuesta -->
            <div class="absolute left-0 z-0 pt-20 -ml-48 transform translate-x-1/2 md:ml-0 md:left-0 top-4/4 rotate-12">
                <img src="https://via.placeholder.com/600x400" alt="Imagen Superpuesta" class="shadow-lg rounded-2xl" />
            </div>
            <!-- Imagen pequeña (flotando y girando) -->
            <div
                class="absolute z-10 hidden lg:flex lg:w-48 animate-float-and-rotate lg:relative lg:ml-[-80px] lg:left-[70%] lg:w-64 top-48">
                <img src="/img/UDHdark.webp" alt="Imagen Pequeña" class="w-full h-auto " />
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-6 text-center text-white ">
            <p>&copy; 2024 UDH LABS. All rights reserved.</p>
        </footer>
    </div>

</template>
