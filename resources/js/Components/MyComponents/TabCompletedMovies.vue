<template>
  <v-container>

    <v-row v-if="!isLoading && completedMovies.length" class="pt-8" align="start">
      <v-container fluid>
        <v-row>
          <v-col class="g-4" v-for="movie in completedMovies" :key="movie.id" cols="12" sm="6" md="6" lg="4" xl="3"
            xxl="2">

            <v-card class="mb-4">
              <v-row no-gutters>
                <v-col cols="4" class="pa-0">
                  <Link :href="route('movies.show', movie.id)">
                  <v-img
                    :src="movie.poster_path ? getImageUrl(movie.poster_path) : 'https://via.placeholder.com/300x450?text=No+Image'"
                    min-height="136" height="100%" width="100%" class="ma-0"></v-img>
                  </Link>
                </v-col>
                <v-col cols="8">
                  <div class="d-flex pt-2">
                    <v-card-title class="pt-0 pr-0" v-if="movie.reviews[0] && movie.reviews[0].id">{{
                      movie.reviews[0].rating }} </v-card-title>
                    <v-rating v-if="movie.reviews[0] && movie.reviews[0].id" v-model="movie.reviews[0].rating"
                      class="mb-4" color="yellow-accent-4" active-color="yellow-accent-4" density="compact" length="1"
                      readonly></v-rating>
                    <v-card-title class="pt-0 pr-0 text-body-2" v-else>Sin crítica</v-card-title>
                  </div>
                  <v-card-title class="pt-0">{{ movie.title }}</v-card-title>
                  <v-card-subtitle>{{ movie.genre }}</v-card-subtitle>
                  <div class="d-flex pt-4 pl-4 mb-3">
                    <v-tooltip location="bottom" v-if="movie.reviews[0] && movie.reviews[0].id">
                      <template v-slot:activator="{ props }">
                        <v-btn v-bind="props" icon size="35" variant="tonal" color="blue-darken-3"
                          @click="loadReviewForEdit(movie.reviews[0])">
                          <v-icon>mdi-text-box-edit-outline</v-icon>
                        </v-btn>
                      </template>
                      <span>Editar</span>
                    </v-tooltip>
                    <v-tooltip location="bottom" v-else>
                      <template v-slot:activator="{ props }">
                        <v-btn v-bind="props" icon size="35" variant="tonal" color="green-darken-3"
                          @click="loadReviewForCreate(movie.id)">
                          <v-icon>mdi-plus-box-outline</v-icon>
                        </v-btn>
                      </template>
                      <span>Añadir</span>
                    </v-tooltip>
                  </div>
                </v-col>
              </v-row>
            </v-card>

          </v-col>
        </v-row>
      </v-container>
    </v-row>

    <v-dialog v-model="isEditing" max-width="500px">
      <v-card>
        <v-card-title>
          <span class="text-h5">{{ form.id ? 'Editar Crítica' : 'Añadir Crítica' }}</span>
        </v-card-title>
        <v-card-text>
          <v-form @submit.prevent="form.id ? updateReview() : createReview()">
            <div class="align-baseline">
              <label>Puntuación:</label>
              <strong class="pl-2">{{ form.rating }}</strong>
            </div>
            <v-rating v-model="form.rating" class="mb-4" hover half-increments color="yellow-accent-4"
              active-color="yellow-accent-4" density="compact" length="10"></v-rating>
            <v-textarea v-model="form.review" clearable bg-color="white" label="Tu crítica" variant="solo"
              auto-grow></v-textarea>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-btn color="red-accent-4" @click="cancelEdit">Cancelar</v-btn>
          <v-btn color="green-darken-3" @click="form.id ? updateReview() : createReview()">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-row v-if="!isLoading && !completedMovies.length">
      <v-col>
        <v-alert type="error" variant="tonal">No hay películas vistas en la lista de seguimiento.</v-alert>
      </v-col>
    </v-row>
    <v-snackbar v-model="snackbar" :color="snackbarColor" :timeout="3000">
      {{ snackbarMessage }}
    </v-snackbar>
  </v-container>
</template>


<script setup>
import { ref, onMounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import axios from 'axios';
import { Link } from '@inertiajs/vue3'
import dayjs from 'dayjs';
import 'dayjs/locale/es';

dayjs.locale('es');

const completedMovies = ref([]);
const user_id = ref('');
const isLoading = ref(false);
const isEditing = ref(false);
const snackbar = ref(false);
const snackbarMessage = ref('');
const snackbarColor = ref('');
const form = useForm({
  id: null,
  movie_id: null,
  user_id: null,
  rating: 0,
  review: ''
});

const getImageUrl = (path) => path ? `https://image.tmdb.org/t/p/w300/${path}` : 'https://via.placeholder.com/300x450?text=No+Image';

const loadData = async () => {
  try {
    isLoading.value = true;
    const response = await fetch(`/watchlist-movies/completed`);
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    const data = await response.json();
    completedMovies.value = data.completedMovies;
    user_id.value = data.userId;
    console.log(completedMovies.value);
  } catch (error) {
    console.error('Error fetching reviews:', error);
  } finally {
    isLoading.value = false;
  }
};

const loadReviewForCreate = (movieId) => {
  isEditing.value = true;
  form.id = null;
  form.rating = 0;
  form.review = '';
  form.movie_id = movieId;
  form.user_id = user_id.value;
  console.log(form.user_id);
};

const createReview = async () => {
  try {
    const response = await axios.post(route('reviews.store', { movieId: form.movie_id }), {
      rating: form.rating,
      review: form.review,
      movie_id: form.movie_id,
      user_id: form.user_id
    });

    const newReview = response.data;
    loadData();
    const movieIndex = completedMovies.value.findIndex(movie => movie.id === form.movie_id);
    if (movieIndex !== -1) {
      completedMovies.value[movieIndex].review = newReview;
    }

    isEditing.value = false;
    snackbarMessage.value = 'Crítica añadida con éxito';
    snackbarColor.value = 'green-darken-3';
    snackbar.value = true;
  } catch (errors) {
    console.error('Error al añadir la crítica:', errors);
    snackbarMessage.value = 'Error al añadir la crítica';
    snackbarColor.value = 'error';
    snackbar.value = true;
  }
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
    await axios.put(route('reviews.update', { review: form.id, movieId: form.movie_id }), {
      rating: form.rating,
      review: form.review,
    });

    loadData();
    completedMovies.value.findIndex(movie => movie.id === form.movie_id);

    isEditing.value = false;
    snackbarMessage.value = 'Crítica actualizada con éxito';
    snackbarColor.value = 'green-darken-3';
    snackbar.value = true;
  } catch (errors) {
    console.error('Error al actualizar la crítica:', errors);
    snackbarMessage.value = 'Error al actualizar la crítica';
    snackbarColor.value = 'error';
    snackbar.value = true;
  }
};

const cancelEdit = () => {
  form.reset();
  isEditing.value = false;
};

onMounted(() => {
  loadData();
});

const formatDate = (date) => {
  if (dayjs(date).format('DD/MM/YYYY') === 'Invalid Date') return ''
  else return dayjs(date).format('DD/MM/YYYY');
};

</script>