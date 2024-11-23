<script setup>
  import { ref, watch, h, defineProps } from 'vue';
  import { usePage } from '@inertiajs/vue3';
  import { HomeOutlined, UserOutlined, CalendarOutlined, ClockCircleOutlined, CodeSandboxOutlined, SettingFilled, QuestionCircleOutlined, MenuOutlined } from '@ant-design/icons-vue';
  import { Button } from 'ant-design-vue';

  const props = defineProps({title: {type: String}})
  document.title = props.title

  function isActive(path) {
    return location.pathname.split('/')[1] == path ? 'primary':'link';
  }
</script>
<template>
  <div class="containerx h-screen overflow-y-hidden grid">
    <nav class="flex flex-col justify-between">
      <div>

        <div class="flex p-2 items-center gap-1 justify-between">
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-neutral-400 rounded-2xl">
              <img src="" class="" id=""/>
            </div>
            <div class="flex flex-col">
              <h1 class="text-sm text-blue-500">Laboratorio</h1>
              <p class="font-bold">Transformación Digital</p>
            </div>
          </div>
          <button>
            <MenuOutlined />
          </button>
        </div>
        <div class="flex flex-col p-3 gap-3">
          <Button :type="isActive('/asistenas')" href="/" class="flex items-center gap-2">
            <HomeOutlined />
            Dashboard
          </Button>
          <Button :type="isActive('/as')" href="/" class="flex items-center gap-2">
            <UserOutlined />
            Usuarios
          </Button>
          <Button :type="isActive('/a')" href="/" class="flex items-center gap-2">
            <CalendarOutlined />
            Reservaciones
          </Button>
          <Button :type="isActive('/as')" href="/" class="flex items-center gap-2">
            <CodeSandboxOutlined />
            Inventario
          </Button>
          <Button :type="isActive('mis_asistencias')" href="/" class="flex items-center gap-2">
            <ClockCircleOutlined />
            Asistencias
          </Button>
        </div>
      </div>
      <div class="p-3 flex flex-col gap-2">
        <Button type="primary" href="/" class="flex items-center gap-2">
          <span class="text-center w-full">Nueva Reservacion</span>
        </Button>
        <Button :type="isActive('/as')" href="/" class="flex items-center gap-2">
          <SettingFilled />
          Ajustes
        </Button>
        <Button :type="isActive('/as')" href="/" class="flex items-center gap-2">
          <QuestionCircleOutlined />
          Soporte
        </Button>
      </div>
    </nav>
    <div>
      <slot></slot>
    </div>
  </div>
  <div>
  </div>
</template>

<style scoped>
  .containerx {
    grid-template-columns: 250px 1fr;
  }
  nav {
    height: 100vh;
  }
  @media (max-width:600px) {
    .containerx {
      grid-template-columns: 1fr;

    }

    nav {
      display: none;
    }
    
  }
</style>
