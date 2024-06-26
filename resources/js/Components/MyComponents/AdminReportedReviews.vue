<template>
  <v-container class="mt-4">
    <v-data-table :headers="headers" :items="reviews" class="elevation-1">
      <template v-slot:top>
        <v-toolbar flat color="blue-darken-2">
          <v-toolbar-title>Críticas Reportadas</v-toolbar-title>
        </v-toolbar>
      </template>
      <template v-slot:item.created_at="{ item }">
        <span>{{ formatDate(item.created_at) }}</span>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon class="mr-2" color="blue-darken-3" @click="openDialog(item)">mdi-eye</v-icon>
        <v-icon class="mx-2" color="green" @click="approveReview(item)">mdi-check</v-icon>
        <v-icon class="mx-2" color="red-darken-1" @click="openDeleteDialog(item)">mdi-delete</v-icon>
      </template>
    </v-data-table>

    <v-snackbar v-model="snackbar" :color="snackbarColor" :timeout="3000">
      {{ snackbarMessage }}
    </v-snackbar>

    <v-dialog v-model="reviewDialog" max-width="500">
      <v-card>
        <v-card-title class="headline">Contenido de la Crítica</v-card-title>
        <v-card-text>
          {{ selectedReviewContent.review }}
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red-darken-1" @click="closeReviewDialog">Cerrar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="deleteDialog" max-width="500">
      <v-card>
        <v-card-title class="headline">Confirmar eliminación</v-card-title>
        <v-card-text>
          ¿Estás seguro de que quieres eliminar esta crítica?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="m-2" color="blue-darken-3" @click="closeDeleteDialog">Cancelar</v-btn>
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
  reviews: Array,
});

const reviews = ref(props.reviews);

const headers = ref([
  { title: 'ID', key: 'id' },
  { title: 'ID de Usuario', key: 'user_id' },
  { title: 'Usuario', key: 'user.name' },
  { title: 'Fecha de creación', key: 'created_at' },
  { title: 'Puntuación', key: 'rating' },
  { title: 'Acciones', key: 'actions', sortable: false },
]);

const snackbar = ref(false);
const snackbarMessage = ref('');
const snackbarColor = ref('');
const deleteDialog = ref(false);
const reviewDialog = ref(false);
const selectedReview = ref(null);
const selectedReviewContent = ref('');

const openDialog = (item) => {
  selectedReviewContent.value = item;
  reviewDialog.value = true;
};

const closeReviewDialog = () => {
  reviewDialog.value = false;
};

const openDeleteDialog = (item) => {
  selectedReview.value = item;
  deleteDialog.value = true;
};

const closeDeleteDialog = () => {
  deleteDialog.value = false;
  selectedReview.value = null;
};

const confirmDelete = () => {
  if (selectedReview.value) {
    router.delete(route('admin.reportedReviews.destroy', { review: selectedReview.value.id }), {
      onSuccess: () => {
        console.log('Funciona');
        reviews.value = reviews.value.filter(review => review.id !== selectedReview.value.id);
        snackbarMessage.value = 'Crítica eliminada correctamente';
        snackbarColor.value = 'green-darken-3';
        snackbar.value = true;
        closeDeleteDialog();
      },
      onError: (errors) => {
        console.error('Error al eliminar la crítica:', errors);
        snackbarMessage.value = 'Error al eliminar la crítica';
        snackbarColor.value = 'error';
        snackbar.value = true;
      },
    });
  }
};

const approveReview = async (review) => {
  try {
    if (!review) {
      console.error('No se ha seleccionado ninguna crítica para aprobar');
      return;
    }

    await axios.patch(route('admin.reportedReviews.approve', { review: review.id }));
    reviews.value = reviews.value.filter(r => r.id !== review.id);

    snackbarMessage.value = 'Crítica aprobada y eliminada de las reportadas';
    snackbarColor.value = 'green-darken-3';
    snackbar.value = true;
  } catch (errors) {
    console.error('Error al aprobar la crítica:', errors);
    snackbarMessage.value = 'Error al aprobar la crítica';
    snackbarColor.value = 'error';
    snackbar.value = true;
  }
};


const formatDate = (date) => {
  return dayjs(date).format('DD/MM/YYYY, HH:mm');
};
</script>