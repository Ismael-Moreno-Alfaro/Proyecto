<template>

  <v-container>
    <v-col class="d-flex justify-end"> 
    <SearchComponent></SearchComponent>
    </v-col>
    <v-tabs>
      <v-tab @click="changeTab(0)">Populares</v-tab>
      <v-tab @click="changeTab(1)">Mejor puntuadas</v-tab>
      <v-tab @click="changeTab(2)">En cartelera</v-tab>
      <v-tab @click="changeTab(3)">Próximamente</v-tab>
    </v-tabs>
    <v-tabs-items v-model="activeTab">
      <v-tab-item v-if="activeTab === 0">
        <TabPopularMovies />
      </v-tab-item>
      <v-tab-item v-else-if="activeTab === 1">
        <TabTopRatedMovies />
      </v-tab-item>
      <v-tab-item v-else-if="activeTab === 2">
        <TabPlayingMoviesSpain />
      </v-tab-item>
      <v-tab-item v-else>
        <TabUpcomingMoviesSpain />
      </v-tab-item>
    </v-tabs-items>
  </v-container>
</template>


<script setup>
import TabPopularMovies from '@/Components/MyComponents/TabPopularMovies.vue'
import TabTopRatedMovies from '@/Components/MyComponents/TabTopRatedMovies.vue';
import TabPlayingMoviesSpain from '@/Components/MyComponents/TabPlayingMoviesSpain.vue';
import TabUpcomingMoviesSpain from '@/Components/MyComponents/TabUpcomingMoviesSpain.vue';
import SearchComponent from './SearchComponent.vue'
import { ref } from 'vue';
const activeTab = ref(0); // Inicializa el primer tab como activo


const changeTab = (tabIndex) => {
  activeTab.value = tabIndex;
};
</script>

