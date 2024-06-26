<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ActorDetails from '@/Components/MyComponents/ActorDetails.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    actorId: String,
    auth: Object,
});
console.log(props);
</script>

<template>
    <Head title="Actor/Actriz" />

    <AuthenticatedLayout>
        <div class="py-2">
            <ActorDetails :actorId="actorId" :auth="auth"></ActorDetails>
        </div> 
    </AuthenticatedLayout>
</template>