<template>
    <h1>aaaaaaaaaaa</h1>

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
      
  },
  methods: {
      
  },
  props: ['token'],
  async mounted() {
    const token = this.token;
    console.log(token)
    const res = await fetch('/api/asistencia/registrar_entrada',{
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': this.token
      },
      body: '{"usuario_id":2,"proyecto_id":null}'
    });
    console.log(await res.text())
  }
};
</script>

<style scoped></style>
