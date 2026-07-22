<script setup>
import { computed } from 'vue'

const props = defineProps({
    type: {
        type: String,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
})

const classes = computed(() => {
    switch (props.type) {
        case 'active':
            return 'badge bg-success-subtle text-success'

        case 'inactive':
            return 'badge bg-danger-subtle text-danger'

        case 'confirmed':
            return 'badge bg-success-subtle text-success'

        case 'pending':
            return 'badge bg-warning-subtle text-warning'

        default:
            return 'badge bg-secondary'
    }
})

const icon = computed(() => {
    switch (props.type) {
        case 'active':
            return 'bi-check-circle'

        case 'inactive':
            return 'bi-x-circle'

        case 'confirmed':
            return 'bi-envelope-check'

        case 'pending':
            return 'bi-envelope-arrow-up'

        default:
            return ''
    }
})
</script>

<template>
    <span v-tooltip :class="classes" :title="title">
        <i :class="['bi', icon]" />
        <span class="d-none d-lg-inline ms-2">
            {{ props.title }}
        </span>
    </span>
</template>

<style lang="scss">
.badge {
    --bs-badge-font-size: 0.85em !important;
}

@media (max-width: 930px) {
    .badge {
        --bs-badge-font-size: 1em !important;
    }
}
</style>
