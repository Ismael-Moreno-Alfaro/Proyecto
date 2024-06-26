<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
        <div>
            <Link href="/">
                <svg width="160" height="110" xmlns="http://www.w3.org/2000/svg">
  <!-- Background -->
  <rect width="100%" height="100%" fill="rgb(107 114 128)"/>

  <!-- TV Rectangle -->
  <rect x="10" y="10" width="140" height="90" rx="10" ry="10" fill="white" stroke="black" stroke-width="2"/>

  <!-- Icon (Eye) -->
  <circle cx="50" cy="55" r="24" stroke="black" stroke-width="4" fill="black"/>
  <circle cx="50" cy="55" r="12" fill="#fff"/>
  <circle cx="50" cy="55" r="6" fill="black"/>

  <!-- Text -->
  <text x="110" y="65" font-family="Arial, sans-serif" font-size="32" fill="#333" text-anchor="middle">MV</text>
</svg>
                <!-- <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" /> -->
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
