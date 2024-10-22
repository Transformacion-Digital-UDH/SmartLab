<template>
    <div class="container mt-5">
        <h1>Editar Área</h1>
        <form @submit.prevent="editarArea">
            <div class="form-group">
                <label>Nombre del Área</label>
                <input type="text" v-model="area.nombre" class="form-control" required />
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea v-model="area.descripcion" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label>Aforo</label>
                <input type="number" v-model="area.aforo" class="form-control" />
            </div>
            <button type="submit" class="btn btn-primary mt-3">Guardar cambios</button>
        </form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            area: {
                nombre: '',
                descripcion: '',
                aforo: null
            }
        };
    },
    props: ['id'],  
    mounted() {
        axios.get(`/api/areas/${this.id}`)
            .then(response => {
                this.area = response.data;
            })
            .catch(error => {
                console.error('Error al cargar el área:', error);
            });
    },
    methods: {
        editarArea() {
            axios.put(`/api/areas/${this.area.id}`, this.area)
                .then(response => {
                    console.log('Área actualizada:', response.data);
                    this.$inertia.visit('/areas');  
                })
                .catch(error => {
                    console.error('Error al actualizar el área:', error);
                });
        }
    }
};
</script>
