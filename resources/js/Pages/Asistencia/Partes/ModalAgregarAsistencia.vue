<script setup>
    import { ref, watch, defineProps, defineEmits } from 'vue';
    import { Modal, Form, FormItem, Input, Select, InputNumber, Button, message, SelectOption } from 'ant-design-vue';
    import { CheckCircleFilled, CheckCircleOutlined, AppleOutlined, AndroidOutlined, ExclamationCircleOutlined, UserOutlined } from '@ant-design/icons-vue';
    

    import axios from 'axios';

    const props = defineProps({
        visible: Boolean,
        responsables: Array,
    });

    const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

    const asistencia = ref({
        usuario_id: null,
        proyecto_id: null,
        laboratorio_id: null,
        hora_entrada: null,
        hora_salida: null
    });

    const cargando = ref(false);
    const opcionesProyectos = ref([]);

    const dni = ref('')
    const proyectos = ref([])
    const proyectoSelectId = ref(null)
    const usuarioId = ref(null)
    const usuarioInfo = ref({nombres:'',appellidos:''})

    // Cierra el modal y emite el evento para cerrar en el componente padre
    const cerrarModal = () => {
        emitir('update:visible', false);
    };

    // Envía el formulario de creación del laboratorio
    const enviarFormulario = async () => {
        cargando.value = true;
        console.log({...asistencia.value})
        try {
            // Enviar la solicitud para crear el laboratorio
            const response = await axios.post(route('asistencias.registroCompleto'), {...asistencia.value});
            message.success('La asistencia a sido registrado exitosamente');
            cerrarModal();
            emitir('actualizar-tabla', response.data["asistencia"]);
        } catch (error) {
            message.error('Error al registrar la asistencia');
            console.error('Error al registrar la asistencia', error);
        } finally {
            cargando.value = false;
        }
    };

    const obtenerInfo = async ()=>{
        const response = await fetch(`/api/asistencia/user/${dni.value}`);
        const data = await response.json();


        asistencia.value.usuario_id = data.id
        proyectos.value = data.proyectos
        usuarioInfo.value = {
            nombres: data.nombres,
            apellidos: data.apellidos
        }
    }
    // Verificar si el modal se abre por primera vez y cargar responsables
    watch(() => props.visible, (val) => {
        if (val) {
            asistencia.value = {
                nombre: '',
                codigo: '',
                descripcion: '',
                aforo: null,
                email: '',
                inauguracion: '',
                responsable_id: null,
            };
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
            <div v-if="asistencia.usuario_id == null">
                <FormItem label="Ingrese el DNI" name="dni" :rules="[{ required: true, message: 'Por favor ingrese el DNI' }]">
                    <Input v-model:value="dni" placeholder="Ingrese el DNI" autofocus />
                </FormItem>
                <Button type="primary" htmlType="submit" :loading="cargando" @click="obtenerInfo">Validar</Button>
                
            </div>

            <div v-if="asistencia.usuario_id != null" class="pt-4">
                <div class="flex">
                    <div class="flex items-center gap-2 pb-4">
                        <UserOutlined class="text-xl block"/> {{ usuarioInfo.nombres }} {{  usuarioInfo.apellidos }}
                    </div>
                </div>
                <FormItem label="Seleccione su proyecto" name="responsable_id">
                    <Select
                        ref="select"
                        v-model:value="asistencia.proyecto_id"
                        style="width: 120px"
                        @focus="focus"
                        @change="handleChange"
                    >
                        <SelectOption v-for="proyecto in proyectos" :key="proyecto.id" :value="proyecto.id">{{proyecto.nombre}}</SelectOption>
                    </Select>
                </FormItem>
    
                <div class="flex gap-2 pr-4">
                    <FormItem label="Fecha de entrada" name="inauguracion">
                        <Input type="datetime-local" v-model:value="asistencia.hora_entrada" style="width: 100%;" />
                    </FormItem>
                    <FormItem label="Fecha de salida" name="inauguracion">
                        <Input type="datetime-local" v-model:value="asistencia.hora_salida" style="width: 100%;" />
                    </FormItem>
                </div>
            </div>

            <FormItem class="flex justify-end mb-0">
                <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
                <Button v-if="asistencia.usuario_id != null" type="primary" htmlType="submit" :loading="cargando">Guardar</Button>
            </FormItem>

        </Form>
    </Modal>
</template>


