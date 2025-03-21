<template>
    <AppLayout title="Usuarios">
      <template #header>
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">
          Usuarios
        </h2>
      </template>

      <div class="px-4 py-3 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Tabs de usuarios -->
        <Tabs default-active-key="1" :activeKey="activeKey" type="line" @change="manejarCambioTab">
          <TabPane :key="tabActivo" tab="Registrados">
            <TablaUsuariosRegistrados
              :usuarios_registrados="usuarios_registrados"
              @editar="abrirModalEditar"
              @actualizar-tabla="actualizarTabla"
              @abrir-modal-crear="abrirModalCrear"
            />
          </TabPane>

          <TabPane key="2" tab="Libres (sin cuenta)">
            <TablaUsuariosLibres
              :usuarios_libres="usuarios_libres"
              @editar="abrirModalEditar"
              @actualizar-tabla="actualizarTabla"
              @abrir-modal-crear="abrirModalCrear"
              @abrir-modal-crear-cuenta="abrirModalCrearCuenta"
            />
          </TabPane>
        </Tabs>

        <!-- Modal para agregar usuario -->
        <ModalAgregar v-model:visible="mostrarModalCrear" @actualizar-tabla="actualizarTabla" />

        <!-- Modal para editar usuario -->
        <ModalEditar
          v-if="userSeleccionado"
          v-model:visible="mostrarModalEditar"
          :usuario="userSeleccionado"
          @actualizar-tabla="actualizarTabla"
        />

        <!-- Modal para crear cuenta -->
        <ModalCrearCuenta
          v-if="usuarioCuenta"
          v-model:visible="mostrarModalCrearCuenta"
          :usuario="usuarioCuenta"
          @actualizar-tabla="actualizarTabla"
        />
      </div>
    </AppLayout>
  </template>

  <script setup>
  import { ref } from "vue";
  import { usePage, router } from "@inertiajs/vue3";
  import { Tabs, TabPane } from "ant-design-vue";
  import AppLayout from "@/Layouts/AppLayout.vue";
  import ModalAgregar from "./Partes/ModalAgregar.vue";
  import ModalEditar from "./Partes/ModalEditar.vue";
  import ModalCrearCuenta from "./Partes/ModalCrearCuenta.vue";
  import TablaUsuariosRegistrados from "./Partes/TablaUsuariosRegistrados.vue";
  import TablaUsuariosLibres from "./Partes/TablaUsuariosLibres.vue";

  const { props } = usePage();

  // Se asume que en el controlador ya se separaron en usuarios_registrados y usuarios_libres
  const usuarios_registrados = ref(props.usuarios_registrados || []);
  const usuarios_libres = ref(props.usuarios_libres || []);

  const tabActivo = ref("1"); // Tab por defecto
  const activeKey = ref("1");

  const mostrarModalCrear = ref(false);
  const mostrarModalEditar = ref(false);
  const mostrarModalCrearCuenta = ref(false);

  const userSeleccionado = ref(null);
  const usuarioCuenta = ref(null);

  // Actualizar la tabla al cerrar los modales
  const actualizarTabla = () => {
    mostrarModalCrear.value = false;
    mostrarModalEditar.value = false;
    mostrarModalCrearCuenta.value = false;
    router.visit(route("usuarios.index"), { preserveScroll: true });
  };

  // Abrir modal de creación de usuario (ModalAgregar)
  const abrirModalCrear = () => {
    mostrarModalCrear.value = true;
  };

  // Abrir modal de edición de usuario
  const abrirModalEditar = (usuario) => {
    mostrarModalEditar.value = true;
    userSeleccionado.value = { ...usuario };
  };

  // Abrir modal de creación de cuenta (ModalCrearCuenta)
  const abrirModalCrearCuenta = (usuario) => {
    usuarioCuenta.value = { ...usuario };
    mostrarModalCrearCuenta.value = true;
  };

  // Manejar cambio de tab si es necesario
  const manejarCambioTab = (key) => {
    activeKey.value = key;
  };
  </script>
