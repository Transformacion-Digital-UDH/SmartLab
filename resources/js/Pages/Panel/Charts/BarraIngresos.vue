<template>
    <div class="max-h-80 flex justify-center">
        <canvas ref="chartCanvas" class="w-full h-full"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { Chart, registerables } from "chart.js";

// Registrar los módulos globales necesarios de Chart.js
Chart.register(...registerables);

const props = defineProps({
    datos: {
        type: Array,
        default: () => [0, 0, 0, 0, 0, 0]
    },
    etiquetas: {
        type: Array,
        default: () => ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio"]
    }
});

const chartCanvas = ref(null);
let chart = null; // Variable para almacenar la instancia del gráfico

onMounted(() => {
    if (chartCanvas.value) {
        crearGrafico();
    }
});

// Observar cambios en los datos para actualizar el gráfico
watch([() => props.datos, () => props.etiquetas], () => {
    if (chart) {
        chart.destroy();
    }
    crearGrafico();
}, { deep: true });

// Función para crear el gráfico
const crearGrafico = () => {
    if (!chartCanvas.value) return;
    
    chart = new Chart(chartCanvas.value, {
        type: "bar",
        data: {
            labels: props.etiquetas,
            datasets: [
                {
                    label: "Cantidad de asistencias",
                    data: props.datos,
                    backgroundColor: "rgba(75, 192, 192, 0.2)",
                    borderColor: "rgba(75, 192, 192, 1)",
                    borderWidth: 1,
                },
            ],
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                },
                title: {
                    display: true,
                    text: "Asistencias mensuales",
                    font: {
                        size: 18
                    }
                }
            },
        },
    });
};
</script>
