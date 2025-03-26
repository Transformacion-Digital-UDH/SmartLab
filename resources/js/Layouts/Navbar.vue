<script setup>
import { ref, onMounted, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Tooltip, Select, Tag, message } from "ant-design-vue";

// Iconos
import {
    ExperimentOutlined,
    AppstoreOutlined,
    TeamOutlined,
    SolutionOutlined,
    UsergroupAddOutlined,
    ProjectOutlined,
    CalendarOutlined,
    UserOutlined,
    LogoutOutlined,
    BarChartOutlined,
    PartitionOutlined,
} from "@ant-design/icons-vue";

import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

const showingNavigationDropdown = ref(false);
const isCollapsed = ref(false);

const { auth, laboratoriosParticipante } = usePage().props;
const selectedLaboratorio = ref(null);

const navigationLinks = [
    { name: "Dashboard", route: "dashboard", icon: BarChartOutlined },
    {
        name: "Laboratorios",
        route: "laboratorios.index",
        icon: ExperimentOutlined,
    },
    { name: "Áreas", route: "areas.index", icon: PartitionOutlined },
    { name: "Inventario", route: "recursos.index", icon: AppstoreOutlined },
    { name: "Usuarios", route: "usuarios.index", icon: TeamOutlined },
    { name: "Asistencias", route: "asistencias.index", icon: SolutionOutlined },
    {
        name: "Mis asistencias",
        route: "asistencias.user",
        icon: SolutionOutlined,
    },
    { name: "Miembros", route: "miembros.index", icon: UsergroupAddOutlined },
    { name: "Proyectos", route: "proyectos.index", icon: ProjectOutlined },
    { name: "Reservas", route: "reservas.index", icon: CalendarOutlined },
];

// Asignar valor inicial del Select
onMounted(() => {
    if (auth.user.laboratorio_seleccionado || auth.user.rol === "Admin") {
        selectedLaboratorio.value = auth.user.laboratorio_seleccionado;
    } else if (laboratoriosParticipante.length > 0) {
        selectedLaboratorio.value = laboratoriosParticipante[0].laboratorio.id;
        updateLaboratorio();
    }
});

// Computed que filtra los links según la lógica solicitada
const filteredNavigationLinks = computed(() => {
    // 1) Admin sin laboratorio seleccionado => todos menos "Miembros"
    if (auth.user.rol === "Admin" && !selectedLaboratorio.value) {
        return navigationLinks.filter((link) => link.name !== "Miembros");
    }

    // 2) Si hay un laboratorio seleccionado
    if (selectedLaboratorio.value) {
        // Buscar el rol en laboratoriosParticipante
        const labEncontrado = laboratoriosParticipante.find(
            (labP) => labP.laboratorio.id === selectedLaboratorio.value
        );
        if (!labEncontrado) {
            return []; // O ajusta según lo que quieras mostrar si no se encuentra
        }

        const rol = labEncontrado.rol;

        // 2a) Rol = "Miembro"
        if (rol === "Miembro") {
            // Solo "Mis asistencias" y "Reservas"
            return navigationLinks.filter((link) =>
                ["Mis asistencias", "Reservas"].includes(link.name)
            );
        }

        // 2b) Rol = "Coordinador" o "Responsable"
        if (["Coordinador", "Responsable"].includes(rol)) {
            // Mostrar todo menos "Usuarios" y "Laboratorios"
            return navigationLinks.filter(
                (link) => !["Usuarios", "Laboratorios"].includes(link.name)
            );
        }
    }

    // Si no se cumple ninguna condición anterior,
    // puedes retornar un arreglo vacío o lo que te convenga
    return [];
});

// Actualizar la selección de laboratorio
const updateLaboratorio = () => {
    router.post(
        route("usuario.seleccionarLaboratorio"),
        { laboratorio_id: selectedLaboratorio.value },
        {
            onSuccess: () => {
                message.success("Laboratorio cambiado correctamente.");
                window.location.reload();
            },
            onError: () => {
                message.error("Error al cambiar el laboratorio.");
            },
        }
    );
};

const logout = () => {
    router.post(route("logout"));
};

// Función para asignar colores a los roles
const getTagColor = (rol) => {
    const colors = {
        Miembro: "blue",
        Responsable: "green",
        Coordinador: "volcano",
        Administrador: "purple",
    };
    return colors[rol] || "default";
};
</script>

<template>
    <div class="z-50 flex shadow-sm">
        <!-- Sidebar Desktop -->
        <aside
            :class="[
                'h-screen bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-600',
                'sm:flex flex-col hidden',
                'transition-all duration-300 ease-in-out',
                isCollapsed
                    ? 'w-16 overflow-x-hidden'
                    : 'w-64 overflow-x-hidden',
            ]"
        >
            <div
                class="flex items-center px-4 h-[74px] py-2 border-b border-gray-200 dark:border-gray-600 justify-between"
            >
                <!-- Select de Laboratorios (si no está colapsado) -->
                <div v-if="!isCollapsed" class="w-full">
                    <Select
                        placeholder="Laboratorio"
                        style="width: 100%"
                        v-model:value="selectedLaboratorio"
                        size="large"
                        @change="updateLaboratorio"
                    >
                        <template #suffixIcon>
                            <!-- Ícono de laboratorio -->
                            <ExperimentOutlined class="text-lg" />
                        </template>
                        <!-- Opción "Todos" para administradores -->
                        <Select.Option
                            v-if="auth.user.rol === 'Admin'"
                            :value="null"
                        >
                            <div>
                                <div>Todos</div>
                                <div>
                                    <Tag
                                        :color="getTagColor('Administrador')"
                                        size="small"
                                    >
                                        Administrador
                                    </Tag>
                                </div>
                            </div>
                        </Select.Option>
                        <Select.Option
                            v-for="lab in laboratoriosParticipante"
                            :key="lab.laboratorio.id"
                            :value="lab.laboratorio.value"
                        >
                            <div>
                                <div>{{ lab.laboratorio.nombre }}</div>
                                <div>
                                    <Tag
                                        :color="getTagColor(lab.rol)"
                                        size="small"
                                    >
                                        {{ lab.rol }}
                                    </Tag>
                                </div>
                            </div>
                        </Select.Option>
                    </Select>
                </div>

                <button
                    @click="isCollapsed = !isCollapsed"
                    class="p-2 pr-0 text-gray-500 hover:text-gray-700 transition"
                >
                    <!-- Flecha para colapsar -->
                    <svg
                        v-if="!isCollapsed"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M15 18l-6-6 6-6" />
                    </svg>
                    <!-- Flecha para expandir -->
                    <svg
                        v-else
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M9 18l6-6-6-6" />
                    </svg>
                </button>
            </div>

            <!-- Menú de navegación con scroll si excede la altura -->
            <nav
                class="flex flex-col flex-1 px-4 py-6 space-y-3 overflow-y-auto"
                :class="isCollapsed ? 'items-center' : ''"
            >
                <NavLink
                    v-for="link in filteredNavigationLinks"
                    :key="link.route"
                    :href="route(link.route)"
                    :active="route().current(link.route)"
                    :class="[
                        'flex items-center',
                        isCollapsed ? 'justify-center' : 'space-x-2',
                    ]"
                >
                    <!-- Tooltip si está colapsado -->
                    <Tooltip
                        v-if="isCollapsed"
                        :title="link.name"
                        placement="right"
                    >
                        <component :is="link.icon" class="text-lg" />
                    </Tooltip>
                    <!-- Ícono normal si NO está colapsado -->
                    <component v-else :is="link.icon" class="text-lg" />
                    <!-- Texto sólo si NO está colapsado -->
                    <span v-if="!isCollapsed">{{ link.name }}</span>
                </NavLink>
            </nav>

            <!-- Sección inferior (Perfil y Logout) -->
            <div
                class="px-4 py-4 border-t border-gray-200 dark:border-gray-600"
            >
                <nav class="flex flex-col space-y-4">
                    <NavLink
                        :href="route('profile.show')"
                        :active="route().current('profile.show')"
                        :class="
                            isCollapsed
                                ? 'flex items-center justify-center'
                                : 'flex items-center space-x-2'
                        "
                    >
                        <a-tooltip
                            v-if="isCollapsed"
                            title="Mi perfil"
                            placement="right"
                        >
                            <component :is="UserOutlined" class="text-lg" />
                        </a-tooltip>
                        <component v-else :is="UserOutlined" class="text-lg" />
                        <span v-if="!isCollapsed">Mi perfil</span>
                    </NavLink>

                    <!-- Logout -->
                    <form
                        @submit.prevent="logout"
                        :class="
                            isCollapsed
                                ? 'flex items-center justify-center'
                                : ''
                        "
                    >
                        <NavLink
                            as="button"
                            :class="
                                isCollapsed
                                    ? 'flex items-center justify-center'
                                    : 'flex items-center space-x-2'
                            "
                        >
                            <Tooltip
                                v-if="isCollapsed"
                                title="Cerrar sesión"
                                placement="right"
                            >
                                <component
                                    :is="LogoutOutlined"
                                    class="text-lg text-red-600"
                                />
                            </Tooltip>
                            <component
                                v-else
                                :is="LogoutOutlined"
                                class="text-lg text-red-600"
                            />
                            <span v-if="!isCollapsed">Cerrar sesión</span>
                        </NavLink>
                    </form>
                </nav>
            </div>
        </aside>

        <!-- Botón responsive (versión móvil) -->
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

        <!-- Sidebar responsive (overlay en móviles) -->
        <div
            :class="{
                block: showingNavigationDropdown,
                hidden: !showingNavigationDropdown,
            }"
            class="fixed inset-0 z-50 bg-gray-800 bg-opacity-75 sm:hidden"
            @click="showingNavigationDropdown = false"
        >
            <div class="w-64 h-full bg-white dark:bg-gray-800" @click.stop>
                <!-- Encabezado en responsive: se agrega el Select -->
                <div
                    class="px-4 py-2 border-b border-gray-200 dark:border-gray-600"
                >
                    <Select
                        placeholder="Selecciona un laboratorio"
                        style="width: 100%"
                        v-model:value="selectedLaboratorio"
                    >
                        <template #suffixIcon>
                            <ExperimentOutlined class="text-lg" />
                        </template>
                        <Select.Option
                            v-for="lab in laboratoriosParticipante"
                            :key="lab.laboratorio.id"
                            :value="lab.laboratorio.nombre"
                        >
                            <div>
                                <div>{{ lab.laboratorio.nombre }}</div>
                                <div>
                                    <Tag size="small">
                                        {{ lab.rol }}
                                    </Tag>
                                </div>
                            </div>
                        </Select.Option>
                    </Select>
                </div>

                <!-- Menú de navegación en responsive -->
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
