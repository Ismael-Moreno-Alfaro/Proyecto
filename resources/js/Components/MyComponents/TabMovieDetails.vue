<template>
  <v-row>
    <v-col>
      <v-snackbar v-model="snackbar" :color="snackbarColor" :timeout="3000" top>
        {{ snackbarMessage }}
      </v-snackbar>
    </v-col>
  </v-row>
  <v-row v-if="!isLoading && movie">
    <v-col class="mt-8" cols="12" md="4">
      <v-img :src="getImageUrl(movie.poster_path)" height="400" width="100%"></v-img>
    </v-col>
    <v-col class="mt-8" cols="12" md="8">
      <v-card>
        <div class="d-flex justify-between ">
          <div class="d-flex justify-between pt-4">
            <v-card-title class="pt-0 pr-0">Puntuación: {{
              movie.vote_average.toFixed(1) }} </v-card-title>
            <v-rating v-model="movie.vote_average" class="mb-4" color="yellow-accent-4" active-color="yellow-accent-4"
              density="compact" length="1" readonly></v-rating>
          </div>
          <v-tooltip location="top">
            <template v-slot:activator="{ props }">
              <v-btn class="mr-4 mt-2" v-if="!isLoading && !isInWatchlist" v-bind="props" icon size="35" variant="tonal"
                color="blue-darken-3" @click="addMovie">
                <v-icon>mdi-plus</v-icon>
              </v-btn>
            </template>
            <span>Añadir a la lista de segumiento</span>
          </v-tooltip>
          <v-tooltip location="top" class="mr-4" v-if="!isLoading && isInWatchlist && watchStatus === 'pending'">
            <template v-slot:activator="{ props }">
              <v-btn class="mr-4 mt-2" v-bind="props" icon size="35" variant="tonal" color="red-accent-4"
                @click="removeMovieFromWatchlist(id)">
                <v-icon>mdi-minus</v-icon>
              </v-btn>
            </template>
            <span>Eliminar de la lista de segumiento</span>
          </v-tooltip>
          <v-tooltip location="top" v-else-if="!isLoading && isInWatchlist && watchStatus === 'completed'">
            <template v-slot:activator="{ props }">
              <v-icon v-bind="props" class="mr-4 mt-2" size="35" variant="tonal" color="green">mdi-eye</v-icon>
            </template>
            <span>Ya has visto esta película</span>
          </v-tooltip>
        </div>
        <v-card-title class="d-flex align-center">{{ movie.title }}

        </v-card-title>
        <v-card-subtitle v-if="movie.tagline">{{ movie.tagline }}</v-card-subtitle>
        <v-card-text>
          <p><strong>Director:</strong> {{ movie.director }}</p>
          <p><strong>Estreno:</strong>
            <text-body-2 v-if="movie.spanishReleaseDate"> {{ formatDate(new
              Date(movie.spanishReleaseDate.release_dates[0].release_date)) }}</text-body-2>

          </p>
          <p><strong>Géneros:</strong> {{ movie.genres ? movie.genres.map(genre => genre.name).join(', ') : '' }}</p>
          <p><strong>Sinopsis:</strong> {{ movie.overview }}</p>
          <p><strong>Duración:</strong> {{ movie.runtime }} minutos</p>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
  <v-row v-show="isLoading || !movie">
    <v-col>
      <v-alert type="info" variant="tonal">Cargando detalles de la película...</v-alert>
    </v-col>

  </v-row>
</template>

<script setup>
import { ref, onMounted, defineProps } from 'vue';
import { router } from '@inertiajs/vue3'


const props = defineProps({
  id: String,
  auth: Object,
  movie: Object,
  watchStatus: String,
  isLoading: Boolean,
  isInWatchlist: Boolean
});

const snackbarColor = ref('');
const snackbar = ref(false);
const snackbarMessage = ref('');
const isInWatchlist = ref(props.isInWatchlist);
const watchStatus = ref(props.watchStatus);

console.log(props.id);

const checkWatchStatus = async (movieId) => {
  try {
    const response = await fetch(`/watchlist-movies/check-status/${movieId}`);
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    const data = await response.json();
    console.log(data);
    watchStatus.value = data.watch_status;
    console.log(data);
  } catch (error) {
    console.error('Error fetching movie status:', error);
  }
};

checkWatchStatus(props.id);

// Función para dar formato a la fecha en el formato día/mes/año
const formatDate = (date) => {
  return date.toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
};

const formatDateTime = (dateString) => {
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  const seconds = String(date.getSeconds()).padStart(2, '0');
  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
};

const getImageUrl = (path) => path ? `https://image.tmdb.org/t/p/w300/${path}` : 'https://via.placeholder.com/300x450?text=No+Image';

const addMovie = () => {
  let date;
  if (isNaN(formatDateTime(props.movie.spanishReleaseDate?.release_dates[0]?.release_date))) {
    date = null
  } else {
    date = formatDateTime(props.movie.spanishReleaseDate?.release_dates[0]?.release_date);
  }
  const movieData = {
    id: props.movie.id,
    title: props.movie.title,
    overview: props.movie.overview,
    poster_path: getImageUrl(props.movie.poster_path),
    genre: props.movie.genres ? props.movie.genres.map(genre => genre.name).join(', ') : '',
    spanish_release_date: date,

    runtime: props.movie.runtime,
    director: props.movie.director,
    tagline: props.movie.tagline
  };

  console.log(formatDateTime(props.movie.spanishReleaseDate?.release_dates[0].release_date));

  // Primero, añado la película a la base de datos
  router.post(route('movies.store'), movieData, {
    onSuccess: () => {
      // Luego añado la película a la lista de seguimiento
      addMovieToWatchlist(props.movie.id);
    },
    onError: (errors) => {
      // Si la película ya existe, continúa añadiéndola a la watchlist
      if (errors.id && errors.id.includes('El campo id ya ha sido registrado')) {
        addMovieToWatchlist(props.movie.id);
      } else {
        console.error('Error adding movie:', errors);
      }
    }
  });
};

const addMovieToWatchlist = (movieId) => {
  const watchlistData = {
    movie_id: movieId
  };

  router.post(route('watchlist-movies.store'), watchlistData, {
    onSuccess: () => {
      isInWatchlist.value = true;
      snackbarMessage.value = 'Película añadida a la lista de seguimiento';
      snackbar.value = true;
      snackbarColor.value = 'green-darken-3';
      watchStatus.value = 'pending';
    },
    onError: (errors) => {
      console.error('Error adding movie to watchlist:', errors);
    }
  });
};

const removeMovieFromWatchlist = (movieId) => {
  router.delete(route('watchlist-movies.destroy', movieId), {
    onSuccess: () => {
      isInWatchlist.value = false;
      snackbarMessage.value = 'Película eliminada de la lista de seguimiento';
      snackbarColor.value = 'green-darken-3';
      snackbar.value = true;
    },
    onError: (errors) => {
      console.error('Error removing movie to watchlist:', errors);
    }
  });
};

</script>