<!-- Sidebar.vue -->
<script setup>
import { ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

// Iconos de Ant Design
import {
    ExperimentOutlined,
    AppstoreOutlined,
    TeamOutlined,
    SolutionOutlined,
    UsergroupAddOutlined,
    ProjectOutlined,
    UserOutlined,
    LogoutOutlined,
    FormatPainterOutlined
} from "@ant-design/icons-vue";

const showingNavigationDropdown = ref(false);

const logout = () => {
    router.post(route("logout"));
};

const navigationLinks = [
    {
        name: "Laboratorios",
        route: "laboratorios.index",
        icon: ExperimentOutlined,
    },
    {
        name: "Recursos",
        route: "recursos.index",
        icon: AppstoreOutlined,
    },
    {
        name: "Usuarios",
        route: "usuarios.index",
        icon: TeamOutlined,
    },
    {
        name: "Asistencias",
        route: "asistencias.index",
        icon: SolutionOutlined,
    },
    {
        name: "Mis Asistencias",
        route: "asistencias.user",
        icon: SolutionOutlined,
    },
    {
        name: "Miembros",
        route: "miembros.index",
        icon: UsergroupAddOutlined,
    },
    {
        name: "Proyectos",
        route: "proyectos.index",
        icon: ProjectOutlined,
    },
];
</script>

<template>
    <div class="z-50 flex shadow-sm">
        <aside
            class="flex-col hidden w-64 h-screen bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-600 sm:flex"
        >
            <!-- Logo y Título -->
            <div
                class="flex items-center px-4 h-[74px] border-b border-gray-200 dark:border-gray-600"
            >
                <Link
                    :href="route('laboratorios.index')"
                    class="flex items-center space-x-4"
                >
                    <ApplicationMark class="w-auto h-12 mt-1" />
                    <div class="flex flex-col">
                        <h1 class="text-sm text-blue-500">Laboratorio</h1>
                        <span class="text-sm">Transformación Digital</span>
                    </div>
                </Link>
            </div>

            <!-- Lista de enlaces en columna con iconos -->
            <nav class="flex flex-col flex-1 px-4 py-6 space-y-4">
                <NavLink
                    v-for="link in navigationLinks"
                    :key="link.route"
                    :href="route(link.route)"
                    :active="route().current(link.route)"
                    class="flex items-center space-x-2"
                >
                    <!-- Icono -->
                    <component :is="link.icon" class="text-lg" />
                    <!-- Texto -->
                    <span>{{ link.name }}</span>
                </NavLink>
            </nav>

            <!-- Opciones del usuario en la parte inferior -->
            <div
                class="px-4 py-4 border-t border-gray-200 dark:border-gray-600"
            >
                <p
                    class="mb-4 text-sm font-bold text-gray-700 dark:text-gray-300"
                >
                    {{ $page.props.auth.user.nombres }}
                </p>
                <nav class="flex flex-col space-y-4">
                    <NavLink
                        :href="route('profile.show')"
                        :active="route().current('profile.show')"
                        class="flex items-center space-x-2"
                    >
                        <component :is="UserOutlined" class="text-lg" />
                        <span>Mi perfil</span>
                    </NavLink>
                    <form @submit.prevent="logout">
                        <NavLink
                            as="button"
                            class="flex items-center space-x-2"
                        >
                            <component
                                :is="LogoutOutlined"
                                class="text-lg text-red-600"
                            />
                            <span>Cerrar sesión</span>
                        </NavLink>
                    </form>
                </nav>
            </div>
        </aside>

        <!-- Responsive toggle -->
        <div class="fixed z-50 sm:hidden top-4 right-4">
            <button
                class="p-2 text-white bg-gray-800 rounded-full shadow-lg"
                @click="showingNavigationDropdown = !showingNavigationDropdown"
            >
                <svg
                    v-if="!showingNavigationDropdown"
                    class="w-6 h-6"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
                <svg
                    v-else
                    class="w-6 h-6"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>
            </button>
        </div>

        <!-- Responsive Sidebar -->
        <div
            :class="{
                block: showingNavigationDropdown,
                hidden: !showingNavigationDropdown,
            }"
            class="fixed inset-0 z-50 bg-gray-800 bg-opacity-75 sm:hidden"
            @click="showingNavigationDropdown = false"
        >
            <div class="w-64 h-full bg-white dark:bg-gray-800" @click.stop>
                <nav class="px-4 py-6 space-y-3">
                    <ResponsiveNavLink
                        v-for="link in navigationLinks"
                        :key="link.route"
                        :href="route(link.route)"
                        :active="route().current(link.route)"
                    >
                        <component :is="link.icon" class="text-lg me-2" />
                        {{ link.name }}
                    </ResponsiveNavLink>

                    <div
                        class="my-2 border-t border-gray-200 dark:border-gray-600"
                    />

                    <ResponsiveNavLink
                        :href="route('profile.show')"
                        :active="route().current('profile.show')"
                    >
                        <component :is="UserOutlined" class="text-lg mr-2" />
                        <span>Mi perfil</span>
                    </ResponsiveNavLink>

                    <form @submit.prevent="logout">
                        <ResponsiveNavLink as="button">
                            <component
                                :is="LogoutOutlined"
                                class="text-lg mr-2 text-red-500"
                            />
                            <span>Cerrar sesión</span>
                        </ResponsiveNavLink>
                    </form>
                </nav>
            </div>
        </div>
    </div>
</template>
