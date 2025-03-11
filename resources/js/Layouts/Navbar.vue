<!-- Sidebar.vue -->
<script setup>
import { ref } from "vue";
import { Link, router } from "@inertiajs/vue3";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Tooltip } from "ant-design-vue";

// Iconos de Ant Design Vue
import {
  ExperimentOutlined,
  AppstoreOutlined,
  TeamOutlined,
  SolutionOutlined,
  UsergroupAddOutlined,
  ProjectOutlined,
  CalendarOutlined,
  UserOutlined,
  // OJO: Usamos LogoutOutlined en lugar de LogoutOutlined
  LogoutOutlined,
  BarChartOutlined
} from "@ant-design/icons-vue";

const showingNavigationDropdown = ref(false);
const isCollapsed = ref(false);

const logout = () => {
  router.post(route("logout"));
};

const navigationLinks = [
  { name: "Dashboard", route: "dashboard", icon: BarChartOutlined },
  { name: "Laboratorios", route: "laboratorios.index", icon: ExperimentOutlined },
  { name: "Inventario", route: "recursos.index", icon: AppstoreOutlined },
  { name: "Usuarios", route: "usuarios.index", icon: TeamOutlined },
  { name: "Asistencias", route: "asistencias.index", icon: SolutionOutlined },
  { name: "Mis Asistencias", route: "asistencias.user", icon: SolutionOutlined },
  { name: "Miembros", route: "miembros.index", icon: UsergroupAddOutlined },
  { name: "Proyectos", route: "proyectos.index", icon: ProjectOutlined },
  { name: "Reservas", route: "reservas.index", icon: CalendarOutlined },
];
</script>

<template>
  <div class="z-50 flex shadow-sm">
    <!-- Sidebar Desktop -->
    <aside
      :class="[
        // Altura total de la pantalla
        'h-screen bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-600',
        // Sólo visible en sm en adelante
        'sm:flex flex-col hidden',
        // Transición suave para el ancho
        'transition-all duration-300 ease-in-out',
        // Importante: en lugar de overflow-hidden, usar scroll vertical
        isCollapsed
          ? 'w-16 overflow-x-hidden overflow-y-auto'
          : 'w-64 overflow-x-hidden overflow-y-auto'
      ]"
    >
      <!-- Encabezado con Logo y Botón Colapsar -->
      <div
        class="flex items-center px-4 py-2.5 border-b border-gray-200 dark:border-gray-600 justify-between"
      >
        <Link :href="route('laboratorios.index')" class="flex items-center space-x-4">
          <ApplicationMark class="w-auto h-12 mt-1" />
          <div v-if="!isCollapsed" class="flex flex-col">
            <h1 class="text-lg font-bold text-blue-500 mb-0">Smartlab</h1>
            <span class="text-xs">Gestión de laboratorios</span>
          </div>
        </Link>

        <button
          @click="isCollapsed = !isCollapsed"
          class="p-2 text-gray-500 hover:text-gray-700 transition"
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

      <!-- Menú de navegación -->
      <nav
        class="flex flex-col flex-1 px-4 py-6 space-y-4"
        :class="isCollapsed ? 'items-center' : ''"
      >
        <NavLink
          v-for="link in navigationLinks"
          :key="link.route"
          :href="route(link.route)"
          :active="route().current(link.route)"
          :class="[
            'flex items-center',
            isCollapsed ? 'justify-center' : 'space-x-2'
          ]"
        >
          <!-- Tooltip si está colapsado -->
          <a-tooltip v-if="isCollapsed" :title="link.name" placement="right">
            <component :is="link.icon" class="text-lg" />
          </a-tooltip>
          <!-- Ícono normal si NO está colapsado -->
          <component v-else :is="link.icon" class="text-lg" />
          <!-- Texto sólo si NO está colapsado -->
          <span v-if="!isCollapsed">{{ link.name }}</span>
        </NavLink>
      </nav>

      <!-- Sección inferior (Perfil y Logout) -->
      <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-600">
        <!-- Perfil -->
        <div class="mb-4 text-sm font-bold text-gray-700 dark:text-gray-300 flex items-center">
          <span v-if="!isCollapsed">{{ $page.props.auth.user.nombres }}</span>
        </div>

        <nav class="flex flex-col space-y-4">
          <NavLink
            :href="route('profile.show')"
            :active="route().current('profile.show')"
            :class="isCollapsed ? 'flex items-center justify-center' : 'flex items-center space-x-2'"
          >
            <a-tooltip v-if="isCollapsed" title="Mi perfil" placement="right">
              <component :is="UserOutlined" class="text-lg" />
            </a-tooltip>
            <component v-else :is="UserOutlined" class="text-lg" />
            <span v-if="!isCollapsed">Mi perfil</span>
          </NavLink>

          <!-- Logout -->
          <form @submit.prevent="logout">
            <NavLink
              as="button"
              :class="isCollapsed ? 'flex items-center justify-center' : 'flex items-center space-x-2'"
            >
              <!-- Aquí usamos LogoutOutlined en vez de LogoutOutlined -->
              <a-tooltip v-if="isCollapsed" title="Cerrar sesión" placement="right">
                <component :is="LogoutOutlined" class="text-lg text-red-600" />
              </a-tooltip>
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
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M6 18L18 6M6 6l12 12"
          />
        </svg>
      </button>
    </div>

    <!-- Sidebar responsive (overlay en móviles) -->
    <div
      :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
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

          <div class="my-2 border-t border-gray-200 dark:border-gray-600" />

          <ResponsiveNavLink
            :href="route('profile.show')"
            :active="route().current('profile.show')"
          >
            <component :is="UserOutlined" class="text-lg mr-2" />
            <span>Mi perfil</span>
          </ResponsiveNavLink>

          <form @submit.prevent="logout">
            <ResponsiveNavLink as="button">
              <!-- Aquí también LogoutOutlined -->
              <component :is="LogoutOutlined" class="text-lg mr-2 text-red-500" />
              <span>Cerrar sesión</span>
            </ResponsiveNavLink>
          </form>
        </nav>
      </div>
    </div>
  </div>
</template>
