<template>
    <Modal
        :open="visible"
        :title="`Áreas del laboratorio ${laboratorio.nombre}`"
        :footer="null"
        width="800px"
        @cancel="cerrarModal"
    >
        <div class="my-3">
            <Button type="primary" @click="abrirModalAgregar" size="middle">
                Agregar área
            </Button>
        </div>
        <div class="table-responsive">
            <Table
                :columns="columnasAreas"
                :dataSource="areas"
                :scroll="{ x: 'max-content' }"
                :pagination="false"
                :loading="isLoading"
                rowKey="id"
            >
                <template #bodyCell="{ column, text, record }">
                    <template
                        v-if="
                            ['nombre', 'descripcion', 'aforo'].includes(
                                column.dataIndex
                            )
                        "
                    >
                        <div>
                            <template v-if="datosEditables[record.key]">
                                <Input
                                    v-model:value="
                                        datosEditables[record.key][
                                            column.dataIndex
                                        ]
                                    "
                                    style="margin: -5px 0"
                                    size="small"
                                />
                                <InputError
                                    :message="
                                        errors[record.key]?.[column.dataIndex]
                                    "
                                />
                            </template>

                            <template v-else>
                                {{ text }}
                            </template>
                        </div>
                    </template>
                    <template v-else-if="column.dataIndex === 'operation'">
                        <div>
                            <span v-if="datosEditables[record.key]">
                                <CheckOutlined
                                    @click="guardar(record.key, record.id)"
                                    class="text-green-600 mr-2"
                                />
                                <CloseOutlined
                                    @click="cancelar(record.key)"
                                    class="text-red-600"
                                />
                            </span>
                            <span v-else>
                                <FormOutlined
                                    @click="editar(record.key)"
                                    class="text-blue-600 mr-2"
                                />
                                <Popconfirm
                                    title="Confirmar acción"
                                    okText="Sí"
                                    cancelText="No"
                                    @confirm="eliminarArea(record.id)"
                                >
                                    <DeleteOutlined class="text-red-600" />
                                </Popconfirm>
                            </span>
                        </div>
                    </template>
                </template>
            </Table>
        </div>
        <AgregarArea
            :visible="modalAgregarVisible"
            :laboratorio_id="laboratorio.id"
            @close="cerrarModalAgregar"
            @areaGuardada="cargarAreas"
        />
    </Modal>
</template>

<script setup>
import { ref, watch, onMounted } from "vue";
import InputError from "@/Components/Inputs/InputError.vue";
import {
    Modal,
    Table,
    Input,
    Button,
    message,
    Popconfirm,
} from "ant-design-vue";
import {
    FormOutlined,
    DeleteOutlined,
    CheckOutlined,
    CloseOutlined,
} from "@ant-design/icons-vue";
import axios from "axios";
import AgregarArea from "./AgregarArea.vue";

const props = defineProps({
    visible: Boolean,
    laboratorio: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["update:visible"]);

// Columnas de la tabla
const columnasAreas = [
    { title: "Nombre", dataIndex: "nombre", key: "nombre" },
    { title: "Descripción", dataIndex: "descripcion", key: "descripcion" },
    { title: "Aforo", dataIndex: "aforo", key: "aforo", width: 80 },
    {
        title: "Acciones",
        dataIndex: "operation",
        key: "operation",
        fixed: "right",
        width: 100,
    },
];

const areas = ref([]);
const modalAgregarVisible = ref(false);
const datosEditables = ref({});
const isLoading = ref(false);
const errors = ref({});

const cerrarModal = () => {
    errors.value = {};
    emit("update:visible", false);
};

const abrirModalAgregar = () => {
    modalAgregarVisible.value = true;
    cerrarModal();
};

const cerrarModalAgregar = () => {
    modalAgregarVisible.value = false;
    emit("update:visible", true);
};

const cargarAreas = async () => {
    isLoading.value = true;
    try {
        const response = await axios.get(
            route("areas.json", { laboratorio_id: props.laboratorio.id })
        );
        areas.value = response.data.map((area, index) => ({
            key: index.toString(),
            ...area,
        }));
    } catch (error) {
        console.error("Error al cargar las áreas:", error);
    } finally {
        isLoading.value = false;
    }
};

const editar = (key) => {
    datosEditables.value[key] = {
        ...areas.value.find((area) => area.key === key),
    };
};

const guardar = async (key, areaId) => {
    errors.value = {};
    const index = areas.value.findIndex((area) => area.key === key);
    if (index !== -1 && datosEditables.value[key]) {
        try {
            await axios.put(route("areas.update", { area_id: areaId }), {
                nombre: datosEditables.value[key].nombre,
                descripcion: datosEditables.value[key].descripcion,
                aforo: datosEditables.value[key].aforo,
            });
            areas.value[index] = { ...datosEditables.value[key] };
            delete datosEditables.value[key];
            message.success("Área actualizada exitosamente");
        } catch (error) {
            if (error.response && error.response.data.errors) {
                const validationErrors = error.response.data.errors;
                Object.keys(validationErrors).forEach((field) => {
                    errors.value[key] = errors.value[key] || {};
                    errors.value[key][field] = validationErrors[field][0];
                });
            }
            message.error("Hay errores al actualizar el área.");
        }
    }
};

const cancelar = (key) => {
    delete datosEditables.value[key];
};

const eliminarArea = async (areaId) => {
    try {
        await axios.delete(route("areas.destroy", { area_id: areaId }));
        message.success("Área eliminada exitosamente");
        cargarAreas();
    } catch (error) {
        console.error("Error al eliminar el área:", error);
    }
};

onMounted(() => {
    cargarAreas();
});

watch(
    () => props.laboratorio.id,
    (nuevoLaboratorioId, previoLaboratorioId) => {
        if (nuevoLaboratorioId !== previoLaboratorioId) {
            cargarAreas();
        }
    }
);
</script>
