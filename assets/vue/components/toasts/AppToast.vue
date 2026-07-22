<script setup>
import { useToastStore } from '@/stores/toast'

const toastStore = useToastStore()
</script>

<template>
    <div class="toast-container position-fixed top-50 start-50 p-3">
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
<style lang="scss" scoped>
.toast-container {
    z-index: 9999;
    transform: translate(-50%, -50%);
}
</style>
