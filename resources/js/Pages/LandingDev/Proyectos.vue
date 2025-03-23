<script setup>
	import Nav from './Partes/Nav.vue';
	import dayjs from 'dayjs';
	import { usePage } from '@inertiajs/vue3';
	import { CalendarOutlined } from "@ant-design/icons-vue";
	const { props } = usePage()
</script>
<template>
	<Nav></Nav>
	<h1 class="text-center text-4xl font-black">Proyectos</h1>
	<section class="flex flex-col gap-4 p-4">
		<article v-for="proyecto in props.proyectos" class="flex gap-2 items-center">
			<div class="w-1 h-20 bg-blue-500 rounded-full"></div>
			<div class="">
				<h2 class="text-2xl font-bold mb-1">{{proyecto.nombre}}</h2>
				<p class="text-blue-500 mb-1"><b class="">Dirigido por:</b> {{ proyecto.responsable.nombres }} {{ proyecto.responsable.apellidos }}</p>
				<div class="flex gap-3">
					<CalendarOutlined />
					<span class="font-bold">Inicio: </span>
					{{ dayjs(proyecto.fecha_inicio).format('D [de] MMMM [de] YYYY') }}
				</div>
				<p>{{proyecto.descripcion}}</p>
			</div>
		</article>
	</section>
</template>
<style scoped>

</style>
