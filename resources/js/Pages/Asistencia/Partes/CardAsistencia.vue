<script lang="ts" setup>
	import 'dayjs/locale/es';
	import moment from 'moment';
	import { CheckCircleFilled, CheckCircleOutlined, AppleOutlined, AndroidOutlined, ExclamationCircleOutlined, UserOutlined, DeploymentUnitOutlined, ExperimentOutlined } from '@ant-design/icons-vue';
	import dayjs from 'dayjs';
	dayjs.locale('es');


	const { asistencia } = defineProps({
		asistencia: Object
	})

	console.log( dayjs(asistencia.salida).diff(asistencia.entrada,'hour'));
</script>
<template>
	<article class="flex justify-between bg-neutral-50 border border-neutral-100 rounded-xl py-1 pl-3 pr-3">
		<!-- RIGTH -->
		<div class="flex items-center gap-2">
			<div class="flex border p-1 rounded-full bg-neutral-50">
				<svg v-if="asistencia.type == 'A'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 28 28"><path fill="#eab308" d="M14.75 2.75a.75.75 0 0 0-1.5 0v1.496a.75.75 0 0 0 1.5 0zM20 14a6 6 0 1 1-12 0a6 6 0 0 1 12 0m-1.5 0A4.5 4.5 0 0 0 14 9.5v9a4.5 4.5 0 0 0 4.5-4.5m7.5 0a.75.75 0 0 1-.75.75h-1.497a.75.75 0 0 1 0-1.5h1.496A.75.75 0 0 1 26 14m-11.25 9.754a.75.75 0 0 0-1.5 0v1.496a.75.75 0 0 0 1.5 0zM5 14a.75.75 0 0 1-.75.75h-1.5a.75.75 0 0 1 0-1.5h1.5A.75.75 0 0 1 5 14m1.28-8.781a.75.75 0 1 0-1.061 1.06l1.5 1.5a.75.75 0 0 0 1.06-1.06zm-1.061 17.56a.75.75 0 0 0 1.06 0l1.5-1.5a.75.75 0 1 0-1.06-1.06l-1.5 1.5a.75.75 0 0 0 0 1.06m16.5-17.56a.75.75 0 1 1 1.06 1.06l-1.5 1.5a.75.75 0 1 1-1.06-1.06zm1.06 17.56a.75.75 0 0 1-1.06 0l-1.5-1.5a.75.75 0 1 1 1.06-1.06l1.5 1.5a.75.75 0 0 1 0 1.06"/></svg>
				<svg v-else-if="asistencia.type == 'B'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 32 32"><path fill="#eab308" d="M16 4a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0V5a1 1 0 0 1 1-1m0 19a7 7 0 1 0 0-14a7 7 0 0 0 0 14m0-2a5 5 0 1 1 0-10a5 5 0 0 1 0 10m11-4a1 1 0 0 0 0-2h-2a1 1 0 1 0 0 2zm-11 7a1 1 0 0 1 1 1v2a1 1 0 0 1-2 0v-2a1 1 0 0 1 1-1m-9-7a1 1 0 1 0 0-2H5a1 1 0 1 0 0 2zm.293-9.707a1 1 0 0 1 1.414 0l1 1a1 1 0 1 1-1.414 1.414l-1-1a1 1 0 0 1 0-1.414m1.414 17.414a1 1 0 0 1-1.414-1.414l1-1a1 1 0 0 1 1.414 1.414zm16-17.414a1 1 0 0 0-1.414 0l-1 1a1 1 0 0 0 1.414 1.414l1-1a1 1 0 0 0 0-1.414m-2.414 16.414l1 1a1 1 0 0 0 1.414-1.414l-1-1a1 1 0 0 0-1.414 1.414"/></svg>
				<svg v-else="asistencia.type == 'C'" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16"><path fill="#0891b2" d="M7.85 3.015a5 5 0 1 1-4.585 7.712c1.403-.38 3.316-1.302 4.16-3.551c.552-1.474.584-2.938.425-4.16M13.456 8a6 6 0 0 0-6.21-5.996a.5.5 0 0 0-.475.592c.23 1.214.28 2.728-.283 4.228c-.8 2.134-2.802 2.84-4.077 3.071a.5.5 0 0 0-.361.71A6 6 0 0 0 13.456 8"/></svg>
			</div>
			<div>
				<div class="text-green-600 font-bold">
					{{dayjs(asistencia.entrada).format('dddd, D MMM YYYY').toLowerCase()}}
				</div class="text-green-500">
				<div class="flex gap-x-3 flex-wrap">
					<div v-if="asistencia.laboratorio != null" class="flex items-center gap-2">
						<ExperimentOutlined />
						{{asistencia.laboratorio}}
					</div>
					<div v-if="asistencia.proyecto != null" class="flex items-center gap-2">
						<DeploymentUnitOutlined />
						{{asistencia.proyecto}}
					</div>
				</div>
			</div>
		</div>
		<!-- LEFT -->
		<div class="flex gap-3">
			<div class="flex flex-col items-end justify-between gap-1">
				<span v-if="asistencia.check" class="text-blue-600">{{ dayjs(asistencia.salida).diff(asistencia.entrada,'hour') }}h {{ dayjs(asistencia.salida).diff(asistencia.entrada,'minute') % 60}}m</span>
				<span v-else class="text-blue-600">0h 0m</span>
				<div class="flex font-semibold text-nowrap text-gray-500">
					{{ moment(asistencia.entrada).locale('es').format('LT') }}
					<svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="hsl(12,0%,80%)"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
					<span v-if="asistencia.check">{{ moment(asistencia.salida).format('LT') }}</span>
					<span v-else>--:--</span>
				</div>
			</div>
			<div class="flex flex-col justify-end pb-1">
					<!-- <svg  xmlns="http://www.w3.org/2000/svg" width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-dots"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg> -->
					<CheckCircleFilled v-if="asistencia.check" class="text-blue-500"/>
					<CheckCircleOutlined v-else class="text-neutral-200"/>
			</div>
		</div>
	</article>
</template>
<style scoped>

</style>
