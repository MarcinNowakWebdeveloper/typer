<script setup>
import { useAuthStore } from '@/stores/auth.js'

const auth = useAuthStore()
</script>

<template>
    <div>
        <h1>Dashboard</h1>

        <p>Witaj {{ auth.user.name }}</p>
    </div>
</template>
