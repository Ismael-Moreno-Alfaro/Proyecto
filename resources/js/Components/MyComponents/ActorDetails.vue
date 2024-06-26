<template>
    <v-container>

        <v-row v-if="!isLoading && actor" class="mt-2" align="start">
            <v-col cols="12" sm="6" md="4" lg="3" xl="2" xxl="1" class="pa-0">
                <v-img
                    :src="actor.profile_path ? getImageUrl(actor.profile_path) : 'https://via.placeholder.com/300x450?text=No+Image'"
                    height="auto" width="100%" class="ma-0"></v-img>
            </v-col>
            <v-col class="pt-0">
                <v-card class="px-4 py-6" min-height="28rem">
                    <v-card-title>{{ actor.name }}</v-card-title>
                    <v-card-text>
                        <p><strong>Fecha de nacimiento:</strong> {{ formatDate(actor.birthday) || 'No hay datos sobre lafecha de nacimiento' }}</p>
                        <p><strong>Lugar de nacimiento:</strong> {{ actor.place_of_birth || 'No hay datos sobre el lugarde nacimiento' }}</p>
                        <p v-if="formatDate(actor.deathday)"><strong>Fecha de fallecimiento:</strong> {{ formatDate(actor.deathday) }}</p>
                    </v-card-text>
                    <v-card-text>{{ actor.biography || 'No hay datos sobre la biografía' }}</v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-row v-if="!isLoading && filmography" class="pt-8" align="start">
            <v-card-title>FILMOGRAFÍA</v-card-title>
            <v-container fluid>
                <v-row>
                    <v-col class="g-4" v-for="movie in filmography" :key="movie.id" cols="12" sm="6" md="6" lg="4"
                        xl="3" xxl="2">
                        <Link :href="route('movies.show', movie.id)">
                        <v-card class="mb-4">
                            <v-row no-gutters>
                                <v-col cols="4" class="pa-0">
                                    <v-img
                                        :src="movie.poster_path ? getImageUrl(movie.poster_path) : 'https://via.placeholder.com/300x450?text=No+Image'"
                                        min-height="136" height="100%" width="100%" class="ma-0"></v-img>
                                </v-col>
                                <v-col cols="8">
                                    <div class="d-flex  pt-2">
                                        <v-card-title class="pt-0 pr-0" v-if="movie.vote_average">{{
                                            movie.vote_average.toFixed(1) }} </v-card-title>
                                        <v-rating v-if="movie.vote_average" v-model="movie.vote_average" class="mb-4"
                                            color="yellow-accent-4" active-color="yellow-accent-4" density="compact"
                                            length="1" readonly></v-rating>
                                    </div>
                                    <v-card-title class="pt-0">{{ movie.title }}</v-card-title>
                                    <v-card-subtitle>{{ formatDate(movie.release_date) }}</v-card-subtitle>
                                    <v-card-subtitle>{{ movie.character }}</v-card-subtitle>
                                </v-col>
                            </v-row>
                        </v-card>
                        </Link>
                    </v-col>
                </v-row>
            </v-container>
        </v-row>

        <v-row v-show="isLoading || !actor">
            <v-col>
                <v-alert type="info" variant="tonal">Cargando datos del actor...</v-alert>
            </v-col>
        </v-row>
    </v-container>
</template>


<script setup>
import tmdbService from '@/../services/tmdbService'
import { ref, onMounted, defineProps } from 'vue';
import { Link } from '@inertiajs/vue3'
import dayjs from 'dayjs';
import 'dayjs/locale/es';

dayjs.locale('es');

const props = defineProps({
    actorId: String,
    auth: Object,
});

const actor = ref(null);
const filmography = ref([]);
const isLoading = ref(true);


const loadData = async () => {
    actor.value = await tmdbService.getActorData(props.actorId);
    console.log(actor.value);
    isLoading.value = false;
    filmography.value = await tmdbService.getFilmography(props.actorId);
    console.log(filmography.value);
}

const getImageUrl = (path) => path ? `https://image.tmdb.org/t/p/w300/${path}` : '';

onMounted(() => {
    loadData();
});

const formatDate = (date) => {
    if (dayjs(date).format('DD/MM/YYYY') === 'Invalid Date') return ''
    else return dayjs(date).format('DD/MM/YYYY');
};

</script>