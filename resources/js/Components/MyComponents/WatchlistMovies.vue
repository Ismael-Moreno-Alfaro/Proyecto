<template>
    <v-container>
        <v-tabs v-model="activeTab" background-color="primary" dark>
            <v-tab>Sin ver</v-tab>
            <v-tab>Vistas</v-tab>
        </v-tabs>
        <v-tabs-items v-model="activeTab">
            <v-tab-item v-if="activeTab === 0">
                <TabPendingMovies></TabPendingMovies>
            </v-tab-item>
            <v-tab-item v-else-if="activeTab === 1">
                <TabCompletedMovies></TabCompletedMovies>
            </v-tab-item>
        </v-tabs-items>
    </v-container>
</template>

<script setup>
import TabPendingMovies from '@/Components/MyComponents/TabPendingMovies.vue';
import TabCompletedMovies from '@/Components/MyComponents/TabCompletedMovies.vue';
import { ref } from 'vue';

const activeTab = ref(0);

</script>
