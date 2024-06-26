<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AdminMovies from '@/Components/MyComponents/AdminMovies.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
  movies: Array
});

</script>

<template>

  <Head title="Admin - Películas" />

  <AdminLayout>
    <div class="py-2">
      <AdminMovies :movies="props.movies"></AdminMovies>
    </div>
  </AdminLayout>
</template>