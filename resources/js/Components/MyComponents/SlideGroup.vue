<template>
  <v-container class="d-flex align-center">
    <h1 class="mr-4">Películas</h1>
  </v-container>
  <v-container>
    <div class="d-flex justify-center align-center" v-if="loading" height="300">
      <v-progress-circular indeterminate></v-progress-circular>
    </div>
    <div v-if="error">
      <v-alert :text="error" type="error" variant="tonal"></v-alert>
    </div>
    <v-slide-group v-else show-arrows>
      <v-slide-item v-for="movie in movies" :key="movie.id" class="pa-2">
        <v-card class="mx-auto" width="200">
          <v-img height="300" :src="`https://image.tmdb.org/t/p/w300${movie.poster_path}`" cover></v-img>

          <v-card-title>
            {{ movie.title }}
          </v-card-title>
        </v-card>
      </v-slide-item>
    </v-slide-group>
  </v-container>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import tmdbService from '@/../services/tmdbService';
const props = defineProps({
  auth: Object
});

const auth = props.auth;

const movies = ref([]);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
  try {
    movies.value = await tmdbService.getMovies();
  } catch (err) {
    error.value = 'Failed to load movies. Please try again later.';
  } finally {
    loading.value = false;
  }
});
</script>

<style></style>