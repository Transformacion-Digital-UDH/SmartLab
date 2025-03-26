<template>
    <Modal
        title="Agregar miembro"
        :open="visible"
        @cancel="cerrarModal"
        centered
        :footer="null"
    >
        <Form layout="vertical" @finish="enviarFormulario" :model="miembro">
            <!-- Select para buscar y seleccionar usuarios -->
            <FormItem label="Seleccione un usuario" name="usuarioSeleccionado">
                <Select
                    v-model:value="usuarioSeleccionado"
                    placeholder="Buscar usuario"
                    show-search
                    :filter-option="filtrarUsuarios"
                    @change="cargarDatosUsuario"
                >
                    <SelectOption
                        v-for="usuario in props.usuarios"
                        :key="usuario.id"
                        :value="usuario.id"
                        :label="`${usuario.dni} - ${usuario.nombres} ${usuario.apellidos} - ${usuario.email}`"
                    >
                        {{
                            `${usuario.dni} - ${usuario.nombres} ${usuario.apellidos} - ${usuario.email}`
                        }}
                    </SelectOption>
                </Select>
            </FormItem>

            <!-- Mostrar advertencia si el usuario ya es miembro -->
            <Alert
                v-if="usuarioYaEsMiembro"
                class="mb-2 text-amber-600"
                type="warning"
                show-icon
                message="Este usuario ya es miembro del laboratorio."
            />

            <!-- Campos del usuario (inicialmente ocultos) -->
            <div v-if="usuarioSeleccionado">
                <div class="block md:flex gap-x-3 pt-3">
                    <FormItem label="Nombres" name="nombres" class="w-full">
                        <Input
                            v-model:value="miembro.nombres"
                            placeholder="Ingrese los nombres"
                            class="rounded-lg"
                            disabled
                        />
                    </FormItem>

                    <FormItem label="Apellidos" name="apellidos" class="w-full">
                        <Input
                            v-model:value="miembro.apellidos"
                            placeholder="Ingrese los apellidos"
                            class="rounded-lg"
                            disabled
                        />
                    </FormItem>
                </div>

                <div class="block md:flex gap-x-3">
                    <FormItem label="DNI" name="dni" class="w-full">
                        <Input
                            v-model:value="miembro.dni"
                            placeholder="Ingrese el DNI"
                            class="rounded-lg"
                            disabled
                        />
                    </FormItem>

                    <FormItem label="Celular" name="celular" class="w-full">
                        <Input
                            v-model:value="miembro.celular"
                            placeholder="Ingrese número de celular"
                            class="rounded-lg"
                            disabled
                        />
                    </FormItem>
                </div>

                <FormItem label="Email" name="email">
                    <Input v-model:value="miembro.email" disabled class="rounded-lg" />
                </FormItem>

                <div class="block md:flex gap-x-3">
                    <FormItem label="Código universitario" name="codigo" class="w-full">
                        <Input v-model:value="miembro.codigo" disabled class="rounded-lg" />
                    </FormItem>

                    <FormItem label="Agregar como" name="cargo" class="w-full">
                        <Input v-model:value="miembro.cargo" disabled class="rounded-lg" />
                    </FormItem>
                </div>

                <FormItem class="flex justify-end mb-0">
                    <Button style="margin-right: 8px" @click="cerrarModal">
                        Cancelar
                    </Button>
                    <!-- Deshabilitamos el botón si ya es miembro -->
                    <Button
                        type="primary"
                        htmlType="submit"
                        :loading="cargando"
                        :disabled="usuarioYaEsMiembro"
                    >
                        Guardar
                    </Button>
                </FormItem>
            </div>
        </Form>
    </Modal>
</template>

<script setup>
import { ref, watch, computed } from "vue";
import {
    Modal,
    Form,
    FormItem,
    Input,
    Select,
    SelectOption,
    Button,
    Alert,
    message,
} from "ant-design-vue";
import axios from "axios";

const props = defineProps({
    visible: Boolean,
    usuarios: Array,
    miembros: Array,
});

const emitir = defineEmits(["update:visible", "actualizar-tabla"]);

// Estado del formulario
const miembro = ref({
    nombres: "",
    apellidos: "",
    dni: "",
    email: "",
    celular: "",
    codigo: "",
    cargo: "Miembro", // Valor fijo
});

const usuarioSeleccionado = ref(props.miembros.map(m => m.id));
const cargando = ref(false);

// Computed que verifica si el usuario seleccionado ya está en miembros
const usuarioYaEsMiembro = computed(() => {
    if (!usuarioSeleccionado.value) return false;
    // Ajusta la comparación según la estructura de 'miembros'
    // Por ejemplo, si en 'miembros' el id del usuario está en 'id':
    return props.miembros.some((m) => m.id === usuarioSeleccionado.value);
});

// Cerrar el modal
const cerrarModal = () => {
    emitir("update:visible", false);
    usuarioSeleccionado.value = null; // Reiniciar selección
};

// Filtrar usuarios en el Select
const filtrarUsuarios = (input, option) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};

// Cargar datos del usuario seleccionado
const cargarDatosUsuario = (usuarioId) => {
    const usuario = props.usuarios.find((u) => u.id === usuarioId);

    if (usuario) {
        miembro.value = {
            nombres: usuario.nombres,
            apellidos: usuario.apellidos,
            dni: usuario.dni,
            email: usuario.email,
            celular: usuario.celular,
            codigo: usuario.codigo,
            cargo: "Miembro",
        };
    }

};

// Enviar formulario
const enviarFormulario = async () => {
    cargando.value = true;
    try {
        const response = await axios.post(route("miembros.store"), {
            ...miembro.value,
            usuario_id: usuarioSeleccionado.value,
        });
        message.success("Miembro agregado exitosamente");
        cerrarModal();
        emitir("actualizar-tabla", response.data.miembro);
    } catch (error) {
        message.error("Error al agregar el miembro");
        console.error("Error al guardar el miembro:", error);
    } finally {
        cargando.value = false;
    }
};

// Reiniciar el formulario cuando el modal se abra
watch(
    () => props.visible,
    (val) => {
        if (val) {
            miembro.value = {
                nombres: "",
                apellidos: "",
                dni: "",
                email: "",
                celular: "",
                codigo: "",
                cargo: "Miembro",
            };
            usuarioSeleccionado.value = null;
        }
    }
);
</script>
