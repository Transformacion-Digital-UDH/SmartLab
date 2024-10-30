<script setup>
  import { ref } from 'vue';
  import { usePage } from '@inertiajs/vue3';
  import { SmileOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons-vue';
  import { InputSearch, Table, Tag } from 'ant-design-vue';

  const {props } = usePage()
  let _asis = JSON.parse(JSON.stringify(props.asistencias)) 
  _asis = _asis.map((asis)=>{
    asis.hora_entrada = formatDateTime(asis.hora_entrada)
    asis.hora_salida = formatDateTime(asis.hora_salida) ?? '-'
    return asis;
  });
  console.log(_asis)
  const asistencias = ref(_asis || []);

  const token = ref(props.token || []);
  const inputSearchValue = ref('');

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

  function formatDateTime(date) {
    if (!date) return null;
    return new Intl.DateTimeFormat('es', { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric' ,
      hour: '2-digit', 
      minute: '2-digit', 
    }).format(new Date(date));
  }

</script>
<template>
  <div class="p-4">
    <div class="flex pb-4">

      <h1 class="font-bold text-4xl">Asistencias</h1>
    </div>
    <!-- Buscador -->
    <div>
       <InputSearch
         v-model:value="inputSearchValue"
         placeholder="Buscar por nombre o DNI"
       />
    </div>
    <div class="pt-4"></div>
    <Table :columns="columns" :data-source="asistencias">
      <!-- Head -->
      <template #headerCell="{ column }">
        <template v-if="column.key === 'nombres'">
          <span  class="flex items-center gap-2">
           <SmileOutlined />
           Nombres
          </span>
        </template>
        <template v-if="column.key === 'dni'">
          <span>
           DNI
          </span>
        </template>
        <template v-if="column.key === 'tipo'">
          <span>
           Tipo
          </span>
        </template>
        <template v-if="column.key === 'rol'">
          <span>
           Rol
          </span>
        </template>
        <template v-if="column.key === 'entrada'">
          <span>
           Entrada
          </span>
        </template>
        <template v-if="column.key === 'salida'">
          <span>
            Salida
          </span>
        </template>
        <template v-if="column.key === 'acciones'">
          <!-- Acciones -->
        </template>
      </template>
      <!-- Body -->
      <template #bodyCell="{ column, record }">
        <template v-if="column.key === 'nombres'">
          <a>
            {{ record.nombres }} 
          </a>
        </template>
        <template v-if="column.key === 'tipo'">
          <Tag v-if="record.hora_salida" :bordered="false" color="green">
            Salida
          </Tag>
          <Tag v-else :bordered="false" color="red">
            Entrada
          </Tag>
        </template>
        <template v-if="column.key === 'acciones'">
          <div class="flex gap-4 justify-end">
            <EditOutlined />
            <DeleteOutlined />
          </div>
        </template>
      </template>
    </Table>
    
  </div>
</template>

<style scoped></style>
