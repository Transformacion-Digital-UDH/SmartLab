<template>
  <div class="p-4">

    <h1 class="font-bold text-4xl">Asistencia</h1>
    <!-- Buscador -->
    <div class="flex items-center border p-2 gap-2 rounded-xl bg-neutral-100 mt-2">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" stroke="#333" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0-14 0m18 11l-6-6"/></svg>
      <input class="bg-transparent p-0 focus:ring-0 border-0 flex-1 " type="text" placeholder="Buscar por nombre o DNI">
    </div>
    <table>
      <thead>
        <tr>
          <th>Nombe</th>
          <th>DNII</th>
          <th>Tipo</th>
          <th>Rol</th>
          <th>Registro</th>
        </tr>
        <tr v-for="asistencia of JSON.parse(JSON.stringify(asistencias))" :key="asistencia.id">
          <td>{{ asistencia.id }}</td>
          <td>h</td>
          <td>{{asistencia.hora_entraada}}</td>
       
        </tr>
      </thead>
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
      
  },
  methods: {
      
  },
  props: ['token','asistencias'],
  async mounted() {
    JSON.parse(JSON.stringify(this.asistencias));
    document.title = "Asistencia"
    const token = this.token;
    console.log(token)
    const res = await fetch('/api/asistencia/registrar_entrada',{
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': this.token
      },
      body: JSON.stringify({"usuario_id":1,"proyecto_id":null})
    });
    console.log(await res.text())
    // const token = this.token;
    // console.log(token)
    // const res = await fetch('/api/asistencia/registrar_salida', {
    //   method: 'PUT',
    //   headers: {
    //     'Content-Type': 'application/json',
    //     'X-CSRF-TOKEN': this.token
    //   },
    //   body: JSON.stringify({
    //     usuario_id: 2
    //   })
    // });
    // console.log(await res.text())
  }
};
</script>

<style scoped></style>
