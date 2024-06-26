<template>
  <v-container>
    <v-row v-if="!isLoading && pendingMovies.length" class="pt-8" align="start">
      <v-container fluid>
        <v-row>
          <v-col class="g-4" v-for="movie in pendingMovies" :key="movie.id" cols="12" sm="6" md="6" lg="4" xl="3"
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

                  <v-card-title class="pt-0 mt-4">{{ movie.title }}</v-card-title>
                  <v-card-subtitle>{{ movie.genre }}</v-card-subtitle>
                  <div class="d-flex gap-3 pt-4 pl-4 mb-3">
                    <v-tooltip location="bottom">
                      <template v-slot:activator="{ props }">
                        <v-btn icon v-bind="props" size="35" color="green-darken-3" variant="tonal"
                          @click="addToCompleted(movie)"><v-icon>mdi-eye-plus-outline</v-icon></v-btn>
                      </template>
                      <span>Añadir a las películas vistas</span>
                    </v-tooltip>
                    <v-tooltip location="bottom">
                      <template v-slot:activator="{ props }">
                        <v-btn icon v-bind="props" size="35" color="red-accent-4" variant="tonal"
                          @click="deleteMovie(movie.id)"><v-icon>mdi-delete</v-icon></v-btn>
                      </template>
                      <span>Eliminar de la lista de segumiento</span>
                    </v-tooltip>
                  </div>
                </v-col>
              </v-row>
            </v-card>

          </v-col>
        </v-row>
      </v-container>
    </v-row>

    <v-row v-if="!isLoading && !pendingMovies.length">
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
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'

const pendingMovies = ref([]);
const isLoading = ref(false);
const updated = ref(true);
const deleted = ref(false);
const completed = ref(false);
const snackbar = ref(false);
const snackbarMessage = ref('');
const snackbarColor = ref('');


const getImageUrl = (path) => path ? `https://image.tmdb.org/t/p/w300/${path}` : 'https://via.placeholder.com/300x450?text=No+Image';

const loadData = async () => {
  try {
    isLoading.value = true;
    const response = await fetch(`/watchlist-movies/pending`);
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    const data = await response.json();
    pendingMovies.value = data;
  } catch (error) {
    console.error('Error fetching reviews:', error);
  } finally {
    isLoading.value = false;
  }
}

const addToCompleted = async (movie) => {
  try {
    updated.value = false;
    const response = await fetch(`/watchlist-movies/update-status`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        movie_id: movie.id,
        watch_status: 'completed'
      })
    });

    if (response.ok) {
      const data = await response.json();
      console.log(data.message);
      completed.value = true;
      loadData();
      snackbarColor.value = 'green-darken-3';
      snackbarMessage.value = 'Película añadida a las vistas';
      snackbar.value = true;

    } else {
      console.error('Error updating watch status:', response.status);
      alert('Ocurrió un error al actualizar el estado. Por favor, intenta nuevamente.');
    }

  } catch (error) {
    console.error('Error:', error);
    alert('Ocurrió un error inesperado. Por favor, intenta nuevamente.');
  } finally {
    updated.value = true;
  }
};

const deleteMovie = (movieId) => {
  router.delete(route('watchlist-movies.destroy', movieId), {
    onSuccess: () => {
      deleted.value = false;
      snackbarMessage.value = 'Película eliminada de la lista de seguimiento';
      snackbarColor.value = 'green-darken-3';
      snackbar.value = true;
      loadData();
    },
    onError: (errors) => {
      console.error('Error removing movie to watchlist:', errors);
    }
  });
};

onMounted(() => {
  loadData();
});

</script>