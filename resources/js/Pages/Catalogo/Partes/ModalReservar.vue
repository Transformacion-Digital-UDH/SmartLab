<script setup>
import "dayjs/locale/es";
import axios from "axios";
import dayjs from "dayjs";
import TimeLine from "./TimeLine.vue";
import ReservaEstado from "./ReservaEstado.vue";
import { ref, watch } from "vue";
import {
    Button,
    DatePicker,
    message,
    Modal,
    Select,
    Carousel,
    SelectOption,
} from "ant-design-vue";
import {
    InfoCircleOutlined,
    ClockCircleOutlined,
    CalendarOutlined,
    LeftCircleOutlined,
    RightCircleOutlined,
} from "@ant-design/icons-vue";
import { usePage } from "@inertiajs/vue3";
import { DateTime } from "@/lib/utils/datetime";

dayjs.locale("es");

const page = usePage();

const props = defineProps({
    recurso: Array,
    open: Boolean,
    tipo: String,
});

const emitir = defineEmits(["send", "close"]);
const recurso = ref(props.recurso ?? {});
const tipo = ref(props.tipo);
const cargando = ref(false);
const tiempo = ref("");
const disabled = ref(true);
const reservas = ref([]);

const descripcionTruncada = ref(true);

const mostrarMas = () => {
    descripcionTruncada.value = false;
};

const mostrarMenos = () => {
    descripcionTruncada.value = true;
};

const fecha = ref(
    new Date().getHours() > 17
        ? new Date(Date.now() + 1000 * 60 * 60 * 24)
        : new Date()
);

const hora_inicio = ref({
    value: "08:00",
    count: 1,
});
const hora_fin = ref({
    value: "08:45",
    count: 2,
});

// Objeto para almacenar los datos de la reserva a enviar
const data = {
    hora_inicio: null,
    hora_fin: null,
};

// Al abrir el modal, se carga el recurso y se obtienen sus reservas.
watch(
    () => props.open,
    (val) => {
        if (val) {
            recurso.value = { ...props.recurso };
            tipo.value = props.tipo;
            cargando.value = true;
            reservas.value = [];
            axios
                .get(`/api/reservas/${tipo.value}/${recurso.value.id}`)
                .then(({ data }) => {
                    // Serializamos las reservas
                    data.reservas = data.reservas.map((reserva) => {
                        return {
                            ...reserva,
                            hora_inicio: new Date(reserva.hora_inicio),
                            hora_fin: new Date(reserva.hora_fin),
                        };
                    });
                    reservas.value = data.reservas;
                    cargando.value = false;

                    // Asigna imagen por defecto si no existen fotos (aplica para área, recurso o equipo)
                    if (
                        !recurso.value.fotos ||
                        recurso.value.fotos.length < 1
                    ) {
                        recurso.value.fotos = [
                            { id: 1, ruta: "default-placeholder.png" },
                        ];
                    }
                });
        }
    }
);

function onRangeChange([start, end]) {
    const h = Math.floor(dayjs(end).diff(dayjs(start), "hour", true));
    const m = Math.floor(dayjs(end).diff(dayjs(start), "minute", true)) % 60;
    tiempo.value = `: ${h}h ${m}m`;
    disabled.value = h < 3 ? false : true;
}

const handleOk = async () => {
    // Dependiendo del tipo se asigna el ID correspondiente y se limpian los otros campos.
    if (tipo.value == "equipo") {
        data.equipo_id = recurso.value.id;
        data.recurso_id = null;
        data.area_id = null;
    } else if (tipo.value == "recurso") {
        data.equipo_id = null;
        data.recurso_id = recurso.value.id;
        data.area_id = null;
    } else if (tipo.value == "area") {
        data.equipo_id = null;
        data.recurso_id = null;
        data.area_id = recurso.value.id;
    }

    cargando.value = true;
    data.hora_inicio = `${dayjs(fecha.value).format("YYYY-MM-DD")} ${
        hora_inicio.value.value
    }:00`;
    data.hora_fin = `${dayjs(fecha.value).format("YYYY-MM-DD")} ${
        hora_fin.value.value
    }:00`;

    try {
        const response = await axios.post(route("reservas.store"), { ...data });
        if (response.status === 201) {
            message.success("Solicitud de reserva enviada correctamente");
            emitir("close");
        } else {
            throw new Error("Error al reservar");
        }
    } catch (error) {
        message.error("Error al reservar");
    } finally {
        cargando.value = false;
    }
};

let now = ref(new Date());

const horas = [
    { label: "08:00 AM", value: "08:00", value2: 1 },
    { label: "08:45 AM", value: "08:45", value2: 2 },
    { label: "09:30 AM", value: "09:30", value2: 3 },
    { label: "10:15 AM", value: "10:15", value2: 4 },
    { label: "11:00 AM", value: "11:00", value2: 5 },
    { label: "11:45 AM", value: "11:45", value2: 6 },
    { label: "12:30 PM", value: "12:30", value2: 7 },
    { label: "01:15 PM", value: "13:15", value2: 8 },
    { label: "02:00 PM", value: "14:00", value2: 9 },
    { label: "02:45 PM", value: "14:45", value2: 10 },
    { label: "03:30 PM", value: "15:30", value2: 11 },
    { label: "04:15 PM", value: "16:15", value2: 12 },
    { label: "05:00 PM", value: "17:00", value2: 13 },
    { label: "05:45 PM", value: "17:45", value2: 14 },
];

// Arreglo para los labels del timeline: se incluye al final el label "06:00 PM"
const timelineLabels = [
    ...horas,
    { label: "06:00 PM", value: "18:00", value2: 15 },
];

const deshabilitarFechas = (current) => {
    const now = dayjs();
    const isPastDate = current && current.isBefore(now, "day");
    // Verificar si es el día actual y si ya son más de las 6 PM
    const isTodayAndLate =
        current && current.isSame(now, "day") && now.hour() >= 18;
    return isPastDate || isTodayAndLate;
};

function onFechaCambio({ $d }) {
    fecha.value = $d;
    validation();
}
function onHoraInicioCambio(v) {
    hora_inicio.value = {
        value: v,
        count: horas.findIndex(({ value }) => value == v) + 1,
    };
    // Se ajusta para que se ofrezcan 4 opciones (45min, 90min, 135min, 180min) a partir de la hora seleccionada
    const horasFilt = horas
        .filter(({ value2 }) => value2 > hora_inicio.value.count)
        .slice(0, 4);
    hora_fin.value = {
        value: horasFilt[0].value,
        count: horas.findIndex(({ value }) => value == horasFilt[0].value) + 1,
    };
    validation();
}
function onHoraFinCambio(v) {
    hora_fin.value = {
        value: v,
        count: horas.findIndex(({ value }) => value == v) + 1,
    };
    validation();
}

function validation() {
    const ini = new Date(
        ...DateTime.toDateList(fecha.value),
        ...hora_inicio.value.value.split(":").map((n) => Number(n))
    ).getTime();
    const fn = new Date(
        ...DateTime.toDateList(fecha.value),
        ...hora_fin.value.value.split(":").map((n) => Number(n))
    ).getTime();
    disabled.value = reservas.value.every((reserva) => {
        if (
            ini <= reserva.hora_inicio.getTime() &&
            fn <= reserva.hora_inicio.getTime()
        ) {
            data.hora_inicio = `${fecha.value.toDateString()} ${
                hora_inicio.value.value
            }`;
            data.hora_fin = `${fecha.value.toDateString()} ${
                hora_fin.value.value
            }`;
            return false;
        }
        if (
            ini >= reserva.hora_fin.getTime() &&
            fn >= reserva.hora_fin.getTime()
        ) {
            return false;
        }
        return true;
    });
}
</script>

<template>
    <Modal
        v-model:open="props.open"
        title="Reservar"
        width="min(620px,100%)"
        @cancel="emitir('close')"
        @ok="handleOk"
    >
        <div class="flex w-auto flex-wrap sm:flex-nowrap sm:justify-end gap-3">
            <!-- Portada -->
            <div>
                <Carousel
                    :arrows="true"
                    class="w-44 rounded-lg overflow-hidden self-start"
                >
                    <template v-slot:prevArrow>
                        <div
                            class="custom-slick-arrow"
                            style="left: 10px; z-index: 1"
                        >
                            <LeftCircleOutlined />
                        </div>
                    </template>
                    <template v-slot:nextArrow>
                        <div class="custom-slick-arrow" style="right: 10px">
                            <RightCircleOutlined />
                        </div>
                    </template>
                    <img
                        v-if="recurso.fotos && recurso.fotos.length > 0"
                        v-for="foto of recurso.fotos"
                        :src="`/storage/${foto.ruta}`"
                        class="w-44 h-44 object-cover rounded"
                    />
                </Carousel>
                <!-- Información adicional -->
                <div class="pb-1 text-gray-500 pt-2">
                    <!-- Cuando es de tipo area: solo se muestra el nombre del laboratorio -->
                    <template v-if="tipo === 'area'">
                        <div class="flex items-center gap-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    fill="none"
                                    stroke="#999"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 3h6m-5 6h4m-4-6v6L6 20a.7.7 0 0 0 .5 1h11a.7.7 0 0 0 .5-1L14 9V3"
                                />
                            </svg>
                            <span>{{
                                recurso.laboratorio?.nombre ?? "Sin definir"
                            }}</span>
                        </div>
                    </template>
                    <!-- Cuando es otro tipo: se muestran el nombre del área y luego el del laboratorio -->
                    <template v-else>
                        <div class="flex items-center gap-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    fill="none"
                                    stroke="#999"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m12 6l-8 4l8 4l8-4zm-8 8l8 4l8-4"
                                />
                            </svg>
                            <span>{{
                                recurso.area?.nombre ?? "Sin definir"
                            }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    fill="none"
                                    stroke="#999"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 3h6m-5 6h4m-4-6v6L6 20a.7.7 0 0 0 .5 1h11a.7.7 0 0 0 .5-1L14 9V3"
                                />
                            </svg>
                            <span>{{
                                recurso.area?.laboratorio?.nombre ??
                                "Sin definir"
                            }}</span>
                        </div>
                    </template>
                </div>
            </div>
            <div class="min-w-96">
                <h3 class="text-xl font-bold text-neutral-500">
                    {{ recurso.nombre }}
                    <span v-if="tipo !== 'area'" class="text-neutral-400"
                        >#{{ recurso.codigo }}</span
                    >
                </h3>

                <div>
                    <span
                        class="mt-1 text-gray-500"
                        :class="{ 'line-clamp-1': descripcionTruncada }"
                    >
                        {{ recurso.descripcion + " " }}
                    </span>
                    <button
                        v-if="descripcionTruncada && recurso.descripcion.length > 100"
                        @click="mostrarMas"
                        class="text-blue-500 hover:underline"
                    >
                        Ver más
                    </button>
                    <button
                        v-else-if="!descripcionTruncada"
                        @click="mostrarMenos"
                        class="text-blue-500 hover:underline"
                    >
                        Ver menos
                    </button>
                </div>

                <div class="pt-2">
                    <h2 class="text-base font-medium text-gray-500">
                        Selecciona el día y la hora {{ tiempo }}
                    </h2>
                    <p class="text-xs text-gray-400">
                        Recomendamos reservar con 30min de anticipación ya que
                        debe ser aprobado el administrador.
                    </p>
                </div>
                <div class="flex pb-2 gap-2">
                    <DatePicker
                        :value="dayjs(fecha)"
                        :disabled-date="deshabilitarFechas"
                        @update:value="onFechaCambio"
                    />
                    <Select
                        :value="hora_inicio.value"
                        style="width: 120px"
                        @change="onHoraInicioCambio"
                    >
                        <SelectOption
                            v-for="hora in horas.slice(0, -1)"
                            :key="hora.value"
                            :value="hora.value"
                        >
                            {{ hora.label }}
                        </SelectOption>
                    </Select>
                    <Select
                        :value="hora_fin.value"
                        style="width: 120px"
                        @change="onHoraFinCambio"
                    >
                        <SelectOption
                            v-for="hora in horas
                                .filter(
                                    ({ value2 }) => value2 > hora_inicio.count
                                )
                                .slice(0, 4)"
                            :value="hora.value"
                            :key="Math.random()"
                        >
                            {{ hora.label }}
                        </SelectOption>
                    </Select>
                </div>
                <p class="flex items-center gap-2 text-gray-500 pt-1">
                    <InfoCircleOutlined />
                    Tiempo máximo 3h.
                    <a
                        :href="
                            'https://wa.me/' +
                            page.props.adminNumber +
                            '?text=' +
                            encodeURIComponent(
                                'Hola, quisiera ampliar mi reserva, ¿es posible?'
                            )
                        "
                        target="_blank"
                        class="text-blue-500 hover:text-blue-600 hover:underline"
                        >¿Deseas más tiempo?</a
                    >
                </p>
                <!-- Sección de horarios reservados -->
                <div class="border rounded-lg m">
                    <details class="px-3 py-2 rounded-lg">
                        <summary>
                            <div
                                class="inline-flex gap-4 items-center font-bold justify-between text-green-500"
                            >
                                <span>Horarios reservados</span>
                                <svg
                                    v-if="cargando"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        fill="currentColor"
                                        d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z"
                                        opacity="0.25"
                                    />
                                    <path
                                        fill="currentColor"
                                        d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z"
                                    >
                                        <animateTransform
                                            attributeName="transform"
                                            dur="0.75s"
                                            repeatCount="indefinite"
                                            type="rotate"
                                            values="0 12 12;360 12 12"
                                        />
                                    </path>
                                </svg>
                            </div>
                        </summary>
                        <div>
                            <table>
                                <tbody>
                                    <tr
                                        v-for="reserva of reservas"
                                        :key="reserva.id"
                                        class="text-sm"
                                    >
                                        <td class="py-1 pr-2">
                                            <div
                                                class="flex items-center gap-2 text-nowrap"
                                            >
                                                <ReservaEstado
                                                    :estado="reserva.estado"
                                                />
                                                <CalendarOutlined
                                                    style="font-size: 14px"
                                                />
                                                {{
                                                    new Intl.DateTimeFormat(
                                                        "es",
                                                        {
                                                            weekday: "short",
                                                            month: "short",
                                                            day: "numeric",
                                                        }
                                                    ).format(
                                                        reserva.hora_inicio
                                                    )
                                                }}
                                            </div>
                                        </td>
                                        <td>
                                            <div
                                                class="flex items-center gap-2 px-2 text-nowrap font-mono"
                                            >
                                                <ClockCircleOutlined
                                                    style="font-size: 14px"
                                                />
                                                {{
                                                    new Intl.DateTimeFormat(
                                                        "en",
                                                        {
                                                            hour: "2-digit",
                                                            minute: "2-digit",
                                                        }
                                                    ).format(
                                                        reserva.hora_inicio
                                                    )
                                                }}
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="20"
                                                    height="20"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="#888"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right"
                                                >
                                                    <path
                                                        stroke="none"
                                                        d="M0 0h24v24H0z"
                                                        fill="none"
                                                    />
                                                    <path d="M9 6l6 6l-6 6" />
                                                </svg>
                                                {{
                                                    new Intl.DateTimeFormat(
                                                        "en",
                                                        {
                                                            hour: "2-digit",
                                                            minute: "2-digit",
                                                        }
                                                    ).format(reserva.hora_fin)
                                                }}
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </details>
                    <div class="m-2 pb-4">
                        <TimeLine
                            :fecha="fecha"
                            :reservas="reservas"
                            :labels="timelineLabels"
                            :alwaysShowLabels="true"
                            :key="Math.random()"
                        />
                    </div>
                </div>
            </div>
        </div>
        <template #footer>
            <Button key="back" @click="emitir('close')">Cancelar</Button>
            <Button
                key="submit"
                type="primary"
                :loading="cargando"
                @click="handleOk"
                v-modal:disabled="disabled"
                >Reservar</Button
            >
        </template>
    </Modal>
</template>

<style scoped>
:deep(.slick-slide) {
    text-align: center;
    width: 170px;
    height: 170px;
    line-height: 60px;
    background: #37b87c;
    overflow: hidden;
}

:deep(.slick-arrow.custom-slick-arrow) {
    width: 25px;
    height: 25px;
    font-size: 25px;
    color: #fff;
    background-color: rgba(31, 45, 61, 0.11);
    transition: ease all 0.3s;
    opacity: 0.3;
    z-index: 1;
}
:deep(.slick-arrow.custom-slick-arrow:before) {
    display: none;
}
:deep(.slick-arrow.custom-slick-arrow:hover) {
    color: #fff;
    opacity: 0.5;
}

:deep(.slick-slide h3) {
    color: #fff;
}
</style>
