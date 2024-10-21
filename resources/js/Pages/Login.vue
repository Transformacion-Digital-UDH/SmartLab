<script setup>
import InputEmail from '@/Components/Inputs/InputEmail.vue';
import InputPassword from '@/Components/Inputs/InputPassword.vue';

  
</script>
<template>
  <div class="center grid place-items-center min-h-screen">
    <fieldset>
      <legend class="text-4xl text-center font-bold pb-4">Iniciar Sesión</legend>
      <div class="flex items-center gap-3 pb-5">
        <img src="" alt="" class="w-10 h-10 rounded-full bg-neutral-200">
        <div class="flex flex-col">
          <p class="text-blue-500 font-bold text-xs">Laboratorio</p>
          <p class="font-bold">Transformacion digital</p>
        </div>
      </div>
      <form action="" class="flex flex-col gap-3 w-[251px]">
        <InputEmail/>
        <InputPassword/>
        <button class="bg-sky-700 font-bold text-white py-2 mt-4 rounded-xl">Ingresar</button>

        <p class="pt-4">¿No tienes una cuenta? <a href="" class="text-blue-500 hover:underline underline-offset-2">Crear aquí</a></p>
      </form>
    </fieldset>
  </div>
</template>
<style scoped>
  .center {
  }
</style>