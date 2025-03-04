<script setup>
import { Button, Card, CardMeta } from 'ant-design-vue';

const props = defineProps({
    area: Object,
});

const emitir = defineEmits(['open-modal']);
const area = props.area ?? {};
</script>

<template>
<Card
  hoverable
  class="h-full flex flex-col"
  :bodyStyle="{
    display: 'flex',
    flexDirection: 'column',
    flexGrow: 1
  }"
>
  <!-- Si usas #cover, va fuera de ant-card-body por defecto -->
  <template #cover>
    <div class="aspect-square">
      <img
        alt="Área"
        src="https://www.nbmchealth.com/wp-content/uploads/2018/04/default-placeholder.png"
        class="w-full h-full object-cover rounded"
      />
    </div>
  </template>

  <div class="flex flex-col justify-between h-full">
    <!-- Contenido superior -->
    <div>
        <p class="mb-0 text-sm text-gray-500">
        Aforo → {{ area.aforo ?? 'No definido' }}
        </p>

        <CardMeta :title="area.nombre">
        <template #description>
            <div class="pb-3 text-gray-500">
            <div class="flex items-center gap-2">
                <!-- Ícono -->
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                <path
                    fill="none"
                    stroke="#999"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 3h6m-5 6h4m-4-6v6L6 20a.7.7 0 0 0 .5 1h11a.7.7 0 0 0 .5-1L14 9V3"
                />
                </svg>
                {{ area.laboratorio?.nombre ?? 'Sin laboratorio' }}
            </div>
            </div>
        </template>
        </CardMeta>
    </div>

    <!-- Botón en la parte inferior -->
    <Button type="primary" class="w-full" @click="emitir('open-modal', area)">
        Reservar
    </Button>

  </div>

</Card>

</template>

<style scoped></style>
