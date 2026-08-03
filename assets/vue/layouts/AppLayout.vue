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
<style lang="scss" scoped>
main:has(> .container-w-100) {
    max-width: 100%;
}
</style>
