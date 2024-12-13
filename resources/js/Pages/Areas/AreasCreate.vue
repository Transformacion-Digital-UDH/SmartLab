<template>
    <div class="container mt-5">
        <h1>Agregar Área</h1>
        <form @submit.prevent="crearArea">
            <div class="form-group">
                <label>Nombre del Área</label>
                <input type="text" v-model="nombre" class="form-control" required />
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea v-model="descripcion" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label>Aforo</label>
                <input type="number" v-model="aforo" class="form-control" />
            </div>
            <button type="submit" class="btn btn-primary mt-3">Crear</button>
        </form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            nombre: '',
            descripcion: '',
            aforo: null,
        };
    },
    methods: {
        crearArea() {
            axios.post('/api/areas', {
                nombre: this.nombre,
                descripcion: this.descripcion,
                aforo: this.aforo,
                laboratorio_id: 1  // Valor temporal por defecto para laboratorio_id
            })
            .then(response => {
                console.log('Área creada:', response.data);
                this.$inertia.visit('/areas');
            })
            .catch(error => {
                console.error('Error al crear el área:', error);
            });
        }
    }
};
</script>
