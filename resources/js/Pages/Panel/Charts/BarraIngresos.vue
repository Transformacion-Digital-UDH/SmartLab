<template>
    <div class="max-h-80 flex justify-center">
        <canvas ref="chartCanvas" class="w-full h-full"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Chart, registerables } from "chart.js";

// Registrar los módulos globales necesarios de Chart.js
Chart.register(...registerables);

const chartCanvas = ref(null);

onMounted(() => {
    if (chartCanvas.value) {
        // Crear la instancia del gráfico
        new Chart(chartCanvas.value, {
            type: "bar", // Tipo de gráfico ('bar', 'line', 'pie', etc.)
            data: {
                labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio"], // Etiquetas del eje X
                datasets: [
                    {
                        label: "Cantidad de asistencias",
                        data: [10, 20, 30, 40, 20, 20],
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
                        display: true, // Mostrar leyenda
                    },
                    title: {
                        display: true,
                        text: "Asistencias mensuales", // Título de la gráfica
                        font: {
                            size: 18
                        }
                    }
                },
            },
        });
    }
});
</script>
