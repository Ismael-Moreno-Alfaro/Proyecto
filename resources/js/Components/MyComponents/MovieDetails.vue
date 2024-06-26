<template>
  <v-container>
    <v-tabs v-model="activeTab" background-color="primary" dark>
      <v-tab>Detalles de la Película</v-tab>
      <v-tab>Reparto</v-tab>
      <v-tab>Críticas</v-tab>
    </v-tabs>
    <v-tabs-items v-model="activeTab">
      <v-tab-item v-if="activeTab === 0">
        <TabMovieDetails :movie="movie" :watchStatus="watchStatus" :isInWatchlist="isInWatchlist" :isLoading="isLoading"
          :id="id" :auth="auth"></TabMovieDetails>
      </v-tab-item>
      <v-tab-item v-else-if="activeTab === 1">
        <TabCast :cast="cast" :isLoading="isLoading" :id="id" :auth="auth"></TabCast>
      </v-tab-item>
      <v-tab-item v-else>
        <TabMovieReviews :watchStatus="watchStatus" :isLoading="isLoading" :id="id" :auth="auth"></TabMovieReviews>
      </v-tab-item>
    </v-tabs-items>
  </v-container>
  <v-snackbar v-model="snackbar" :timeout="3000" top>
    {{ snackbarMessage }}
  </v-snackbar>
</template>

<script setup>
import tmdbService from '@/../services/tmdbService'
import TabMovieDetails from '@/Components/MyComponents/TabMovieDetails.vue'
import TabCast from '@/Components/MyComponents/TabCast.vue'
import TabMovieReviews from '@/Components/MyComponents/TabMovieReviews.vue'
import { ref, onMounted, defineProps, watch } from 'vue'

const props = defineProps({
  id: String,
  auth: Object,
  isInWatchlist: Boolean
});

const checkWatchStatus = async (movieId) => {
  try {
    const response = await fetch(`/watchlist-movies/check-status/${movieId}`);
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    const data = await response.json();
    watchStatus.value = data.watch_status;
    console.log(data);
  } catch (error) {
    console.error('Error fetching movie status:', error);
  }
};


const id = props.id;
const auth = props.auth;
const movie = ref(null);
const cast = ref([]);
const activeTab = ref(0);
const snackbar = ref(false);
const snackbarMessage = ref('');
const isLoading = ref(true);
const error = ref(null);
const watchStatus = ref('');

const loadData = async () => {
  try {
    isLoading.value = true;
    if (activeTab.value === 0 && !movie.value) {
      const director = await tmdbService.getDirector(id);
      movie.value = await tmdbService.getMovieDetails(id);
      const releaseDates = await tmdbService.getMovieReleaseDates(id);
      let spanishReleaseDate = releaseDates.find(date => date.iso_3166_1 === 'ES') ?? releaseDates.find(date => date.iso_3166_1 === 'US');
      movie.value.director = director;
      movie.value.spanishReleaseDate = spanishReleaseDate;

      checkWatchStatus(movie.value.id);
    } else if (activeTab.value === 1 && cast.value.length === 0) {
      cast.value = await tmdbService.getMovieCast(id);
      console.log(cast.value);
    }
    else {
      checkWatchStatus(movie.value.id);
    }
  } catch (err) {
    error.value = 'Error al cargar los datos. Por favor, inténtalo de nuevo más tarde.';
  } finally {
    isLoading.value = false;
  }
};

watch(activeTab, () => {
  loadData();
});

onMounted(() => {
  loadData();
});

</script>