<script setup>
  
</script>
<template>
  <label for="" class="flex items-center p-3 gap-3 rounded-xl bg-blue-100 border-0">
    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="#666"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class=""><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" /><path d="M3 7l9 6l9 -6" /></svg>
    <input class="bg-transparent p-0 focus:ring-0 border-0 text-primary" type="email" placeholder="Correo electronico" >
  </label>

</template>
<style scoped>
  label:has(input:focus) {
    outline: 1px solid #0866ff;
    box-shadow: 0 0 6px #0866ff;
    @apply bg-white;
  }
  label:has(input:focus) svg{
    stroke: #0866ff;
  }
  
</style>