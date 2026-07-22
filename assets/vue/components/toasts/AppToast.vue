<script setup>
import { useToastStore } from '@/stores/toast'

const toastStore = useToastStore()
</script>

<template>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999">
        <div
            v-for="toast in toastStore.toasts"
            :key="toast.id"
            class="toast show text-white border-0 mb-2"
            :class="'bg-' + toast.type"
        >
            <div class="d-flex">
                <div class="toast-body">
                    {{ toast.message }}
                </div>

                <button class="btn-close btn-close-white me-2 m-auto" @click="toastStore.remove(toast.id)" />
            </div>
        </div>
    </div>
</template>
