<script setup>
	import { ref, watch, h } from 'vue';
	import { router, usePage} from '@inertiajs/vue3';
	import { SmileOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons-vue';
	import { InputSearch, Table, Tag, Pagination, Button, Modal, message, TimePicker } from 'ant-design-vue';
	import NavBar from '@/Layouts/AppLayout.vue';
	import Asistencia from '@/lib/Asistencia';
	import TablaAsistencia from './Partes/TablaAsistencia.vue';
	import ModalEditarSalida from './Partes/ModalEditarSalida.vue';
	import ModalAgregarAsistencia from './Partes/ModalAgregarAsistencia.vue';

	const { props } = usePage();
	let asistenciasPaginate = JSON.parse(JSON.stringify(props.asistencias))

	// Damos formato de fecha de entrada y salida
	let asistencias = ref(asistenciasPaginate.data.map((asis)=>{
		return new Asistencia(asis);
	}));

	console.log(props)
	const token = ref(props.token || []);
	const loading = ref(false)

	// Manejo de Paginacion
	const total = ref(asistenciasPaginate.total);
	const pageSize = ref(asistencias.length);

	const currentPage = ref(asistenciasPaginate.current_page);

	const onShowSizeChange = (current, pageSize) => {
		setURLPage(current.value, pageSize)
	};

	watch(pageSize, () => {
		setURLPage(currentPage.value, pageSize.value)
	});

	watch(currentPage, () => {
		setURLPage(currentPage.value, pageSize.value)
	});

	// Dar la ruta de paginacion
	function setURLPage(page, cantidad) {
		const url = new URL(location.href)
		url.searchParams.set('page', page)
		location.href = url.toString()
	}

	// Formatear la fecha
	const open = ref(false)
	const selectedTime = ref(null)

	const mostrarModalEditar = ref(false);
	const mostrarModalAgregarAsistencia = ref(false);
	const asistenciaSeleccionado = ref(null);

	function showModal(asistencia) {
		open.value = true
		selectedTime.value = new Date(asistencia.horaEntrada);
		console.log(selectedTime)
		console.log(asistencia.horaEntrada)
	}

	const actualizarTabla = () => {
		router.visit(route('asistencias.index'), { preserveScroll: true });
	};

	const abrirModalEditar = (asistencia) => {
		console.clear()
		console.log(asistencia)
		mostrarModalEditar.value = true;
		asistenciaSeleccionado.value = { ...asistencia };
	};

	let buscar = ref(new URLSearchParams(window.location.search).get('q') ?? '')

	async function buscarAsistencias() {

		router.visit(route('asistencias.index')+'?'+ new URLSearchParams({ q: buscar.value }).toString(), { preserveScroll: true });
	}

	function abrirModalCrear() {
		mostrarModalAgregarAsistencia.value = true
	}

	const current = ref(asistencias.current_page);
	watch(current, ()=>{
		setURLPage(current.value)
	})
</script>
<template>
	<NavBar title="Asistencias">
		<template #header>
			<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">
				Asistencias
			</h2>
		</template>
		<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 px-4">
			<div
				class="flex flex-col-reverse justify-end gap-y-4 mb-6 sm:flex-row sm:justify-between sm:items-center gap-x-4"
			>
				<InputSearch
					placeholder="Buscar por nombre o DNI"
					class="w-full"
					size="large"
					:loading="loading"
					@search="buscarAsistencias"
					v-model:value="buscar"
				/>
				<Button type="primary" @click="abrirModalCrear" size="large" class="font-medium" >Agregar asistencia</Button>
			</div>
			<TablaAsistencia
				:asistencias="asistencias"
				@actualizar-tabla="actualizarTabla"
				@editar="abrirModalEditar"
				/>
			<div class="grid place-items-center pt-4">
				<Pagination v-model:current="current" :total="total" show-less-items/>
			</div>
			<ModalEditarSalida
				v-if="asistenciaSeleccionado"
				v-model:visible="mostrarModalEditar"
				:asistencia="asistenciaSeleccionado"
				@actualizar-tabla="actualizarTabla"
			/>
			<ModalAgregarAsistencia
				v-model:visible="mostrarModalAgregarAsistencia"
				:asistencia="asistenciaSeleccionado"
				@actualizar-tabla="actualizarTabla"
			/>
		</div>
	</NavBar>
</template>

<style scoped></style>
