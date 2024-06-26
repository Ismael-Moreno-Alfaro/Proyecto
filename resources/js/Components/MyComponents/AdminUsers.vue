<template>
  <v-container class="mt-4">
    <v-data-table :headers="headers" :items="users" class="elevation-1">
      <template v-slot:top>
        <v-toolbar flat color="blue-darken-2">
          <v-toolbar-title>Usuarios</v-toolbar-title>
        </v-toolbar>
      </template>
      <template v-slot:item.created_at="{ item }">
        <span>{{ formatDate(item.created_at) }}</span>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon class="mr-2" color="red-darken-1" @click="openDialog(item)">mdi-delete</v-icon>
      </template>
    </v-data-table>
    <v-snackbar v-model="snackbar" :color="snackbarColor" :timeout="3000">
      {{ snackbarMessage }}
    </v-snackbar>

    <v-dialog v-model="dialog" max-width="500">
      <v-card>
        <v-card-title class="headline">Confirmar eliminación</v-card-title>
        <v-card-text>
          ¿Estás seguro de que quieres eliminar este usuario?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="m-2" color="blue-darken-3" @click="closeDialog">Cancelar</v-btn>
          <v-btn class="m-2" color="red-accent-4" @click="confirmDelete">Eliminar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, defineProps } from 'vue';
import { router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import 'dayjs/locale/es';

dayjs.locale('es');

const props = defineProps({
  users: Array,
});

const users = ref(props.users);

// Definir headers para la tabla
const headers = ref([
  { title: 'ID', key: 'id' },
  { title: 'Nombre', key: 'name' },
  { title: 'Email', key: 'email' },
  { title: 'Fecha de creación', key: 'created_at' },
  { title: 'Rol', key: 'role' },
  { title: 'Acciones', key: 'actions', sortable: false },
]);

const snackbar = ref(false);
const snackbarMessage = ref('');
const snackbarColor = ref('');
const dialog = ref(false);
const userToDelete = ref(null);

const openDialog = (item) => {
  userToDelete.value = item;
  dialog.value = true;
};

const closeDialog = () => {
  dialog.value = false;
  userToDelete.value = null;
};

const confirmDelete = () => {
  if (userToDelete.value) {
    router.delete(route('admin.users.destroy', { user: userToDelete.value.id }), {
      onSuccess: () => {
        users.value = users.value.filter(user => user.id !== userToDelete.value.id);
        snackbarMessage.value = 'Usuario eliminado correctamente';
        snackbarColor.value = 'green-darken-3';
        snackbar.value = true;
        closeDialog();
      },
      onError: (errors) => {
        console.error('Error al eliminar el usuario:', errors);
        snackbarMessage.value = 'Error al eliminar el usuario';
        snackbarColor.value = 'error';
        snackbar.value = true;
      },
    });
  }
};

const formatDate = (date) => {
  return dayjs(date).format('DD/MM/YYYY, HH:mm');
};
</script>
