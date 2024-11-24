<script setup>
    import { ref, watch, h } from 'vue';
    import { router, usePage} from '@inertiajs/vue3';
    import { SmileOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons-vue';
    import { InputSearch, Table, Tag, Pagination, Button, Modal, message, TimePicker } from 'ant-design-vue';
    import NavBar from '@/Layouts/AppLayout.vue';
    import Asistencia from '@/lib/Asistencia';
    import TablaAsistencia from './Partes/TablaAsistencia.vue';
    import ModalEditarSalida from './Partes/ModalEditarSalida.vue';
    import ModalAgregarAsistencia from './Partes/ModalAgregarAsistencia.vue';

    const { props } = usePage();
    let asistenciasPaginate = JSON.parse(JSON.stringify(props.asistencias))

    // Damos formato de fecha de entrada y salida
    let asistencias = ref(asistenciasPaginate.data.map((asis)=>{
        return new Asistencia(asis);
    }));

    console.log(props)
    const token = ref(props.token || []);
    const loading = ref(false)

    // Manejo de Paginacion
    const total = ref(asistenciasPaginate.total);
    const pageSize = ref(asistencias.length);

    const currentPage = ref(asistenciasPaginate.current_page);

    const onShowSizeChange = (current, pageSize) => {
        setURLPage(current.value, pageSize)
    };

    watch(pageSize, () => {
        setURLPage(currentPage.value, pageSize.value)
    });

    watch(currentPage, () => {
        setURLPage(currentPage.value, pageSize.value)
    });

    // Dar la ruta de paginacion
    function setURLPage(page, cantidad) {
        const url = new URL(location.href)
        url.searchParams.set('page', page)
        url.searchParams.set('cantidad', cantidad)
        location.href = url.toString()
    }

    // Formatear la fecha
    const open = ref(false)
    const selectedTime = ref(null)

    const mostrarModalEditar = ref(false);
    const mostrarModalAgregarAsistencia = ref(false);
    const asistenciaSeleccionado = ref(null);

    function showModal(asistencia) {
        open.value = true
        selectedTime.value = new Date(asistencia.horaEntrada);
        console.log(selectedTime)
        console.log(asistencia.horaEntrada)
    }

    const actualizarTabla = () => {
        router.visit(route('asistencias.index'), { preserveScroll: true });
    };

    const abrirModalEditar = (asistencia) => {
        console.clear()
        console.log(asistencia)
        mostrarModalEditar.value = true;
        asistenciaSeleccionado.value = { ...asistencia };
    };

    let buscar = ref(new URLSearchParams(window.location.search).get('q') ?? '')

    async function buscarAsistencias() {
        
        router.visit(route('asistencias.index')+'?'+ new URLSearchParams({ q: buscar.value }).toString(), { preserveScroll: true });
    }

    function abrirModalCrear() {
        mostrarModalAgregarAsistencia.value = true
    }
</script>
<template>
    <NavBar title="Asistencias">
        <div class="grid p-4">
            <div class="flex pb-4">
                <h1 class="font-bold text-xl">Asistencias</h1>
            </div>
            <!-- Buscador -->
            <div class="flex gap-3">
                <InputSearch
                    placeholder="Buscar por nombre o DNI"
                    class="w-full"
                    size="large"
                    :loading="loading"
                    @search="buscarAsistencias"
                    v-model:value="buscar"

                />
                <Button type="primary" @click="abrirModalCrear" size="large" class="font-medium" >Agregar Asistencia</Button>
            </div>
            
            <div class="pt-4"></div>
            <div class="">
                <TablaAsistencia 
                    :asistencias="asistencias"
                    @actualizar-tabla="actualizarTabla"
                    @editar="abrirModalEditar"
                />
                <div class="grid place-items-center pt-4">

                    <Pagination v-model:current="asistencias.current_page" :total="asistencias.total" show-less-items/>
                </div>

                <ModalEditarSalida
                    v-if="asistenciaSeleccionado"
                    v-model:visible="mostrarModalEditar"
                    :asistencia="asistenciaSeleccionado"
                    @actualizar-tabla="actualizarTabla"
                />
                <ModalAgregarAsistencia
                    v-model:visible="mostrarModalAgregarAsistencia"
                    :asistencia="asistenciaSeleccionado"
                    @actualizar-tabla="actualizarTabla"
                />
            </div>
        </div>
    </NavBar>
</template>

<style scoped></style>
