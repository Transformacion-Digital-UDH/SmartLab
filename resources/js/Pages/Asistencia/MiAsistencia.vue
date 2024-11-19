<script setup>
  import { ref, watch, h } from 'vue';
  import { usePage } from '@inertiajs/vue3';
  import { SmileOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons-vue';
  import { InputSearch, Table, Tag, Pagination, Button } from 'ant-design-vue';
  import NavBar from '@/Components/App/NavBar.vue';

  const { props } = usePage()

  const token = props.token || [];
  const inputSearchValue = ref('');

  let asistenciasPaginate = JSON.parse(JSON.stringify(props.asistencias));

  class Asistencia {
    constructor(props){ 
      this.data = props
      this.fecha = formatDateTime(new Date(props.hora_entrada)).slice(0,11),
      this.check = true
      this.entrada = new Date(props.hora_entrada)
      this.salida = new Date(props.hora_salida)
      this.type = this.entrada.getHours() < 12 ? 'A': this.entrada.getHours() < 18 ? 'B' : 'C';
    }
    getFecha() {
      return 'jjjjj';
    }
    getHourStart() {
      return this.getTime(this.entrada)
    }

    getHourEnd() {

    }
    getTime(date) {
      const h = date.getHours().toString()
      const m = date.getMinutes().toString()
      console.log(h,m)
      return `${h.length < 2 ? '0' : ''}${h}:${m.length < 2 ? '0' : ''}${m}`
    }
  }

  
  // Damos formato de fecha de entrada y salida
  let asistencias = asistenciasPaginate.map((asis)=>{
    return new Asistencia(asis);
  });
  console.clear()
  console.log(asistencias)
  
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
        <h1 class="font-bold text-4xl px-2">Mis Asistencias</h1>
      </div>
      <!-- Buscador -->
      <div>
         <!-- <InputSearch
           v-model:value="inputSearchValue"
           placeholder="Buscar por nombre o DNI"
         /> -->
      </div>
      <div class="flex gap-2 overflow-x-scroll scrollbar-none px-2">
        <div class="flex gap-2 text-neutral-500 px-2 py-1 bg-neutral-100 rounded-full"><span class="text-green-600 font-bold">12 </span> Asistencias</div>
        <div class="flex gap-2 text-neutral-500 px-2 py-1 bg-neutral-100 rounded-full"><span class="text-blue-600 font-bold">1 </span> Completas</div>
        <div class="flex gap-2 text-neutral-500 px-2 py-1 bg-neutral-100 rounded-full"><span class="text-violet-600 font-bold">2 </span> Incompletas</div>
        <div class="flex gap-2 text-neutral-500 px-2 py-1 bg-neutral-100 rounded-full"><span class="text-orange-600 font-bold">0 </span> Pendientes</div>
      </div>
      <div class="pt-4"></div>
      <div class="appy gap-2 p-2">
        <article v-for="asistencia in asistencias" class="flex justify-between bg-neutral-50 border border-neutral-100 rounded-xl py-1 pl-3 pr-3">
          <!-- RIGTH -->
          <div class="flex items-center gap-2">
            <div class="flex border p-1 rounded-full bg-neutral-50">
              <svg v-if="asistencia.type == 'A'" xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="hsl(12,100%,80%)"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-brightness-2"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M6 6h3.5l2.5 -2.5l2.5 2.5h3.5v3.5l2.5 2.5l-2.5 2.5v3.5h-3.5l-2.5 2.5l-2.5 -2.5h-3.5v-3.5l-2.5 -2.5l2.5 -2.5z" /></svg>
              <svg v-else-if="asistencia.type == 'B'" xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="hsl(160,100%,30%)"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-moon-stars"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" /><path d="M17 4a2 2 0 0 0 2 2a2 2 0 0 0 -2 2a2 2 0 0 0 -2 -2a2 2 0 0 0 2 -2" /><path d="M19 11h2m-1 -1v2" /></svg>
              <svg v-else="asistencia.type == 'C'" xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="hsl(60,100%,40%)"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-brightness-down"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" /><path d="M12 5l0 .01" /><path d="M17 7l0 .01" /><path d="M19 12l0 .01" /><path d="M17 17l0 .01" /><path d="M12 19l0 .01" /><path d="M7 17l0 .01" /><path d="M5 12l0 .01" /><path d="M7 7l0 .01" /></svg>
            </div>
            <div>
              <div class="text-green-600 font-bold">
                {{asistencia.fecha}}
              </div class="text-green-500"> 
              <div class="flex items-center gap-2">
                <svg  xmlns="http://www.w3.org/2000/svg"  width="16"  height="16"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-device-imac"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 4a1 1 0 0 1 1 -1h16a1 1 0 0 1 1 1v12a1 1 0 0 1 -1 1h-16a1 1 0 0 1 -1 -1v-12z" /><path d="M3 13h18" /><path d="M8 21h8" /><path d="M10 17l-.5 4" /><path d="M14 17l.5 4" /></svg>
                Laboratorio
              </div>
            </div>
          </div>
          <!-- LEFT -->
          <div class="flex gap-3">
            <div class="flex flex-col items-end gap-1">
              <span v-if="asistencia.check" class="text-blue-600"> 2h 45m</span>
              <span v-else class="text-blue-600">0h 0m</span>
              <div class="flex font-bold">
                {{ asistencia.getHourStart() }}
                <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="hsl(12,0%,80%)"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6l6 6l-6 6" /></svg>
                <span v-if="asistencia.check">10:12</span>
                <span v-else>--:--</span>
              </div>
                
            </div>
            <div class="flex flex-col justify-between">
                <svg  xmlns="http://www.w3.org/2000/svg" width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-dots"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /><path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" /></svg>
                <svg v-if="asistencia.check" xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="hsl(210,100%,50%)"  class="icon icon-tabler icons-tabler-filled icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" /></svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  viewBox="0 0 24 24"  fill="none"  stroke="hsl(0,0%,80%)"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-progress-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 20.777a8.942 8.942 0 0 1 -2.48 -.969" /><path d="M14 3.223a9.003 9.003 0 0 1 0 17.554" /><path d="M4.579 17.093a8.961 8.961 0 0 1 -1.227 -2.592" /><path d="M3.124 10.5c.16 -.95 .468 -1.85 .9 -2.675l.169 -.305" /><path d="M6.907 4.579a8.954 8.954 0 0 1 3.093 -1.356" /><path d="M9 12l2 2l4 -4" /></svg>
          
            </div>
          </div>
        </article>
   
        
      </div>
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
