<template>
  <v-row class="mt-4">
    <v-col v-for="(movie, index) in movies" :key="index" cols="12" sm="6" md="4" lg="3" xl="2">
      <Link :href="route('movies.show', movie.id)">
      <v-card class="my-4 mx-4" hover>
        <v-img :src="getImageUrl(movie.poster_path)" :aspect-ratio="2 / 3" cover></v-img>
        <v-card-title>{{ movie.title }}</v-card-title>
      </v-card>
      </Link>
    </v-col>
  </v-row>

  <v-intersection v-if="hasMore" class="observed">
    <div style="height: 50px;"></div>
  </v-intersection>
</template>

<script setup>
import tmdbService from '@/../services/tmdbService';
import { Link } from '@inertiajs/vue3';
import { ref, onMounted, nextTick, watch } from 'vue';

const loading = ref(false);
const page = ref(1);
const hasMore = ref(true);
const movieIds = ref(new Set());
let observer = null;
const movies = ref([]);

const getImageUrl = (poster) => poster ? `https://image.tmdb.org/t/p/w300${poster}` : 'https://via.placeholder.com/300x450?text=No+Image';

const loadMore = async () => {
  try {
    loading.value = true;
    const data = await tmdbService.getUpcomingMoviesSpain(page.value);
    const newMovies = data.results.filter((movie) => !movieIds.value.has(movie.id));
    newMovies.forEach((movie) => movieIds.value.add(movie.id));

    movies.value.push(...newMovies);
    page.value++;
    hasMore.value = page.value <= data.total_pages;
  } catch (err) {
    console.error('Failed to load movies:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  observer = new IntersectionObserver((entries) => {
    if (entries[0].isIntersecting && hasMore.value) {
      loadMore();
    }
  });

  const target = document.querySelector('.observed');
  if (target) observer.observe(target);
});

watch(hasMore, async (newVal) => {
  if (newVal) {
    await nextTick();
    const target = document.querySelector('.observed');
    if (target) observer.observe(target);
  } else {
    if (observer) {
      const target = document.querySelector('.observed');
      if (target) observer.unobserve(target);
    }
  }
});
</script>