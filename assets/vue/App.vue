<script setup>
import { useAuthStore } from '@/stores/auth'
import AppToast from '@/components/toasts/AppToast.vue'
import AppLoader from '@/components/loaders/AppLoader.vue'

const auth = useAuthStore()
</script>

<template>
    <AppLoader v-if="!auth.initialized" />

    <router-view v-else />

    <AppToast />
</template>
