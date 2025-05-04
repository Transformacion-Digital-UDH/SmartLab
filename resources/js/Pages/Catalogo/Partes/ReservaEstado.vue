<script setup>
  const props = defineProps({
		estado: String
	});

</script>
<template>
  <div v-if="props.estado == 'Aprobada'" class="size-3 rounded-full success"></div>
  <div v-else-if="props.estado == 'Por aprobar'" class="size-3 rounded-full wait"></div>
  <div v-else class="size-3 rounded-full danger"></div>
</template>
<style scoped>
  .success {
    background: radial-gradient(circle, #72f483 40%, #1caf1c);
	}

	.wait {
    background: radial-gradient(circle, #f4b172 40%, #af6f1c);
	}
	.danger {
    background: radial-gradient(circle, #f47272 40%, #af1c4d);
	}
</style>
