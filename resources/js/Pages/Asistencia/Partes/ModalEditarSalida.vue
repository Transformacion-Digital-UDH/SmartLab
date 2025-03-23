<script setup>
  import { ref, watch } from 'vue';
  import { Modal, Form, FormItem, Input, Button, message } from 'ant-design-vue';
  import axios from 'axios';
  import moment from 'moment'


  const props = defineProps({
    visible: Boolean,
    asistencia: {
        type: Object,
        default: () => ({}),
    },
  });

  const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

  const asistencia = ref({ ...props.asistencia })
  const cargando = ref(false);
  const selectedTime = ref(asistencia.value.hora_entrada);

  // Cierra el modal y emite el evento para cerrar en el componente padre
  const cerrarModal = () => {
    emitir('update:visible', false);
  };

  const enviarFormulario = async () => {
    cargando.value = true;
    try {
        const response = await axios.put(route(
          'asistencia.editar.salida',{
            id: asistencia.value.id,
            date: `${moment(asistencia.entrada).format('YYYY-MM-DD')} ${selectedTime.value}:00`
          }
        ));
        message.success('Asistencia actualizado exitosamente');
        cerrarModal();
        emitir('actualizar-tabla', response.data["laboratorio"]);
    } catch (error) {
        message.error('Error al actualizar la hora de salida');
    } finally {
        cargando.value = false;
    }
  };

  watch(() => props.visible, (val) => {
    if (val) {
        asistencia.value = { ...props.asistencia };
        selectedTime.value = asistencia.value.hora_entrada
    }
  });

</script>
<template>
  <Modal
      title="Editar la hora de salida"
      :open="visible"
      @cancel="cerrarModal"
      centered
      :footer="null"
  >
      <Form layout="vertical" @finish="enviarFormulario" :model="asistencia">

          <FormItem label="Hora de entrada" name="inauguracion">
              <Input type="time" v-model:value="asistencia.hora_entrada" readonly style="width: 100%;" />
          </FormItem>
          <FormItem label="Hora de salida" name="inauguracion">
              <Input type="time" v-model:value="selectedTime" style="width: 100%;" />
          </FormItem>

          <FormItem class="flex justify-end mb-0">
              <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
              <Button type="primary" htmlType="submit" :loading="cargando">Guardar</Button>
          </FormItem>

      </Form>
  </Modal>
</template>


