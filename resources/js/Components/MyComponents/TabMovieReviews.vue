<template>
  <v-container class="mt-4">
    <v-col v-if="!hasReview && watchStatus === 'completed' && !isLoading">
      <v-form @submit.prevent="saveReview" class="mx-6 mb-10 d-flex flex-col">
        <div class=" align-baseline">
          <label>Puntuación:</label>
          <strong class="pl-2">{{ form.rating }}</strong>
        </div>
        <v-rating v-model="form.rating" class="mb-4" hover half-increments color="yellow-accent-4"
          active-color="yellow-accent-4" density="compact" length="10"></v-rating>
        <v-textarea v-model="form.review" clearable bg-color="white" label="Escribe tu crítica" variant="solo"
          auto-grow></v-textarea>
        <v-btn color="blue-darken-3" type="submit">Guardar</v-btn>
      </v-form>
    </v-col>

    <v-dialog v-model="isEditing" max-width="500px">
      <v-card>
        <v-card-title>
          <span class="text-h5">Editar Crítica</span>
        </v-card-title>
        <v-card-text>
          <v-form @submit.prevent="updateReview">
            <div class="align-baseline">
              <label>Puntuación:</label>
              <strong class="pl-2">{{ form.rating }}</strong>
            </div>
            <v-rating v-model="form.rating" class="mb-4" hover half-increments color="yellow-accent-4"
              active-color="yellow-accent-4" density="compact" length="10"></v-rating>
            <v-textarea v-model="form.review" clearable bg-color="white" label="Edita tu crítica" variant="solo"
              auto-grow></v-textarea>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn color="red-accent-4" @click="cancelEdit">Cancelar</v-btn>
          <v-btn color="green-darken-3" @click="updateReview">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-row v-if="!isLoading && reviews.length">
      <v-col v-for="review in reviews" :key="review.id" cols="12">
        <v-card>
          <div class="d-flex justify-between align-center">
            <div class="d-flex pt-2">
              <v-card-title class="pt-0 pr-0">Puntuación: {{
                review.rating }} </v-card-title>
              <v-rating v-model="review.rating" class="mb-4" color="yellow-accent-4" active-color="yellow-accent-4"
                density="compact" length="1" readonly></v-rating>
            </div>
            <v-container class="d-flex justify-end gap-2">
              <v-tooltip location="top" v-if="review.user.id === $page.props.auth.user.id">
                <template v-slot:activator="{ props }">
                  <v-btn v-bind="props" icon size="35" variant="tonal" color="blue-darken-3"
                    @click="loadReviewForEdit(review)">
                    <v-icon>mdi-text-box-edit-outline</v-icon>
                  </v-btn>
                </template>
                <span>Editar</span>
              </v-tooltip>
              <v-menu v-if="review.user.id !== $page.props.auth.user.id && !review.reported" location="start"
                transition="scale-transition">
                <template v-slot:activator="{ props }">
                  <v-btn size="30" icon="mdi-dots-horizontal" v-bind="props"></v-btn>
                </template>
                <v-list>
                  <v-list-item @click="reportReview(review)">
                    <v-list-item-title>Reportar</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
              <v-btn v-else-if="review.reported" readonly color="red-darken-1" variant="text">REPORTADA</v-btn>
            </v-container>
          </div>
          <v-card-title>{{ review.user.name }}</v-card-title>
          <v-card-subtitle>{{ dayjs(review.created_at).fromNow() }}</v-card-subtitle>
          <v-card-text class="text-body-1">{{ review.review }}</v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row v-show="isLoading">
      <v-col>
        <v-alert type="info" color="blue-darken-3" variant="tonal">Cargando críticas...</v-alert>
      </v-col>
    </v-row>
    <v-row v-if="!isLoading && !reviews.length">
      <v-col>
        <v-alert type="error" variant="tonal">Todavía no hay críticas para esta película.</v-alert>
      </v-col>
    </v-row>
    <v-snackbar v-model="snackbar" :color="snackbarColor" :timeout="3000">
      {{ snackbarMessage }}
    </v-snackbar>
  </v-container>
</template>


<script setup>
import { ref, onMounted, defineProps } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import 'dayjs/locale/es';

dayjs.extend(relativeTime);
dayjs.locale('es');

const props = defineProps({
  watchStatus: String,
  id: String,
  auth: Object,
})

const reviews = ref([]);
const isLoading = ref(true);
const hasReview = ref(false);
const isEditing = ref(false);
const snackbar = ref(false);
const snackbarMessage = ref('');
const snackbarColor = ref('');
const form = useForm({
  movie_id: props.id,
  user_id: props.auth.user.id,
  rating: 0,
  review: ''
});

const movieId = props.id;

const getReviews = async () => {
  try {
    const response = await fetch(`/movies/${movieId}/reviews`);
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    const data = await response.json();
    reviews.value = data;
    hasReview.value = reviews.value.some((review) => review.user.id === props.auth.user.id);
  } catch (error) {
    console.error('Error fetching reviews:', error);
  } finally {
    isLoading.value = false;
  }
};

const saveReview = () => {
  form.post(route('reviews.store', { movieId }), {
    onSuccess: () => {
      getReviews();
      form.reset();
      hasReview.value = true;
    },
    onError: (errors) => {
      console.error('Error saving review:', errors);
    }
  });
};

const reportReview = (review) => {
  router.put(route('reviews.report', { review }), { reported: true }, {
    onSuccess: () => {
      getReviews();
      console.log('Crítica reportada con éxito');
    },
    onError: (errors) => {
      console.error('Error al reportar la crítica:', errors);
    }
  });
};

const loadReviewForEdit = (review) => {
  isEditing.value = true;
  form.id = review.id;
  form.rating = review.rating;
  form.review = review.review;
  form.movie_id = review.movie_id;
  form.user_id = review.user_id;
};

const updateReview = async () => {
  try {
    await axios.put(route('reviews.update', { movieId: form.movie_id, review: form.id }), {
      rating: form.rating,
      review: form.review,
    });
    getReviews();

    reviews.value.findIndex(review => review.id === form.review);

    isEditing.value = false;
    snackbarMessage.value = 'Crítica actualizada con éxito';
    snackbarColor.value = 'green-darken-3';
    snackbar.value = true;
  } catch (errors) {
    console.error('Error al aprobar la crítica:', errors);
    snackbarMessage.value = 'Error al actualizar la crítica';
    snackbarColor.value = 'error';
    snackbar.value = true;
  }
};

const cancelEdit = () => {
  form.reset();
  isEditing.value = false;
};

onMounted(getReviews);
</script>
