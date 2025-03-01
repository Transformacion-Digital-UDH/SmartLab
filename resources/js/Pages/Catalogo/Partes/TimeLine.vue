<script setup>
  import { DateTimeRange } from '@/lib/utils/datetime';
import { Tooltip } from 'ant-design-vue';
  import dayjs from 'dayjs';
  import { ref } from 'vue';

  const {reservas, fecha,  ...props} = defineProps({
		reservas: Array,
    fecha: Date,
	});

  const fecha_inicio = new Date(fecha.getFullYear(), fecha.getMonth(), fecha.getDate(), 8, 0)

  let limites = Array.from({ length: 13 }).map((_, i) => {
    const inicio = new Date(fecha_inicio.getTime() + (1000 * 60 * i * 45))
    const fin = new Date(fecha_inicio.getTime() + (1000 * 60 * (i+1) * 45))

    return { 
      label: `${dayjs(inicio).format('hh:mm A')} a ${dayjs(fin).format('hh:mm A')}`, 
      color: 'bg-neutral-300', 
      time: {
        inicio, 
        fin
      }
    }
  });

  limites = limites.map((limite) => {
    for (const reserva of reservas) {
      if (reserva.hora_inicio.toDateString() !== fecha.toDateString()) {
        continue
      }
      const range = new DateTimeRange(reserva.hora_inicio, reserva.hora_fin)
      
      if (range.isInOfRange(limite.time.inicio, limite.time.fin)) {
        limite.color = reserva.estado == 'Por aprobar' ? 'bg-orange-500' : reserva.estado == 'Aprobado' ? 'bg-green-500' : 'bg-red-500';
        break;
      }
    }
    return limite
  })

</script>
<template>
  <div class="grid h-1 gap-1" style="grid-template-columns: repeat(13, 1fr);">
    <Tooltip v-for="(hora, i) of limites" :key="i" placement="top">
      <template #title>
        <span>{{hora.label}}</span>
      </template>
      <div :class="`${hora.color} rounded-full col-span-1`"></div>
    </Tooltip>
  </div>
</template>
<style scoped>
  
</style>