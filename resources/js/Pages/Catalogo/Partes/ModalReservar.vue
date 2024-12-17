<script setup>

import { ref, watch } from 'vue';

import { Button, Card, CardMeta, Modal, RangePicker, Space } from 'ant-design-vue';
	const props = defineProps({
		recurso: Array,
		open: Boolean
	});
	
	watch(() => props.open, (val)=>{
		if (val) {
      recurso.value = { ...props.recurso };
    }
	})

	const emitir = defineEmits(['send','close']);
	const recurso = ref(props.recurso ?? {});

	const loading = ref(false)

const handleOk = async () => {
        console.clear()
        cargando.value = true;
        console.log({...asistencia.value})
        try {
            const data = {
                ...asistencia.value,
                hora_entrada: asistencia.value.hora_entrada.replace('T',' ').concat(':00'),
                hora_salida: asistencia.value.hora_salida.replace('T',' ').concat(':00')
            }
            console.log(data)
            // Enviar la solicitud para crear el laboratorio
            const response = await axios.post(route('asistencias.registroCompleto'), data);

            if (response.status === 201) {
                
                message.success('La asistencia a sido registrado exitosamente');
                cerrarModal();
                emitir('actualizar-tabla', response.data["asistencia"]);
            } else {
                throw new 'Error al registrar la asistencia'
            }
           
        } catch (error) {
            message.error('Error al registrar la asistencia');
        } finally {
            cargando.value = false;
        }
    };
</script>
<template>
	<Modal :open="open" title="Reservación">
      <div class="flex flex-row-reverse justify-end gap-3">
				<div>
					<h3 class="text-2xl font-bold">{{recurso.nombre}} • <span class="text-neutral-400">{{recurso.codigo}}</span></h3>
					<p>{{recurso.descripcion}}</p>
					<div class="">
						<!-- Area y Laboratorio -->
						<div class="pb-3 text-gray-500">
							<div class="flex items-center gap-2">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="none" stroke="#999" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m12 6l-8 4l8 4l8-4zm-8 8l8 4l8-4"/></svg>
								{{recurso.area?.nombre ?? 'Sin definir'}}
							</div>
							<div class="flex items-center gap-2">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><path fill="none" stroke="#999" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1zm4 15h10m-8-4v4m6-4v4"/></svg>
								{{recurso.area?.laboratorio?.nombre ?? 'Sin definir'}}
							</div>
						</div>
						<div class="">
							<h2 class="text-base">Selecciona el dia y la hora</h2>
							<Space direction="vertical" :size="12">
								<RangePicker
									:show-time="{ format: 'HH:mm' }"
									format="YYYY-MM-DD HH:mm"
									:placeholder="['Start Time', 'End Time']"
									@change="onRangeChange"
									@ok="onRangeOk"
								/>
							</Space>
						</div>
					</div>
				</div>
				<!-- portada -->
				<div class="max-w-60 rounded-lg overflow-hidden">
					<img
					v-if="recurso.fotos && recurso.fotos.length > 0"
					:src="`/storage/${recurso.fotos[0].ruta}`"
					class="w-16 h-16 object-cover rounded"
					/>
					<img v-else alt="example" src="https://www.nbmchealth.com/wp-content/uploads/2018/04/default-placeholder.png" />
				</div>
			</div>
			<template #footer>
        <Button key="back" @click="emitir('close')">Cancelar</Button>
        <Button key="submit" type="primary" :loading="loading" @click="handleOk">Reseservar</Button>
      </template>
    </Modal>
</template>
<style scoped>
	
</style>