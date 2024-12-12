<script setup>
	import 'moment/locale/es.js'
	import NavBar from '@/Layouts/AppLayout.vue';
	import moment from 'moment';
	import CardAsistencia from './Partes/CardAsistencia.vue';
	import { ref, watch } from 'vue';
	import { usePage } from '@inertiajs/vue3';
	import { CheckCircleOutlined, ExclamationCircleOutlined, UserOutlined } from '@ant-design/icons-vue';
	import { Pagination, Tabs, TabPane } from 'ant-design-vue';

	const { props } = usePage()

	const token = props.token || [];
	console.log(props.asistencias);

	class Asistencia {
		constructor(props){
			this.id = props.id
			this.data = props
			this.check = props.hora_salida != null;
			this.entrada = new Date(props.hora_entrada)
			this.salida = new Date(props.hora_salida)
			this.type = this.entrada.getHours() < 12 ? 'A': this.entrada.getHours() < 18 ? 'B' : 'C';
			this.laboratorio = props.laboratorio;
			this.proyecto = props.proyecto;

			const totalMinutes = moment(this.salida).diff(this.entrada, 'minutes');
			const hours = Math.floor(totalMinutes / 60);
			const minutes = totalMinutes % 60;

			this.diff = `${hours}h ${minutes}m`;
		}
	}

	// Damos formato de fecha de entrada y salida
	const asistencias = props.asistencias;
	asistencias.data = asistencias.data.map((asis) => new Asistencia(asis));

	const asistenciasCompletas = asistencias.data.filter((a)=> a.check)
	const asistenciasIncompletas = asistencias.data.filter((a)=>!a.check)

	console.log(asistencias)
	const current = ref(asistencias.current_page)
	
	watch(current, ()=>{
		setURLPage(current.value)
	})
	// Dar la ruta de paginacion
	function setURLPage(page) {
		const url = new URL(location.href)
		url.searchParams.set('page', page)
		location.href = url.toString()
	}

</script>
<template>
	<NavBar title="Mis Asistencias">
		<div class="grid p-4">
			<div class="flex pb-4">
				<h1 class="font-bold text-xl px-2">Mis Asistencias</h1>
			</div>
			<!-- Tabs -->
			<Tabs v-model:activeKey="activeKey">
				<TabPane key="1">
					<template #tab>
						<div class="flex items-center">
							<UserOutlined class="block"/>
							Asistencias
							<span class="text-green-600 font-bold ml-2 bg-neutral-100 px-1 rounded-full">
								{{asistencias.total}}
							</span>
						</div>
					</template>
					<div class="appy gap-2">
						<CardAsistencia :asistencia="asistencia" v-for="(asistencia) in asistencias.data" :key="asistencia.id"/>
					</div>
				</TabPane>
				<TabPane key="2">
					<template #tab >
						<div class="flex items-center">
							<CheckCircleOutlined />
							Completas
							<span class="text-blue-600 font-bold ml-2 bg-blue-100 px-1 rounded-full">
									{{asistenciasCompletas.length}}
							</span>
						</div>
					</template>
					<div class="appy gap-2">
						<CardAsistencia :asistencia="asistenciaCompleta" v-for="(asistenciaCompleta) in asistenciasCompletas" :key="asistenciaCompleta.id"/>
					</div>
				</TabPane>
				<TabPane key="3">
					<template #tab>
						<div class="flex items-center" @click="cargarAsistenciasInompletas">
							<ExclamationCircleOutlined class="block" />
							Incompletas
							<span class="text-red-600 font-bold ml-2 bg-red-100 px-1 rounded-full">
									{{asistenciasIncompletas.length}}
							</span>
						</div>
					</template>
					<div class="appy gap-2">
						<CardAsistencia :asistencia="asistencia" v-for="(asistencia) in asistenciasIncompletas" :key="asistencia.id"/>
					</div>
				</TabPane>
			</Tabs>
		</div>
		<div class="grid place-items-center pb-5">
			<div class="grid place-items-center pt-4">
				<Pagination v-model:current="current" :total="asistencias.total" show-less-items/>
			</div>
		</div>
	</NavBar>
</template>
<style scoped>
	.appy {
		display: grid;
		grid-template-columns: 1fr;
	}
</style>
