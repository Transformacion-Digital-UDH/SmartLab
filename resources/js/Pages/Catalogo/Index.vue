<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { usePage, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { Button, InputSearch, Select, SelectOption, Modal, DatePicker, Space, RangePicker } from "ant-design-vue";
import { } from '@ant-design/icons-vue';

import CardRecurso from "./Partes/CardRecurso.vue";
import CardArea from "./Partes/CardArea.vue";
import ModalReservar from "./Partes/ModalReservar.vue";

const { props } = usePage();
const recursos = ref(props.recursos || []);
const laboratorios = ref(props.laboratorios || []);
const equipos = ref(props.equipos || []);
const areas = ref(props.areas || []);
const recursoSeleccionado = ref(null);
const valorBuscar = ref('');
const open = ref(false)
const loading = ref(false)
const idLabSelected = ref(-1);
const options = [{value: -1, label: 'Todo'},...laboratorios.value.map((lab)=>({value: lab.id, label: lab.nombre}))];
const tipo = ref('')

// Filtrar equipos
const equiposFiltrados = computed(() => equipos.value.filter((equipo) => {
        const a = equipo?.nombre.toLowerCase().includes(valorBuscar.value.toLowerCase());
        const b = equipo.codigo?.toLowerCase().includes(valorBuscar.value.toLowerCase())
        const c = idLabSelected.value === -1 ? true : equipo?.area?.laboratorio.id === idLabSelected.value
        return (a || b ) && c
    })
);

// Filtrar recursos
const recursosFiltrados = computed(() => recursos.value.filter((recurso) => {
    const a = recurso?.nombre.toLowerCase().includes(valorBuscar.value.toLowerCase())
    const b = recurso.codigo?.toLowerCase().includes(valorBuscar.value.toLowerCase())
    const c = idLabSelected.value === -1 ? true : recurso?.area?.laboratorio.id === idLabSelected.value
        return (a || b ) && c
    })
);

// Filtrar áreas
const areasFiltradas = computed(() => areas.value.filter((area) => {
    const a = area?.nombre.toLowerCase().includes(valorBuscar.value.toLowerCase())
    const b = idLabSelected.value === -1 ? true : area?.laboratorio?.id === idLabSelected.value
    return (a || b)
}));

function handleSelect(recurso,tipex) {
    recursoSeleccionado.value = recurso
    open.value = true
    tipo.value = tipex;
}

function buscar() {
    loading.value = true
    router.visit(route('catalogo.index')+'?'+ new URLSearchParams({ q: valorBuscar.value }).toString(), { preserveScroll: true });
}

function filterOption(input, option) {
    return option.value.toLowerCase().indexOf(input.toLowerCase()) >= 0;
}

function handleChange() {
}
</script>
<template>
    <AppLayout title="Catálogo">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-0">
                Catálogo
            </h2>
        </template>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 px-4">
            <div class="flex flex-col-reverse justify-end gap-y-4 mb-6 sm:flex-row sm:justify-between sm:items-center gap-x-4">
                <InputSearch
                    placeholder="Buscar"
                    class="w-full"
                    size="large"
                    :loading="loading"
                    @search="buscar"
                    v-model:value="valorBuscar"
                />
                <Select
                    v-model:value="idLabSelected"
                    show-search
                    placeholder="Selecciona el laboratorio"
                    size="large"
                    style="width: 200px"
                    :options="options"
                    :filter-option="filterOption"
                    @change="handleChange"
                >
                </Select>
            </div>
            <!-- Catalogos -->
            <section>
                <div class="catalogos grid gap-4">
                    <CardRecurso v-for="equipo in equiposFiltrados" :key="equipo.id" :recurso="equipo" @open-modal="handleSelect(equipo, 'equipo')"/>
                    <CardRecurso v-for="recurso in recursosFiltrados" :key="recurso.id" :recurso="recurso" @open-modal="handleSelect(recurso, 'recurso')"/>
                    <CardArea v-for="area in areasFiltradas" :key="area.id" :area="area" @open-modal="handleSelect(area, 'area')"/>
                </div>
            </section>
            <ModalReservar :recurso="recursoSeleccionado" :tipo="tipo" v-model:open="open" @close="open=false"/>
        </div>
    </AppLayout>
</template>
<style scoped>
    .catalogos{
        grid-template-columns: repeat(auto-fill,minmax(200px,1fr));
    }
</style>
