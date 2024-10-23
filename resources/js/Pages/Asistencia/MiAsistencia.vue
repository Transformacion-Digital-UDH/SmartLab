<script>
      
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
    // this.asistencias.value = JSON.parse(JSON.stringify(this.asistencias))
    
    
    console.log(JSON.parse(JSON.stringify(this.asistencias))[0])
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
<template>
  <div class="p-4">

    <h1 class="font-bold text-4xl">Mis Asistencias</h1>
    <!-- Buscador -->
    <div class="flex items-center border p-2 gap-2 rounded-xl bg-neutral-100 mt-2">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" stroke="#333" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10a7 7 0 1 0 14 0a7 7 0 1 0-14 0m18 11l-6-6"/></svg>
      <input class="bg-transparent p-0 focus:ring-0 border-0 flex-1 " type="text" placeholder="Buscar por nombre o DNI">
    </div>
    <table class="mt-2 border w-full rounded-md">
      <thead>
        <tr>
          <th class="px-3 py-2">Nombre</th>
          <th class="px-3 py-2">DNI</th>
          <th class="px-3 py-2">Tipo</th>
          <th class="px-3 py-2">Rol</th>
          <th class="px-3 py-2">Registro</th>
        </tr>
        <tr v-for="asistencia in asistencias" :key="asistencia.id" :data-id="asistencia.id" class="border">
          <td class="pl-4">{{ asistencia.nombres }}</td>
          <td>{{ asistencia.dni }}</td>
          <td class="p-1 py-3">
            <span class="bg-neutral-200 py-1 px-2 rounded-md font-bold">
              {{ asistencia.hora_salida ? 'Salida':'Entrada' }}
            </span>
          </td>
          <td class="p-1">
            <span class="bg-neutral-200 py-1 px-2 rounded-md font-bold">
              {{ asistencia.rol }}
            </span>
          </td>
          <td>
            <div class="flex">
              {{asistencia.hora_entrada}}
              {{asistencia.hora_salida}}
            </div>
          </td>
          <!-- Acciones de editar y Eliminar -->
          <td>
            <div class="actions flex justify-end gap-3 px-3">
              <button>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="none" stroke="#333333" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M7 7H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2v-1"/><path d="M20.385 6.585a2.1 2.1 0 0 0-2.97-2.97L9 12v3h3zM16 5l3 3"/></g></svg>
              </button>
              <button>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" stroke="#333333" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16m-10 4v6m4-6v6M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2l1-12M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/></svg>
              </button>
            </div>
          </td>
       
          
       
        </tr>
      </thead>
    </table>
  </div>
</template>




<style scoped></style>
