<script setup>
  import { ref, watch, h } from 'vue';
  import { usePage } from '@inertiajs/vue3';
  import { CheckCircleOutlined, ExclamationCircleOutlined, UserOutlined } from '@ant-design/icons-vue';
  import { InputSearch, Table, Tag, Pagination, Button, Tabs, TabPane } from 'ant-design-vue';
  import NavBar from '@/Components/App/NavBar.vue';
  import moment from 'moment';
  import 'moment/locale/es.js'
  import CardAsistencia from './Partes/CardAsistencia.vue';


  const { props } = usePage()

  const token = props.token || [];

  const asistencias = props.asistencias;

  const asistenciasCompletas = props.asistenciasCompletas
  const asistenciasIncompletas = props.asistenciasIncompletas

  class Asistencia {
    constructor(props){ 
      this.id = props.id
      this.data = props
      this.check = props.hora_salida != null;
      this.entrada = new Date(props.hora_entrada)
      this.salida = new Date(props.hora_salida)
      this.type = this.entrada.getHours() < 12 ? 'A': this.entrada.getHours() < 18 ? 'B' : 'C';

      const totalMinutes = moment(this.salida).diff(this.entrada, 'minutes');
      const hours = Math.floor(totalMinutes / 60);
      const minutes = totalMinutes % 60;

      this.diff = `${hours}h ${minutes}m`;
    }
  }

  
  // Damos formato de fecha de entrada y salida
  asistencias.data = asistencias.data.map((asis)=>{
    return new Asistencia(asis);
  });

  console.log(asistencias)


  // Dar la ruta de paginacion
  function setURLPage(page, cantidad) {
    const url = new URL(location.href)
    url.searchParams.set('page', page)
    url.searchParams.set('cantidad', cantidad)
    location.href = url.toString()
  }

  // Formatear la fecha
  function formatDateTime(date) {
    // if (!date) return null;
    return new Intl.DateTimeFormat('es', { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric' ,
      hour: '2-digit', 
      minute: '2-digit', 
    }).format(date);
  }

</script>
<template>
  <NavBar title="Asistencias">
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
          <div class="appy gap-2 overflow-y-scroll">
						<CardAsistencia :asistencia="asistencia" v-for="(asistencia) in asistencias.data" :key="asistencia.id"/>
					</div>
        </TabPane>
        <TabPane key="2">
					<template #tab >
						<div class="flex items-center">

							<CheckCircleOutlined />
							Completas
							<span class="text-blue-600 font-bold ml-2 bg-blue-100 px-1 rounded-full">
									{{asistenciasCompletas.total}} 
							</span> 
						</div>
          </template>
          <div class="appy gap-2">
						<CardAsistencia :asistencia="asistencia" v-for="(asistencia) in asistenciasCompletas.data" :key="asistencia.id"/>
					</div>
				</TabPane>
        <TabPane key="3">
					<template #tab>
						<div class="flex items-center" @click="cargarAsistenciasInompletas">

							<ExclamationCircleOutlined class="block" />
							Incompletas
							<span class="text-red-600 font-bold ml-2 bg-red-100 px-1 rounded-full">
									{{asistenciasIncompletas.total}} 
							</span> 
						</div>
          </template>
          <div class="appy gap-2">
						<CardAsistencia :asistencia="asistencia" v-for="(asistencia) in asistenciasIncompletas.data" :key="asistencia.id"/>
					</div>
				</TabPane>
      </Tabs>
      <div class="pt-4"></div>
      
    </div>
    <div class="grid place-items-center pb-5">
      <Pagination
        v-model:current="currentPage"
        v-model:pageSize="pageSize"
        show-size-changer
        :total="total"
        @showSizeChange="onShowSizeChange"
      />
    </div>
  </NavBar>
</template>

<style scoped>
  .appy {
    display: grid;
    grid-template-columns: 1fr;
  }
</style>
