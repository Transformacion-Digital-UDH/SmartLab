<script setup>
  import AppLayout from "@/Layouts/AppLayout.vue";
  import { Button, Result } from 'ant-design-vue';


  const goBack = () => {
    window.history.back(); // O puedes usar router.push('/ruta') si
  };
</script>

<template>
  <AppLayout title="Catálogo">
		<template #header>
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">
				Catálogo
			</h2>
		</template>
    <Result
      status="403"
      title="Acceso no permitido"
      sub-title="Para tener acceso al catálogo, debes ser miembro de un grupo de investigación."
    >
      <template #extra>
        <Button type="primary" @click="goBack">
          Regresar
        </Button>
      </template>
    </Result>
  </AppLayout>
</template>

<style scoped>
</style>
