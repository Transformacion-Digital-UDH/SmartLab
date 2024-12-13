<script setup>
	import { ref, computed } from "vue";
	import { usePage, router } from "@inertiajs/vue3";
	import AppLayout from "@/Layouts/AppLayout.vue";
	import { Tabs, TabPane, Card, CardMeta, Button } from "ant-design-vue";
	import TablaRecursos from "./Partes/Recursos/TablaRecursos.vue";
	import ModalAgregar from "./Partes/Recursos/ModalAgregar.vue";
	import ModalEditar from "./Partes/Recursos/ModalEditar.vue";

	const { props } = usePage();
	const recursos = ref(props.recursos || []);
	const areas = ref(props.areas || []);
	const equipos = ref(props.equipos || []);
	const mostrarModalCrear = ref(false);
	const mostrarModalEditar = ref(false);
	const recursoSeleccionado = ref(null);
	const valorBuscar = ref("");

	// Filtrar recursos por nombre o código
	const recursosFiltrados = computed(() =>
		!valorBuscar.value
			? recursos.value
			: recursos.value.filter((recurso) =>
				recurso.nombre.toLowerCase().includes(valorBuscar.value.toLowerCase()) ||
				recurso.codigo?.toLowerCase().includes(valorBuscar.value.toLowerCase())
			)
	);

	console.log(recursos.value);

</script>


<template>
	<AppLayout title="Recursos">
		<template #header>
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">
				Catalogo
			</h2>
		</template>
		<div class="catalogos grid gap-4 p-4">
			<Card hoverable v-for="recurso in recursos">
				<template #cover>
				
				<img alt="example" src="https://www.pcspecialist.es/images/landing/pcs/gaming-pc/bundle.jpg" />
				</template>
				<div class="">
					<p class="mb-0">{{recurso.codigo}}</p>
				</div>
				<CardMeta :title="recurso.nombre">
					<template #description>
						<p>{{recurso.area ?? 'Sin definir'}}</p>
					</template>
				</CardMeta>
				<div>
					<Button type="primary" class="">Reservar</Button>
				</div>
			</Card>
		</div>
	</AppLayout>
</template>
<style scoped>
	.catalogos{
		grid-template-columns: repeat(auto-fill,minmax(200px,1fr));
	}
</style>