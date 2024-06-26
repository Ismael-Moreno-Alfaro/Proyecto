<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AdminUsers from '@/Components/MyComponents/AdminUsers.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';
  
  // Definir props
  const props = defineProps({
    users: Array
  });
</script>

<template>
    <Head title="Admin - Usuarios" />

    <AdminLayout>
        <div class="py-2">
            <AdminUsers :users="props.users"></AdminUsers>
        </div> 
    </AdminLayout>
</template>