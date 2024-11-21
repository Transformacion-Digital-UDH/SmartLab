<script setup>
    import { ref, watch, h } from 'vue';
    import { router, usePage} from '@inertiajs/vue3';
    import { SmileOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons-vue';
    import { InputSearch, Table, Tag, Pagination, Button, Modal, message, TimePicker } from 'ant-design-vue';
    import NavBar from '@/Components/App/NavBar.vue';
    import { formatDate } from '@/lib/date';


    const { props } = usePage()
    let asistenciasPaginate = JSON.parse(JSON.stringify(props.asistencias))

    // Damos formato de fecha de entrada y salida
    let asistencias = asistenciasPaginate.data.map((asis)=>{
        asis.horaEntrada = asis.hora_entrada
        asis.horaSalida = asis.hora_salida
        asis.hora_entrada = formatDateTime(asis.hora_entrada)
        asis.hora_salida = formatDateTime(asis.hora_salida) ?? '-'
        return asis;
    });

    asistencias = ref(asistencias);
    const token = ref(props.token || []);
    const inputSearchValue = ref('');

    // Manejo de Paginacion
    const total = ref(asistenciasPaginate.total);
    const pageSize = ref(asistencias.value.length);

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


    // Dar la ruta de paginacion
    function setURLPage(page, cantidad) {
        const url = new URL(location.href)
        url.searchParams.set('page', page)
        url.searchParams.set('cantidad', cantidad)
        location.href = url.toString()
    }

    // Formatear la fecha
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
    function delete_asistencia(asistencia) {
        Modal.confirm({
            title: '¿Estás seguro de eliminar la asistencia?',
            content: `${JSON.stringify(asistencia)}`,
            okText: 'Eliminar',
            cancelText: 'Cancelar',
            onOk() {
                router.delete(route('asistencia.eliminar', asistencia.id), {
                    preserveScroll: true,
                    onSuccess: () => {
                        message.success('Recurso eliminado exitosamente');
                        emitir('actualizar-tabla');
                    },
                    onError: (error) => {
                        console.error('Error al eliminar el recurso:', error);
                        message.error('Error al eliminar el recurso');
                    }
                });
            },
        });
    }

    const open = ref(false)
    const selectedTime = ref(null)

    function showModal(asistencia) {
        open.value = true
        selectedTime.value = new Date(asistencia.horaEntrada);
        console.log(selectedTime)
        console.log(asistencia.horaEntrada)
    }


    function handleOk(asistencia) {
       
        router.put(route('asistencia.editar.salida', {
            id: 2, 
            date: formatDate(selectedTime.value)
        }), {
            preserveScroll: true,
            onSuccess: () => {
                open.value = false
                message.success('Se actualizo exitosamente');
                emitir('actualizar-tabla');
            },
            onError: (error) => {
                open.value = false

                console.error('Error al editar la hora de entrada:', error);
                message.error('Error al editar la hora de salida');
            }
        });
           
    }
</script>
<template>
    <NavBar title="Asistencias">
        <Modal v-model:open="open" title="Corregir la hora de salida" @ok="handleOk">
            <div>
                <div class="border"></div>
                <TimePicker v-model="selectedTime" format="HH:mm" placeholder="Selecciona la hora" :disabled-hours="() => [0, 1, 2, 3,4,5,6]" />
                </div>
        </Modal>
        <div class="grid p-4">
            <div class="flex pb-4">
                <h1 class="font-bold text-xl">Asistencias</h1>
            </div>
            <!-- Buscador -->
            <div class="flex gap-3">
                <InputSearch
                    v-model:value="inputSearchValue"
                    placeholder="Buscar por nombre o DNI"
                    class="w-full"
                    size="large"
                />
                <Button type="primary" @click="abrirModalCrear" size="large" class="font-medium" >Agregar Asistencia</Button>

            </div>
            <div class="pt-4"></div>
            <div class="">
                <Table :columns="columns" :data-source="asistencias" :pagination="false" :scroll="{ y: '70vh' }">
                    <!-- Head -->
                    <template #headerCell="{ column }">
                        <template v-if="column.key === 'nombres'">
                            <span  class="flex items-center gap-2">
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
                        Acciones
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
                            <div class="flex gap-2 justify-end">
                                <Button type="" shape="circle" :icon="h(EditOutlined)" @click="showModal(record)" class="block"/>
                                <Button type="link" shape="circle" danger :icon="h(DeleteOutlined)" @click="delete_asistencia(record)"/>
                            </div>
                        </template>
                    </template>
                </Table>
            </div>
        </div>
    </NavBar>
</template>

<style scoped></style>
