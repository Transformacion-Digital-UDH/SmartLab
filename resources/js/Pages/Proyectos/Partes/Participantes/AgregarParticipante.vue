<template>
    <!-- Modal para Agregar Área -->
    <Modal
        :open="visible"
        title="Agregar área"
        :footer="null"
        @cancel="cerrarModal"
    >
        <Form layout="vertical" @finish="guardarArea" :model="nuevaArea">
            <FormItem label="Nombre" name="nombre" :rules="[{ required: true, message: 'Por favor ingrese el nombre' }]">
                <Input v-model:value="nuevaArea.nombre" placeholder="Ingrese el nombre del área" />
            </FormItem>

            <FormItem label="Descripción" name="descripcion">
                <Input.TextArea v-model:value="nuevaArea.descripcion" placeholder="Ingrese una descripción" auto-size />
            </FormItem>

            <FormItem label="Aforo" name="aforo">
                <InputNumber
                    v-model:value="nuevaArea.aforo"
                    placeholder="Ingrese el aforo"
                    style="width: 100%;"
                    type="number"
                    step="1"
                    min="0"
                />
            </FormItem>

            <FormItem class="flex justify-end mb-0">
                <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
                <Button type="primary" htmlType="submit" :loading="cargando">Guardar</Button>
            </FormItem>
        </Form>
    </Modal>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';
import { Modal, Form, FormItem, Input, InputNumber, Button, message } from 'ant-design-vue';
import axios from 'axios';

const props = defineProps({
    visible: Boolean,
    laboratorio_id: {
        type: Number,
        required: true,
    },
});

const emit = defineEmits(['close', 'areaGuardada']);

const nuevaArea = ref({
    nombre: '',
    descripcion: '',
    aforo: null,
});
const cargando = ref(false);

const cerrarModal = () => {
    emit('close');
    nuevaArea.value = { nombre: '', descripcion: '', aforo: null };
};

const guardarArea = async () => {
    cargando.value = true;
    try {
        await axios.post(route('areas.store'), {
            nombre: nuevaArea.value.nombre,
            descripcion: nuevaArea.value.descripcion,
            aforo: nuevaArea.value.aforo,
            laboratorio_id: props.laboratorio_id,
        });

        message.success('Área agregada exitosamente');
        emit('areaGuardada');
        cerrarModal();
    } catch (error) {
        console.error("Error al guardar el área:", error);
    } finally {
        cargando.value = false;
    }
};
</script>
