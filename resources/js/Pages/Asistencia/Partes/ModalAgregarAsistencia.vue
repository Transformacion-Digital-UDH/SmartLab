<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue';
import { Modal, Form, FormItem, Input, Select, InputNumber, Button, message } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
  visible: Boolean,
  responsables: Array,
});

const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

const asistencia = ref({
  nombre: '',
  codigo: '',
  descripcion: '',
  aforo: null,
  email: '',
  inauguracion: null,
  responsable_id: null,
});

const cargando = ref(false);
const opcionesProyectos = ref([]);


// Cierra el modal y emite el evento para cerrar en el componente padre
const cerrarModal = () => {
  emitir('update:visible', false);
};

// Envía el formulario de creación del laboratorio
const enviarFormulario = async () => {
  cargando.value = true;
  try {
      // Enviar la solicitud para crear el laboratorio
      const response = await axios.post(route('laboratorios.store'), asistencia.value);
      message.success('Laboratorio agregado exitosamente');
      cerrarModal();
      emitir('actualizar-tabla', response.data["laboratorio"]);
  } catch (error) {
      message.error('Error al agregar el laboratorio');
      console.error('Error al guardar el laboratorio:', error);
  } finally {
      cargando.value = false;
  }
};

// Verificar si el modal se abre por primera vez y cargar responsables
watch(() => props.visible, (val) => {
  if (val) {
      console.log('watch ag: ', val)

      asistencia.value = {
          nombre: '',
          codigo: '',
          descripcion: '',
          aforo: null,
          email: '',
          inauguracion: '',
          responsable_id: null,
      };

      // Cargar las opciones de los responsables
      // opcionesProyectos.value = props.responsables.map(responsable => ({
      //     label: responsable.nombres,
      //     value: responsable.id,
      // }));
  }
});
</script>
<template>
  <Modal
      title="Agregar una asistencia"
      :open="visible"
      @cancel="cerrarModal"
      centered
      :footer="null"
  >
      <Form layout="vertical" @finish="enviarFormulario" :model="asistencia">
        <FormItem label="DNI" name="nombre" :rules="[{ required: true, message: 'Por favor ingrese el nombre' }]">
            <Input v-model:value="asistencia.nombre" placeholder="Ingrese el nombre" autofocus />
        </FormItem>
          <FormItem label="Nombre" name="nombre" :rules="[{ required: true, message: 'Por favor ingrese el nombre' }]">
              <Input v-model:value="asistencia.nombre" placeholder="Ingrese el nombre" autofocus />
          </FormItem>
          <FormItem label="Apellido" name="nombre" :rules="[{ required: true, message: 'Por favor ingrese el nombre' }]">
              <Input v-model:value="asistencia.nombre" placeholder="Ingrese el nombre" autofocus />
          </FormItem>

          <FormItem label="Proyectos" name="responsable_id">
              <Select
                  v-model:value="asistencia.responsable_id"
                  placeholder="Seleccione un responsable"
                  :options="[]"
                  show-search
                  :filter-option="buscarResponsable"
              />
          </FormItem>

          <div class="flex gap-2 pr-4">
            <FormItem label="Fecha de entrada" name="inauguracion">
                <Input type="datetime-local" v-model:value="asistencia.inauguracion" style="width: 100%;" />
            </FormItem>
            <FormItem label="Fecha de salida" name="inauguracion">
                <Input type="datetime-local" v-model:value="asistencia.inauguracion" style="width: 100%;" />
            </FormItem>
          </div>

          <FormItem class="flex justify-end mb-0">
              <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
              <Button type="primary" htmlType="submit" :loading="cargando">Guardar</Button>
          </FormItem>

      </Form>
  </Modal>
</template>


