<template>
  <v-row class="mt-8" v-show="!isLoading && cast.length">
    <v-col v-for="castMember in cast" :key="castMember.cast_id" cols="12" sm="6" md="4" lg="3" xl="2">
      <Link :href="route('actors.show', castMember.id)">
      <v-card class="mx-auto" width="250">
        <v-img width="100%" :src="getImageUrl(castMember.profile_path)"></v-img>
        <v-card-title>{{ castMember.name }}</v-card-title>
        <v-card-subtitle class="pb-4">{{ castMember.character }}</v-card-subtitle>
      </v-card>
      </Link>
    </v-col>
  </v-row>
  <v-row v-if="isLoading && !cast.length">
    <v-col cols="12">
      <v-alert type="info" color="blue-darken-2" variant="tonal">Cargando reparto...</v-alert>
    </v-col>
  </v-row>
  <v-row v-else-if="!cast.length">
    <v-col cols="12">
      <v-alert type="error" variant="tonal">No hay reparto disponible</v-alert>
    </v-col>
  </v-row>

</template>

<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';


const props = defineProps({
  id: String,
  auth: Object,
  cast: Array,
  isLoading: Boolean
});


const getImageUrl = (path) => path ? `https://image.tmdb.org/t/p/w300/${path}` : 'https://via.placeholder.com/300x450?text=No+Image';

</script>