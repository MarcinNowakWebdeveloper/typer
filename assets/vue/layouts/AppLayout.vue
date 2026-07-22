<script setup>
import AppNavbar from '@/components/navigation/AppNavbar.vue'
</script>

<template>
    <div class="app-wrappe">
        <AppNavbar />

        <main class="container py-4">
            <router-view />
        </main>
    </div>
</template>
