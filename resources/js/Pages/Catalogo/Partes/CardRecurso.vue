<script setup>
import { Button, Card, CardMeta } from 'ant-design-vue';

const props = defineProps({
	recurso: Array,
});

const emitir = defineEmits(['open-modal']);
const recurso = props.recurso ?? {};

function nombreDeLaboratorio() {
	return recurso.area?.laboratorio?.nombre ?? 'Sin definir';
}

</script>
<template>
	<Card hoverable>
		<template #cover>
			<div class="aspect-square">
				<img v-if="recurso?.fotos && recurso.fotos.length > 0" :src="`/storage/${recurso.fotos[0].ruta}`"
					class="w-16 h-16 object-cover rounded" />
				<img v-else alt="example"
					src="https://www.nbmchealth.com/wp-content/uploads/2018/04/default-placeholder.png" />
			</div>
		</template>
		<div class="">
			<p class="mb-0">{{ recurso.codigo }}</p>
		</div>
		<CardMeta :title="recurso.nombre">
			<template #description>
				<div class="pb-3 text-gray-500">
					<div class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
							<path fill="none" stroke="#999" stroke-linecap="round" stroke-linejoin="round"
								stroke-width="2" d="m12 6l-8 4l8 4l8-4zm-8 8l8 4l8-4" />
						</svg>
						{{ recurso.area?.nombre ?? 'Sin definir' }}
					</div>
					<div class="flex items-center gap-2">
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
							<path fill="none" stroke="#999" stroke-linecap="round" stroke-linejoin="round"
								stroke-width="2"
								d="M9 3h6m-5 6h4m-4-6v6L6 20a.7.7 0 0 0 .5 1h11a.7.7 0 0 0 .5-1L14 9V3" />
						</svg>
						{{ nombreDeLaboratorio() }}
					</div>
				</div>
			</template>
		</CardMeta>
		<div>
			<Button type="primary" class="w-full" @click="emitir('open-modal', recurso)">Reservar</Button>
		</div>
	</Card>
</template>
<style scoped></style>