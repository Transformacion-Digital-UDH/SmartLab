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
}));

// Dividir asistencias
const asistenciasCompletas = ref(props.asistenciasCompletas.data.map((asis) => ({
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
})));
const asistenciasIncompletas = ref(props.asistenciasIncompletas.data.map((asis) => ({
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
})));

// Estado de la paginación y tabs
const current = ref(asistencias.current_page);
const currentCompletas = ref(props.asistenciasCompletas.current_page);
const currentIncompletas = ref(props.asistenciasIncompletas.current_page);
const activeKey = ref('1'); // Clave activa del Tab inicial

watch(current, () => {
  setURLPage(current.value);
});

watch(currentCompletas, () => {
  setURLPageCompletas(currentCompletas.value);
});

watch(currentIncompletas, () => {
  setURLPageIncompletas(currentIncompletas.value);
});

watch(activeKey, () => {
  current.value = 1;
  currentCompletas.value = 1;
  currentIncompletas.value = 1;
  setURLPage(1);
  setURLPageCompletas(1);
  setURLPageIncompletas(1);
});

// Dar la ruta de paginación utilizando Inertia.js
function setURLPage(page) {
  router.get(route('asistencias.user', { page, per_page: 10, tab: activeKey.value }), {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      asistencias.data = page.props.asistencias.data.map((asis) => ({
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
      }));
      asistencias.total = page.props.asistencias.total;
      asistencias.current_page = page.props.asistencias.current_page;
    }
  });
}

function setURLPageCompletas(page) {
  router.get(route('asistencias.completas', { page, per_page: 10 }), {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      asistenciasCompletas.value = page.props.asistenciasCompletas.data.map((asis) => ({
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
      }));
      props.asistenciasCompletas.total = page.props.asistenciasCompletas.total;
      props.asistenciasCompletas.current_page = page.props.asistenciasCompletas.current_page;
    }
  });
}

function setURLPageIncompletas(page) {
  router.get(route('asistencias.incompletas', { page, per_page: 10 }), {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: (page) => {
      asistenciasIncompletas.value = page.props.asistenciasIncompletas.data.map((asis) => ({
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
      }));
      props.asistenciasIncompletas.total = page.props.asistenciasIncompletas.total;
      props.asistenciasIncompletas.current_page = page.props.asistenciasIncompletas.current_page;
    }
  });
}
</script>

<template>
  <AppLayout title="Mis asistencias">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">
        Mis asistencias
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
                {{ props.totalAsistencias }}
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
          <div class="grid place-items-center pb-5">
            <Pagination v-model:current="current" :total="asistencias.total" :pageSize="10" show-less-items @change="setURLPage" />
          </div>
        </TabPane>
        <TabPane key="2">
          <template #tab>
            <div class="flex items-center">
              <CheckCircleOutlined />
              Completas
              <span class="text-blue-600 font-bold ml-2 bg-blue-100 px-1 rounded-full">
                {{ props.totalCompletas }}
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
          <div class="grid place-items-center pb-5">
            <Pagination v-model:current="currentCompletas" :total="props.asistenciasCompletas.total" :pageSize="10" show-less-items @change="setURLPageCompletas" />
          </div>
        </TabPane>
        <TabPane key="3">
          <template #tab>
            <div class="flex items-center">
              <ExclamationCircleOutlined class="block" />
              Incompletas
              <span class="text-red-600 font-bold ml-2 bg-red-100 px-1 rounded-full">
                {{ props.totalIncompletas }}
              </span>
            </div>
          </template>
          <div class="appy gap-2">
            <CardAsistencia
              :asistencia="asistenciaIncompleta"
              v-for="(asistenciaIncompleta) in asistenciasIncompletas"
              :key="asistenciaIncompleta.id"
            />
          </div>
          <div class="grid place-items-center pb-5">
            <Pagination v-model:current="currentIncompletas" :total="props.asistenciasIncompletas.total" :pageSize="10" show-less-items @change="setURLPageIncompletas" />
          </div>
        </TabPane>
      </Tabs>
    </div>
  </AppLayout>
</template>

<style scoped>
.appy {
  display: grid;
  grid-template-columns: 1fr;
}
</style>
