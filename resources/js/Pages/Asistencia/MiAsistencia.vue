<script setup>
  import { ref, watch, h } from 'vue';
  import { usePage } from '@inertiajs/vue3';
  import { SmileOutlined, EditOutlined, DeleteOutlined, ClockCircleOutlined,CheckCircleFilled, CheckCircleOutlined, AppleOutlined, AndroidOutlined, ExclamationCircleOutlined, UserOutlined } from '@ant-design/icons-vue';
  import { InputSearch, Table, Tag, Pagination, Button, Tabs, TabPane } from 'ant-design-vue';
  import NavBar from '@/Components/App/NavBar.vue';
  import moment from 'moment';
  import 'moment/locale/es.js'
import CardAsistencia from './Partes/CardAsistencia.vue';


  const { props } = usePage()

  const token = props.token || [];
  const inputSearchValue = ref('');

  console.log(props)
  let asistenciasPaginate = props.asistencias.data;


  class Asistencia {
    constructor(props){ 
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
  let asistencias = asistenciasPaginate.map((asis)=>{
    return new Asistencia(asis);
  });


  const columns = [{
    name: 'Name',
    dataIndex: 'nombres',
    key: 'nombres',
  },{
    name: 'Name',
    dataIndex: 'dni',
    key: 'dni',
  },{
    name: 'Name',
    dataIndex: 'tipo',
    key: 'tipo',
  },{
    name: 'Name',
    dataIndex: 'rol',
    key: 'rol',
  },{
    name: 'Name',
    dataIndex: 'hora_entrada',
    key: 'entrada',
  },{
    name: 'Name',
    dataIndex: 'hora_salida',
    key: 'salida',
  },{
    name: 'Acciones',
    key: 'acciones',
  }];


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

  function day() {
    let d = new Date(Date.now())
    d.setDate(Math.round(Math.random()*30))
    d.setMonth(Math.round(Math.random()*30))
    return {
      title: formatDateTime(d),
      check: Math.random() > 0.5,
      type: ['M','N','T'][Math.round(Math.random()*3)]
    }
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
									{{props.asistencias.total}} 
							</span> 
						</div>
          </template>
          <div class="appy gap-2">
						<CardAsistencia :asistencia="asistencia" v-for="(asistencia) in asistencias" :key="asistencia.id"/>
					</div>
        </TabPane>
        <TabPane key="2">
					<template #tab>
						<div class="flex items-center">

							<CheckCircleOutlined />
							Completas
							<span class="text-green-600 font-bold ml-2 bg-neutral-100 px-1 rounded-full">
									{{props.asistencias.total}} 
							</span> 
						</div>
          </template>
          <div class="appy gap-2">
						<CardAsistencia :asistencia="asistencia" v-for="(asistencia) in asistencias" :key="asistencia.id"/>
					</div>
				</TabPane>
        <TabPane key="3">
					<template #tab>
						<div class="flex items-center">

							<ExclamationCircleOutlined class="block" />
							Incompletas
							<span class="text-green-600 font-bold ml-2 bg-neutral-100 px-1 rounded-full">
									{{props.asistencias.total}} 
							</span> 
						</div>
          </template>
          <div class="appy gap-2">
						<CardAsistencia :asistencia="asistencia" v-for="(asistencia) in asistencias" :key="asistencia.id"/>
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
