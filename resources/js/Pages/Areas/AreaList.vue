<template>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Áreas</h1>
            <button class="btn btn-primary" @click="$inertia.visit('/areas/create')">Agregar Área</button>
        </div>

        <div class="mb-3">
            <input type="text" v-model="searchQuery" class="form-control" placeholder="Buscar por nombre de área" />
        </div>

        <table class="table table-striped mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Aforo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="area in filteredAreas" :key="area.id">
                    <td>{{ area.nombre }}</td>
                    <td>{{ area.descripcion || 'Sin descripción' }}</td>
                    <td>{{ area.aforo || 'N/A' }}</td>
                    <td>
                        <button class="btn btn-secondary btn-sm me-2" @click="$inertia.visit(`/areas/edit/${area.id}`)">Editar</button>
                        <button class="btn btn-danger btn-sm" @click="eliminarArea(area.id)">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>


<script>
import axios from 'axios';
export default {
    data() {
        return {
            searchQuery: "",
            areas: [],
        };
    },
    computed: {
        filteredAreas() {
            if (this.searchQuery) {
                return this.areas.filter((area) =>
                    area.nombre.toLowerCase().includes(this.searchQuery.toLowerCase())
                );
            }
            return this.areas;
        },
    },
    methods: {
        editarArea(area) {
            console.log("Editar área:", area);
        },
        eliminarArea(id) {
            if (confirm("¿Estás seguro de que deseas eliminar esta área?")) {
                axios.delete(`/api/areas/${id}`)
                    .then(() => {
                        this.areas = this.areas.filter(area => area.id !== id);
                    })
                    .catch(error => {
                        console.error("Error al eliminar el área:", error);
                    });
            }
        }
    },
    mounted() {
        axios.get("/api/areas")
            .then((response) => {
                console.log(response.data);
                this.areas = response.data.data;
            })
            .catch((error) => {
                console.error("Error al cargar las áreas:", error);
            });
    }
};
</script>

<style scoped></style>
