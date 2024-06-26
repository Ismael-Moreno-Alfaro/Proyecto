<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import WatchlistMovies from '@/Components/MyComponents/WatchlistMovies.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Lista de Seguimiento" />

    <AuthenticatedLayout>
        <div class="py-2">
            <WatchlistMovies></WatchlistMovies>
        </div> 
    </AuthenticatedLayout>
</template>