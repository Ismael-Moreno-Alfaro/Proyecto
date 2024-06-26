<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import MovieDetails from '@/Components/MyComponents/MovieDetails.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    id: String,
    auth: Object,
    isInWatchlist: Boolean
});
</script>

<template>
    <Head title="Película" />

    <AuthenticatedLayout>
        <div class="py-2">
            <MovieDetails :id="id" :auth="auth" :isInWatchlist="isInWatchlist"></MovieDetails>
        </div> 
    </AuthenticatedLayout>
</template>