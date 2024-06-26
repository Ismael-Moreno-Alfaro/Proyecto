<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SlideMovies from '@/Components/MyComponents/SlideMoviesAuth.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Inicio" />

    <AuthenticatedLayout>

        <div class="py-4">
            <SlideMovies></SlideMovies>
        </div> 
    </AuthenticatedLayout>
</template>
