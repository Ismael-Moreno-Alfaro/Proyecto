<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Movies from '@/Components/MyComponents/MoviesInfiniteScroll.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Películas" />

    <AuthenticatedLayout>


        <div class="py-2">
            <Movies></Movies>
        </div> 
    </AuthenticatedLayout>
</template>