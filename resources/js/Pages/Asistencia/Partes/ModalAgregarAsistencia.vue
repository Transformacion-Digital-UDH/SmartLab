<script setup>
    import { ref, watch } from 'vue';
    import { Modal, Form, FormItem, Input, Select, InputNumber, Button, message, SelectOption } from 'ant-design-vue';
    import { CheckCircleFilled, CheckCircleOutlined, AppleOutlined, AndroidOutlined, ExclamationCircleOutlined, UserOutlined, InfoCircleOutlined } from '@ant-design/icons-vue';


    import axios from 'axios';

    const props = defineProps({
        visible: Boolean,
        responsables: Array,
    });

    const emitir = defineEmits(['update:visible', 'actualizar-tabla']);

    const asistenciaDefault = {
        usuario_id: null,
        proyecto_id: null,
        laboratorio_id: null,
        hora_entrada: null,
        hora_salida: null
    }

    const asistencia = ref({...asistenciaDefault});

    const cargando = ref(false);
    const opcionesProyectos = ref([]);

    const dni = ref('')
    const proyectos = ref([])
    const proyectoSelectId = ref(null)
    const usuarioId = ref(null)
    const usuarioEncontrado = ref(false);
    const usuarioInfo = ref({nombres:'',appellidos:''})

    // Cierra el modal y emite el evento para cerrar en el componente padre
    const cerrarModal = () => {
        emitir('update:visible', false);
    };

    // Envía el formulario de creación del laboratorio
    const enviarFormulario = async () => {
        console.clear()
        cargando.value = true;
        try {
            const data = {
                ...asistencia.value,
                hora_entrada: asistencia.value.hora_entrada.replace('T',' ').concat(':00'),
                hora_salida: asistencia.value.hora_salida.replace('T',' ').concat(':00')
            }
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

    const obtenerInfo = async ()=>{
        cargando.value = true;
        usuarioEncontrado.value = false
        const response = await fetch(`/api/asistencia/user/${dni.value}`);
        cargando.value = false
        if (response.ok) {

            const data = await response.json();


            asistencia.value.usuario_id = data.id
            proyectos.value = data.proyectos
            usuarioInfo.value = {
                nombres: data.nombres,
                apellidos: data.apellidos
            }
        }else {
            usuarioEncontrado.value = true
        }
    }
    // Verificar si el modal se abre por primera vez y cargar responsables
    watch(() => props.visible, (val) => {
        if (val) {
            dni.value = ''
            asistencia.value = asistenciaDefault;
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
        <div>
            <div v-if="asistencia.usuario_id == null">

                <Input v-model:value="dni" placeholder="Ingrese el DNI" autofocus class=" placeholder:text-neutral-400 mb-2" />
                <!-- Error -->
                 <div v-if="usuarioEncontrado" class="flex items-center gap-2 px-2 text-red-500 pb-4">
                    <InfoCircleOutlined class="text-red-500"/>
                    Usuario no encontrado
                 </div>
                <Button type="primary" htmlType="submit" :loading="cargando" @click="obtenerInfo">Validar</Button>
            </div>

            <div v-if="asistencia.usuario_id != null" class="pt-4">
                <div class="flex">
                    <div class="flex items-center gap-2 mb-4 bg-neutral-100 p-2 rounded-xl w-full">
                        <UserOutlined class="text-xl"/> {{ usuarioInfo.nombres }} {{  usuarioInfo.apellidos }}
                    </div>
                </div>

                <h1 class="font-bold mb-2">Selecione su proyecto</h1>
                <Select
                    ref="select"
                    v-model:value="asistencia.proyecto_id"
                    style="width: 120px"
                    @focus="focus"
                    @change="handleChange"
                >
                    <SelectOption v-for="proyecto in proyectos" :key="proyecto.id" :value="proyecto.id">{{proyecto.nombre}}</SelectOption>
                </Select>

                <h1 class="font-bold mb-2 mt-4">Selecione la fecha de entrada</h1>
                <Input type="datetime-local" v-model:value="asistencia.hora_entrada"   class="w-full rounded-xl"/>


                <h1 class="font-bold mb-2 mt-3">Selecione la fecha de salida</h1>
                <Input type="datetime-local" v-model:value="asistencia.hora_salida" class="rounded-xl" />

            </div>

            <FormItem class="flex justify-end mb-0 pt-3">
                <Button style="margin-right: 8px" @click="cerrarModal">Cancelar</Button>
                <Button v-if="asistencia.usuario_id != null" type="primary" :loading="cargando" @click="enviarFormulario" :disabled="asistencia.usuario_id == null|| asistencia.hora_entrada == null || asistencia.hora_salida == null">Guardar</Button>
            </FormItem>

        </div>
    </Modal>
</template>


