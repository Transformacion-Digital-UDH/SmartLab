<script setup>
	import "dayjs/locale/es";
	import dayjs from 'dayjs';
	import { ref, watch } from 'vue';
	import { Button, message, Modal, RangePicker, Space } from 'ant-design-vue';
	import { InfoCircleOutlined } from '@ant-design/icons-vue';
	import { usePage, router } from "@inertiajs/vue3";

	dayjs.locale('es');

	const page = usePage()

	const props = defineProps({
		recurso: Array,
		open: Boolean,
		tipo: String
	});

	const emitir = defineEmits(['send','close']);
	const recurso = ref(props.recurso ?? {});
	const tipo = ref(props.tipo)
	const cargando = ref(false)
	const tiempo = ref('')
	const disabled = ref(true)

	const data = {
		hora_inicio: null,
		hora_fin: null,
	}

	watch(() => props.open, (val)=>{
		if (val) {
			recurso.value = { ...props.recurso };
			tipo.value = props.tipo
		}
	})


	function onRangeChange([start,end]) {
		const h = Math.floor(dayjs(end).diff(dayjs(start),'hour',true))
		const m = Math.floor(dayjs(end).diff(dayjs(start),'minute',true))%60

		console.log(`${Math.floor(h)}h ${m%60}m`);
		tiempo.value = `: ${h}h ${m}m`

		if (h < 3) {
			disabled.value = false
		} else {
			disabled.value = true
		}
	}

	function onRangeOk([start,end]){
		data.hora_inicio = dayjs(start).format('YYYY-MM-DD HH:mm:ss')
		data.hora_fin = dayjs(end).format('YYYY-MM-DD HH:mm:ss')
	}

	const handleOk = async () => {
		if (tipo.value == 'equipo') {
			data.equipo_id = recurso.value.id;
			data.recurso_id = null;
		} else if(tipo.value == 'recurso'){
			data.equipo_id = null;
			data.recurso_id = recurso.value.id;
		}
		console.log(data);
		cargando.value = true;

		try {
			const response = await axios.post(route('reserva.create'), {...data});
			console.log(response.data);
			if (response.status === 201) {
				message.success('Solicitud de reserva enviada correctamente');
				emitir('close');
			} else {
				throw new Error('Error al reservar')
			}
		} catch (error) {
				message.error('Error al reservar');
				console.log(error);
		} finally {
				cargando.value = false;
		}
	};
</script>
<template>
	<Modal :open="open" title="Reservación" width="min(620px,100%)">
		<div class="flex w-auto  flex-wrap sm:flex-nowrap sm:justify-end gap-3">
			<!-- portada -->
			<div>
				<div class="max-w-60 rounded-lg overflow-hidden self-start">
					<img
						v-if="recurso.fotos && recurso.fotos.length > 0"
						:src="`/storage/${recurso.fotos[0].ruta}`"
						class="w-16 h-16 object-cover rounded"
					/>
					<img v-else alt="example" src="https://www.nbmchealth.com/wp-content/uploads/2018/04/default-placeholder.png" />
				</div>
				<!-- Area y Laboratorio -->
				<div class="pb-1 text-gray-500 pt-2">
						<div class="flex items-center gap-2">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="none" stroke="#999" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 6l-8 4l8 4l8-4zm-8 8l8 4l8-4"/></svg>
							{{recurso.area?.nombre ?? 'Sin definir'}}
						</div>
						<div class="flex items-center gap-2">
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="none" stroke="#999" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3h6m-5 6h4m-4-6v6L6 20a.7.7 0 0 0 .5 1h11a.7.7 0 0 0 .5-1L14 9V3"/></svg>
							{{recurso.area?.laboratorio?.nombre ?? 'Sin definir'}}
						</div>
						<details class="m-1">
							<summary><b>Horarios reservados</b></summary>
							<div>
								<div class="flex items-center gap-2">
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="none" stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.5 21H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v6M16 3v4M8 3v4m-4 4h16m-5 8l2 2l4-4"/></svg>
									{{ dayjs('2025-01-06').format('dddd, D [de] MMMM: [de] hh:mm A') }} a {{ dayjs('2025-01-06').format('hh:mm A') }}
								</div>
							</div>
						</details>
					</div>
			</div>
			<div class="min-w-96">
				<h3 class="text-2xl font-bold">{{recurso.nombre}} • <span class="text-neutral-400">#{{recurso.codigo}}</span></h3>
				<p>{{recurso.descripcion}}</p>
				<div class="">
					
					<details class="m-1">
						<summary><b>Horarios reservados</b></summary>
						<div>
							<div class="flex items-center gap-2">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="none" stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.5 21H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v6M16 3v4M8 3v4m-4 4h16m-5 8l2 2l4-4"/></svg>
								{{ dayjs('2025-01-06').format('dddd, D [de] MMMM: [de] hh:mm A') }} a {{ dayjs('2025-01-06 13:34').format('hh:mm A') }}
							</div>
							<div class="flex items-center gap-2">
								<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="none" stroke="#666" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.5 21H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v6M16 3v4M8 3v4m-4 4h16m-5 8l2 2l4-4"/></svg>
								{{ dayjs('2025-01-07 13:15').format('dddd, D [de] MMMM: [de] hh:mm A') }} a {{ dayjs('2025-01-07 13:34').format('hh:mm A') }}
							</div>
						</div>
					</details>
					<div class="">
						<h2 class="text-base">Selecciona el dia y la hora {{tiempo}}</h2>
						<RangePicker
							:show-time="{ format: 'HH:mm' }"
							format="YYYY-MM-DD HH:mm"
							:placeholder="['Start Time', 'End Time']"
							@change="onRangeChange"
							@ok="onRangeOk"
						/>
						<p class="flex items-center gap-2 pl-3 text-gray-500 pt-1">
							<InfoCircleOutlined />
							Maximo tiempo 2h 59m
						</p>
					</div>
				</div>
			</div>
		</div>
		<template #footer>
			<Button key="back" @click="emitir('close')">Cancelar</Button>
			<Button key="submit" type="primary" :loading="cargando" @click="handleOk" :disabled="disabled">Reservar</Button>
		</template>
	</Modal>
</template>
<style scoped>

</style>
