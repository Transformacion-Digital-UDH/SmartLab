<script setup>
import 'moment/locale/es.js';
import AppLayout from '@/Layouts/AppLayout.vue';
import moment from 'moment';
import CardAsistencia from './Partes/CardAsistencia.vue';
import { ref, watch } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { CheckCircleOutlined, ExclamationCircleOutlined, UserOutlined } from '@ant-design/icons-vue';
import { Pagination, Tabs, TabPane } from 'ant-design-vue';

const { props } = usePage();

// Convertir asistencias en objetos planos serializables
const asistencias = props.asistencias;
asistencias.data = asistencias.data.map((asis) => ({
  id: asis.id,
  check: asis.hora_salida != null,
  entrada: asis.hora_entrada,
  salida: asis.hora_salida,
  type: new Date(asis.hora_entrada).getHours() < 12
    ? 'A'
    : new Date(asis.hora_entrada).getHours() < 18
    ? 'B'
    : 'C',
  laboratorio: asis.laboratorio,
  proyecto: asis.proyecto,
  diff: moment(asis.hora_salida).diff(moment(asis.hora_entrada), 'minutes'),
}));

// Dividir asistencias
const asistenciasCompletas = asistencias.data.filter((a) => a.check);
const asistenciasIncompletas = asistencias.data.filter((a) => !a.check);

// Estado de la paginación y tabs
const current = ref(asistencias.current_page);
const activeKey = ref('1'); // Clave activa del Tab inicial

watch(current, () => {
  setURLPage(current.value);
});

// Dar la ruta de paginación utilizando Inertia.js
function setURLPage(page) {
  router.get(route(route().current(), { page }), {}, { preserveState: true, preserveScroll: true });
}
</script>

<template>
  <AppLayout title="Mis Asistencias">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">
        Mis Asistencias
      </h2>
    </template>
    <div class="max-w-7xl mx-auto pb-10 pt-3 sm:px-6 lg:px-8 px-4">
      <Tabs v-model:activeKey="activeKey">
        <TabPane key="1">
          <template #tab>
            <div class="flex items-center">
              <UserOutlined class="block" />
              Asistencias
              <span class="text-green-600 font-bold ml-2 bg-neutral-100 px-1 rounded-full">
                {{ asistencias.total }}
              </span>
            </div>
          </template>
          <div class="appy gap-2">
            <CardAsistencia
              :asistencia="asistencia"
              v-for="(asistencia) in asistencias.data"
              :key="asistencia.id"
            />
          </div>
        </TabPane>
        <TabPane key="2">
          <template #tab>
            <div class="flex items-center">
              <CheckCircleOutlined />
              Completas
              <span class="text-blue-600 font-bold ml-2 bg-blue-100 px-1 rounded-full">
                {{ asistenciasCompletas.length }}
              </span>
            </div>
          </template>
          <div class="appy gap-2">
            <CardAsistencia
              :asistencia="asistenciaCompleta"
              v-for="(asistenciaCompleta) in asistenciasCompletas"
              :key="asistenciaCompleta.id"
            />
          </div>
        </TabPane>
        <TabPane key="3">
          <template #tab>
            <div class="flex items-center">
              <ExclamationCircleOutlined class="block" />
              Incompletas
              <span class="text-red-600 font-bold ml-2 bg-red-100 px-1 rounded-full">
                {{ asistenciasIncompletas.length }}
              </span>
            </div>
          </template>
          <div class="appy gap-2">
            <CardAsistencia
              :asistencia="asistencia"
              v-for="(asistencia) in asistenciasIncompletas"
              :key="asistencia.id"
            />
          </div>
        </TabPane>
      </Tabs>
    </div>
    <div class="grid place-items-center pb-5">
      <Pagination v-model:current="current" :total="asistencias.total" show-less-items />
    </div>
  </AppLayout>
</template>

<style scoped>
.appy {
  display: grid;
  grid-template-columns: 1fr;
}
</style>
