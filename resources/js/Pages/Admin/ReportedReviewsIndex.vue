<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AdminReportedReviews from '@/Components/MyComponents/AdminReportedReviews.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
  reviews: Array
});

</script>

<template>

  <Head title="Admin - Críticas Reportadas" />

  <AdminLayout>
    <div class="py-2">
      <AdminReportedReviews :reviews="props.reviews"></AdminReportedReviews>
    </div>
  </AdminLayout>
</template>