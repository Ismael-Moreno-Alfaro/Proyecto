<template>
  <v-container class="mt-4">
    <v-data-table :headers="headers" :items="movies" class="elevation-1">
      <template v-slot:top>
        <v-toolbar flat color="blue-darken-2">
          <v-toolbar-title>Películas</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn color="white" class="mr-4" variant="flat" @click="openAddDialog">Añadir Película</v-btn>
        </v-toolbar>
      </template>
      <template v-slot:item.spanish_release_date="{ item }">
        <span>{{ formatDate(item.spanish_release_date) }}</span>
      </template>
      <template v-slot:item.poster_path="{ item }">
        <img :src="item?.poster_path" alt="Poster" width="50">
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon class="mx-2" color="green" @click="openEditDialog(item)"
          :disabled="item?.poster_path.startsWith('https')">mdi-pencil</v-icon>
        <v-icon class="mx-2" color="red-darken-1" @click="openDeleteDialog(item)"
          :disabled="item.in_watchlist > 0">mdi-delete</v-icon>
      </template>
    </v-data-table>

    <v-snackbar v-model="snackbar" :color="snackbarColor" :timeout="3000">
      {{ snackbarMessage }}
    </v-snackbar>

    <v-dialog v-model="addDialog" max-width="600">
      <v-card>
        <v-card-title class="headline">Añadir Película</v-card-title>
        <v-card-text>
          <v-form ref="addForm" @submit.prevent="confirmAdd">
            <v-text-field v-model="newMovie.title" label="Título" :error-messages="errors.title" required
              @input="clearError('title')"></v-text-field>
            <v-text-field v-model="newMovie.director" label="Director" :error-messages="errors.director" required
              @input="clearError('director')"></v-text-field>
            <v-textarea v-model="newMovie.overview" label="Sinopsis" :error-messages="errors.overview" required
              @input="clearError('overview')"></v-textarea>
            <v-text-field v-model="newMovie.genre" label="Géneros" :error-messages="errors.genre" required
              @input="clearError('genre')"></v-text-field>
            <v-text-field v-model="newMovie.runtime" label="Duración (minutos)" type="number"
              :error-messages="errors.runtime" required @input="clearError('runtime')"></v-text-field>
            <v-text-field v-model="newMovie.spanish_release_date" label="Fecha de lanzamiento en España" type="date"
              :error-messages="errors.spanish_release_date" required
              @input="clearError('spanish_release_date')"></v-text-field>
            <v-text-field v-model="newMovie.vote_average" label="Puntuación" type="number" step="0.1"
              :error-messages="errors.vote_average" required @input="clearError('vote_average')"></v-text-field>
            <v-file-input v-model="newMovie.poster_path" label="Poster" accept="image/*"
              :error-messages="errors.poster_path" @change="clearError('poster_path')" show-size></v-file-input>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red-accent-4" @click="closeAddDialog">Cancelar</v-btn>
          <v-btn color="green-darken-3" @click="confirmAdd">Añadir</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="editDialog" max-width="500">
      <v-card>
        <v-card-title class="headline">Editar Película</v-card-title>
        <v-card-text>
          <v-form ref="editForm" @submit.prevent="confirmEdit">
            <v-text-field v-model="selectedMovie.title" label="Título" :error-messages="editErrors.title" required
              @input="clearEditError('title')"></v-text-field>
            <v-text-field v-model="selectedMovie.director" label="Director" :error-messages="editErrors.director"
              required @input="clearEditError('director')"></v-text-field>
            <v-textarea v-model="selectedMovie.overview" label="Sinopsis" :error-messages="editErrors.overview" required
              @input="clearEditError('overview')"></v-textarea>
            <v-text-field v-model="selectedMovie.genre" label="Géneros" :error-messages="editErrors.genre" required
              @input="clearEditError('genre')"></v-text-field>
            <v-text-field v-model="selectedMovie.runtime" label="Duración (minutos)" type="number"
              :error-messages="editErrors.runtime" required @input="clearEditError('runtime')"></v-text-field>
            <v-text-field v-model="selectedMovie.spanish_release_date" label="Fecha de lanzamiento en España"
              type="date" :error-messages="editErrors.spanish_release_date" required
              @input="clearEditError('spanish_release_date')"></v-text-field>
            <v-text-field v-model="selectedMovie.vote_average" label="Puntuación" type="number" step="0.1"
              :error-messages="editErrors.vote_average" required @input="clearEditError('vote_average')"></v-text-field>
            <v-file-input v-model="selectedMovie.poster_path" label="Poster (opcional)" accept="image/*"
              :error-messages="editErrors.poster_path" @change="clearEditError('poster_path')" show-size></v-file-input>
          </v-form>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="red-accent-4" @click="closeEditDialog">Cancelar</v-btn>
          <v-btn color="green-darken-3" @click="confirmEdit">Guardar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="deleteDialog" max-width="400">
      <v-card>
        <v-card-title class="headline">Confirmar Eliminación</v-card-title>
        <v-card-text>
          ¿Estás seguro de que quieres eliminar esta película?
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue-darken-3" text @click="closeDeleteDialog">Cancelar</v-btn>
          <v-btn color="red-darken-1" text @click="confirmDelete">Eliminar</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref, defineProps } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import dayjs from 'dayjs';


dayjs.locale('es');

const props = defineProps({
  movies: Array,
});

const movies = ref(props.movies);
console.log(props.movies);

const headers = ref([
  { title: 'ID', key: 'id' },
  { title: 'Título', key: 'title' },
  { title: 'Director', key: 'director' },
  { title: 'Géneros', key: 'genre' },
  { title: 'Fecha de estreno', key: 'spanish_release_date' },
  { title: 'Duración en minutos', key: 'runtime' },
  { title: 'Póster', key: 'poster_path' },
  { title: 'Acciones', key: 'actions', sortable: false },
]);

const snackbar = ref(false);
const snackbarMessage = ref('');
const snackbarColor = ref('');

const addDialog = ref(false);

const newMovie = ref({
  title: '',
  director: '',
  overview: '',
  genre: '',
  runtime: '',
  spanish_release_date: '',
  vote_average: '',
  poster_path: '',
});

const errors = ref({
  title: '',
  director: '',
  overview: '',
  genre: '',
  runtime: '',
  spanish_release_date: '',
  vote_average: '',
  poster_path: '',
});

const openAddDialog = () => {
  newMovie.value = {
    title: '',
    director: '',
    overview: '',
    genre: '',
    runtime: '',
    spanish_release_date: '',
    vote_average: '',
    poster_path: '',
  };
  errors.value = {
    title: '',
    director: '',
    overview: '',
    genre: '',
    runtime: '',
    spanish_release_date: '',
    vote_average: '',
    poster_path: '',
  };
  addDialog.value = true;
};

const closeAddDialog = () => {
  addDialog.value = false;
};

const clearError = (field) => {
  errors.value[field] = '';
};

const confirmAdd = async () => {
  // Limpiar errores previos
  errors.value = {
    title: '',
    director: '',
    overview: '',
    genre: '',
    runtime: '',
    spanish_release_date: '',
    vote_average: '',
    poster_path: '',
  };

  // Validar campos
  let hasErrors = false;

  if (!newMovie.value.title) {
    errors.value.title = 'El título es requerido';
    hasErrors = true;
  }

  if (!newMovie.value.director) {
    errors.value.director = 'El director es requerido';
    hasErrors = true;
  }

  const poster = newMovie.value.poster_path;
  if (poster) {
    if (!(poster instanceof File)) {
      errors.value.poster_path = 'El póster debe ser un archivo de imagen';
      hasErrors = true;
    } else if (poster.size > 2 * 1024 * 1024) {
      errors.value.poster_path = 'El póster debe ocupar menos de 2 MB';
      hasErrors = true;
    } else if (!['image/jpeg', 'image/jpg', 'image/svg', 'image/png', 'image/gif'].includes(poster.type)) {
      errors.value.poster_path = 'El póster debe ser una imagen';
      hasErrors = true;
    }
  }

  if (hasErrors) {
    return;
  }

  const formData = new FormData();
  formData.append('title', newMovie.value.title);
  formData.append('director', newMovie.value.director);
  formData.append('overview', newMovie.value.overview);
  formData.append('genre', newMovie.value.genre);
  formData.append('runtime', newMovie.value.runtime);
  formData.append('vote_average', newMovie.value.vote_average);
  formData.append('spanish_release_date', newMovie.value.spanish_release_date);
  formData.append('poster_path', newMovie.value.poster_path);

  try {
    const response = await axios.post(route('admin.movies.store'), formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    });

    movies.value.unshift(response.data);

    snackbarMessage.value = 'Película añadida correctamente';
    snackbarColor.value = 'green-darken-3';
    snackbar.value = true;
    closeAddDialog();
  } catch (error) {
    console.error('Error al añadir la película:', error.response);
    const responseErrors = error.response?.data?.errors || {};
    for (const key in responseErrors) {
      if (responseErrors[key]) {
        errors.value[key] = responseErrors[key][0];
      }
    }
    snackbarMessage.value = 'Error al añadir la película';
    snackbarColor.value = 'error';
    snackbar.value = true;
  }
};


const editDialog = ref(false);
const selectedMovie = ref({});
const editErrors = ref({
  title: '',
  director: '',
  poster_path: '',
});

const openEditDialog = (item) => {
  console.log(item);
  selectedMovie.value = { ...item };
  console.log(selectedMovie.value.id);
  editErrors.value = { title: '', director: '', poster_path: '' };
  selectedMovie.value.poster_path = null;
  editDialog.value = true;
};

const closeEditDialog = () => {
  editDialog.value = false;
};

const clearEditError = (field) => {
  editErrors.value[field] = '';
};

const confirmEdit = async () => {
  let hasErrors = false;
  if (!selectedMovie.value.title) {
    editErrors.value.title = 'El título es requerido';
    hasErrors = true;
  }

  if (!selectedMovie.value.director) {
    editErrors.value.director = 'El director es requerido';
    hasErrors = true;
  }

  const poster = selectedMovie.value.poster_path;
  if (poster && poster instanceof File) {
    if (poster.size > 2 * 1024 * 1024) {
      editErrors.value.poster_path = 'El póster debe ocupar menos de 2 MB';
      hasErrors = true;
    } else if (!['image/jpeg', 'image/jpg', 'image/svg', 'image/png', 'image/gif'].includes(poster.type)) {
      editErrors.value.poster_path = 'El póster debe ser una imagen';
      hasErrors = true;
    }
  }

  if (hasErrors) {
    return;
  }

  const formEditData = new FormData();
  formEditData.append('title', selectedMovie.value.title || '');
  formEditData.append('director', selectedMovie.value.director || '');
  formEditData.append('overview', selectedMovie.value.overview || '');
  formEditData.append('genre', selectedMovie.value.genre || '');
  formEditData.append('runtime', selectedMovie.value.runtime || '');
  formEditData.append('vote_average', selectedMovie.value.vote_average || '');
  formEditData.append('spanish_release_date', selectedMovie.value.spanish_release_date || '');

  if (selectedMovie.value.poster_path instanceof File) {
    formEditData.append('poster_path', selectedMovie.value.poster_path);
  }

  formEditData.append('_method', 'PUT');

  try {
    const response = await axios.post(
      route('admin.movies.update', selectedMovie.value.id),
      formEditData,
      {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      }
    );

    console.log('Response Data:', response.data);
    const updatedMovie = response.data;

    if (!updatedMovie || !updatedMovie.id) {
      throw new Error('Respuesta del servidor no válida');
    }

    movies.value = movies.value.map(movie =>
      movie.id === updatedMovie.id ? updatedMovie : movie
    );

    closeEditDialog();
    snackbarMessage.value = 'Película editada correctamente';
    snackbarColor.value = 'green-darken-3';
    snackbar.value = true;

  } catch (error) {
    console.error('Error al editar la película:', error);
    if (error.response && error.response.data && error.response.data.errors) {
      const responseErrors = error.response.data.errors;
      for (const key in responseErrors) {
        if (responseErrors.hasOwnProperty(key)) {
          editErrors.value[key] = responseErrors[key][0];
        }
      }
    }
    snackbarMessage.value = 'Error al editar la película';
    snackbarColor.value = 'error';
    snackbar.value = true;
  }
};



const deleteDialog = ref(false);

const openDeleteDialog = (item) => {
  selectedMovie.value = item;
  deleteDialog.value = true;
};

const closeDeleteDialog = () => {
  deleteDialog.value = false;
};

const confirmDelete = async () => {
  try {
    await axios.delete(route('admin.movies.destroy', selectedMovie.value.id));
    movies.value = movies.value.filter(movie => movie.id !== selectedMovie.value.id);

    closeDeleteDialog();
    snackbarMessage.value = 'Película eliminada correctamente';
    snackbarColor.value = 'green-darken-3';
    snackbar.value = true;

  } catch (error) {
    if (error.response && error.response.data && error.response.data.error) {
      snackbarMessage.value = error.response.data.error;
    } else {
      snackbarMessage.value = 'Error al eliminar la película';
    }
    snackbarColor.value = 'error';
    snackbar.value = true;
    deleteDialog.value = false;
  }
};

const formatDate = (date) => {
  if (dayjs(date).format('DD/MM/YYYY') === 'Invalid Date') return ''
  else return dayjs(date).format('DD/MM/YYYY');
};

</script>
