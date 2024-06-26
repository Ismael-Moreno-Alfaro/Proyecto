<template>

  <v-autocomplete v-model:search="search" :items="movieSuggestions" label="Buscar película" density="compact"
    :loading="loading" no-data-text="No se encontraron películas" item-text="title" item-value="id"
    @update:search="onSearchChange" max-width="400" variant="outlined">
    <template v-slot:selection="{ item, index }">
      <v-chip v-if="index === 0">{{ item.title }}</v-chip>
    </template>
    <template v-slot:item="{ item }">
      <Link :href="route('movies.show', item.raw.id)">
      <v-list-item clas @click="selectMovie(item.raw.poster_path)">

        <v-list-item-avatar>
          <v-img height="80" :src="getImageUrl(item.raw.poster_path)" v-if="item.raw.poster_path"></v-img>
        </v-list-item-avatar>
        <v-list-item-content>
          <v-list-item-title class="text-center">{{ item.title }}</v-list-item-title>
        </v-list-item-content>

      </v-list-item>
      </Link>
    </template>
  </v-autocomplete>

</template>

<script setup>
import { ref, watch } from 'vue';
import tmdbService from '@/../services/tmdbService';
import { Link } from '@inertiajs/vue3';

const search = ref('');
const movieSuggestions = ref([]);
const loading = ref(false);

const getImageUrl = (poster) => poster ? `https://image.tmdb.org/t/p/w300${poster}` : 'https://via.placeholder.com/300x450?text=No+Image';

// Función para manejar cambios en la entrada de búsqueda
const onSearchChange = async (query) => {
  if (query) {
    loading.value = true;
    try {
      const data = await tmdbService.searchMovies(query);
      if (data) {
        movieSuggestions.value = data.results || [];
      } else {
        movieSuggestions.value = [];
      }
    } catch (error) {
      console.error('Error en la búsqueda:', error);
    }
    loading.value = false;
  } else {
    movieSuggestions.value = [];
  }
};

</script>