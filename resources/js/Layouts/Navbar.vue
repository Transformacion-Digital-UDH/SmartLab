<!-- Navbar.vue -->
<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <nav class="bg-white dark:!bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex items-center shrink-0">
                        <Link :href="route('laboratorios.index')">
                            <ApplicationMark class="block w-auto h-9" />
                        </Link>
                    </div>

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <NavLink :href="route('laboratorios.index')" :active="route().current('laboratorios.index')">Laboratorios</NavLink>
                        <NavLink :href="route('recursos.index')" :active="route().current('recursos.index')">Recursos</NavLink>
                        <NavLink :href="route('usuarios.index')" :active="route().current('usuarios.index')">Usuarios</NavLink>
                        <NavLink :href="route('asistencias.index')" :active="route().current('asistencias.index')">Asistencias</NavLink>
                        <NavLink :href="route('miembros.index')" :active="route().current('miembros.index')">Miembros</NavLink>
                        <NavLink :href="route('proyectos.index')" :active="route().current('proyectos.index')">Proyectos</NavLink>
                    </div>
                </div>

                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <div class="relative ms-3" v-if="$page.props.jetstream.hasTeamFeatures">
                        <Dropdown align="right" width="60">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:!bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300">
                                        {{ $page.props.auth.user.current_team.name }}
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </template>
                            <template #content>
                                <div class="w-60">
                                    <div class="block px-4 py-2 text-xs text-gray-400">Gestionar equipo</div>
                                    <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">Configuración del equipo</DropdownLink>
                                    <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">Crear nuevo equipo</DropdownLink>
                                    <template v-if="$page.props.auth.user.all_teams.length > 1">
                                        <div class="border-t border-gray-200 dark:border-gray-600" />
                                        <div class="block px-4 py-2 text-xs text-gray-400">Cambiar de equipo</div>
                                        <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                            <form @submit.prevent="switchToTeam(team)">
                                                <DropdownLink as="button">
                                                    <div class="flex items-center">
                                                        <svg v-if="team.id == $page.props.auth.user.current_team_id" class="w-5 h-5 text-green-400 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <div>{{ team.name }}</div>
                                                    </div>
                                                </DropdownLink>
                                            </form>
                                        </template>
                                    </template>
                                </div>
                            </template>
                        </Dropdown>
                    </div>
                    <div class="relative ms-3">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm transition border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300">
                                    <img class="object-cover w-8 h-8 rounded-full" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.nombres">
                                </button>
                                <span v-else class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:!bg-gray-800 hover:text-gray-700">
                                        {{ $page.props.auth.user.nombres }}
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            </template>
                            <template #content>
                                <div class="block px-4 py-2 text-xs text-gray-400">Gestionar cuenta</div>
                                <DropdownLink :href="route('profile.show')">Perfil</DropdownLink>
                                <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">Tokens de API</DropdownLink>
                                <div class="border-t border-gray-200 dark:border-gray-600" />
                                <form @submit.prevent="logout">
                                    <DropdownLink as="button">Cerrar sesión</DropdownLink>
                                </form>
                            </template>
                        </Dropdown>
                    </div>
                </div>
                <div class="flex items-center -me-2 sm:hidden">
                    <button class="inline-flex items-center p-2 text-gray-400 dark:text-gray-500" @click="showingNavigationDropdown = !showingNavigationDropdown">
                        <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <div :class="{ 'block': showingNavigationDropdown, 'hidden': !showingNavigationDropdown }" class="sm:hidden">
            <ResponsiveNavLink :href="route('laboratorios.index')" :active="route().current('laboratorios.index')">Laboratorios</ResponsiveNavLink>
            <ResponsiveNavLink :href="route('recursos.index')" :active="route().current('recursos.index')">Recursos</ResponsiveNavLink>
            <ResponsiveNavLink :href="route('asistencias.index')" :active="route().current('asistencias.index')">Asistencias</ResponsiveNavLink>
            <ResponsiveNavLink :href="route('miembros.index')" :active="route().current('miembros.index')">Miembros</ResponsiveNavLink>
            <ResponsiveNavLink :href="route('proyectos.index')" :active="route().current('proyectos.index')">Proyectos</ResponsiveNavLink>
        </div>
    </nav>
</template>
