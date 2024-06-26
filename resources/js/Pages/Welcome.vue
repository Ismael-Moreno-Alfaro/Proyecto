<script setup>
import { Head, Link } from '@inertiajs/vue3';
import SlideGroup from '@/Components/MyComponents/SlideGroup.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>

    <Head title="Inicio" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">

                        <svg width="95" height="60" xmlns="http://www.w3.org/2000/svg">


                            <!-- TV Rectangle -->
                            <rect x="4" y="10" width="90" height="40" rx="10" ry="10" fill="white" stroke="black"
                                stroke-width="2" />

                            <!-- Icon (Eye) -->
                            <circle cx="30" cy="30" r="12" stroke="black" stroke-width="4" fill="black" />
                            <circle cx="30" cy="30" r="6" fill="#fff" />
                            <circle cx="30" cy="30" r="3" fill="black" />

                            <!-- Text -->
                            <text x="68" y="39" font-family="Arial, sans-serif" font-size="24" fill="#333"
                                text-anchor="middle">MV</text>
                        </svg>
                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                        Inicio
                        </Link>

                        <template v-else>
                            <Link :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                            Iniciar Sesión
                            </Link>

                            <Link v-if="canRegister" :href="route('register')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                            Registrarse
                            </Link>
                        </template>
                    </nav>
                </header>

                <main class="mt-6">
                    <SlideGroup></SlideGroup>
                </main>

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    Mis Visionados - 2024
                </footer>
            </div>
        </div>
    </div>
</template>
